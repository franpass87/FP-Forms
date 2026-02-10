<?php
namespace FPForms\Email;

/**
 * Configura SMTP per l'invio email tramite PHPMailer.
 *
 * Quando abilitato, sovrascrive il transport di wp_mail() con SMTP
 * utilizzando l'hook phpmailer_init di WordPress.
 *
 * @since 1.4.0
 */
class Smtp {

    /**
     * Inizializza la configurazione SMTP.
     */
    public static function init() {
        $settings = self::get_settings();

        if ( empty( $settings['enabled'] ) ) {
            return;
        }

        add_action( 'phpmailer_init', [ __CLASS__, 'configure_phpmailer' ] );
    }

    /**
     * Restituisce le impostazioni SMTP correnti.
     *
     * @return array
     */
    public static function get_settings() {
        return get_option( 'fp_forms_smtp_settings', [
            'enabled'    => false,
            'host'       => '',
            'port'       => 587,
            'encryption' => 'tls',
            'auth'       => true,
            'username'   => '',
            'password'   => '',
        ] );
    }

    /**
     * Configura PHPMailer per usare SMTP.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer Istanza PHPMailer.
     */
    public static function configure_phpmailer( $phpmailer ) {
        $settings = self::get_settings();

        if ( empty( $settings['host'] ) ) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = $settings['host'];
        $phpmailer->Port       = intval( $settings['port'] );
        $phpmailer->SMTPAuth   = ! empty( $settings['auth'] );

        if ( $phpmailer->SMTPAuth ) {
            $phpmailer->Username = $settings['username'];
            $phpmailer->Password = $settings['password'];
        }

        // Encryption
        $encryption = $settings['encryption'] ?? 'tls';
        if ( 'tls' === $encryption ) {
            $phpmailer->SMTPSecure = 'tls';
        } elseif ( 'ssl' === $encryption ) {
            $phpmailer->SMTPSecure = 'ssl';
        } else {
            $phpmailer->SMTPSecure = '';
            $phpmailer->SMTPAutoTLS = false;
        }

        // From override (usa le impostazioni email del plugin)
        $from_email = get_option( 'fp_forms_email_from_address' );
        $from_name  = get_option( 'fp_forms_email_from_name' );

        if ( $from_email ) {
            $phpmailer->From     = $from_email;
            $phpmailer->FromName = $from_name ?: get_bloginfo( 'name' );
        }
    }

    /**
     * Invia un'email di test per verificare la configurazione SMTP.
     *
     * @param string $to Indirizzo email destinatario.
     * @return array [ 'success' => bool, 'message' => string ]
     */
    public static function send_test( $to ) {
        $settings = self::get_settings();

        if ( empty( $settings['host'] ) ) {
            return [
                'success' => false,
                'message' => __( 'Host SMTP non configurato.', 'fp-forms' ),
            ];
        }

        // Assicura che l'hook phpmailer_init sia attivo per il test,
        // anche se SMTP non è ancora "abilitato" nelle impostazioni.
        // Senza questo, wp_mail() usa PHP mail() nativo che spesso fallisce.
        $hook_registered = has_action( 'phpmailer_init', [ __CLASS__, 'configure_phpmailer' ] );
        if ( ! $hook_registered ) {
            add_action( 'phpmailer_init', [ __CLASS__, 'configure_phpmailer' ] );
        }

        $subject = sprintf(
            /* translators: %s: site name */
            __( '[%s] Email di test SMTP - FP Forms', 'fp-forms' ),
            get_bloginfo( 'name' )
        );

        $message  = __( 'Questa è un\'email di test inviata da FP Forms.', 'fp-forms' ) . "\n\n";
        $message .= __( 'Se ricevi questa email, la configurazione SMTP funziona correttamente!', 'fp-forms' ) . "\n\n";
        $message .= '---' . "\n";
        $message .= 'Host: ' . $settings['host'] . "\n";
        $message .= 'Port: ' . $settings['port'] . "\n";
        $message .= 'Encryption: ' . ( $settings['encryption'] ?: 'none' ) . "\n";
        $message .= 'Auth: ' . ( $settings['auth'] ? 'yes' : 'no' ) . "\n";
        $message .= 'Date: ' . date_i18n( 'Y-m-d H:i:s' ) . "\n";

        // Abilita debug PHPMailer per catturare errori dettagliati
        add_action( 'phpmailer_init', function( $phpmailer ) {
            $phpmailer->SMTPDebug = 0; // No output, ma errori catturati
            $phpmailer->Debugoutput = function() {}; // Silenzia output
        }, 999 );

        // Cattura eventuali errori da PHPMailer
        $mail_error = null;
        add_action( 'wp_mail_failed', function( $wp_error ) use ( &$mail_error ) {
            $mail_error = $wp_error;
        } );

        $result = wp_mail( $to, $subject, $message );

        // Rimuovi hook temporaneo se non era registrato prima
        if ( ! $hook_registered ) {
            remove_action( 'phpmailer_init', [ __CLASS__, 'configure_phpmailer' ] );
        }

        if ( $result ) {
            return [
                'success' => true,
                'message' => sprintf(
                    __( 'Email di test inviata con successo a %s', 'fp-forms' ),
                    $to
                ),
            ];
        }

        $error_msg = __( 'Invio email fallito.', 'fp-forms' );
        if ( $mail_error && is_wp_error( $mail_error ) ) {
            $error_msg .= ' ' . $mail_error->get_error_message();
        }

        return [
            'success' => false,
            'message' => $error_msg,
        ];
    }
}
