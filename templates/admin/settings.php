<?php
declare(strict_types=1);

/**
 * Template: Impostazioni plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$email_from_name = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
$email_from_address = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );

// Email template settings
$email_logo_url = get_option( 'fp_forms_email_logo_url', '' );
$email_accent_color = get_option( 'fp_forms_email_accent_color', '#3b82f6' );
$email_footer_text = get_option( 'fp_forms_email_footer_text', '' );
$email_response_time = get_option( 'fp_forms_email_response_time', '' );

$email_queue_enabled = (bool) get_option( 'fp_forms_email_queue_enabled', true );
$email_rate_limit_max = (int) get_option( 'fp_forms_email_rate_limit_max', 50 );
$spam_score_threshold = (int) get_option( 'fp_forms_spam_score_threshold', 80 );
$spam_score_threshold = max( 1, min( 100, $spam_score_threshold ) );

$stripe_settings = get_option( 'fp_forms_stripe_settings', [
    'secret_key'       => '',
    'publishable_key'  => '',
    'webhook_secret'   => '',
] );
$stripe_secret_key      = $stripe_settings['secret_key'] ?? '';
$stripe_publishable_key = $stripe_settings['publishable_key'] ?? '';
$stripe_webhook_secret  = $stripe_settings['webhook_secret'] ?? '';

// SMTP settings
$smtp_settings = get_option( 'fp_forms_smtp_settings', [
    'enabled'    => false,
    'host'       => '',
    'port'       => 587,
    'encryption' => 'tls',
    'auth'       => true,
    'username'   => '',
    'password'   => '',
] );
$smtp_enabled    = $smtp_settings['enabled'] ?? false;
$smtp_host       = $smtp_settings['host'] ?? '';
$smtp_port       = $smtp_settings['port'] ?? 587;
$smtp_encryption = $smtp_settings['encryption'] ?? 'tls';
$smtp_auth       = $smtp_settings['auth'] ?? true;
$smtp_username   = $smtp_settings['username'] ?? '';
$smtp_password   = $smtp_settings['password'] ?? '';

// reCAPTCHA settings
$recaptcha_settings = get_option( 'fp_forms_recaptcha_settings', [
    'version' => 'v2',
    'site_key' => '',
    'secret_key' => '',
    'min_score' => 0.5,
] );
$recaptcha_version = $recaptcha_settings['version'] ?? 'v2';
$recaptcha_site_key = $recaptcha_settings['site_key'] ?? '';
$recaptcha_secret_key = $recaptcha_settings['secret_key'] ?? '';
$recaptcha_min_score = $recaptcha_settings['min_score'] ?? 0.5;

// Brevo: API key e liste sono in FP-Tracking. Qui solo opzioni Forms-specifiche.
$brevo_settings = get_option( 'fp_forms_brevo_settings', [
    'double_optin' => false,
    'track_events' => true,
] );
$brevo_central = function_exists( 'fp_tracking_get_brevo_settings' ) ? fp_tracking_get_brevo_settings() : null;
$brevo_has_central = $brevo_central && ! empty( $brevo_central['enabled'] );
$brevo_double_optin = $brevo_settings['double_optin'] ?? false;
$brevo_track_events = $brevo_settings['track_events'] ?? true;

$simulation_forms = \FPForms\Plugin::instance()->forms->get_forms();
?>

<div class="wrap fp-forms-admin">
    <h1 class="screen-reader-text"><?php esc_html_e( 'Impostazioni FP Forms', 'fp-forms' ); ?></h1>
    <div class="fp-forms-admin__header">
        <div class="fpforms-page-header-content">
            <h2 class="fp-forms-page-header-title" aria-hidden="true"><?php esc_html_e( 'Impostazioni FP Forms', 'fp-forms' ); ?></h2>
            <p class="fpforms-page-header-desc"><?php esc_html_e( 'Configura email, reCAPTCHA, pagamenti e integrazioni.', 'fp-forms' ); ?></p>
        </div>
        <span class="fpforms-page-header-badge">v<?php echo esc_html( defined( 'FP_FORMS_VERSION' ) ? FP_FORMS_VERSION : '0' ); ?></span>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'fp_forms_settings' ); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th colspan="2">
                        <h2><?php _e( 'Impostazioni Email', 'fp-forms' ); ?></h2>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_from_name"><?php _e( 'Nome Mittente', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="email_from_name" 
                               name="email_from_name" 
                               value="<?php echo esc_attr( $email_from_name ); ?>" 
                               class="regular-text">
                        <p class="description">
                            <?php _e( 'Il nome che apparira come mittente nelle email inviate dal plugin.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_from_address"><?php _e( 'Email Mittente', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="email" 
                               id="email_from_address" 
                               name="email_from_address" 
                               value="<?php echo esc_attr( $email_from_address ); ?>" 
                               class="regular-text">
                        <p class="description">
                            <?php _e( 'L\'indirizzo email che apparira come mittente.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="description" style="margin-top:0; padding:10px; background:#f0f6fc; border-left:4px solid #2271b1; border-radius:2px;">
                            <strong><?php _e( 'Per ridurre lo spam (es. email al proprietario):', 'fp-forms' ); ?></strong>
                            <?php _e( 'Usa un indirizzo sul dominio del sito (es. no-reply@tuodominio.it) e configura SPF e DKIM nel DNS. Chiedi al hosting i record da aggiungere.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th colspan="2">
                        <h2 id="email-template"><?php _e( 'Template Email HTML', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Configura l\'aspetto delle email di conferma inviate ai clienti. Il template si seleziona nelle impostazioni di ogni singolo form.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_logo_url"><?php _e( 'Logo Aziendale', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <input type="text"
                                   id="email_logo_url"
                                   name="email_logo_url"
                                   value="<?php echo esc_url( $email_logo_url ); ?>"
                                   class="regular-text"
                                   placeholder="https://example.com/logo.png">
                            <button type="button" id="fp-upload-email-logo" class="button">
                                <span class="dashicons dashicons-upload"></span>
                                <?php _e( 'Carica', 'fp-forms' ); ?>
                            </button>
                        </div>
                        <?php if ( $email_logo_url ) : ?>
                        <div style="margin-top: 10px;">
                            <img src="<?php echo esc_url( $email_logo_url ); ?>" alt="Logo" style="max-height: 60px; border: 1px solid #ddd; border-radius: 4px; padding: 4px;">
                        </div>
                        <?php endif; ?>
                        <p class="description">
                            <?php _e( 'Mostrato nell\'header delle email HTML. Dimensione consigliata: 200x60px.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_accent_color"><?php _e( 'Colore Accent', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="color"
                                   id="email_accent_color"
                                   name="email_accent_color"
                                   value="<?php echo esc_attr( $email_accent_color ); ?>"
                                   style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <input type="text" value="<?php echo esc_attr( $email_accent_color ); ?>" style="width: 100px;" readonly id="email_accent_color_text">
                        </div>
                        <p class="description">
                            <?php _e( 'Colore principale delle email (header, pulsanti, accenti). Sovrascrivibile per-form.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_response_time"><?php _e( 'Tempi di Risposta', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text"
                               id="email_response_time"
                               name="email_response_time"
                               value="<?php echo esc_attr( $email_response_time ); ?>"
                               class="regular-text"
                               placeholder="<?php esc_attr_e( 'entro 24 ore', 'fp-forms' ); ?>">
                        <p class="description">
                            <?php _e( 'Mostrato nell\'email di conferma (es. "entro 24 ore", "entro 48 ore lavorative").', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_footer_text"><?php _e( 'Footer Email', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <textarea id="email_footer_text"
                                  name="email_footer_text"
                                  rows="4"
                                  class="large-text"
                                  placeholder="<?php esc_attr_e( "Via Roma 1 - 00100 Roma\nTel: +39 06 1234567\nP.IVA 12345678901", 'fp-forms' ); ?>"><?php echo esc_textarea( $email_footer_text ); ?></textarea>
                        <p class="description">
                            <?php _e( 'Indirizzo, contatti, P.IVA e altre informazioni mostrate nel footer delle email.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_queue_enabled"><?php esc_html_e( 'Coda email', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="email_queue_enabled" name="email_queue_enabled" value="1" <?php checked( $email_queue_enabled ); ?>>
                            <?php esc_html_e( 'Abilita coda email (invio in background)', 'fp-forms' ); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e( 'Quando attiva, le email vengono accodate e inviate via cron invece che nella richiesta di submit.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_rate_limit_max"><?php esc_html_e( 'Rate limit email (max/ora)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" id="email_rate_limit_max" name="email_rate_limit_max" value="<?php echo esc_attr( $email_rate_limit_max ); ?>" min="0" max="1000" class="small-text">
                        <p class="description">
                            <?php esc_html_e( 'Numero massimo di email inviate per ora (0 = illimitato).', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>

                <script>
                (function($) {
                    // Logo upload via WP Media Library
                    $('#fp-upload-email-logo').on('click', function(e) {
                        e.preventDefault();
                        var frame = wp.media({ title: '<?php echo esc_js( __( 'Seleziona Logo', 'fp-forms' ) ); ?>', multiple: false, library: { type: 'image' } });
                        frame.on('select', function() {
                            var url = frame.state().get('selection').first().toJSON().url;
                            $('#email_logo_url').val(url);
                        });
                        frame.open();
                    });
                    // Sync color picker
                    $('#email_accent_color').on('input change', function() {
                        $('#email_accent_color_text').val(this.value);
                    });
                })(jQuery);
                </script>

                <tr>
                    <th colspan="2">
                        <h2 id="smtp"><?php _e( 'Configurazione SMTP', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Configura un server SMTP per inviare email in modo affidabile. Senza SMTP, WordPress usa la funzione PHP mail() che spesso finisce in spam.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Abilita SMTP', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="smtp_enabled" id="smtp_enabled" value="1" <?php checked( $smtp_enabled, true ); ?>>
                            <strong><?php _e( 'Usa server SMTP per l\'invio email', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'Quando abilitato, tutte le email inviate da FP Forms passeranno attraverso il server SMTP configurato.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field">
                    <th scope="row">
                        <label for="smtp_host"><?php _e( 'Host SMTP', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="smtp_host" 
                               name="smtp_host" 
                               value="<?php echo esc_attr( $smtp_host ); ?>" 
                               class="regular-text"
                               placeholder="smtp.gmail.com">
                        <p class="description">
                            <?php _e( 'Indirizzo del server SMTP (es. smtp.gmail.com, smtp.office365.com, in-v3.mailjet.com)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field">
                    <th scope="row">
                        <label for="smtp_port"><?php _e( 'Porta SMTP', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="smtp_port" 
                               name="smtp_port" 
                               value="<?php echo esc_attr( $smtp_port ); ?>" 
                               class="small-text"
                               min="1" 
                               max="65535">
                        <span class="description">
                            <?php _e( 'Porte comuni: 587 (TLS), 465 (SSL), 25 (no encryption)', 'fp-forms' ); ?>
                        </span>
                    </td>
                </tr>
                <tr class="fp-smtp-field">
                    <th scope="row">
                        <label for="smtp_encryption"><?php _e( 'Crittografia', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <select id="smtp_encryption" name="smtp_encryption">
                            <option value="tls" <?php selected( $smtp_encryption, 'tls' ); ?>>TLS (<?php _e( 'Raccomandato', 'fp-forms' ); ?>)</option>
                            <option value="ssl" <?php selected( $smtp_encryption, 'ssl' ); ?>>SSL</option>
                            <option value="none" <?php selected( $smtp_encryption, 'none' ); ?>><?php _e( 'Nessuna', 'fp-forms' ); ?></option>
                        </select>
                        <p class="description">
                            <?php _e( 'TLS sulla porta 587 è la configurazione più comune e sicura.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field">
                    <th scope="row">
                        <?php _e( 'Autenticazione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="smtp_auth" id="smtp_auth" value="1" <?php checked( $smtp_auth, true ); ?>>
                            <strong><?php _e( 'Richiedi autenticazione SMTP', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'La maggior parte dei server SMTP richiede autenticazione con username e password.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field fp-smtp-auth-field">
                    <th scope="row">
                        <label for="smtp_username"><?php _e( 'Username SMTP', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="smtp_username" 
                               name="smtp_username" 
                               value="<?php echo esc_attr( $smtp_username ); ?>" 
                               class="regular-text"
                               autocomplete="off"
                               placeholder="user@example.com">
                        <p class="description">
                            <?php _e( 'Solitamente è il tuo indirizzo email completo.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field fp-smtp-auth-field">
                    <th scope="row">
                        <label for="smtp_password"><?php _e( 'Password SMTP', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <div style="position: relative; display: inline-block; width: 100%; max-width: 400px;">
                            <input type="password" 
                                   id="smtp_password" 
                                   name="smtp_password" 
                                   value="<?php echo esc_attr( $smtp_password ); ?>" 
                                   class="regular-text"
                                   autocomplete="new-password"
                                   style="width: 100%; padding-right: 40px;">
                            <button type="button" id="fp-toggle-smtp-password" 
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px;"
                                    title="<?php esc_attr_e( 'Mostra/Nascondi password', 'fp-forms' ); ?>">
                                <span class="dashicons dashicons-visibility"></span>
                            </button>
                        </div>
                        <p class="description">
                            <?php _e( 'Per Gmail usa una "App Password" (non la password dell\'account). Per altri provider controlla la documentazione.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="fp-smtp-field">
                    <th scope="row">
                        <?php _e( 'Test Email SMTP', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <input type="email" 
                                   id="smtp_test_email" 
                                   value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" 
                                   class="regular-text"
                                   placeholder="test@example.com">
                            <button type="button" id="fp-test-smtp" class="button">
                                <span class="dashicons dashicons-email-alt"></span>
                                <?php _e( 'Invia Email di Test', 'fp-forms' ); ?>
                            </button>
                        </div>
                        <div id="fp-smtp-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Salva prima le impostazioni, poi invia un\'email di test per verificare la configurazione.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>

                <script>
                (function($) {
                    // Toggle SMTP fields visibility
                    function toggleSmtpFields() {
                        var enabled = $('#smtp_enabled').is(':checked');
                        $('.fp-smtp-field').toggle(enabled);
                        toggleSmtpAuthFields();
                    }
                    function toggleSmtpAuthFields() {
                        var auth = $('#smtp_auth').is(':checked') && $('#smtp_enabled').is(':checked');
                        $('.fp-smtp-auth-field').toggle(auth);
                    }
                    $('#smtp_enabled').on('change', toggleSmtpFields);
                    $('#smtp_auth').on('change', toggleSmtpAuthFields);
                    toggleSmtpFields();

                    // Toggle password visibility
                    $('#fp-toggle-smtp-password').on('click', function() {
                        var $input = $('#smtp_password');
                        var $icon = $(this).find('.dashicons');
                        if ($input.attr('type') === 'password') {
                            $input.attr('type', 'text');
                            $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                        } else {
                            $input.attr('type', 'password');
                            $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                        }
                    });

                    // Test SMTP
                    $('#fp-test-smtp').on('click', function() {
                        var $btn = $(this);
                        var $result = $('#fp-smtp-test-result');
                        var email = $('#smtp_test_email').val();

                        if (!email) {
                            $result.html('<div class="notice notice-error inline"><p><?php echo esc_js( __( 'Inserisci un indirizzo email.', 'fp-forms' ) ); ?></p></div>');
                            return;
                        }

                        $btn.prop('disabled', true).text('<?php echo esc_js( __( 'Invio in corso...', 'fp-forms' ) ); ?>');
                        $result.html('<p><span class="spinner is-active" style="float:none;"></span> <?php echo esc_js( __( 'Invio email di test...', 'fp-forms' ) ); ?></p>');

                        $.post(ajaxurl, {
                            action: 'fp_forms_test_smtp',
                            nonce: '<?php echo wp_create_nonce( 'fp_forms_admin' ); ?>',
                            email: email
                        }, function(response) {
                            var msg = (response && response.data && response.data.message) ? String(response.data.message) : '';
                            var $p = $('<p>');
                            if (response.success) {
                                var $div = $('<div>').addClass('notice notice-success inline');
                                $p.append(document.createTextNode('✅ ' + msg));
                                $result.empty().append($div.append($p));
                            } else {
                                var $div = $('<div>').addClass('notice notice-error inline');
                                $p.append(document.createTextNode('❌ ' + msg));
                                $result.empty().append($div.append($p));
                            }
                        }).fail(function() {
                            $result.html('<div class="notice notice-error inline"><p>❌ <?php echo esc_js( __( 'Errore di connessione.', 'fp-forms' ) ); ?></p></div>');
                        }).always(function() {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-email-alt"></span> <?php echo esc_js( __( 'Invia Email di Test', 'fp-forms' ) ); ?>');
                        });
                    });
                })(jQuery);
                </script>

                <tr>
                    <th colspan="2">
                        <h2 id="recaptcha"><?php _e( 'Google reCAPTCHA 2025', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf( 
                                __( 'Proteggi i tuoi form da spam e bot. Ottieni le chiavi da %s', 'fp-forms' ),
                                '<a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="noopener"><strong>Google reCAPTCHA Admin Console</strong></a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="recaptcha_version"><?php _e( 'Versione reCAPTCHA', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <select id="recaptcha_version" name="recaptcha_version" class="regular-text">
                            <option value="v2" <?php selected( $recaptcha_version, 'v2' ); ?>>
                                <?php _e( 'v2 (Checkbox "Non sono un robot")', 'fp-forms' ); ?>
                            </option>
                            <option value="v3" <?php selected( $recaptcha_version, 'v3' ); ?>>
                                <?php _e( 'v3 (Invisibile - Score Based)', 'fp-forms' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <strong>v2:</strong> <?php _e( 'Mostra il checkbox "Non sono un robot" (piu affidabile)', 'fp-forms' ); ?><br>
                            <strong>v3:</strong> <?php _e( 'Completamente invisibile, analizza il comportamento utente (piu fluido)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="recaptcha_site_key"><?php _e( 'Site Key (Chiave Sito)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="recaptcha_site_key" 
                               name="recaptcha_site_key" 
                               value="<?php echo esc_attr( $recaptcha_site_key ); ?>" 
                               class="regular-text"
                               placeholder="6Lc...">
                        <p class="description">
                            <?php _e( 'La chiave pubblica usata nei form (visibile nel codice HTML)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="recaptcha_secret_key"><?php _e( 'Secret Key (Chiave Segreta)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="recaptcha_secret_key" 
                               name="recaptcha_secret_key" 
                               value="<?php echo esc_attr( $recaptcha_secret_key ); ?>" 
                               class="regular-text"
                               placeholder="6Lc...">
                        <p class="description">
                            <?php _e( 'La chiave privata per validazione server-side (non condividere mai!)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr id="recaptcha_score_row" style="<?php echo $recaptcha_version === 'v2' ? 'display:none;' : ''; ?>">
                    <th scope="row">
                        <label for="recaptcha_min_score"><?php _e( 'Score Minimo (solo v3)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="recaptcha_min_score" 
                               name="recaptcha_min_score" 
                               value="<?php echo esc_attr( $recaptcha_min_score ); ?>" 
                               min="0.0" 
                               max="1.0" 
                               step="0.1"
                               style="width: 100px;">
                        <span class="description">
                            (0.0 - 1.0) &nbsp;
                            <strong>Consigliato: 0.5</strong>
                        </span>
                        <p class="description">
                            <?php _e( 'Score piu alto = piu restrittivo. 0.0 = bot sicuro, 1.0 = umano sicuro. Valori tipici:', 'fp-forms' ); ?><br>
                            &#x2022; <code>0.9</code>: <?php _e( 'Molto restrittivo (pochi falsi positivi)', 'fp-forms' ); ?><br>
                            &#x2022; <code>0.7</code>: <?php _e( 'Restrittivo', 'fp-forms' ); ?><br>
                            &#x2022; <code>0.5</code>: <?php _e( 'Bilanciato (raccomandato)', 'fp-forms' ); ?><br>
                            &#x2022; <code>0.3</code>: <?php _e( 'Permissivo', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( ! empty( $recaptcha_site_key ) && ! empty( $recaptcha_secret_key ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-recaptcha" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione reCAPTCHA', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-recaptcha-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che le chiavi siano corrette e che Google risponda', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row">
                        <label for="spam_score_threshold"><?php esc_html_e( 'Soglia spam score', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" id="spam_score_threshold" name="spam_score_threshold" value="<?php echo esc_attr( $spam_score_threshold ); ?>" min="1" max="100" class="small-text">
                        <span><?php esc_html_e( 'su 100', 'fp-forms' ); ?></span>
                        <p class="description">
                            <?php esc_html_e( 'Se il punteggio composito anti-spam supera questa soglia, l\'invio viene bloccato (honeypot, tempo, reCAPTCHA).', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <h2 id="stripe"><?php esc_html_e( 'Stripe (pagamenti)', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php esc_html_e( 'Configura le chiavi API Stripe per abilitare il pagamento nei form. Ottieni le chiavi dalla dashboard Stripe.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row"><label for="stripe_secret_key"><?php esc_html_e( 'Secret Key', 'fp-forms' ); ?></label></th>
                    <td>
                        <input type="password" id="stripe_secret_key" name="stripe_secret_key" value="<?php echo esc_attr( $stripe_secret_key ); ?>" class="regular-text" autocomplete="off">
                        <p class="description"><?php esc_html_e( 'Chiave segreta (sk_live_... o sk_test_...). Non condividerla.', 'fp-forms' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="stripe_publishable_key"><?php esc_html_e( 'Publishable Key', 'fp-forms' ); ?></label></th>
                    <td>
                        <input type="text" id="stripe_publishable_key" name="stripe_publishable_key" value="<?php echo esc_attr( $stripe_publishable_key ); ?>" class="regular-text" placeholder="pk_live_...">
                        <p class="description"><?php esc_html_e( 'Chiave pubblica (pk_live_... o pk_test_...).', 'fp-forms' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="stripe_webhook_secret"><?php esc_html_e( 'Webhook Secret', 'fp-forms' ); ?></label></th>
                    <td>
                        <input type="password" id="stripe_webhook_secret" name="stripe_webhook_secret" value="<?php echo esc_attr( $stripe_webhook_secret ); ?>" class="regular-text" autocomplete="off" placeholder="whsec_...">
                        <p class="description"><?php esc_html_e( 'Firma webhook (whsec_...) per verificare gli eventi da Stripe.', 'fp-forms' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <h2 id="tracking"><?php _e( 'Tracking marketing', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'FP Forms invia eventi al layer centralizzato. Configura GTM, GA4, Google Ads, Meta Pixel e Clarity solo in FP Marketing Tracking Layer.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Configurazione centralizzata', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div class="notice notice-info inline" style="margin: 0; padding: 8px 12px;">
                            <p style="margin: 0;">
                                <strong><?php _e( 'Nessun ID da inserire qui.', 'fp-forms' ); ?></strong>
                                <?php _e( ' Per evitare doppi tracciamenti, gli ID marketing vengono gestiti solo in FP Tracking.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <h2 id="brevo"><?php _e( 'Brevo (Sendinblue) Integration', 'fp-forms' ); ?></h2>
                        <?php if ( $brevo_has_central ) : ?>
                        <div class="notice notice-info inline" style="margin: 8px 0; padding: 10px 12px;">
                            <p style="margin: 0;">
                                <?php printf(
                                    /* translators: %s: link to FP Tracking settings */
                                    __( 'API Key e liste ITA/ENG sono configurate in FP Tracking. Configura Brevo in %s.', 'fp-forms' ),
                                    '<a href="' . esc_url( admin_url( 'admin.php?page=fp-tracking' ) ) . '">FP Tracking → Configurazione</a>'
                                ); ?>
                            </p>
                        </div>
                        <?php else : ?>
                        <div class="notice notice-warning inline" style="margin: 8px 0; padding: 10px 12px;">
                            <p style="margin: 0;">
                                <?php _e( 'Per usare Brevo, attiva FP Marketing Tracking Layer e configura API Key e liste ITA/ENG nella sua pagina impostazioni.', 'fp-forms' ); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Sincronizza contatti con Brevo e traccia eventi. La lista per form si imposta nel builder di ogni form.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Opzioni Sync', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="brevo_double_optin" value="1" <?php checked( $brevo_double_optin, true ); ?>>
                                <strong><?php _e( 'Double Opt-In', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description">
                                <?php _e( 'Invia email di conferma prima di aggiungere il contatto alla lista (raccomandato per GDPR)', 'fp-forms' ); ?>
                            </p>
                            <br>
                            <label>
                                <input type="checkbox" name="brevo_track_events" value="1" <?php checked( $brevo_track_events, true ); ?>>
                                <strong><?php _e( 'Traccia Eventi', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description">
                                <?php _e( 'Invia eventi personalizzati a Brevo per ogni submission (utile per automazioni e segmentazione)', 'fp-forms' ); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
                

                <tr>
                    <th colspan="2">
                        <h2 id="simulation"><?php _e( 'Simulazione Flussi (Dry-Run)', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Esegue un controllo completo di email, tracking e integrazioni senza invii reali e senza credenziali obbligatorie.', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="fp_simulation_form_id"><?php _e( 'Form da simulare', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <select id="fp_simulation_form_id" class="regular-text">
                            <?php if ( empty( $simulation_forms ) ) : ?>
                                <option value=""><?php _e( 'Nessun form disponibile', 'fp-forms' ); ?></option>
                            <?php else : ?>
                                <?php foreach ( $simulation_forms as $sim_form ) : ?>
                                    <option value="<?php echo esc_attr( (string) ( $sim_form['id'] ?? 0 ) ); ?>">
                                        <?php
                                        echo esc_html(
                                            sprintf(
                                                '#%1$d - %2$s',
                                                (int) ( $sim_form['id'] ?? 0 ),
                                                (string) ( $sim_form['title'] ?? __( 'Form senza titolo', 'fp-forms' ) )
                                            )
                                        );
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="button" id="fp-run-simulation" class="button button-secondary" <?php disabled( empty( $simulation_forms ) ); ?>>
                            <span class="dashicons dashicons-controls-play"></span>
                            <?php _e( 'Esegui Simulazione', 'fp-forms' ); ?>
                        </button>
                        <p class="description" style="margin-top:8px;">
                            <?php _e( 'La simulazione verifica configurazione e readiness dei flussi senza inviare email, webhook o chiamate esterne.', 'fp-forms' ); ?>
                        </p>
                        <div id="fp-simulation-result" style="margin-top: 12px;"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="fp_forms_settings_submit" 
                   class="button button-primary" 
                   value="<?php esc_attr_e( 'Salva Impostazioni', 'fp-forms' ); ?>">
        </p>
    </form>
</div>
