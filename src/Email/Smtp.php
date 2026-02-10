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

        // Usa PHPMailer direttamente per avere il pieno controllo e debug.
        // wp_mail() maschera gli errori e può restituire true anche se l'email non parte.
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

        $phpmailer = new \PHPMailer\PHPMailer\PHPMailer( true ); // true = abilita eccezioni

        try {
            // Configura SMTP
            $phpmailer->isSMTP();
            $phpmailer->Host       = $settings['host'];
            $phpmailer->Port       = intval( $settings['port'] );
            $phpmailer->SMTPAuth   = ! empty( $settings['auth'] );

            if ( $phpmailer->SMTPAuth ) {
                $phpmailer->Username = $settings['username'];
                $phpmailer->Password = $settings['password'];
            }

            $encryption = $settings['encryption'] ?? 'tls';
            if ( 'tls' === $encryption ) {
                $phpmailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ( 'ssl' === $encryption ) {
                $phpmailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $phpmailer->SMTPSecure = '';
                $phpmailer->SMTPAutoTLS = false;
            }

            // Timeout ragionevole
            $phpmailer->Timeout = 15;

            // From
            $from_email = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );
            $from_name  = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
            $phpmailer->setFrom( $from_email, $from_name );

            // Destinatario
            $phpmailer->addAddress( $to );

            // Contenuto
            $phpmailer->CharSet = 'UTF-8';
            $phpmailer->isHTML( false );
            $phpmailer->Subject = sprintf( '[%s] Email di test SMTP - FP Forms', get_bloginfo( 'name' ) );

            $body  = "Questa email di test è stata inviata da FP Forms.\n\n";
            $body .= "Se la ricevi, la configurazione SMTP funziona correttamente!\n\n";
            $body .= "---\n";
            $body .= 'Host: ' . $settings['host'] . "\n";
            $body .= 'Port: ' . $settings['port'] . "\n";
            $body .= 'Encryption: ' . ( $encryption ?: 'none' ) . "\n";
            $body .= 'Auth: ' . ( $settings['auth'] ? 'yes' : 'no' ) . "\n";
            $body .= 'From: ' . $from_email . "\n";
            $body .= 'Date: ' . date_i18n( 'Y-m-d H:i:s' ) . "\n";
            $phpmailer->Body = $body;

            // Invia
            $phpmailer->send();

            return [
                'success' => true,
                'message' => sprintf(
                    __( 'Email di test inviata con successo a %s', 'fp-forms' ),
                    $to
                ),
            ];

        } catch ( \PHPMailer\PHPMailer\Exception $e ) {
            return [
                'success' => false,
                'message' => __( 'Errore SMTP:', 'fp-forms' ) . ' ' . $phpmailer->ErrorInfo,
            ];
        } catch ( \Exception $e ) {
            return [
                'success' => false,
                'message' => __( 'Errore:', 'fp-forms' ) . ' ' . $e->getMessage(),
            ];
        }
    }
}
