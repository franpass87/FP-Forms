<?php
/**
 * Template: Impostazioni plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$email_from_name = get_option( 'fp_forms_email_from_name', get_bloginfo( 'name' ) );
$email_from_address = get_option( 'fp_forms_email_from_address', get_bloginfo( 'admin_email' ) );

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

// Tracking settings (GTM & GA4)
$tracking_settings = get_option( 'fp_forms_tracking_settings', [
    'gtm_id' => '',
    'ga4_id' => '',
    'track_views' => true,
    'track_interactions' => false,
] );
$gtm_id = $tracking_settings['gtm_id'] ?? '';
$ga4_id = $tracking_settings['ga4_id'] ?? '';
$track_views = $tracking_settings['track_views'] ?? true;
$track_interactions = $tracking_settings['track_interactions'] ?? false;

// Brevo (Sendinblue) settings
$brevo_settings = get_option( 'fp_forms_brevo_settings', [
    'api_key' => '',
    'default_list_id' => '',
    'double_optin' => false,
    'track_events' => true,
] );
$brevo_api_key = $brevo_settings['api_key'] ?? '';
$brevo_default_list = $brevo_settings['default_list_id'] ?? '';
$brevo_double_optin = $brevo_settings['double_optin'] ?? false;
$brevo_track_events = $brevo_settings['track_events'] ?? true;

// Meta (Facebook) Pixel settings
$meta_settings = get_option( 'fp_forms_meta_settings', [
    'pixel_id' => '',
    'access_token' => '',
    'track_views' => true,
] );
$meta_pixel_id = $meta_settings['pixel_id'] ?? '';
$meta_access_token = $meta_settings['access_token'] ?? '';
$meta_track_views = $meta_settings['track_views'] ?? true;
?>

<div class="wrap fp-forms-admin">
    <div class="fp-forms-admin__header">
        <h1><?php _e( 'Impostazioni FP Forms', 'fp-forms' ); ?></h1>
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
                            <?php _e( 'Il nome che apparirà come mittente nelle email inviate dal plugin.', 'fp-forms' ); ?>
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
                            <?php _e( 'L\'indirizzo email che apparirà come mittente.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                
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
                            <strong>v2:</strong> <?php _e( 'Mostra il checkbox "Non sono un robot" (più affidabile)', 'fp-forms' ); ?><br>
                            <strong>v3:</strong> <?php _e( 'Completamente invisibile, analizza il comportamento utente (più fluido)', 'fp-forms' ); ?>
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
                            <?php _e( 'Score più alto = più restrittivo. 0.0 = bot sicuro, 1.0 = umano sicuro. Valori tipici:', 'fp-forms' ); ?><br>
                            • <code>0.9</code>: <?php _e( 'Molto restrittivo (pochi falsi positivi)', 'fp-forms' ); ?><br>
                            • <code>0.7</code>: <?php _e( 'Restrittivo', 'fp-forms' ); ?><br>
                            • <code>0.5</code>: <?php _e( 'Bilanciato (raccomandato)', 'fp-forms' ); ?><br>
                            • <code>0.3</code>: <?php _e( 'Permissivo', 'fp-forms' ); ?>
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
                    <th colspan="2">
                        <h2 id="tracking"><?php _e( 'Google Tag Manager & Analytics', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Traccia submissions, conversioni e comportamento utenti con GTM e GA4', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gtm_id"><?php _e( 'Google Tag Manager ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gtm_id" 
                               name="gtm_id" 
                               value="<?php echo esc_attr( $gtm_id ); ?>" 
                               class="regular-text"
                               placeholder="GTM-XXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Container ID di Google Tag Manager. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ga4_id"><?php _e( 'Google Analytics 4 ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="ga4_id" 
                               name="ga4_id" 
                               value="<?php echo esc_attr( $ga4_id ); ?>" 
                               class="regular-text"
                               placeholder="G-XXXXXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Measurement ID di Google Analytics 4. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://analytics.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi da Tracciare', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Eventi da tracciare', 'fp-forms' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="track_views" value="1" <?php checked( $track_views, true ); ?>>
                                <strong><?php _e( 'Form Views', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia quando un form viene visualizzato (page view con form)', 'fp-forms' ); ?></p>
                            
                            <br>
                            
                            <label>
                                <input type="checkbox" name="track_interactions" value="1" <?php checked( $track_interactions, true ); ?>>
                                <strong><?php _e( 'Field Interactions', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia ogni interazione con i campi (focus, input). Può generare molti eventi.', 'fp-forms' ); ?></p>
                        </fieldset>
                        
                        <div style="margin-top: 16px; padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Tracciati Automaticamente (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 8px 0 0 20px; font-size: 13px;">
                                <li><code>fp_form_view</code> - 👁️ <?php _e( 'Form visualizzato (awareness)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_start</code> - ✏️ <?php _e( 'Inizio compilazione - primo campo focus (interest)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_progress</code> - 📊 <?php _e( 'Progressione form (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_submit</code> - ✅ <?php _e( 'Form completato con successo (conversion)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_conversion</code> - 🎯 <?php _e( 'Conversione per Google Ads', 'fp-forms' ); ?></li>
                                <li><code>fp_form_abandon</code> - ❌ <?php _e( 'Abbandono form (remarketing)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_validation_error</code> - ⚠️ <?php _e( 'Errore validazione campo (optimization)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_error</code> - 🚫 <?php _e( 'Errore invio generale', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 12px 0 0; font-size: 12px; padding: 8px; background: #fff; border-radius: 4px;">
                                <strong>📈 Metriche Incluse:</strong> Tempo compilazione, % progress, error_field, conversion_value
                            </p>
                        </div>
                    </td>
                </tr>
                <?php if ( ! empty( $gtm_id ) || ! empty( $ga4_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Status Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div class="notice notice-success inline" style="margin: 0; padding: 8px 12px;">
                            <p style="margin: 0;">
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                <strong><?php _e( 'Tracking Attivo!', 'fp-forms' ); ?></strong>
                            </p>
                            <?php if ( ! empty( $gtm_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Tag Manager: <code><?php echo esc_html( $gtm_id ); ?></code>
                            </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $ga4_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Analytics 4: <code><?php echo esc_html( $ga4_id ); ?></code>
                            </p>
                            <?php endif; ?>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            <?php _e( 'Verifica che gli script siano caricati correttamente usando la console del browser o Google Tag Assistant.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="brevo"><?php _e( 'Brevo (Sendinblue) Integration', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Sincronizza automaticamente contatti con Brevo CRM e traccia eventi. %sOttieni API Key%s', 'fp-forms' ),
                                '<a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_api_key"><?php _e( 'Brevo API Key', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="brevo_api_key" 
                               name="brevo_api_key" 
                               value="<?php echo esc_attr( $brevo_api_key ); ?>" 
                               class="regular-text"
                               placeholder="xkeysib-...">
                        <p class="description">
                            <?php _e( 'La tua API Key v3 da Brevo (Settings → API Keys)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_default_list"><?php _e( 'Lista Default', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="brevo_default_list" 
                               name="brevo_default_list" 
                               value="<?php echo esc_attr( $brevo_default_list ); ?>" 
                               class="small-text"
                               placeholder="2">
                        <button type="button" id="fp-load-brevo-lists" class="button" <?php disabled( empty( $brevo_api_key ) ); ?>>
                            <span class="dashicons dashicons-update"></span>
                            <?php _e( 'Carica Liste', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-lists-container" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'ID della lista Brevo a cui aggiungere i contatti (se non specificato nel form)', 'fp-forms' ); ?>
                        </p>
                    </td>
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
                <?php if ( ! empty( $brevo_api_key ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-brevo" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Brevo', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che la API Key sia valida e che Brevo risponda', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati a Brevo', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📤 <?php _e( 'Per ogni submission:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><strong><?php _e( 'Contatto:', 'fp-forms' ); ?></strong> <?php _e( 'Email + attributi (nome, cognome, telefono, ecc.)', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Liste:', 'fp-forms' ); ?></strong> <?php _e( 'Aggiunto alla lista configurata nel form', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Evento:', 'fp-forms' ); ?></strong> <code>form_submission</code> <?php _e( 'con metadata (form_id, form_title, submission_id)', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 0; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Puoi personalizzare il nome evento e la lista per ogni form nelle impostazioni form.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="meta"><?php _e( 'Meta (Facebook) Pixel & Conversions API', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Traccia conversioni con Facebook Pixel (client-side) e Conversions API (server-side per iOS 14.5+). %sOttieni Pixel ID%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_pixel_id"><?php _e( 'Facebook Pixel ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_pixel_id" 
                               name="meta_pixel_id" 
                               value="<?php echo esc_attr( $meta_pixel_id ); ?>" 
                               class="regular-text"
                               placeholder="1234567890123456">
                        <p class="description">
                            <?php _e( 'Il tuo Pixel ID da Facebook Events Manager (15-16 cifre)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_access_token"><?php _e( 'Conversions API Token (opzionale)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_access_token" 
                               name="meta_access_token" 
                               value="<?php echo esc_attr( $meta_access_token ); ?>" 
                               class="regular-text"
                               placeholder="EAAG...">
                        <p class="description">
                            <strong><?php _e( 'Raccomandato!', 'fp-forms' ); ?></strong>
                            <?php printf(
                                __( 'Access Token per tracking server-side (bypassa ad blockers e iOS 14.5+ restrictions). %sGenera Token%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2/list/pixel/settings" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                        <div style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; font-size: 13px; color: #856404;">
                                <strong>⚠️ Perché Conversions API?</strong><br>
                                • iOS 14.5+ blocca molti pixel client-side<br>
                                • Ad blockers impediscono tracking<br>
                                • Server-side = 100% affidabile<br>
                                • Migliora accuratezza campagne Meta Ads
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Opzioni Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="meta_track_views" value="1" <?php checked( $meta_track_views, true ); ?>>
                            <strong><?php _e( 'Traccia Form Views', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'Invia evento ViewContent quando un form viene visualizzato', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( ! empty( $meta_pixel_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-meta" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Meta', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-meta-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che Pixel ID e Access Token siano validi', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi Tracciati', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Meta (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #0073aa;">
                                <?php _e( '✅ Eventi Standard (usabili in Meta Ads):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>PageView</code> - <?php _e( 'Caricamento pagina', 'fp-forms' ); ?></li>
                                <li><code>ViewContent</code> - 👁️ <?php _e( 'Form visualizzato', 'fp-forms' ); ?></li>
                                <li><code>Lead</code> - 🎯 <?php _e( 'Form submission (CONVERSIONE PRINCIPALE)', 'fp-forms' ); ?></li>
                                <li><code>CompleteRegistration</code> - ✅ <?php _e( 'Completata registrazione (se signup form)', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #666;">
                                <?php _e( '🔧 Eventi Custom (analytics & optimization):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>FormStart</code> - ✏️ <?php _e( 'Inizio compilazione', 'fp-forms' ); ?></li>
                                <li><code>FormProgress</code> - 📊 <?php _e( 'Progressione (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>FormAbandoned</code> - ❌ <?php _e( 'Abbandono form', 'fp-forms' ); ?></li>
                                <li><code>FormValidationError</code> - ⚠️ <?php _e( 'Errore validazione', 'fp-forms' ); ?></li>
                                <li><code>FormSubmission</code> - 📝 <?php _e( 'Metadata dettagliati submission', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 0; padding: 8px; background: #fff; border-radius: 4px; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Eventi Lead e CompleteRegistration sono ottimizzabili in campagne Meta Ads. Eventi custom per remarketing e analytics.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati (CAPI)', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                🔒 <?php _e( 'Dati Hashed (SHA256) per Privacy:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0; font-size: 13px;">
                                <li><strong>em</strong> - Email (hashed)</li>
                                <li><strong>fn</strong> - Nome (hashed)</li>
                                <li><strong>ln</strong> - Cognome (hashed)</li>
                                <li><strong>ph</strong> - Telefono (hashed)</li>
                                <li><strong>client_ip_address</strong> - IP utente</li>
                                <li><strong>client_user_agent</strong> - User Agent</li>
                                <li><strong>fbp/fbc</strong> - Cookie Facebook (se presenti)</li>
                            </ul>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            ℹ️ <?php _e( 'Tutti i dati personali sono hashed con SHA256 prima dell\'invio (GDPR compliant)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="fp_forms_settings_submit" 
                   class="button button-primary" 
                   value="<?php _e( 'Salva Impostazioni', 'fp-forms' ); ?>">
        </p>
    </form>
</div>


                            <option value="v3" <?php selected( $recaptcha_version, 'v3' ); ?>>
                                <?php _e( 'v3 (Invisibile - Score Based)', 'fp-forms' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <strong>v2:</strong> <?php _e( 'Mostra il checkbox "Non sono un robot" (più affidabile)', 'fp-forms' ); ?><br>
                            <strong>v3:</strong> <?php _e( 'Completamente invisibile, analizza il comportamento utente (più fluido)', 'fp-forms' ); ?>
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
                            <?php _e( 'Score più alto = più restrittivo. 0.0 = bot sicuro, 1.0 = umano sicuro. Valori tipici:', 'fp-forms' ); ?><br>
                            • <code>0.9</code>: <?php _e( 'Molto restrittivo (pochi falsi positivi)', 'fp-forms' ); ?><br>
                            • <code>0.7</code>: <?php _e( 'Restrittivo', 'fp-forms' ); ?><br>
                            • <code>0.5</code>: <?php _e( 'Bilanciato (raccomandato)', 'fp-forms' ); ?><br>
                            • <code>0.3</code>: <?php _e( 'Permissivo', 'fp-forms' ); ?>
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
                    <th colspan="2">
                        <h2 id="tracking"><?php _e( 'Google Tag Manager & Analytics', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Traccia submissions, conversioni e comportamento utenti con GTM e GA4', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gtm_id"><?php _e( 'Google Tag Manager ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gtm_id" 
                               name="gtm_id" 
                               value="<?php echo esc_attr( $gtm_id ); ?>" 
                               class="regular-text"
                               placeholder="GTM-XXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Container ID di Google Tag Manager. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ga4_id"><?php _e( 'Google Analytics 4 ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="ga4_id" 
                               name="ga4_id" 
                               value="<?php echo esc_attr( $ga4_id ); ?>" 
                               class="regular-text"
                               placeholder="G-XXXXXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Measurement ID di Google Analytics 4. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://analytics.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi da Tracciare', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Eventi da tracciare', 'fp-forms' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="track_views" value="1" <?php checked( $track_views, true ); ?>>
                                <strong><?php _e( 'Form Views', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia quando un form viene visualizzato (page view con form)', 'fp-forms' ); ?></p>
                            
                            <br>
                            
                            <label>
                                <input type="checkbox" name="track_interactions" value="1" <?php checked( $track_interactions, true ); ?>>
                                <strong><?php _e( 'Field Interactions', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia ogni interazione con i campi (focus, input). Può generare molti eventi.', 'fp-forms' ); ?></p>
                        </fieldset>
                        
                        <div style="margin-top: 16px; padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Tracciati Automaticamente (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 8px 0 0 20px; font-size: 13px;">
                                <li><code>fp_form_view</code> - 👁️ <?php _e( 'Form visualizzato (awareness)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_start</code> - ✏️ <?php _e( 'Inizio compilazione - primo campo focus (interest)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_progress</code> - 📊 <?php _e( 'Progressione form (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_submit</code> - ✅ <?php _e( 'Form completato con successo (conversion)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_conversion</code> - 🎯 <?php _e( 'Conversione per Google Ads', 'fp-forms' ); ?></li>
                                <li><code>fp_form_abandon</code> - ❌ <?php _e( 'Abbandono form (remarketing)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_validation_error</code> - ⚠️ <?php _e( 'Errore validazione campo (optimization)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_error</code> - 🚫 <?php _e( 'Errore invio generale', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 12px 0 0; font-size: 12px; padding: 8px; background: #fff; border-radius: 4px;">
                                <strong>📈 Metriche Incluse:</strong> Tempo compilazione, % progress, error_field, conversion_value
                            </p>
                        </div>
                    </td>
                </tr>
                <?php if ( ! empty( $gtm_id ) || ! empty( $ga4_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Status Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div class="notice notice-success inline" style="margin: 0; padding: 8px 12px;">
                            <p style="margin: 0;">
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                <strong><?php _e( 'Tracking Attivo!', 'fp-forms' ); ?></strong>
                            </p>
                            <?php if ( ! empty( $gtm_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Tag Manager: <code><?php echo esc_html( $gtm_id ); ?></code>
                            </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $ga4_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Analytics 4: <code><?php echo esc_html( $ga4_id ); ?></code>
                            </p>
                            <?php endif; ?>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            <?php _e( 'Verifica che gli script siano caricati correttamente usando la console del browser o Google Tag Assistant.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="brevo"><?php _e( 'Brevo (Sendinblue) Integration', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Sincronizza automaticamente contatti con Brevo CRM e traccia eventi. %sOttieni API Key%s', 'fp-forms' ),
                                '<a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_api_key"><?php _e( 'Brevo API Key', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="brevo_api_key" 
                               name="brevo_api_key" 
                               value="<?php echo esc_attr( $brevo_api_key ); ?>" 
                               class="regular-text"
                               placeholder="xkeysib-...">
                        <p class="description">
                            <?php _e( 'La tua API Key v3 da Brevo (Settings → API Keys)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_default_list"><?php _e( 'Lista Default', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="brevo_default_list" 
                               name="brevo_default_list" 
                               value="<?php echo esc_attr( $brevo_default_list ); ?>" 
                               class="small-text"
                               placeholder="2">
                        <button type="button" id="fp-load-brevo-lists" class="button" <?php disabled( empty( $brevo_api_key ) ); ?>>
                            <span class="dashicons dashicons-update"></span>
                            <?php _e( 'Carica Liste', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-lists-container" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'ID della lista Brevo a cui aggiungere i contatti (se non specificato nel form)', 'fp-forms' ); ?>
                        </p>
                    </td>
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
                <?php if ( ! empty( $brevo_api_key ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-brevo" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Brevo', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che la API Key sia valida e che Brevo risponda', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati a Brevo', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📤 <?php _e( 'Per ogni submission:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><strong><?php _e( 'Contatto:', 'fp-forms' ); ?></strong> <?php _e( 'Email + attributi (nome, cognome, telefono, ecc.)', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Liste:', 'fp-forms' ); ?></strong> <?php _e( 'Aggiunto alla lista configurata nel form', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Evento:', 'fp-forms' ); ?></strong> <code>form_submission</code> <?php _e( 'con metadata (form_id, form_title, submission_id)', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 0; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Puoi personalizzare il nome evento e la lista per ogni form nelle impostazioni form.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="meta"><?php _e( 'Meta (Facebook) Pixel & Conversions API', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Traccia conversioni con Facebook Pixel (client-side) e Conversions API (server-side per iOS 14.5+). %sOttieni Pixel ID%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_pixel_id"><?php _e( 'Facebook Pixel ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_pixel_id" 
                               name="meta_pixel_id" 
                               value="<?php echo esc_attr( $meta_pixel_id ); ?>" 
                               class="regular-text"
                               placeholder="1234567890123456">
                        <p class="description">
                            <?php _e( 'Il tuo Pixel ID da Facebook Events Manager (15-16 cifre)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_access_token"><?php _e( 'Conversions API Token (opzionale)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_access_token" 
                               name="meta_access_token" 
                               value="<?php echo esc_attr( $meta_access_token ); ?>" 
                               class="regular-text"
                               placeholder="EAAG...">
                        <p class="description">
                            <strong><?php _e( 'Raccomandato!', 'fp-forms' ); ?></strong>
                            <?php printf(
                                __( 'Access Token per tracking server-side (bypassa ad blockers e iOS 14.5+ restrictions). %sGenera Token%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2/list/pixel/settings" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                        <div style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; font-size: 13px; color: #856404;">
                                <strong>⚠️ Perché Conversions API?</strong><br>
                                • iOS 14.5+ blocca molti pixel client-side<br>
                                • Ad blockers impediscono tracking<br>
                                • Server-side = 100% affidabile<br>
                                • Migliora accuratezza campagne Meta Ads
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Opzioni Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="meta_track_views" value="1" <?php checked( $meta_track_views, true ); ?>>
                            <strong><?php _e( 'Traccia Form Views', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'Invia evento ViewContent quando un form viene visualizzato', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( ! empty( $meta_pixel_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-meta" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Meta', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-meta-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che Pixel ID e Access Token siano validi', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi Tracciati', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Meta (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #0073aa;">
                                <?php _e( '✅ Eventi Standard (usabili in Meta Ads):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>PageView</code> - <?php _e( 'Caricamento pagina', 'fp-forms' ); ?></li>
                                <li><code>ViewContent</code> - 👁️ <?php _e( 'Form visualizzato', 'fp-forms' ); ?></li>
                                <li><code>Lead</code> - 🎯 <?php _e( 'Form submission (CONVERSIONE PRINCIPALE)', 'fp-forms' ); ?></li>
                                <li><code>CompleteRegistration</code> - ✅ <?php _e( 'Completata registrazione (se signup form)', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #666;">
                                <?php _e( '🔧 Eventi Custom (analytics & optimization):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>FormStart</code> - ✏️ <?php _e( 'Inizio compilazione', 'fp-forms' ); ?></li>
                                <li><code>FormProgress</code> - 📊 <?php _e( 'Progressione (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>FormAbandoned</code> - ❌ <?php _e( 'Abbandono form', 'fp-forms' ); ?></li>
                                <li><code>FormValidationError</code> - ⚠️ <?php _e( 'Errore validazione', 'fp-forms' ); ?></li>
                                <li><code>FormSubmission</code> - 📝 <?php _e( 'Metadata dettagliati submission', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 0; padding: 8px; background: #fff; border-radius: 4px; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Eventi Lead e CompleteRegistration sono ottimizzabili in campagne Meta Ads. Eventi custom per remarketing e analytics.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati (CAPI)', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                🔒 <?php _e( 'Dati Hashed (SHA256) per Privacy:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0; font-size: 13px;">
                                <li><strong>em</strong> - Email (hashed)</li>
                                <li><strong>fn</strong> - Nome (hashed)</li>
                                <li><strong>ln</strong> - Cognome (hashed)</li>
                                <li><strong>ph</strong> - Telefono (hashed)</li>
                                <li><strong>client_ip_address</strong> - IP utente</li>
                                <li><strong>client_user_agent</strong> - User Agent</li>
                                <li><strong>fbp/fbc</strong> - Cookie Facebook (se presenti)</li>
                            </ul>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            ℹ️ <?php _e( 'Tutti i dati personali sono hashed con SHA256 prima dell\'invio (GDPR compliant)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="fp_forms_settings_submit" 
                   class="button button-primary" 
                   value="<?php _e( 'Salva Impostazioni', 'fp-forms' ); ?>">
        </p>
    </form>
</div>


                            <option value="v3" <?php selected( $recaptcha_version, 'v3' ); ?>>
                                <?php _e( 'v3 (Invisibile - Score Based)', 'fp-forms' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <strong>v2:</strong> <?php _e( 'Mostra il checkbox "Non sono un robot" (più affidabile)', 'fp-forms' ); ?><br>
                            <strong>v3:</strong> <?php _e( 'Completamente invisibile, analizza il comportamento utente (più fluido)', 'fp-forms' ); ?>
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
                            <?php _e( 'Score più alto = più restrittivo. 0.0 = bot sicuro, 1.0 = umano sicuro. Valori tipici:', 'fp-forms' ); ?><br>
                            • <code>0.9</code>: <?php _e( 'Molto restrittivo (pochi falsi positivi)', 'fp-forms' ); ?><br>
                            • <code>0.7</code>: <?php _e( 'Restrittivo', 'fp-forms' ); ?><br>
                            • <code>0.5</code>: <?php _e( 'Bilanciato (raccomandato)', 'fp-forms' ); ?><br>
                            • <code>0.3</code>: <?php _e( 'Permissivo', 'fp-forms' ); ?>
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
                    <th colspan="2">
                        <h2 id="tracking"><?php _e( 'Google Tag Manager & Analytics', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Traccia submissions, conversioni e comportamento utenti con GTM e GA4', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gtm_id"><?php _e( 'Google Tag Manager ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gtm_id" 
                               name="gtm_id" 
                               value="<?php echo esc_attr( $gtm_id ); ?>" 
                               class="regular-text"
                               placeholder="GTM-XXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Container ID di Google Tag Manager. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ga4_id"><?php _e( 'Google Analytics 4 ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="ga4_id" 
                               name="ga4_id" 
                               value="<?php echo esc_attr( $ga4_id ); ?>" 
                               class="regular-text"
                               placeholder="G-XXXXXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Measurement ID di Google Analytics 4. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://analytics.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi da Tracciare', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Eventi da tracciare', 'fp-forms' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="track_views" value="1" <?php checked( $track_views, true ); ?>>
                                <strong><?php _e( 'Form Views', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia quando un form viene visualizzato (page view con form)', 'fp-forms' ); ?></p>
                            
                            <br>
                            
                            <label>
                                <input type="checkbox" name="track_interactions" value="1" <?php checked( $track_interactions, true ); ?>>
                                <strong><?php _e( 'Field Interactions', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia ogni interazione con i campi (focus, input). Può generare molti eventi.', 'fp-forms' ); ?></p>
                        </fieldset>
                        
                        <div style="margin-top: 16px; padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Tracciati Automaticamente (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 8px 0 0 20px; font-size: 13px;">
                                <li><code>fp_form_view</code> - 👁️ <?php _e( 'Form visualizzato (awareness)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_start</code> - ✏️ <?php _e( 'Inizio compilazione - primo campo focus (interest)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_progress</code> - 📊 <?php _e( 'Progressione form (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_submit</code> - ✅ <?php _e( 'Form completato con successo (conversion)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_conversion</code> - 🎯 <?php _e( 'Conversione per Google Ads', 'fp-forms' ); ?></li>
                                <li><code>fp_form_abandon</code> - ❌ <?php _e( 'Abbandono form (remarketing)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_validation_error</code> - ⚠️ <?php _e( 'Errore validazione campo (optimization)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_error</code> - 🚫 <?php _e( 'Errore invio generale', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 12px 0 0; font-size: 12px; padding: 8px; background: #fff; border-radius: 4px;">
                                <strong>📈 Metriche Incluse:</strong> Tempo compilazione, % progress, error_field, conversion_value
                            </p>
                        </div>
                    </td>
                </tr>
                <?php if ( ! empty( $gtm_id ) || ! empty( $ga4_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Status Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div class="notice notice-success inline" style="margin: 0; padding: 8px 12px;">
                            <p style="margin: 0;">
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                <strong><?php _e( 'Tracking Attivo!', 'fp-forms' ); ?></strong>
                            </p>
                            <?php if ( ! empty( $gtm_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Tag Manager: <code><?php echo esc_html( $gtm_id ); ?></code>
                            </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $ga4_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Analytics 4: <code><?php echo esc_html( $ga4_id ); ?></code>
                            </p>
                            <?php endif; ?>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            <?php _e( 'Verifica che gli script siano caricati correttamente usando la console del browser o Google Tag Assistant.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="brevo"><?php _e( 'Brevo (Sendinblue) Integration', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Sincronizza automaticamente contatti con Brevo CRM e traccia eventi. %sOttieni API Key%s', 'fp-forms' ),
                                '<a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_api_key"><?php _e( 'Brevo API Key', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="brevo_api_key" 
                               name="brevo_api_key" 
                               value="<?php echo esc_attr( $brevo_api_key ); ?>" 
                               class="regular-text"
                               placeholder="xkeysib-...">
                        <p class="description">
                            <?php _e( 'La tua API Key v3 da Brevo (Settings → API Keys)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_default_list"><?php _e( 'Lista Default', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="brevo_default_list" 
                               name="brevo_default_list" 
                               value="<?php echo esc_attr( $brevo_default_list ); ?>" 
                               class="small-text"
                               placeholder="2">
                        <button type="button" id="fp-load-brevo-lists" class="button" <?php disabled( empty( $brevo_api_key ) ); ?>>
                            <span class="dashicons dashicons-update"></span>
                            <?php _e( 'Carica Liste', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-lists-container" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'ID della lista Brevo a cui aggiungere i contatti (se non specificato nel form)', 'fp-forms' ); ?>
                        </p>
                    </td>
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
                <?php if ( ! empty( $brevo_api_key ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-brevo" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Brevo', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che la API Key sia valida e che Brevo risponda', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati a Brevo', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📤 <?php _e( 'Per ogni submission:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><strong><?php _e( 'Contatto:', 'fp-forms' ); ?></strong> <?php _e( 'Email + attributi (nome, cognome, telefono, ecc.)', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Liste:', 'fp-forms' ); ?></strong> <?php _e( 'Aggiunto alla lista configurata nel form', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Evento:', 'fp-forms' ); ?></strong> <code>form_submission</code> <?php _e( 'con metadata (form_id, form_title, submission_id)', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 0; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Puoi personalizzare il nome evento e la lista per ogni form nelle impostazioni form.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="meta"><?php _e( 'Meta (Facebook) Pixel & Conversions API', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Traccia conversioni con Facebook Pixel (client-side) e Conversions API (server-side per iOS 14.5+). %sOttieni Pixel ID%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_pixel_id"><?php _e( 'Facebook Pixel ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_pixel_id" 
                               name="meta_pixel_id" 
                               value="<?php echo esc_attr( $meta_pixel_id ); ?>" 
                               class="regular-text"
                               placeholder="1234567890123456">
                        <p class="description">
                            <?php _e( 'Il tuo Pixel ID da Facebook Events Manager (15-16 cifre)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_access_token"><?php _e( 'Conversions API Token (opzionale)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_access_token" 
                               name="meta_access_token" 
                               value="<?php echo esc_attr( $meta_access_token ); ?>" 
                               class="regular-text"
                               placeholder="EAAG...">
                        <p class="description">
                            <strong><?php _e( 'Raccomandato!', 'fp-forms' ); ?></strong>
                            <?php printf(
                                __( 'Access Token per tracking server-side (bypassa ad blockers e iOS 14.5+ restrictions). %sGenera Token%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2/list/pixel/settings" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                        <div style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; font-size: 13px; color: #856404;">
                                <strong>⚠️ Perché Conversions API?</strong><br>
                                • iOS 14.5+ blocca molti pixel client-side<br>
                                • Ad blockers impediscono tracking<br>
                                • Server-side = 100% affidabile<br>
                                • Migliora accuratezza campagne Meta Ads
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Opzioni Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="meta_track_views" value="1" <?php checked( $meta_track_views, true ); ?>>
                            <strong><?php _e( 'Traccia Form Views', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'Invia evento ViewContent quando un form viene visualizzato', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( ! empty( $meta_pixel_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-meta" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Meta', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-meta-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che Pixel ID e Access Token siano validi', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi Tracciati', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Meta (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #0073aa;">
                                <?php _e( '✅ Eventi Standard (usabili in Meta Ads):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>PageView</code> - <?php _e( 'Caricamento pagina', 'fp-forms' ); ?></li>
                                <li><code>ViewContent</code> - 👁️ <?php _e( 'Form visualizzato', 'fp-forms' ); ?></li>
                                <li><code>Lead</code> - 🎯 <?php _e( 'Form submission (CONVERSIONE PRINCIPALE)', 'fp-forms' ); ?></li>
                                <li><code>CompleteRegistration</code> - ✅ <?php _e( 'Completata registrazione (se signup form)', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #666;">
                                <?php _e( '🔧 Eventi Custom (analytics & optimization):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>FormStart</code> - ✏️ <?php _e( 'Inizio compilazione', 'fp-forms' ); ?></li>
                                <li><code>FormProgress</code> - 📊 <?php _e( 'Progressione (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>FormAbandoned</code> - ❌ <?php _e( 'Abbandono form', 'fp-forms' ); ?></li>
                                <li><code>FormValidationError</code> - ⚠️ <?php _e( 'Errore validazione', 'fp-forms' ); ?></li>
                                <li><code>FormSubmission</code> - 📝 <?php _e( 'Metadata dettagliati submission', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 0; padding: 8px; background: #fff; border-radius: 4px; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Eventi Lead e CompleteRegistration sono ottimizzabili in campagne Meta Ads. Eventi custom per remarketing e analytics.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati (CAPI)', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                🔒 <?php _e( 'Dati Hashed (SHA256) per Privacy:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0; font-size: 13px;">
                                <li><strong>em</strong> - Email (hashed)</li>
                                <li><strong>fn</strong> - Nome (hashed)</li>
                                <li><strong>ln</strong> - Cognome (hashed)</li>
                                <li><strong>ph</strong> - Telefono (hashed)</li>
                                <li><strong>client_ip_address</strong> - IP utente</li>
                                <li><strong>client_user_agent</strong> - User Agent</li>
                                <li><strong>fbp/fbc</strong> - Cookie Facebook (se presenti)</li>
                            </ul>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            ℹ️ <?php _e( 'Tutti i dati personali sono hashed con SHA256 prima dell\'invio (GDPR compliant)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="fp_forms_settings_submit" 
                   class="button button-primary" 
                   value="<?php _e( 'Salva Impostazioni', 'fp-forms' ); ?>">
        </p>
    </form>
</div>


                            <option value="v3" <?php selected( $recaptcha_version, 'v3' ); ?>>
                                <?php _e( 'v3 (Invisibile - Score Based)', 'fp-forms' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <strong>v2:</strong> <?php _e( 'Mostra il checkbox "Non sono un robot" (più affidabile)', 'fp-forms' ); ?><br>
                            <strong>v3:</strong> <?php _e( 'Completamente invisibile, analizza il comportamento utente (più fluido)', 'fp-forms' ); ?>
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
                            <?php _e( 'Score più alto = più restrittivo. 0.0 = bot sicuro, 1.0 = umano sicuro. Valori tipici:', 'fp-forms' ); ?><br>
                            • <code>0.9</code>: <?php _e( 'Molto restrittivo (pochi falsi positivi)', 'fp-forms' ); ?><br>
                            • <code>0.7</code>: <?php _e( 'Restrittivo', 'fp-forms' ); ?><br>
                            • <code>0.5</code>: <?php _e( 'Bilanciato (raccomandato)', 'fp-forms' ); ?><br>
                            • <code>0.3</code>: <?php _e( 'Permissivo', 'fp-forms' ); ?>
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
                    <th colspan="2">
                        <h2 id="tracking"><?php _e( 'Google Tag Manager & Analytics', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php _e( 'Traccia submissions, conversioni e comportamento utenti con GTM e GA4', 'fp-forms' ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gtm_id"><?php _e( 'Google Tag Manager ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="gtm_id" 
                               name="gtm_id" 
                               value="<?php echo esc_attr( $gtm_id ); ?>" 
                               class="regular-text"
                               placeholder="GTM-XXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Container ID di Google Tag Manager. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://tagmanager.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ga4_id"><?php _e( 'Google Analytics 4 ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="ga4_id" 
                               name="ga4_id" 
                               value="<?php echo esc_attr( $ga4_id ); ?>" 
                               class="regular-text"
                               placeholder="G-XXXXXXXXXX">
                        <p class="description">
                            <?php printf(
                                __( 'Il tuo Measurement ID di Google Analytics 4. %sOttieni il tuo ID%s', 'fp-forms' ),
                                '<a href="https://analytics.google.com/" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi da Tracciare', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Eventi da tracciare', 'fp-forms' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="track_views" value="1" <?php checked( $track_views, true ); ?>>
                                <strong><?php _e( 'Form Views', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia quando un form viene visualizzato (page view con form)', 'fp-forms' ); ?></p>
                            
                            <br>
                            
                            <label>
                                <input type="checkbox" name="track_interactions" value="1" <?php checked( $track_interactions, true ); ?>>
                                <strong><?php _e( 'Field Interactions', 'fp-forms' ); ?></strong>
                            </label>
                            <p class="description"><?php _e( 'Traccia ogni interazione con i campi (focus, input). Può generare molti eventi.', 'fp-forms' ); ?></p>
                        </fieldset>
                        
                        <div style="margin-top: 16px; padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Tracciati Automaticamente (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 8px 0 0 20px; font-size: 13px;">
                                <li><code>fp_form_view</code> - 👁️ <?php _e( 'Form visualizzato (awareness)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_start</code> - ✏️ <?php _e( 'Inizio compilazione - primo campo focus (interest)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_progress</code> - 📊 <?php _e( 'Progressione form (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_submit</code> - ✅ <?php _e( 'Form completato con successo (conversion)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_conversion</code> - 🎯 <?php _e( 'Conversione per Google Ads', 'fp-forms' ); ?></li>
                                <li><code>fp_form_abandon</code> - ❌ <?php _e( 'Abbandono form (remarketing)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_validation_error</code> - ⚠️ <?php _e( 'Errore validazione campo (optimization)', 'fp-forms' ); ?></li>
                                <li><code>fp_form_error</code> - 🚫 <?php _e( 'Errore invio generale', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 12px 0 0; font-size: 12px; padding: 8px; background: #fff; border-radius: 4px;">
                                <strong>📈 Metriche Incluse:</strong> Tempo compilazione, % progress, error_field, conversion_value
                            </p>
                        </div>
                    </td>
                </tr>
                <?php if ( ! empty( $gtm_id ) || ! empty( $ga4_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Status Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div class="notice notice-success inline" style="margin: 0; padding: 8px 12px;">
                            <p style="margin: 0;">
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                <strong><?php _e( 'Tracking Attivo!', 'fp-forms' ); ?></strong>
                            </p>
                            <?php if ( ! empty( $gtm_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Tag Manager: <code><?php echo esc_html( $gtm_id ); ?></code>
                            </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $ga4_id ) ) : ?>
                            <p style="margin: 8px 0 0; font-size: 13px;">
                                ✅ Google Analytics 4: <code><?php echo esc_html( $ga4_id ); ?></code>
                            </p>
                            <?php endif; ?>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            <?php _e( 'Verifica che gli script siano caricati correttamente usando la console del browser o Google Tag Assistant.', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="brevo"><?php _e( 'Brevo (Sendinblue) Integration', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Sincronizza automaticamente contatti con Brevo CRM e traccia eventi. %sOttieni API Key%s', 'fp-forms' ),
                                '<a href="https://app.brevo.com/settings/keys/api" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_api_key"><?php _e( 'Brevo API Key', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="brevo_api_key" 
                               name="brevo_api_key" 
                               value="<?php echo esc_attr( $brevo_api_key ); ?>" 
                               class="regular-text"
                               placeholder="xkeysib-...">
                        <p class="description">
                            <?php _e( 'La tua API Key v3 da Brevo (Settings → API Keys)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="brevo_default_list"><?php _e( 'Lista Default', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="brevo_default_list" 
                               name="brevo_default_list" 
                               value="<?php echo esc_attr( $brevo_default_list ); ?>" 
                               class="small-text"
                               placeholder="2">
                        <button type="button" id="fp-load-brevo-lists" class="button" <?php disabled( empty( $brevo_api_key ) ); ?>>
                            <span class="dashicons dashicons-update"></span>
                            <?php _e( 'Carica Liste', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-lists-container" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'ID della lista Brevo a cui aggiungere i contatti (se non specificato nel form)', 'fp-forms' ); ?>
                        </p>
                    </td>
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
                <?php if ( ! empty( $brevo_api_key ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-brevo" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Brevo', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-brevo-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che la API Key sia valida e che Brevo risponda', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati a Brevo', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📤 <?php _e( 'Per ogni submission:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><strong><?php _e( 'Contatto:', 'fp-forms' ); ?></strong> <?php _e( 'Email + attributi (nome, cognome, telefono, ecc.)', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Liste:', 'fp-forms' ); ?></strong> <?php _e( 'Aggiunto alla lista configurata nel form', 'fp-forms' ); ?></li>
                                <li><strong><?php _e( 'Evento:', 'fp-forms' ); ?></strong> <code>form_submission</code> <?php _e( 'con metadata (form_id, form_title, submission_id)', 'fp-forms' ); ?></li>
                            </ul>
                            <p style="margin: 0; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Puoi personalizzare il nome evento e la lista per ogni form nelle impostazioni form.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <th colspan="2">
                        <h2 id="meta"><?php _e( 'Meta (Facebook) Pixel & Conversions API', 'fp-forms' ); ?></h2>
                        <p class="description" style="font-weight: normal;">
                            <?php printf(
                                __( 'Traccia conversioni con Facebook Pixel (client-side) e Conversions API (server-side per iOS 14.5+). %sOttieni Pixel ID%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                    </th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_pixel_id"><?php _e( 'Facebook Pixel ID', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_pixel_id" 
                               name="meta_pixel_id" 
                               value="<?php echo esc_attr( $meta_pixel_id ); ?>" 
                               class="regular-text"
                               placeholder="1234567890123456">
                        <p class="description">
                            <?php _e( 'Il tuo Pixel ID da Facebook Events Manager (15-16 cifre)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="meta_access_token"><?php _e( 'Conversions API Token (opzionale)', 'fp-forms' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="meta_access_token" 
                               name="meta_access_token" 
                               value="<?php echo esc_attr( $meta_access_token ); ?>" 
                               class="regular-text"
                               placeholder="EAAG...">
                        <p class="description">
                            <strong><?php _e( 'Raccomandato!', 'fp-forms' ); ?></strong>
                            <?php printf(
                                __( 'Access Token per tracking server-side (bypassa ad blockers e iOS 14.5+ restrictions). %sGenera Token%s', 'fp-forms' ),
                                '<a href="https://business.facebook.com/events_manager2/list/pixel/settings" target="_blank" rel="noopener">',
                                '</a>'
                            ); ?>
                        </p>
                        <div style="margin-top: 12px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; font-size: 13px; color: #856404;">
                                <strong>⚠️ Perché Conversions API?</strong><br>
                                • iOS 14.5+ blocca molti pixel client-side<br>
                                • Ad blockers impediscono tracking<br>
                                • Server-side = 100% affidabile<br>
                                • Migliora accuratezza campagne Meta Ads
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Opzioni Tracking', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="meta_track_views" value="1" <?php checked( $meta_track_views, true ); ?>>
                            <strong><?php _e( 'Traccia Form Views', 'fp-forms' ); ?></strong>
                        </label>
                        <p class="description">
                            <?php _e( 'Invia evento ViewContent quando un form viene visualizzato', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php if ( ! empty( $meta_pixel_id ) ) : ?>
                <tr>
                    <th scope="row">
                        <?php _e( 'Test Connessione', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <button type="button" id="fp-test-meta" class="button">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php _e( 'Testa Connessione Meta', 'fp-forms' ); ?>
                        </button>
                        <div id="fp-meta-test-result" style="margin-top: 10px;"></div>
                        <p class="description">
                            <?php _e( 'Verifica che Pixel ID e Access Token siano validi', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Eventi Tracciati', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                📊 <?php _e( 'Eventi Meta (Funnel Completo):', 'fp-forms' ); ?>
                            </p>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #0073aa;">
                                <?php _e( '✅ Eventi Standard (usabili in Meta Ads):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>PageView</code> - <?php _e( 'Caricamento pagina', 'fp-forms' ); ?></li>
                                <li><code>ViewContent</code> - 👁️ <?php _e( 'Form visualizzato', 'fp-forms' ); ?></li>
                                <li><code>Lead</code> - 🎯 <?php _e( 'Form submission (CONVERSIONE PRINCIPALE)', 'fp-forms' ); ?></li>
                                <li><code>CompleteRegistration</code> - ✅ <?php _e( 'Completata registrazione (se signup form)', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 8px 0 4px; font-weight: 600; font-size: 12px; color: #666;">
                                <?php _e( '🔧 Eventi Custom (analytics & optimization):', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0 0 12px 20px; font-size: 13px;">
                                <li><code>FormStart</code> - ✏️ <?php _e( 'Inizio compilazione', 'fp-forms' ); ?></li>
                                <li><code>FormProgress</code> - 📊 <?php _e( 'Progressione (25%, 50%, 75%)', 'fp-forms' ); ?></li>
                                <li><code>FormAbandoned</code> - ❌ <?php _e( 'Abbandono form', 'fp-forms' ); ?></li>
                                <li><code>FormValidationError</code> - ⚠️ <?php _e( 'Errore validazione', 'fp-forms' ); ?></li>
                                <li><code>FormSubmission</code> - 📝 <?php _e( 'Metadata dettagliati submission', 'fp-forms' ); ?></li>
                            </ul>
                            
                            <p style="margin: 0; padding: 8px; background: #fff; border-radius: 4px; font-size: 13px; color: #666;">
                                💡 <?php _e( 'Eventi Lead e CompleteRegistration sono ottimizzabili in campagne Meta Ads. Eventi custom per remarketing e analytics.', 'fp-forms' ); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Dati Inviati (CAPI)', 'fp-forms' ); ?>
                    </th>
                    <td>
                        <div style="padding: 12px; background: #f0f6fc; border-left: 4px solid #0073aa; border-radius: 4px;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #0073aa;">
                                🔒 <?php _e( 'Dati Hashed (SHA256) per Privacy:', 'fp-forms' ); ?>
                            </p>
                            <ul style="margin: 0; font-size: 13px;">
                                <li><strong>em</strong> - Email (hashed)</li>
                                <li><strong>fn</strong> - Nome (hashed)</li>
                                <li><strong>ln</strong> - Cognome (hashed)</li>
                                <li><strong>ph</strong> - Telefono (hashed)</li>
                                <li><strong>client_ip_address</strong> - IP utente</li>
                                <li><strong>client_user_agent</strong> - User Agent</li>
                                <li><strong>fbp/fbc</strong> - Cookie Facebook (se presenti)</li>
                            </ul>
                        </div>
                        <p class="description" style="margin-top: 12px;">
                            ℹ️ <?php _e( 'Tutti i dati personali sono hashed con SHA256 prima dell\'invio (GDPR compliant)', 'fp-forms' ); ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="fp_forms_settings_submit" 
                   class="button button-primary" 
                   value="<?php _e( 'Salva Impostazioni', 'fp-forms' ); ?>">
        </p>
    </form>
</div>

