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
        \FPForms\Core\Logger::debug( 'AJAX Submission received', [
            'POST_keys' => array_keys( $_POST ),
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

        $submit_token = isset( $_POST['fp_submit_token'] ) ? sanitize_text_field( wp_unslash( $_POST['fp_submit_token'] ) ) : '';
        if ( $submit_token !== '' ) {
            $lock_key = 'fp_forms_submit_lock_' . $submit_token;
            $lock_val = get_transient( $lock_key );
            if ( $lock_val !== false && $lock_val !== 0 && $lock_val !== '0' ) {
                \FPForms\Core\Logger::warning( 'Submission rejected: submit token already used', [ 'form_id' => $form_id ] );
                wp_send_json_error( [
                    'message' => __( 'Questo modulo è già stato inviato. Non inviare di nuovo.', 'fp-forms' ),
                ] );
            }
        }
        
        // Ottieni i dati del form
        // Limite di profondità esplicito per prevenire stack overflow su payload profondamente annidati
        $form_data = isset( $_POST['form_data'] ) ? json_decode( stripslashes( $_POST['form_data'] ), true, 20 ) : [];
        
        if ( ! is_array( $form_data ) ) {
            $form_data = [];
        }
        
        // Rimuovi prefisso "fp_field_" dai nomi dei campi (solo se è il prefisso iniziale)
        $cleaned_data = [];
        foreach ( $form_data as $key => $value ) {
            $clean_key = strpos( $key, 'fp_field_' ) === 0 ? substr( $key, strlen( 'fp_field_' ) ) : $key;
            // Evita sovrascrittura silenziosa di chiavi duplicate
            if ( ! isset( $cleaned_data[ $clean_key ] ) ) {
                $cleaned_data[ $clean_key ] = $value;
            }
        }
        $form_data = $cleaned_data;
        
        \FPForms\Core\Logger::debug( 'Form data parsed and cleaned', [
            'form_id'    => $form_id,
            'data_keys'  => array_keys( $form_data ),
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

        $antispam = new \FPForms\Security\AntiSpam();
        if ( method_exists( $antispam, 'compute_spam_score' ) ) {
            $spam_context = [ 'recaptcha_score' => $recaptcha_validation['score'] ?? null ];
            $spam_score = $antispam->compute_spam_score( $form_id, $form_data, $spam_context );
            $threshold = (int) get_option( 'fp_forms_spam_score_threshold', 80 );
            if ( $spam_score >= $threshold ) {
                \FPForms\Core\Logger::warning( 'Submission blocked by spam score', [
                    'form_id' => $form_id,
                    'score'   => $spam_score,
                    'threshold' => $threshold,
                ] );
                wp_send_json_error( [
                    'message' => __( 'Non è possibile inviare il form. Riprova più tardi.', 'fp-forms' ),
                ] );
            }
        }
        
        // Sanitizza prima di validare (i validator ricevono dati già puliti)
        $sanitized_data = $this->sanitize_data( $form_data, $form_id );
        
        // Valida i dati sanitizzati
        $validation = $this->validate_submission( $form_id, $sanitized_data );
        
        \FPForms\Core\Logger::debug( 'Validation result', [
            'valid'       => $validation['valid'],
            'errors'      => $validation['errors'],
            'error_count' => count( $validation['errors'] ),
        ] );
        
        if ( ! $validation['valid'] ) {
            wp_send_json_error( [
                'message' => __( 'Alcuni campi non sono validi.', 'fp-forms' ),
                'errors' => $validation['errors'],
            ] );
        }
        
        // Ricalcola i campi di tipo "calculated" server-side per prevenire manipolazioni client
        $sanitized_data = $this->recalculate_calculated_fields( $form_id, $sanitized_data );
        
        $db = \FPForms\Plugin::instance()->database;
        $payments = \FPForms\Plugin::instance()->payments;
        $form_requires_payment = $payments && method_exists( $payments, 'form_requires_payment' ) && $payments->form_requires_payment( $form_id );
        $save_status = $form_requires_payment ? 'pending_payment' : 'unread';
        $submission_id = $db->save_submission( $form_id, $sanitized_data, [ 'status' => $save_status ] );
        
        if ( ! $submission_id ) {
            wp_send_json_error( [
                'message' => __( 'Errore nel salvare i dati. Riprova.', 'fp-forms' ),
            ] );
        }

        if ( $submit_token !== '' ) {
            set_transient( 'fp_forms_submit_lock_' . $submit_token, $submission_id, 120 );
        }
        
        // Gestisci upload file dopo il salvataggio DB
        $uploaded_files = $this->handle_file_uploads( $form_id );
        
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

        $email_manager = \FPForms\Plugin::instance()->email;
        $queue_enabled = $email_manager && method_exists( $email_manager, 'is_queue_enabled' ) && $email_manager->is_queue_enabled();
        
        if ( ! $emails_disabled && ! $form_requires_payment ) {
            if ( $queue_enabled ) {
                $email_manager->enqueue_email_job( (int) $form_id, (int) $submission_id, 'notification' );
                $email_manager->enqueue_email_job( (int) $form_id, (int) $submission_id, 'confirmation' );
                $email_manager->enqueue_email_job( (int) $form_id, (int) $submission_id, 'staff' );
            } else {
                try {
                    $this->send_notification( $form_id, $submission_id, $sanitized_data );
                } catch ( \Throwable $e ) {
                    \FPForms\Core\Logger::error( 'Admin notification failed', [
                        'submission_id' => $submission_id,
                        'error' => $e->getMessage(),
                    ] );
                }
                try {
                    $this->send_confirmation( $form_id, $sanitized_data );
                } catch ( \Throwable $e ) {
                    \FPForms\Core\Logger::error( 'User confirmation failed', [
                        'submission_id' => $submission_id,
                        'error' => $e->getMessage(),
                    ] );
                }
                try {
                    $this->send_staff_notifications( $form_id, $submission_id, $sanitized_data );
                } catch ( \Throwable $e ) {
                    \FPForms\Core\Logger::error( 'Staff notifications failed', [
                        'submission_id' => $submission_id,
                        'error' => $e->getMessage(),
                    ] );
                }
            }
        }
        
        // Ottieni messaggio di successo personalizzato
        $success_message = isset( $form['settings']['success_message'] ) && ! empty( $form['settings']['success_message'] )
            ? $form['settings']['success_message']
            : __( 'Grazie! Il tuo messaggio è stato inviato con successo.', 'fp-forms' );
        
        // Replace tag dinamici nel messaggio di successo
        $success_message = $this->replace_success_tags( $success_message, $form, $sanitized_data );
        
        // Permette a QuickFeatures (e plugin terzi) di modificare il messaggio o aggiungere redirect
        $success_message = apply_filters( 'fp_forms_success_message', $success_message, $form_id, $sanitized_data );
        
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
        
        // 🔥 HOOK CRITICO: Trigger per integrazioni esterne (TrackingBridge, Brevo, Meta CAPI, etc.)
        // Il tracking è gestito da FP-Marketing-Tracking-Layer via TrackingBridge
        do_action( 'fp_forms_after_save_submission', $submission_id, $form_id, $sanitized_data );
        
        $response = [
            'message' => $success_message,
            'message_type' => $message_type,
            'message_duration' => $message_duration,
            'submission_id' => $submission_id,
        ];

        if ( $form_requires_payment && $payments ) {
            $checkout = $payments->get_checkout_response( (int) $submission_id, (int) $form_id, $sanitized_data );
            if ( ! empty( $checkout['error'] ) ) {
                wp_send_json_error( [ 'message' => $checkout['error'] ] );
            }
            if ( ! empty( $checkout['checkout_url'] ) ) {
                $response['payment_required'] = true;
                $response['checkout_url'] = $checkout['checkout_url'];
            } elseif ( ! empty( $checkout['client_secret'] ) ) {
                $response['payment_required'] = true;
                $response['client_secret'] = $checkout['client_secret'];
            }
        }
        
        $response = apply_filters( 'fp_forms_ajax_response', $response, $form_id, $sanitized_data );
        
        wp_send_json_success( $response );
    }
    
    /**
     * Valida i dati della submission
     */
    private function validate_submission( $form_id, $data ) {
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        if ( ! is_array( $fields ) ) {
            $fields = [];
        }

        $visible_fields  = [];
        $required_fields = [];
        $form = $forms_manager->get_form( $form_id );
        if ( $form && ! empty( $form['settings']['conditional_rules'] ) ) {
            $logic = \FPForms\Plugin::instance()->conditional_logic;
            if ( $logic && method_exists( $logic, 'evaluate_rules' ) ) {
                $evaluated = $logic->evaluate_rules( $form_id, $data );
                $visible_fields  = isset( $evaluated['visible_fields'] ) ? $evaluated['visible_fields'] : [];
                $required_fields = isset( $evaluated['required_fields'] ) ? $evaluated['required_fields'] : [];
            }
        }
        
        $validator = new \FPForms\Validators\Validator();
        
        foreach ( $fields as $field ) {
            $field_name = $field['name'];
            $field_label = $field['label'];
            $field_type = isset( $field['type'] ) ? $field['type'] : 'text';
            $custom_error = isset( $field['options']['error_message'] ) ? $field['options']['error_message'] : '';

            if ( ! empty( $visible_fields ) && ! in_array( $field_name, $visible_fields, true ) ) {
                continue;
            }

            $is_required = ! empty( $field['required'] ) || in_array( $field_name, $required_fields, true );

            if ( $field_type === 'fullname' ) {
                $field_value_nome = isset( $data[ $field_name . '_nome' ] ) ? $data[ $field_name . '_nome' ] : '';
                $field_value_cognome = isset( $data[ $field_name . '_cognome' ] ) ? $data[ $field_name . '_cognome' ] : '';
                $nome_visible = empty( $visible_fields ) || in_array( $field_name . '_nome', $visible_fields, true );
                $cognome_visible = empty( $visible_fields ) || in_array( $field_name . '_cognome', $visible_fields, true );
                if ( $nome_visible && ( $is_required || in_array( $field_name . '_nome', $required_fields, true ) ) ) {
                    $validator->validate_required( $field_value_nome, $field_name . '_nome', __( 'Nome', 'fp-forms' ), $custom_error );
                }
                if ( $cognome_visible && ( $is_required || in_array( $field_name . '_cognome', $required_fields, true ) ) ) {
                    $validator->validate_required( $field_value_cognome, $field_name . '_cognome', __( 'Cognome', 'fp-forms' ), $custom_error );
                }
                continue;
            }

            $field_value = isset( $data[ $field_name ] ) ? $data[ $field_name ] : '';
            
            if ( $is_required ) {
                $validator->validate_required( $field_value, $field_name, $field_label, $custom_error );
            }
            
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
                
                case 'select':
                case 'radio':
                    $choices = $field['options']['choices'] ?? [];
                    $validator->validate_choices( $field_value, $choices, $field_name, $field_label );
                    break;
                
                case 'checkbox':
                    $choices = $field['options']['choices'] ?? [];
                    if ( ! empty( $choices ) ) {
                        $validator->validate_choices( $field_value, $choices, $field_name, $field_label );
                    }
                    break;
            }
        }
        
        $errors = $validator->get_errors();
        
        $errors = \FPForms\Core\Hooks::filter_validation_errors( $errors, $form_id, $data );
        
        return [
            'valid' => empty( $errors ),
            'errors' => $errors,
        ];
    }
    
    /**
     * Sanitizza i dati
     */
    private function sanitize_data( $data, $form_id = 0 ) {
        if ( ! $form_id ) {
            $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        }
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        if ( ! is_array( $fields ) ) {
            $fields = [];
        }
        
        $sanitizer = new \FPForms\Sanitizers\Sanitizer();
        $sanitized = $sanitizer->sanitize_submission_data( $data, $fields );
        
        // Applica filtro per permettere sanitizzazione custom
        $sanitized = \FPForms\Core\Hooks::filter_submission_data( $sanitized, $form_id );
        
        return $sanitized;
    }
    
    /**
     * Ricalcola i campi di tipo "calculated" server-side.
     * Previene la manipolazione del valore calcolato da parte del client.
     * La formula usa i nomi dei campi (es. {field_name}) sostituiti con i valori numerici sanitizzati.
     * Usa un parser matematico ricorsivo senza eval().
     */
    private function recalculate_calculated_fields( $form_id, $data ) {
        $fields = \FPForms\Plugin::instance()->forms->get_fields( $form_id );
        
        if ( ! is_array( $fields ) ) {
            return $data;
        }
        
        foreach ( $fields as $field ) {
            if ( ( $field['type'] ?? '' ) !== 'calculated' ) {
                continue;
            }
            
            $formula = $field['options']['formula'] ?? '';
            if ( empty( $formula ) ) {
                continue;
            }
            
            // Sostituisce i placeholder {nome_campo} con i valori numerici sanitizzati
            $expression = $formula;
            foreach ( $data as $key => $value ) {
                $numeric = is_numeric( $value ) ? (float) $value : 0;
                $expression = str_replace( '{' . $key . '}', (string) $numeric, $expression );
            }
            
            // Rimuove placeholder non sostituiti
            $expression = preg_replace( '/\{[^}]+\}/', '0', $expression );
            
            // Valida che l'espressione contenga solo caratteri matematici sicuri
            if ( ! preg_match( '/^[0-9+\-*\/().\s]+$/', $expression ) ) {
                \FPForms\Core\Logger::warning( 'Calculated field: formula non sicura ignorata', [
                    'form_id'    => $form_id,
                    'field_name' => $field['name'],
                ] );
                continue;
            }
            
            // Normalizza doppi segni per prevenire ricorsione infinita nel parser:
            // -- → +, -+ → -, +- → -, ++ → +
            $expression = str_replace( [ '--', '-+', '+-', '++' ], [ '+', '-', '-', '+' ], $expression );
            
            // Parser matematico sicuro senza eval()
            $result = $this->math_evaluate( trim( $expression ) );
            
            $decimals = isset( $field['options']['decimals'] ) ? (int) $field['options']['decimals'] : 2;
            $data[ $field['name'] ] = round( (float) $result, $decimals );
        }
        
        return $data;
    }
    
    /**
     * Parser matematico ricorsivo per espressioni con +, -, *, /, parentesi.
     * Non usa eval(). Supporta numeri decimali e negativi.
     *
     * @param string $expr Espressione già validata (solo [0-9+\-*\/().\s])
     * @return float
     */
    private function math_evaluate( $expr ) {
        $expr = str_replace( ' ', '', $expr );
        
        if ( $expr === '' ) {
            return 0.0;
        }
        
        // Gestisce addizione e sottrazione (priorità più bassa, valutate per ultime)
        $result = $this->math_parse_additive( $expr, 0, strlen( $expr ) );
        return is_numeric( $result ) ? (float) $result : 0.0;
    }
    
    private function math_parse_additive( $expr, $start, $end ) {
        $depth  = 0;
        $result = null;
        $op     = '+';
        $i      = $start;
        $seg_start = $start;
        
        while ( $i <= $end ) {
            $ch = $i < $end ? $expr[ $i ] : null;
            
            if ( $ch === '(' ) {
                $depth++;
            } elseif ( $ch === ')' ) {
                $depth--;
            }
            
            $is_additive = $depth === 0 && ( $ch === '+' || $ch === '-' );
            // Segno unario all'inizio o dopo un operatore: non è addizione
            $is_unary = $is_additive && ( $i === $start || in_array( $expr[ $i - 1 ] ?? '', [ '+', '-', '*', '/', '(' ], true ) );
            
            if ( ( $is_additive && ! $is_unary ) || $ch === null ) {
                $segment = substr( $expr, $seg_start, $i - $seg_start );
                $val     = $this->math_parse_multiplicative( $segment, 0, strlen( $segment ) );
                
                if ( $result === null ) {
                    $result = (float) $val;
                } elseif ( $op === '+' ) {
                    $result += (float) $val;
                } else {
                    $result -= (float) $val;
                }
                
                $op        = $ch;
                $seg_start = $i + 1;
            }
            
            $i++;
        }
        
        return $result ?? 0.0;
    }
    
    private function math_parse_multiplicative( $expr, $start, $end ) {
        $depth     = 0;
        $result    = null;
        $op        = '*';
        $i         = $start;
        $seg_start = $start;
        $first     = true;
        
        while ( $i <= $end ) {
            $ch = $i < $end ? $expr[ $i ] : null;
            
            if ( $ch === '(' ) {
                $depth++;
            } elseif ( $ch === ')' ) {
                $depth--;
            }
            
            $is_mult = $depth === 0 && ( $ch === '*' || $ch === '/' );
            
            if ( $is_mult || $ch === null ) {
                $segment = substr( $expr, $seg_start, $i - $seg_start );
                $val     = $this->math_parse_primary( $segment );
                
                if ( $first ) {
                    $result = (float) $val;
                    $first  = false;
                } elseif ( $op === '*' ) {
                    $result *= (float) $val;
                } else {
                    $divisor = (float) $val;
                    $result  = $divisor != 0.0 ? $result / $divisor : 0.0;
                }
                
                $op        = $ch;
                $seg_start = $i + 1;
            }
            
            $i++;
        }
        
        return $result ?? 0.0;
    }
    
    private function math_parse_primary( $expr ) {
        $expr = trim( $expr );
        
        if ( $expr === '' ) {
            return 0.0;
        }
        
        // Parentesi: verifica che inizino e finiscano con () E che siano bilanciate
        if ( $expr[0] === '(' && $expr[ strlen( $expr ) - 1 ] === ')' ) {
            // Verifica bilanciamento: la parentesi aperta iniziale deve chiudersi all'ultima posizione
            $depth = 0;
            $len   = strlen( $expr );
            for ( $i = 0; $i < $len - 1; $i++ ) {
                if ( $expr[ $i ] === '(' ) {
                    $depth++;
                } elseif ( $expr[ $i ] === ')' ) {
                    $depth--;
                }
                // Se la profondità torna a 0 prima della fine, le parentesi esterne non avvolgono tutto
                if ( $depth === 0 ) {
                    break;
                }
            }
            if ( $depth > 0 ) {
                return $this->math_evaluate( substr( $expr, 1, $len - 2 ) );
            }
        }
        
        // Numero (inclusi negativi e decimali)
        if ( is_numeric( $expr ) ) {
            return (float) $expr;
        }
        
        // Segno unario negativo: -(...) o -numero
        if ( $expr[0] === '-' ) {
            return -1.0 * (float) $this->math_parse_primary( substr( $expr, 1 ) );
        }
        
        return 0.0;
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
        $user_email = null;
        
        // 1. Cerca per TIPO di campo (approccio più affidabile)
        $forms_manager = \FPForms\Plugin::instance()->forms;
        $fields = $forms_manager->get_fields( $form_id );
        
        if ( is_array( $fields ) ) {
            foreach ( $fields as $field ) {
                if ( isset( $field['type'] ) && $field['type'] === 'email' ) {
                    $field_name = $field['name'];
                    if ( isset( $data[ $field_name ] ) && is_email( $data[ $field_name ] ) ) {
                        $user_email = $data[ $field_name ];
                        break;
                    }
                }
            }
        }
        
        // 2. Fallback: cerca per NOME del campo contenente "email"
        if ( ! $user_email ) {
            foreach ( $data as $key => $value ) {
                if ( is_string( $value ) && stripos( $key, 'email' ) !== false && is_email( $value ) ) {
                    $user_email = $value;
                    break;
                }
            }
        }
        
        // Il terzo fallback (qualsiasi email nei dati) è stato rimosso perché troppo permissivo:
        // potrebbe inviare la conferma all'indirizzo sbagliato (es. email dell'admin nel form).
        // Se i primi due metodi falliscono, la conferma viene semplicemente saltata.
        
        if ( ! $user_email ) {
            \FPForms\Core\Logger::warning( 'Confirmation email skipped: no user email found in form data', [
                'form_id' => $form_id,
                'data_keys' => array_keys( $data ),
            ] );
            return;
        }
        
        \FPForms\Core\Logger::debug( 'Sending confirmation email', [
            'form_id' => $form_id,
            'user_email' => $user_email,
        ] );
        
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
            
            $submission->data = is_array( $decoded ) ? $decoded : [];
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
        
        if ( ! is_array( $fields ) ) {
            return [];
        }
        
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
                'score' => null,
            ];
        }
        
        return [
            'valid' => true,
            'error' => null,
            'score' => isset( $result['score'] ) ? (float) $result['score'] : null,
        ];
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
