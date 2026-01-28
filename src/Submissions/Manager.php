<?php
namespace FPForms\Submissions;

/**
 * Gestisce le submissions dei form
 */
class Manager {
    
    /**
     * Costruttore
     */
    public function __construct() {
        add_action( 'wp_ajax_fp_forms_submit', [ $this, 'handle_submission' ] );
        add_action( 'wp_ajax_nopriv_fp_forms_submit', [ $this, 'handle_submission' ] );
    }
    
    /**
     * Gestisce la submission di un form via AJAX
     */
    public function handle_submission() {
        // DEBUG: Log raw POST data
        \FPForms\Core\Logger::debug( 'AJAX Submission received', [
            'POST_keys' => array_keys( $_POST ),
            'form_data_raw' => isset( $_POST['form_data'] ) ? $_POST['form_data'] : 'NOT SET',
        ] );
        
        // Verifica nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'fp_forms_submit' ) ) {
            wp_send_json_error( [
                'message' => __( 'Errore di sicurezza. Riprova.', 'fp-forms' ),
            ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_send_json_error( [
                'message' => __( 'Form non valido.', 'fp-forms' ),
            ] );
        }
        
        // Ottieni i dati del form
        $form_data = isset( $_POST['form_data'] ) ? json_decode( stripslashes( $_POST['form_data'] ), true ) : [];
        
        if ( ! is_array( $form_data ) ) {
            $form_data = [];
        }
        
        // BUGFIX: Rimuovi prefisso "fp_field_" dai nomi dei campi
        // Il frontend aggiunge "fp_field_" ma il validator si aspetta solo il nome del campo
        $cleaned_data = [];
        foreach ( $form_data as $key => $value ) {
            // Rimuovi prefisso "fp_field_" se presente
            $clean_key = str_replace( 'fp_field_', '', $key );
            $cleaned_data[ $clean_key ] = $value;
        }
        $form_data = $cleaned_data;
        
        // DEBUG: Log parsed data
        \FPForms\Core\Logger::debug( 'Form data parsed and cleaned', [
            'form_id' => $form_id,
            'form_data' => $form_data,
            'data_count' => count( $form_data ),
        ] );
        
        // Hook PRIMA della validazione (per anti-spam)
        do_action( 'fp_forms_before_validate_submission', $form_id, $form_data );
        
        // Verifica reCAPTCHA se presente nel form
        $recaptcha_validation = $this->validate_recaptcha( $form_id, $_POST );
        if ( ! $recaptcha_validation['valid'] ) {
            wp_send_json_error( [
                'message' => $recaptcha_validation['error'],
                'errors' => [ 'recaptcha' => $recaptcha_validation['error'] ],
            ] );
        }
        
        // Valida i dati
        $validation = $this->validate_submission( $form_id, $form_data );
        
        // DEBUG: Log validation result
        \FPForms\Core\Logger::debug( 'Validation result', [
            'valid' => $validation['valid'],
            'errors' => $validation['errors'],
            'error_count' => count( $validation['errors'] ),
        ] );
        
        if ( ! $validation['valid'] ) {
            wp_send_json_error( [
                'message' => __( 'Alcuni campi non sono validi.', 'fp-forms' ),
                'errors' => $validation['errors'],
            ] );
        }
        
        // Sanitizza i dati
        $sanitized_data = $this->sanitize_data( $form_data );
        
        // Gestisci upload file se presenti
        $uploaded_files = $this->handle_file_uploads( $form_id );
        
        // Salva la submission
        $db = \FPForms\Plugin::instance()->database;
        $submission_id = $db->save_submission( $form_id, $sanitized_data );
        
        if ( ! $submission_id ) {
            wp_send_json_error( [
                'message' => __( 'Errore nel salvare i dati. Riprova.', 'fp-forms' ),
            ] );
        }
        
        // Salva riferimenti file se presenti
        if ( ! empty( $uploaded_files ) ) {
            $this->save_submission_files( $submission_id, $uploaded_files );
        }
        
        // Ottieni form per controllare impostazioni
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        // BUGFIX #8: Null check - Se form non esiste, usa default
        if ( ! $form || ! is_array( $form ) ) {
            \FPForms\Core\Logger::error( 'Form not found during submission', [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
            $form = [ 'settings' => [], 'title' => 'Unknown Form' ];
        }
        
        // Check se email WordPress sono disabilitate (usa solo Brevo/CRM)
        $emails_disabled = isset( $form['settings']['disable_wordpress_emails'] ) && $form['settings']['disable_wordpress_emails'];
        
        if ( $emails_disabled ) {
            \FPForms\Core\Logger::info( 'WordPress emails disabled for this form, using only external integrations (Brevo/Meta)', [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
        }
        
        // 1. Invia email di notifica al webmaster/admin (solo se email attive)
        if ( ! $emails_disabled ) {
            try {
                $this->send_notification( $form_id, $submission_id, $sanitized_data );
            } catch ( \Exception $e ) {
                \FPForms\Core\Logger::error( 'Admin notification failed', [
                    'submission_id' => $submission_id,
                    'error' => $e->getMessage(),
                ] );
                // Non bloccare submission se email fallisce
            }
        }
        
        // 2. Invia email di conferma al cliente (solo se email attive E abilitata)
        if ( ! $emails_disabled ) {
            try {
                $this->send_confirmation( $form_id, $sanitized_data );
            } catch ( \Exception $e ) {
                \FPForms\Core\Logger::error( 'User confirmation failed', [
                    'submission_id' => $submission_id,
                    'error' => $e->getMessage(),
                ] );
            }
        }
        
        // 3. Invia notifiche allo staff (solo se email attive E configurato)
        if ( ! $emails_disabled ) {
            try {
                $this->send_staff_notifications( $form_id, $submission_id, $sanitized_data );
            } catch ( \Exception $e ) {
                \FPForms\Core\Logger::error( 'Staff notifications failed', [
                    'submission_id' => $submission_id,
                    'error' => $e->getMessage(),
                ] );
            }
        }
        
        // Ottieni messaggio di successo personalizzato
        $success_message = isset( $form['settings']['success_message'] ) && ! empty( $form['settings']['success_message'] )
            ? $form['settings']['success_message']
            : __( 'Grazie! Il tuo messaggio è stato inviato con successo.', 'fp-forms' );
        
        // Replace tag dinamici nel messaggio di successo
        $success_message = $this->replace_success_tags( $success_message, $form, $sanitized_data );
        
        // Ottieni tipo e durata messaggio con validazione
        $message_type = isset( $form['settings']['success_message_type'] ) ? $form['settings']['success_message_type'] : 'success';
        
        // BUGFIX #9: Whitelist message_type
        $allowed_types = [ 'success', 'info', 'celebration' ];
        if ( ! in_array( $message_type, $allowed_types, true ) ) {
            $message_type = 'success';
        }
        
        // BUGFIX #10: Validate duration (must be non-negative and whitelisted)
        $message_duration = isset( $form['settings']['success_message_duration'] ) ? intval( $form['settings']['success_message_duration'] ) : 0;
        $allowed_durations = [ 0, 3000, 5000, 10000 ];
        if ( ! in_array( $message_duration, $allowed_durations, true ) ) {
            $message_duration = 0;
        }
        
        // Traccia submission con GTM/GA4 (server-side logging)
        $tracking = \FPForms\Plugin::instance()->tracking;
        if ( $tracking && $tracking->is_enabled() ) {
            $tracking->track_submission( $form_id, $submission_id, true );
        }
        
        // 🔥 HOOK CRITICO: Trigger per integrazioni esterne (Brevo, Meta CAPI, etc.)
        do_action( 'fp_forms_after_save_submission', $submission_id, $form_id, $sanitized_data );
        
        // Prepara response
        $response = [
            'message' => $success_message,
            'message_type' => $message_type,
            'message_duration' => $message_duration,
            'submission_id' => $submission_id,
        ];
        
        // BUGFIX: Applica filtro per success redirect (QuickFeatures)
        $response = apply_filters( 'fp_forms_ajax_response', $response, $form_id, $sanitized_data );
        
        wp_send_json_success( $response );
    }
    
    /**
     * Valida i dati della submission
     */
    private function validate_submission( $form_id, $data ) {
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        $validator = new \FPForms\Validators\Validator();
        
        foreach ( $fields as $field ) {
            $field_name = $field['name'];
            $field_label = $field['label'];
            $field_type = isset( $field['type'] ) ? $field['type'] : 'text';
            $custom_error = isset( $field['options']['error_message'] ) ? $field['options']['error_message'] : '';

            if ( $field_type === 'fullname' ) {
                $field_value_nome = isset( $data[ $field_name . '_nome' ] ) ? $data[ $field_name . '_nome' ] : '';
                $field_value_cognome = isset( $data[ $field_name . '_cognome' ] ) ? $data[ $field_name . '_cognome' ] : '';
                if ( $field['required'] ) {
                    $validator->validate_required( $field_value_nome, $field_name . '_nome', __( 'Nome', 'fp-forms' ), $custom_error );
                    $validator->validate_required( $field_value_cognome, $field_name . '_cognome', __( 'Cognome', 'fp-forms' ), $custom_error );
                }
                continue;
            }

            $field_value = isset( $data[ $field_name ] ) ? $data[ $field_name ] : '';
            
            // Valida campo obbligatorio
            if ( $field['required'] ) {
                $validator->validate_required( $field_value, $field_name, $field_label, $custom_error );
            }
            
            // Valida in base al tipo
            switch ( $field_type ) {
                case 'email':
                    $validator->validate_email( $field_value, $field_name, $field_label, $custom_error );
                    break;
                    
                case 'phone':
                    $validator->validate_phone( $field_value, $field_name, $field_label, $custom_error );
                    break;
                    
                case 'number':
                    $validator->validate_number( $field_value, $field_name, $field_label, $field['options'] ?? [] );
                    break;
                    
                case 'date':
                    $validator->validate_date( $field_value, $field_name, $field_label, $field['options'] ?? [] );
                    break;
            }
        }
        
        $errors = $validator->get_errors();
        
        // Applica filtro per permettere validazione custom
        $errors = \FPForms\Core\Hooks::filter_validation_errors( $errors, $form_id, $data );
        
        return [
            'valid' => empty( $errors ),
            'errors' => $errors,
        ];
    }
    
    /**
     * Sanitizza i dati
     */
    private function sanitize_data( $data ) {
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        $sanitizer = new \FPForms\Sanitizers\Sanitizer();
        $sanitized = $sanitizer->sanitize_submission_data( $data, $fields );
        
        // Applica filtro per permettere sanitizzazione custom
        $sanitized = \FPForms\Core\Hooks::filter_submission_data( $sanitized, $form_id );
        
        return $sanitized;
    }
    
    /**
     * Invia notifica email al webmaster/admin
     */
    private function send_notification( $form_id, $submission_id, $data ) {
        $email_manager = \FPForms\Plugin::instance()->email;
        $email_manager->send_notification( $form_id, $submission_id, $data );
    }
    
    /**
     * Invia email di conferma al cliente
     */
    private function send_confirmation( $form_id, $data ) {
        // Estrai email dal form data
        $user_email = null;
        
        foreach ( $data as $key => $value ) {
            $clean_key = str_replace( 'fp_field_', '', $key );
            
            if ( stripos( $clean_key, 'email' ) !== false && is_email( $value ) ) {
                $user_email = $value;
                break;
            }
        }
        
        if ( ! $user_email ) {
            \FPForms\Core\Logger::warning( 'Confirmation email skipped: no user email found', [
                'form_id' => $form_id,
            ] );
            return;
        }
        
        $email_manager = \FPForms\Plugin::instance()->email;
        $email_manager->send_confirmation( $form_id, $user_email, $data );
    }
    
    /**
     * Invia notifiche allo staff
     */
    private function send_staff_notifications( $form_id, $submission_id, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return;
        }
        
        // Check se staff notifications sono abilitate
        $staff_enabled = $form['settings']['staff_notifications_enabled'] ?? false;
        
        if ( ! $staff_enabled ) {
            return;
        }
        
        // Ottieni lista email staff
        $staff_emails = $form['settings']['staff_emails'] ?? '';
        
        if ( empty( $staff_emails ) ) {
            return;
        }
        
        // Parsea email (supporta virgola, punto e virgola, newline)
        $emails = preg_split( '/[,;\n\r]+/', $staff_emails );
        $emails = array_map( 'trim', $emails );
        $emails = array_filter( $emails, 'is_email' );
        
        if ( empty( $emails ) ) {
            \FPForms\Core\Logger::warning( 'Staff notifications skipped: no valid emails', [
                'form_id' => $form_id,
            ] );
            return;
        }
        
        // Invia a ogni membro dello staff
        $email_manager = \FPForms\Plugin::instance()->email;
        
        foreach ( $emails as $staff_email ) {
            $email_manager->send_staff_notification( $form_id, $submission_id, $staff_email, $data );
        }
        
        \FPForms\Core\Logger::info( 'Staff notifications sent', [
            'form_id' => $form_id,
            'count' => count( $emails ),
        ] );
    }
    
    /**
     * Ottiene le submissions di un form
     */
    public function get_submissions( $form_id, $args = [] ) {
        $db = \FPForms\Plugin::instance()->database;
        return $db->get_submissions( $form_id, $args );
    }
    
    /**
     * Ottiene una submission
     */
    public function get_submission( $submission_id ) {
        $db = \FPForms\Plugin::instance()->database;
        $submission = $db->get_submission( $submission_id );
        
        if ( $submission ) {
            $decoded = json_decode( $submission->data, true );
            
            // Gestisci errori JSON
            if ( json_last_error() !== JSON_ERROR_NONE ) {
                \FPForms\Core\Logger::warning( 'JSON decode error in get_submission', [
                    'submission_id' => $submission_id,
                    'error' => json_last_error_msg(),
                ] );
                $decoded = [];
            }
            
            $submission->data = $decoded;
        }
        
        return $submission;
    }
    
    /**
     * Elimina una submission
     */
    public function delete_submission( $submission_id ) {
        $db = \FPForms\Plugin::instance()->database;
        return $db->delete_submission( $submission_id );
    }
    
    /**
     * Marca come letta
     */
    public function mark_as_read( $submission_id ) {
        $db = \FPForms\Plugin::instance()->database;
        return $db->update_submission_status( $submission_id, 'read' );
    }
    
    /**
     * Marca come non letta
     */
    public function mark_as_unread( $submission_id ) {
        $db = \FPForms\Plugin::instance()->database;
        return $db->update_submission_status( $submission_id, 'unread' );
    }
    
    /**
     * Gestisce upload file
     */
    private function handle_file_uploads( $form_id ) {
        if ( empty( $_FILES ) ) {
            return [];
        }
        
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        $uploaded_files = [];
        $file_handler = new \FPForms\Fields\FileField();
        
        foreach ( $fields as $field ) {
            if ( $field['type'] !== 'file' ) {
                continue;
            }
            
            $field_name = 'fp_field_' . $field['name'];
            
            if ( ! isset( $_FILES[ $field_name ] ) ) {
                continue;
            }
            
            $files = $file_handler->handle_upload( 
                $_FILES[ $field_name ], 
                $field['name'],
                $field['options'] ?? []
            );
            
            if ( ! empty( $files ) ) {
                $uploaded_files[ $field['name'] ] = $files;
            }
        }
        
        return $uploaded_files;
    }
    
    /**
     * Salva file della submission
     */
    private function save_submission_files( $submission_id, $files ) {
        global $wpdb;
        $table = $wpdb->prefix . 'fp_forms_files';
        
        foreach ( $files as $field_name => $field_files ) {
            foreach ( $field_files as $file ) {
                $wpdb->insert(
                    $table,
                    [
                        'submission_id' => $submission_id,
                        'field_name' => $field_name,
                        'file_name' => $file['name'],
                        'file_path' => $file['path'],
                        'file_url' => $file['url'],
                        'file_type' => $file['type'],
                        'file_size' => $file['size'],
                    ],
                    [ '%d', '%s', '%s', '%s', '%s', '%s', '%d' ]
                );
            }
        }
    }
    
    /**
     * Ottiene file di una submission
     */
    public function get_submission_files( $submission_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'fp_forms_files';
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE submission_id = %d ORDER BY uploaded_at DESC",
            $submission_id
        ) );
    }
    
    /**
     * Valida reCAPTCHA se presente nel form
     * 
     * @param int $form_id ID del form
     * @param array $post_data Dati POST della request
     * @return array ['valid' => bool, 'error' => string|null]
     */
    private function validate_recaptcha( $form_id, $post_data ) {
        // Ottieni configurazione form
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form || empty( $form['fields'] ) ) {
            return [ 'valid' => true, 'error' => null ];
        }
        
        // Controlla se form ha campo reCAPTCHA
        $has_recaptcha = false;
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] === 'recaptcha' ) {
                $has_recaptcha = true;
                break;
            }
        }
        
        // Se non c'è reCAPTCHA, passa validazione
        if ( ! $has_recaptcha ) {
            return [ 'valid' => true, 'error' => null ];
        }
        
        // Inizializza reCAPTCHA
        $recaptcha = new \FPForms\Security\ReCaptcha();
        
        if ( ! $recaptcha->is_configured() ) {
            // Se reCAPTCHA non è configurato ma è presente nel form, log error
            \FPForms\Core\Logger::warning( 'reCAPTCHA field present but not configured', [
                'form_id' => $form_id,
            ] );
            
            // Non bloccare submission in questo caso (graceful degradation)
            return [ 'valid' => true, 'error' => null ];
        }
        
        // Ottieni token dalla richiesta
        $token = '';
        
        if ( $recaptcha->get_version() === 'v3' ) {
            // v3: token invisibile
            $token = isset( $post_data['fp_recaptcha_token'] ) ? sanitize_text_field( $post_data['fp_recaptcha_token'] ) : '';
        } else {
            // v2: risposta checkbox
            $token = isset( $post_data['g-recaptcha-response'] ) ? sanitize_text_field( $post_data['g-recaptcha-response'] ) : '';
        }
        
        // Verifica token
        $result = $recaptcha->verify( $token );
        
        // Log per debug
        \FPForms\Core\Logger::info( 'reCAPTCHA verification', [
            'form_id' => $form_id,
            'version' => $recaptcha->get_version(),
            'success' => $result['success'],
            'score' => $result['score'],
        ] );
        
        if ( ! $result['success'] ) {
            return [
                'valid' => false,
                'error' => $result['error'] ?? __( 'Verifica reCAPTCHA fallita', 'fp-forms' ),
            ];
        }
        
        return [ 'valid' => true, 'error' => null ];
    }
    
    /**
     * Replace tag dinamici nel messaggio di successo
     */
    private function replace_success_tags( $message, $form, $data ) {
        // Prepara replacements array (performance optimization)
        $replacements = [];
        
        // Tag form (escaped per sicurezza)
        $replacements['{form_title}'] = esc_html( $form['title'] ?? '' );
        $replacements['{site_name}'] = esc_html( get_bloginfo( 'name' ) );
        $replacements['{site_url}'] = esc_url( home_url() );
        
        // Tag data/ora
        $replacements['{date}'] = esc_html( date_i18n( get_option( 'date_format' ) ) );
        $replacements['{time}'] = esc_html( date_i18n( get_option( 'time_format' ) ) );
        
        // Tag campi form (tutti i campi submitted) - ESCAPED per XSS protection
        foreach ( $data as $field_name => $field_value ) {
            // Gestisci array (checkbox multipli, ecc)
            if ( is_array( $field_value ) ) {
                // Filtra solo scalari (no array multidimensionali)
                $field_value = array_filter( $field_value, 'is_scalar' );
                $field_value = implode( ', ', array_map( 'esc_html', $field_value ) );
            } elseif ( is_object( $field_value ) ) {
                // Skip oggetti
                $field_value = '';
            } else {
                // Escape valore scalare
                $field_value = esc_html( (string) $field_value );
            }
            
            $replacements['{' . $field_name . '}'] = $field_value;
        }
        
        // Single str_replace per performance
        $message = str_replace( array_keys( $replacements ), array_values( $replacements ), $message );
        
        return $message;
    }
}
