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
        
        // Headers
        $headers = $this->get_email_headers( $form, $data );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );
        
        // Hook before send
        do_action( 'fp_forms_before_send_notification', $form_id, $data, $to );
        
        // Invia email
        $success = wp_mail( $to, $subject, $message, $headers );
        
        // Log email
        \FPForms\Core\Logger::log_email( $to, $subject, $success );
        
        // Hook after send
        do_action( 'fp_forms_after_send_notification', $form_id, $data, $success );
        
        return $success;
    }
    
    /**
     * Costruisce il messaggio di notifica
     */
    private function build_notification_message( $form, $data, $submission_id ) {
        $message = '';
        
        // Intestazione
        $message .= sprintf( __( 'Hai ricevuto una nuova submission dal form "%s"', 'fp-forms' ), $form['title'] ) . "\n\n";
        $message .= str_repeat( '-', 50 ) . "\n\n";
        
        // Campi
        foreach ( $form['fields'] as $field ) {
            $field_name = $field['name'];
            $field_label = $field['label'];
            $field_value = $this->get_field_display_value( $field, $data );
            
            if ( is_array( $field_value ) ) {
                $field_value = implode( ', ', $field_value );
            }
            
            $message .= $field_label . ': ' . $field_value . "\n";
        }
        
        $message .= "\n" . str_repeat( '-', 50 ) . "\n\n";
        
        // Info aggiuntive
        $submission = \FPForms\Plugin::instance()->submissions->get_submission( $submission_id );
        if ( $submission ) {
            $message .= __( 'Informazioni aggiuntive:', 'fp-forms' ) . "\n";
            $message .= __( 'Data:', 'fp-forms' ) . ' ' . $submission->created_at . "\n";
            $message .= __( 'IP:', 'fp-forms' ) . ' ' . $submission->user_ip . "\n";
            
            if ( $submission->user_id ) {
                $user = get_user_by( 'id', $submission->user_id );
                if ( $user ) {
                    $message .= __( 'Utente:', 'fp-forms' ) . ' ' . $user->display_name . ' (' . $user->user_email . ')' . "\n";
                }
            }
        }
        
        return $message;
    }
    
    /**
     * Ottiene gli headers dell'email
     */
    private function get_email_headers( $form, $data ) {
        $headers = [];
        
        // From
        $from_name = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
        $from_email = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );
        
        $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
        
        // Reply-To (cerca campo email nel form)
        $reply_to = '';
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] === 'email' ) {
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
        
        // Content-Type
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        
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
        
        // Verifica se l'email di conferma è abilitata
        if ( ! isset( $form['settings']['confirmation_enabled'] ) || ! $form['settings']['confirmation_enabled'] ) {
            return false;
        }
        
        // Oggetto
        $subject = isset( $form['settings']['confirmation_subject'] ) && ! empty( $form['settings']['confirmation_subject'] )
            ? $form['settings']['confirmation_subject']
            : __( 'Conferma ricezione del tuo messaggio', 'fp-forms' );
        
        $subject = $this->replace_tags( $subject, $data, $form );
        $subject = \FPForms\Core\Hooks::filter_email_subject( $subject, $form_id, $data );
        
        // Messaggio
        $message = isset( $form['settings']['confirmation_message'] ) && ! empty( $form['settings']['confirmation_message'] )
            ? $form['settings']['confirmation_message']
            : __( 'Grazie per averci contattato. Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.', 'fp-forms' );
        
        $message = $this->replace_tags( $message, $data, $form );
        $message = \FPForms\Core\Hooks::filter_email_message( $message, $form_id, $data );
        
        // Headers
        $headers = [];
        $from_name = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
        $from_email = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );
        $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );
        
        // Hook before send
        do_action( 'fp_forms_before_send_confirmation', $form_id, $data, $user_email );
        
        $success = wp_mail( $user_email, $subject, $message, $headers );
        
        // Log email
        \FPForms\Core\Logger::log_email( $user_email, $subject, $success );
        
        // Hook after send
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
        $headers = $this->get_email_headers( $form, $data );
        $headers = \FPForms\Core\Hooks::filter_email_headers( $headers, $form_id, $data );
        
        // Hook before send
        do_action( 'fp_forms_before_send_staff_notification', $form_id, $data, $staff_email );
        
        // Invia email
        $success = wp_mail( $staff_email, $subject, $message, $headers );
        
        // Log email
        \FPForms\Core\Logger::log_email( $staff_email, $subject, $success );
        
        // Hook after send
        do_action( 'fp_forms_after_send_staff_notification', $form_id, $data, $success );
        
        return $success;
    }
}
