<?php
declare(strict_types=1);

namespace FPForms\Email;

/**
 * Gestisce l'invio delle email
 */
class Manager {

    /**
     * Nome azione cron per elaborazione coda email
     */
    const CRON_ACTION = 'fp_forms_process_email_queue';

    /**
     * Registra hook cron per coda email e invio email dopo pagamento completato
     */
    public function __construct() {
        add_action( self::CRON_ACTION, [ $this, 'process_queue_job' ], 10, 3 );
        add_action( 'fp_forms_payment_completed', [ $this, 'send_emails_after_payment' ], 10, 3 );
    }

    /**
     * Invia (o accoda) le email dopo il completamento del pagamento Stripe.
     *
     * @param int   $submission_id   ID submission.
     * @param int   $form_id        ID form.
     * @param array $transaction_data Dati transazione (per log).
     */
    public function send_emails_after_payment( int $submission_id, int $form_id, array $transaction_data = [] ): void {
        $db = \FPForms\Plugin::instance()->database;
        $submission = $db->get_submission( $submission_id );
        if ( ! $submission || (int) $submission->form_id !== $form_id ) {
            return;
        }
        $data = isset( $submission->data ) ? json_decode( $submission->data, true ) : [];
        if ( ! is_array( $data ) ) {
            $data = [];
        }
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        if ( ! $form || ! empty( $form['settings']['disable_wordpress_emails'] ) ) {
            return;
        }
        if ( $this->is_queue_enabled() ) {
            $this->enqueue_email_job( $form_id, $submission_id, 'notification' );
            $this->enqueue_email_job( $form_id, $submission_id, 'confirmation' );
            $this->enqueue_email_job( $form_id, $submission_id, 'staff' );
        } else {
            $this->send_notification( $form_id, $submission_id, $data );
            $user_email = $this->get_user_email_from_data( $form_id, $data );
            if ( $user_email ) {
                $this->send_confirmation( $form_id, $user_email, $data );
            }
            $this->send_staff_notifications_bulk( $form_id, $submission_id, $data );
        }
    }

    /**
     * Verifica se la coda email è abilitata
     */
    public function is_queue_enabled(): bool {
        return (bool) get_option( 'fp_forms_email_queue_enabled', true );
    }

    /**
     * Accoda un job email (notification, confirmation, staff).
     *
     * @param int    $form_id       ID form.
     * @param int    $submission_id ID submission.
     * @param string $type          'notification' | 'confirmation' | 'staff'.
     */
    public function enqueue_email_job( int $form_id, int $submission_id, string $type ): void {
        $allowed = [ 'notification', 'confirmation', 'staff' ];
        if ( ! in_array( $type, $allowed, true ) ) {
            return;
        }
        wp_schedule_single_event( time(), self::CRON_ACTION, [ $form_id, $submission_id, $type ] );
        \FPForms\Core\Logger::debug( 'Email job enqueued', [
            'form_id'       => $form_id,
            'submission_id' => $submission_id,
            'type'          => $type,
        ] );
    }

    /**
     * Elabora un job della coda email (chiamato da cron).
     *
     * @param int    $form_id       ID form.
     * @param int    $submission_id ID submission.
     * @param string $type          'notification' | 'confirmation' | 'staff'.
     */
    public function process_queue_job( int $form_id, int $submission_id, string $type ): void {
        $max_per_hour = (int) apply_filters( 'fp_forms_email_rate_limit_max', get_option( 'fp_forms_email_rate_limit_max', 50 ) );
        $slot = date( 'Y-m-d-H', current_time( 'timestamp' ) );
        $key = 'fp_forms_email_rate_' . $slot;
        $count = (int) get_transient( $key );
        if ( $max_per_hour > 0 && $count >= $max_per_hour ) {
            \FPForms\Core\Logger::warning( 'Email rate limit reached, skipping send', [
                'form_id' => $form_id,
                'type'    => $type,
                'slot'    => $slot,
            ] );
            return;
        }

        $submission = \FPForms\Plugin::instance()->database->get_submission( $submission_id );
        if ( ! $submission || (int) $submission->form_id !== $form_id ) {
            \FPForms\Core\Logger::warning( 'Process queue job: submission not found or form mismatch', [
                'form_id'       => $form_id,
                'submission_id' => $submission_id,
            ] );
            return;
        }
        $data = isset( $submission->data ) ? json_decode( $submission->data, true ) : [];
        if ( ! is_array( $data ) ) {
            $data = [];
        }

        try {
            if ( $type === 'notification' ) {
                $this->send_notification( $form_id, $submission_id, $data );
            } elseif ( $type === 'confirmation' ) {
                $user_email = $this->get_user_email_from_data( $form_id, $data );
                if ( $user_email ) {
                    $this->send_confirmation( $form_id, $user_email, $data );
                }
            } elseif ( $type === 'staff' ) {
                $this->send_staff_notifications_bulk( $form_id, $submission_id, $data );
            }
        } catch ( \Throwable $e ) {
            \FPForms\Core\Logger::error( 'Email queue job failed', [
                'form_id'       => $form_id,
                'submission_id' => $submission_id,
                'type'          => $type,
                'error'         => $e->getMessage(),
            ] );
            wp_schedule_single_event( time() + 300, self::CRON_ACTION, [ $form_id, $submission_id, $type ] );
            return;
        }

        if ( $max_per_hour > 0 ) {
            set_transient( $key, $count + 1, HOUR_IN_SECONDS );
        }
    }

    /**
     * Restituisce l'email utente dai dati submission (per conferma)
     */
    private function get_user_email_from_data( int $form_id, array $data ): ?string {
        $fields = \FPForms\Plugin::instance()->forms->get_fields( $form_id );
        if ( is_array( $fields ) ) {
            foreach ( $fields as $field ) {
                if ( isset( $field['type'] ) && $field['type'] === 'email' && ! empty( $field['name'] ) ) {
                    $v = $data[ $field['name'] ] ?? '';
                    if ( is_email( $v ) ) {
                        return $v;
                    }
                }
            }
        }
        foreach ( $data as $key => $value ) {
            if ( is_string( $value ) && stripos( $key, 'email' ) !== false && is_email( $value ) ) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Invia notifiche a tutti gli indirizzi staff (usato dalla coda)
     */
    private function send_staff_notifications_bulk( int $form_id, int $submission_id, array $data ): void {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        if ( ! $form || empty( $form['settings']['staff_notifications_enabled'] ) ) {
            return;
        }
        $staff_emails = $form['settings']['staff_emails'] ?? '';
        if ( $staff_emails === '' ) {
            return;
        }
        $emails = array_filter( array_map( 'trim', preg_split( '/[,;\n\r]+/', $staff_emails ) ), 'is_email' );
        foreach ( $emails as $staff_email ) {
            $this->send_staff_notification( $form_id, $submission_id, $staff_email, $data );
        }
    }
    
    /**
     * Invia notifica per nuova submission
     */
    public function send_notification( $form_id, $submission_id, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            \FPForms\Core\Logger::error( 'Form not found for notification', [ 'form_id' => $form_id ] );
            return false;
        }
        
        // Email destinatario
        $to = isset( $form['settings']['notification_email'] ) && ! empty( $form['settings']['notification_email'] )
            ? $form['settings']['notification_email']
            : get_option( 'admin_email' );
        
        // Applica filtro per recipients personalizzati
        $to = \FPForms\Core\Hooks::filter_notification_recipients( $to, $form_id, $data );
        
        // Oggetto
        $subject = isset( $form['settings']['notification_subject'] ) && ! empty( $form['settings']['notification_subject'] )
            ? $form['settings']['notification_subject']
            : sprintf( __( 'Nuovo invio dal form: %s', 'fp-forms' ), $form['title'] );
        
        // Sostituisci tag dinamici, rimuovi newline (prevenzione header injection) e applica filtro
        $subject = $this->replace_tags( $subject, $data, $form );
        $subject = str_replace( [ "\r", "\n" ], '', $subject );
        $subject = \FPForms\Core\Hooks::filter_email_subject( $subject, $form_id, $data );
        
        // Messaggio (usa template custom se specificato, altrimenti auto-generato)
        $message = isset( $form['settings']['notification_message'] ) && ! empty( $form['settings']['notification_message'] )
            ? $form['settings']['notification_message']
            : $this->build_notification_message( $form, $data, $submission_id );
        
        $message = $this->replace_tags( $message, $data, $form );
        $message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
        
        // Headers (con Message-ID identificabile per notifica proprietario)
        $headers = $this->get_email_headers( $form, $data, [ 'submission_id' => $submission_id ] );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );

        // Hook before send
        do_action( 'fp_forms_before_send_notification', $form_id, $data, $to );

        $success = false;
        $this->apply_fp_forms_mail_from();
        try {
            $success = $this->send_via_wp_mail_with_fallback( $to, $subject, $message, $headers );
        } catch ( \Throwable $e ) {
            self::log_email_diagnostic( 'notification', $to, $subject, false, $e->getMessage() );
            throw $e;
        } finally {
            $this->remove_fp_forms_mail_from();
        }

        self::log_email_diagnostic( 'notification', $to, $subject, $success );
        \FPForms\Core\Logger::log_email( $to, $subject, $success );

        do_action( 'fp_forms_after_send_notification', $form_id, $data, $success );

        return $success;
    }
    
    /**
     * Costruisce il messaggio di notifica
     */
    private function build_notification_message( $form, $data, $submission_id ) {
        $site_name = get_bloginfo( 'name' );
        $lines = [];

        $lines[] = sprintf( __( 'Nuova richiesta ricevuta - %s', 'fp-forms' ), $form['title'] );
        $lines[] = str_repeat( '=', 50 );
        $lines[] = '';

        // Dati compilati (solo campi utili a chi legge l'email)
        $lines[] = __( 'RIEPILOGO DATI', 'fp-forms' );
        $lines[] = str_repeat( '-', 50 );

        foreach ( $form['fields'] as $field ) {
            $field_type = $field['type'] ?? '';

            if ( in_array( $field_type, [ 'hidden', 'honeypot', 'recaptcha', 'step_break', 'privacy-checkbox', 'marketing-checkbox' ], true ) ) {
                continue;
            }

            $value = $this->get_field_display_value( $field, $data );
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }

            if ( trim( (string) $value ) === '' ) {
                continue;
            }

            $label = isset( $field['label'] ) && trim( (string) $field['label'] ) !== ''
                ? (string) $field['label']
                : (string) ( $field['name'] ?? __( 'Campo', 'fp-forms' ) );

            $lines[] = $label . ': ' . (string) $value;
        }

        $lines[] = '';

        // Info tecniche
        $submission = \FPForms\Plugin::instance()->submissions->get_submission( $submission_id );
        if ( $submission ) {
            $lines[] = __( 'DETTAGLI INVIO', 'fp-forms' );
            $lines[] = str_repeat( '-', 50 );
            $lines[] = __( 'Data:', 'fp-forms' ) . ' ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $submission->created_at ) );
            $lines[] = __( 'IP:', 'fp-forms' ) . ' ' . $submission->user_ip;

            if ( $submission->user_id ) {
                $user = get_user_by( 'id', $submission->user_id );
                if ( $user ) {
                    $lines[] = __( 'Utente:', 'fp-forms' ) . ' ' . $user->display_name . ' (' . $user->user_email . ')';
                }
            }

            $lines[] = '';
        }

        // Link admin
        $admin_url = admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] );
        $lines[] = __( 'Apri tutte le richieste in admin:', 'fp-forms' );
        $lines[] = $admin_url;
        $lines[] = '';

        // Footer
        $lines[] = str_repeat( '-', 50 );
        $lines[] = sprintf( __( '%s - Notifica automatica via FP Forms', 'fp-forms' ), $site_name );

        return implode( "\n", $lines );
    }

    /**
     * Costruisce il messaggio di notifica per lo staff con focus operativo.
     */
    private function build_staff_notification_message( $form, $data, $submission_id ) {
        $site_name = get_bloginfo( 'name' );
        $lines = [];

        $lines[] = sprintf( __( '[STAFF] Nuova richiesta da gestire - %s', 'fp-forms' ), $form['title'] );
        $lines[] = str_repeat( '=', 50 );
        $lines[] = '';
        $lines[] = __( 'AZIONE RICHIESTA', 'fp-forms' );
        $lines[] = __( 'Rispondi a questa email per contattare il cliente il prima possibile.', 'fp-forms' );
        $lines[] = '';

        $lines[] = __( 'RIEPILOGO DATI', 'fp-forms' );
        $lines[] = str_repeat( '-', 50 );

        foreach ( $form['fields'] as $field ) {
            $field_type = $field['type'] ?? '';

            if ( in_array( $field_type, [ 'hidden', 'honeypot', 'recaptcha', 'step_break', 'privacy-checkbox', 'marketing-checkbox' ], true ) ) {
                continue;
            }

            $value = $this->get_field_display_value( $field, $data );
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }

            if ( trim( (string) $value ) === '' ) {
                continue;
            }

            $label = isset( $field['label'] ) && trim( (string) $field['label'] ) !== ''
                ? (string) $field['label']
                : (string) ( $field['name'] ?? __( 'Campo', 'fp-forms' ) );

            $lines[] = $label . ': ' . (string) $value;
        }

        $lines[] = '';

        $submission = \FPForms\Plugin::instance()->submissions->get_submission( $submission_id );
        if ( $submission ) {
            $lines[] = __( 'DETTAGLI INVIO', 'fp-forms' );
            $lines[] = str_repeat( '-', 50 );
            $lines[] = __( 'Data:', 'fp-forms' ) . ' ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $submission->created_at ) );
            $lines[] = __( 'IP:', 'fp-forms' ) . ' ' . $submission->user_ip;
            $lines[] = '';
        }

        $lines[] = __( 'Suggerimento:', 'fp-forms' );
        $lines[] = __( 'Usa il pulsante "Rispondi" del tuo client email per contattare direttamente il mittente.', 'fp-forms' );
        $lines[] = '';

        $lines[] = str_repeat( '-', 50 );
        $lines[] = sprintf( __( '%s - Notifica staff via FP Forms', 'fp-forms' ), $site_name );

        return implode( "\n", $lines );
    }

    /**
     * Costruisce il messaggio di conferma per l'utente
     */
    private function build_confirmation_message( $form, $data ) {
        $site_name = get_bloginfo( 'name' );
        $lines = [];

        // Saluto personalizzato (cerca nome tra i campi)
        $user_name = $this->extract_user_name( $form, $data );
        if ( $user_name ) {
            $lines[] = sprintf( __( 'Ciao %s,', 'fp-forms' ), $user_name );
        } else {
            $lines[] = __( 'Ciao,', 'fp-forms' );
        }
        $lines[] = '';

        $lines[] = sprintf(
            __( 'grazie per averci contattato tramite il form "%s" su %s.', 'fp-forms' ),
            $form['title'],
            $site_name
        );
        $lines[] = __( 'Abbiamo ricevuto correttamente il tuo messaggio.', 'fp-forms' );
        $lines[] = '';

        // Riepilogo dati inviati
        $lines[] = __( 'RIEPILOGO DATI INVIATI', 'fp-forms' );
        $lines[] = str_repeat( '-', 40 );

        foreach ( $form['fields'] as $field ) {
            if ( in_array( $field['type'] ?? '', [ 'hidden', 'honeypot', 'recaptcha', 'step_break', 'privacy-checkbox', 'marketing-checkbox' ], true ) ) {
                continue;
            }
            $value = $this->get_field_display_value( $field, $data );
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            if ( $value !== '' ) {
                $lines[] = $field['label'] . ': ' . $value;
            }
        }

        $lines[] = '';
        $lines[] = __( 'Ti risponderemo il prima possibile.', 'fp-forms' );
        $lines[] = '';

        // Firma
        $lines[] = str_repeat( '-', 40 );
        $lines[] = __( 'Cordiali saluti,', 'fp-forms' );
        $lines[] = sprintf( __( 'Il team di %s', 'fp-forms' ), $site_name );
        $lines[] = get_bloginfo( 'url' );

        return implode( "\n", $lines );
    }

    /**
     * Estrae il nome dell'utente dai dati della submission
     */
    private function extract_user_name( $form, $data ) {
        foreach ( $form['fields'] as $field ) {
            $type = $field['type'] ?? '';
            if ( $type === 'fullname' ) {
                $value = $this->get_field_display_value( $field, $data );
                if ( ! empty( trim( $value ) ) ) {
                    return trim( $value );
                }
            }
        }
        foreach ( $form['fields'] as $field ) {
            $name = strtolower( $field['name'] ?? '' );
            if ( in_array( $name, [ 'nome', 'name', 'first_name', 'nome_cognome' ], true ) ) {
                $value = isset( $data[ $field['name'] ] ) ? $data[ $field['name'] ] : '';
                if ( ! empty( trim( $value ) ) ) {
                    return trim( $value );
                }
            }
        }
        return '';
    }
    
    /**
     * Ottiene gli headers dell'email (From, Reply-To, Message-ID, X-Mailer, MIME).
     * Header ottimizzati per ridurre rischio spam e migliorare deliverability.
     *
     * @param array $form Form config.
     * @param array $data Dati submission.
     * @param array $options Opzionale: 'submission_id' per Message-ID più identificabile.
     * @return array Lista header.
     */
    private function get_email_headers( $form, $data, $options = [] ) {
        $headers = [];

        $skip_reply_to = ! empty( $options['skip_reply_to'] );

        if ( ! $skip_reply_to ) {
            $reply_to = '';
            foreach ( $form['fields'] as $field ) {
                if ( isset( $field['type'] ) && $field['type'] === 'email' ) {
                    $field_name = $field['name'];
                    if ( isset( $data[ $field_name ] ) ) {
                        $reply_to = sanitize_email( $data[ $field_name ] );
                        if ( is_email( $reply_to ) ) {
                            break;
                        }
                        $reply_to = '';
                    }
                }
            }
            if ( $reply_to ) {
                // Rimuove newline per prevenire header injection
                $reply_to = str_replace( [ "\r", "\n" ], '', $reply_to );
                $headers[] = 'Reply-To: ' . $reply_to;
            }
        }

        $content_type = ! empty( $options['html'] ) ? 'text/html' : 'text/plain';
        $headers[] = 'Content-Type: ' . $content_type . '; charset=UTF-8';

        return $headers;
    }

    /**
     * True se gli header includono Content-Type: text/html.
     *
     * @param array|string $headers
     */
    private function email_headers_indicate_html( $headers ): bool {
        $flat = is_array( $headers ) ? implode( "\n", $headers ) : (string) $headers;
        return (bool) preg_match( '/Content-Type:\s*text\/html/i', $flat );
    }
    
    /**
     * Restituisce il valore da mostrare per un campo (gestisce fullname)
     */
    private function get_field_display_value( $field, $data ) {
        $name = $field['name'];
        $type = isset( $field['type'] ) ? $field['type'] : 'text';
        if ( $type === 'fullname' ) {
            $n = isset( $data[ $name . '_nome' ] ) ? $data[ $name . '_nome' ] : '';
            $c = isset( $data[ $name . '_cognome' ] ) ? $data[ $name . '_cognome' ] : '';
            return trim( $n . ' ' . $c );
        }
        return isset( $data[ $name ] ) ? $data[ $name ] : '';
    }

    /**
     * Sostituisce i tag dinamici
     */
    /**
     * Sostituisce i tag {field_name} nel testo con i valori del form.
     *
     * @param string $text      Testo con tag da sostituire.
     * @param array  $data      Dati del form.
     * @param array  $form      Dati del form (con fields e settings).
     * @param bool   $html_mode Se true, i valori vengono escaped con esc_html() per uso in HTML.
     */
    private function replace_tags( $text, $data, $form, $html_mode = false ) {
        // Tag per i campi del form
        foreach ( $form['fields'] as $field ) {
            $field_name  = $field['name'];
            $field_value = $this->get_field_display_value( $field, $data );
            
            if ( is_array( $field_value ) ) {
                $field_value = implode( ', ', $field_value );
            }
            
            // In modalità HTML, escapa il valore per prevenire XSS
            $safe_value = $html_mode ? esc_html( (string) $field_value ) : (string) $field_value;
            $text = str_replace( '{' . $field_name . '}', $safe_value, $text );
        }
        
        // Tag generali
        $text = str_replace( '{form_title}', $html_mode ? esc_html( $form['title'] ) : $form['title'], $text );
        $text = str_replace( '{site_name}', $html_mode ? esc_html( get_bloginfo( 'name' ) ) : get_bloginfo( 'name' ), $text );
        $text = str_replace( '{site_url}', get_bloginfo( 'url' ), $text );
        $text = str_replace( '{date}', date_i18n( get_option( 'date_format' ) ), $text );
        $text = str_replace( '{time}', date_i18n( get_option( 'time_format' ) ), $text );
        
        return $text;
    }
    
    /**
     * Invia email di conferma all'utente
     */
    public function send_confirmation( $form_id, $user_email, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            \FPForms\Core\Logger::error( 'Form not found for confirmation', [ 'form_id' => $form_id ] );
            return false;
        }
        
        if ( ! isset( $form['settings']['confirmation_enabled'] ) || ! $form['settings']['confirmation_enabled'] ) {
            \FPForms\Core\Logger::debug( 'Confirmation email disabled for form', [
                'form_id' => $form_id,
                'confirmation_enabled' => $form['settings']['confirmation_enabled'] ?? 'NOT SET',
            ] );
            return false;
        }
        
        // Oggetto
        $subject = isset( $form['settings']['confirmation_subject'] ) && ! empty( $form['settings']['confirmation_subject'] )
            ? $form['settings']['confirmation_subject']
            : sprintf( __( 'Abbiamo ricevuto il tuo messaggio - %s', 'fp-forms' ), get_bloginfo( 'name' ) );
        
        $subject = $this->replace_tags( $subject, $data, $form );
        $subject = \FPForms\Core\Hooks::filter_email_subject( $subject, $form_id, $data );

        // Template HTML o plain text?
        $template_slug = $form['settings']['confirmation_template'] ?? '';
        $is_html = ! empty( $template_slug );

        if ( $is_html ) {
            // Messaggio custom sovrascrive il template? No: il template HTML genera tutto.
            // Se c'è un messaggio custom, usiamo plain text come fallback.
            $has_custom_message = isset( $form['settings']['confirmation_message'] ) && ! empty( $form['settings']['confirmation_message'] );
            if ( $has_custom_message ) {
                $is_html = false;
            }
        }

        if ( $is_html ) {
            $accent = $form['settings']['confirmation_accent_color'] ?? '';
            if ( ! $accent || ! preg_match( '/^#[0-9A-Fa-f]{6}$/', $accent ) ) {
                $accent = get_option( 'fp_forms_email_accent_color', '#3b82f6' );
            }

            $footer_extra = $form['settings']['confirmation_footer_info'] ?? '';
            $global_footer = get_option( 'fp_forms_email_footer_text', '' );
            $footer = $global_footer;
            if ( $footer_extra ) {
                $footer = $footer ? $footer . "\n" . $footer_extra : $footer_extra;
            }

            $fields_html = Templates::build_fields_table( $form, $data, $accent );

            $message = Templates::render( $template_slug, [
                'accent_color' => $accent,
                'footer_text'  => $footer,
                'user_name'    => $this->extract_user_name( $form, $data ),
                'form_title'   => $form['title'],
                'fields_html'  => $fields_html,
            ] );
        } else {
            $message = isset( $form['settings']['confirmation_message'] ) && ! empty( $form['settings']['confirmation_message'] )
                ? $form['settings']['confirmation_message']
                : $this->build_confirmation_message( $form, $data );
            
            $message = $this->replace_tags( $message, $data, $form );
        }

        $message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
        
        $headers = $this->get_email_headers( $form, $data, [ 'skip_reply_to' => true, 'html' => $is_html ] );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );

        do_action( 'fp_forms_before_send_confirmation', $form_id, $data, $user_email );

        $success = false;
        $this->apply_fp_forms_mail_from();
        try {
            $success = $this->send_via_wp_mail_with_fallback( $user_email, $subject, $message, $headers );
        } catch ( \Throwable $e ) {
            self::log_email_diagnostic( 'confirmation', $user_email, $subject, false, $e->getMessage() );
            throw $e;
        } finally {
            $this->remove_fp_forms_mail_from();
        }

        self::log_email_diagnostic( 'confirmation', $user_email, $subject, $success );
        \FPForms\Core\Logger::log_email( $user_email, $subject, $success );

        do_action( 'fp_forms_after_send_confirmation', $form_id, $data, $success );

        return $success;
    }
    
    /**
     * Invia notifica a membro dello staff
     */
    public function send_staff_notification( $form_id, $submission_id, $staff_email, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            \FPForms\Core\Logger::error( 'Form not found for staff notification', [ 'form_id' => $form_id ] );
            return false;
        }
        
        // Oggetto
        $subject = isset( $form['settings']['staff_notification_subject'] ) && ! empty( $form['settings']['staff_notification_subject'] )
            ? $form['settings']['staff_notification_subject']
            : sprintf( __( '[STAFF] Nuova richiesta dal form: %s', 'fp-forms' ), $form['title'] );
        
        $subject = $this->replace_tags( $subject, $data, $form );
        $subject = \FPForms\Core\Hooks::filter_email_subject( $subject, $form_id, $data );
        
        // Messaggio (usa template staff o fallback a notifica standard)
        $message = isset( $form['settings']['staff_notification_message'] ) && ! empty( $form['settings']['staff_notification_message'] )
            ? $form['settings']['staff_notification_message']
            : null;
        
        if ( $message ) {
            // Template personalizzato
            $message = $this->replace_tags( $message, $data, $form );
        } else {
            // Fallback: usa template staff, più orientato all'azione
            $message = $this->build_staff_notification_message( $form, $data, $submission_id );
        }
        
        $message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
        
        // Headers
        $headers = $this->get_email_headers( $form, $data, [ 'submission_id' => $submission_id ] );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );

        // Hook before send
        do_action( 'fp_forms_before_send_staff_notification', $form_id, $data, $staff_email );

        $success = false;
        $this->apply_fp_forms_mail_from();
        try {
            $success = $this->send_via_wp_mail_with_fallback( $staff_email, $subject, $message, $headers );
        } catch ( \Throwable $e ) {
            self::log_email_diagnostic( 'staff', $staff_email, $subject, false, $e->getMessage() );
            throw $e;
        } finally {
            $this->remove_fp_forms_mail_from();
        }

        self::log_email_diagnostic( 'staff', $staff_email, $subject, $success );
        \FPForms\Core\Logger::log_email( $staff_email, $subject, $success );

        do_action( 'fp_forms_after_send_staff_notification', $form_id, $data, $success );

        return $success;
    }

    /**
     * Invia email via wp_mail con fallback: se fallisce e SMTP FP è attivo, ritenta senza SMTP.
     *
     * @param string|string[] $to      Destinatario/i.
     * @param string          $subject Oggetto.
     * @param string          $message Corpo.
     * @param array|string    $headers Headers.
     * @return bool
     */
    private function send_via_wp_mail_with_fallback( $to, $subject, $message, $headers ) {
        if ( function_exists( 'fp_fpmail_brand_html' ) && $this->email_headers_indicate_html( $headers ) && is_string( $message ) && trim( $message ) !== '' ) {
            $message = fp_fpmail_brand_html( $message );
        }
        $ok = wp_mail( $to, $subject, $message, $headers );
        if ( $ok ) {
            return true;
        }
        $settings = Smtp::get_settings();
        if ( empty( $settings['enabled'] ) || empty( $settings['host'] ) ) {
            return false;
        }
        // Se cediamo a FP Mail SMTP (o altro), il nostro SMTP non è in uso: retry non aiuta, evita di inquinare phpmailer_init
        if ( Smtp::is_yielding_to_external_smtp() ) {
            return false;
        }
        remove_action( 'phpmailer_init', [ Smtp::class, 'configure_phpmailer' ] );
        $ok = wp_mail( $to, $subject, $message, $headers );
        add_action( 'phpmailer_init', [ Smtp::class, 'configure_phpmailer' ] );
        if ( $ok ) {
            \FPForms\Core\Logger::info( 'Email sent via fallback (default transport) after SMTP failure' );
        }
        return $ok;
    }

    /**
     * Applica i filtri WordPress wp_mail_from e wp_mail_from_name con le impostazioni FP Forms.
     * Garantisce From coerente (es. no-reply@stefanosansevero.it) per tutti i transport (mail/SMTP).
     */
    private function apply_fp_forms_mail_from() {
        $from_email = get_option( 'fp_forms_email_from_address', get_option( 'admin_email' ) );
        if ( $from_email && is_email( $from_email ) ) {
            add_filter( 'wp_mail_from', [ $this, 'filter_wp_mail_from' ], 20 );
            add_filter( 'wp_mail_from_name', [ $this, 'filter_wp_mail_from_name' ], 20 );
        }
    }

    /**
     * Rimuove i filtri From applicati da apply_fp_forms_mail_from.
     */
    private function remove_fp_forms_mail_from() {
        remove_filter( 'wp_mail_from', [ $this, 'filter_wp_mail_from' ], 20 );
        remove_filter( 'wp_mail_from_name', [ $this, 'filter_wp_mail_from_name' ], 20 );
    }

    /** Callable per wp_mail_from (usato da apply/remove_fp_forms_mail_from). */
    public function filter_wp_mail_from( $from ) {
        $fp_from = get_option( 'fp_forms_email_from_address', get_option( 'admin_email' ) );
        return ( $fp_from && is_email( $fp_from ) ) ? $fp_from : $from;
    }

    /** Callable per wp_mail_from_name (usato da apply/remove_fp_forms_mail_from). */
    public function filter_wp_mail_from_name( $name ) {
        $fp_name = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
        return ( $fp_name !== '' ) ? $fp_name : $name;
    }

    /**
     * Scrive diagnostica email in un log dedicato (sempre attivo, indipendente da WP_DEBUG).
     * Il file ruota automaticamente oltre 500 KB.
     */
    private static function log_email_diagnostic( string $type, string $to, string $subject, bool $success, string $error = '' ): void {
        $dir = WP_CONTENT_DIR . '/uploads/fp-forms-logs/';
        if ( ! is_dir( $dir ) ) {
            wp_mkdir_p( $dir );
        }

        // Proteggi la directory da accesso web diretto
        $htaccess = $dir . '.htaccess';
        if ( ! file_exists( $htaccess ) ) {
            @file_put_contents( $htaccess, "Deny from all\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n" );
        }
        $index = $dir . 'index.php';
        if ( ! file_exists( $index ) ) {
            @file_put_contents( $index, '<?php // Silence is golden.' );
        }

        $file = $dir . 'email-diagnostic.log';

        if ( file_exists( $file ) && filesize( $file ) > 512000 ) {
            $prev = $dir . 'email-diagnostic-prev.log';
            if ( ! rename( $file, $prev ) ) {
                \FPForms\Core\Logger::warning( 'Email diagnostic: impossibile ruotare il file di log', [
                    'file' => $file,
                ] );
            }
        }

        // Maschera l'email per conformità GDPR (es. j***@example.com)
        $masked_to = self::mask_email( $to );

        $line = sprintf(
            "[%s] %s | to=%s | subject=%s | result=%s%s\n",
            current_time( 'Y-m-d H:i:s' ),
            strtoupper( $type ),
            $masked_to,
            mb_substr( $subject, 0, 80 ),
            $success ? 'OK' : 'FAIL',
            $error ? ' | error=' . $error : ''
        );

        @file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
    }

    /**
     * Maschera un indirizzo email per i log (es. jo***@example.com).
     */
    private static function mask_email( string $email ): string {
        $parts = explode( '@', $email, 2 );
        if ( count( $parts ) !== 2 ) {
            return '***';
        }
        $local  = $parts[0];
        $domain = $parts[1];
        $visible = min( 2, strlen( $local ) );
        return substr( $local, 0, $visible ) . str_repeat( '*', max( 1, strlen( $local ) - $visible ) ) . '@' . $domain;
    }
}
