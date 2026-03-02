<?php
namespace FPForms\Email;

/**
 * Gestisce l'invio delle email
 */
class Manager {
    
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
            : sprintf( __( 'Nuova submission dal form: %s', 'fp-forms' ), $form['title'] );
        
        // Sostituisci tag dinamici e applica filtro
        $subject = $this->replace_tags( $subject, $data, $form );
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

        $this->apply_fp_forms_mail_from();
        try {
            $success = wp_mail( $to, $subject, $message, $headers );
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

        $lines[] = sprintf( __( 'Nuova submission - %s', 'fp-forms' ), $form['title'] );
        $lines[] = str_repeat( '=', 50 );
        $lines[] = '';

        // Dati compilati
        $lines[] = __( 'DATI RICEVUTI', 'fp-forms' );
        $lines[] = str_repeat( '-', 50 );

        foreach ( $form['fields'] as $field ) {
            $value = $this->get_field_display_value( $field, $data );
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            $lines[] = $field['label'] . ': ' . ( $value !== '' ? $value : '—' );
        }

        $lines[] = '';

        // Info tecniche
        $submission = \FPForms\Plugin::instance()->submissions->get_submission( $submission_id );
        if ( $submission ) {
            $lines[] = __( 'DETTAGLI TECNICI', 'fp-forms' );
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
        $lines[] = __( 'Vedi tutte le submissions:', 'fp-forms' );
        $lines[] = $admin_url;
        $lines[] = '';

        // Footer
        $lines[] = str_repeat( '-', 50 );
        $lines[] = sprintf( __( '%s - Notifica automatica via FP Forms', 'fp-forms' ), $site_name );

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
                    if ( isset( $data[ $field_name ] ) && is_email( $data[ $field_name ] ) ) {
                        $reply_to = $data[ $field_name ];
                        break;
                    }
                }
            }
            if ( $reply_to ) {
                $headers[] = 'Reply-To: ' . $reply_to;
            }
        }

        $content_type = ! empty( $options['html'] ) ? 'text/html' : 'text/plain';
        $headers[] = 'Content-Type: ' . $content_type . '; charset=UTF-8';

        return $headers;
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
    private function replace_tags( $text, $data, $form ) {
        // Tag per i campi del form
        foreach ( $form['fields'] as $field ) {
            $field_name = $field['name'];
            $field_value = $this->get_field_display_value( $field, $data );
            
            if ( is_array( $field_value ) ) {
                $field_value = implode( ', ', $field_value );
            }
            
            $text = str_replace( '{' . $field_name . '}', $field_value, $text );
        }
        
        // Tag generali
        $text = str_replace( '{form_title}', $form['title'], $text );
        $text = str_replace( '{site_name}', get_bloginfo( 'name' ), $text );
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

        $this->apply_fp_forms_mail_from();
        try {
            $success = wp_mail( $user_email, $subject, $message, $headers );
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
            : sprintf( __( '[STAFF] Nuova submission: %s', 'fp-forms' ), $form['title'] );
        
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
            // Fallback: usa template standard
            $message = $this->build_notification_message( $form, $data, $submission_id );
        }
        
        $message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
        
        // Headers
        $headers = $this->get_email_headers( $form, $data, [ 'submission_id' => $submission_id ] );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );

        // Hook before send
        do_action( 'fp_forms_before_send_staff_notification', $form_id, $data, $staff_email );

        $this->apply_fp_forms_mail_from();
        try {
            $success = wp_mail( $staff_email, $subject, $message, $headers );
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
     * Applica i filtri WordPress wp_mail_from e wp_mail_from_name con le impostazioni FP Forms.
     * Garantisce From coerente (es. no-reply@stefanosansevero.it) per tutti i transport (mail/SMTP).
     */
    private function apply_fp_forms_mail_from() {
        $from_email = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );
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
        $fp_from = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );
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

        $file = $dir . 'email-diagnostic.log';

        if ( file_exists( $file ) && filesize( $file ) > 512000 ) {
            @rename( $file, $dir . 'email-diagnostic-prev.log' );
        }

        $line = sprintf(
            "[%s] %s | to=%s | subject=%s | result=%s%s\n",
            current_time( 'Y-m-d H:i:s' ),
            strtoupper( $type ),
            $to,
            mb_substr( $subject, 0, 80 ),
            $success ? 'OK' : 'FAIL',
            $error ? ' | error=' . $error : ''
        );

        @file_put_contents( $file, $line, FILE_APPEND | LOCK_EX );
    }
}
