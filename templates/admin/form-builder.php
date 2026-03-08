<?php
/**
 * Template: Form Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_new = ! isset( $form );
$form_id = $is_new ? 0 : $form['id'];
$form_title = $is_new ? '' : $form['title'];
$form_description = $is_new ? '' : $form['description'];
$form_fields = $is_new ? [] : $form['fields'];
$form_settings = $is_new ? [] : $form['settings'];

// Settings di default
$default_settings = [
    'submit_button_text' => __( 'Invia', 'fp-forms' ),
    'submit_button_color' => '#3b82f6',
    'submit_button_size' => 'medium',
    'submit_button_style' => 'solid',
    'submit_button_align' => 'center',
    'submit_button_width' => 'auto',
    'submit_button_icon' => '',
    'trust_badges' => [],
    'success_message' => __( 'Grazie! Il tuo messaggio è stato inviato con successo.', 'fp-forms' ),
    'success_message_type' => 'success',
    'success_message_duration' => '0',
    'disable_wordpress_emails' => false,
    'notification_email' => get_option( 'admin_email' ),
    'notification_subject' => __( 'Nuova submission da {form_title}', 'fp-forms' ),
    'notification_message' => '',
    'confirmation_enabled' => false,
    'confirmation_template' => '',
    'confirmation_accent_color' => '',
    'confirmation_footer_info' => '',
    'confirmation_subject' => __( 'Abbiamo ricevuto il tuo messaggio - {site_name}', 'fp-forms' ),
    'confirmation_message' => '',
    'staff_notifications_enabled' => false,
    'staff_emails' => '',
    'staff_notification_subject' => __( '[STAFF] Nuova submission: {form_title}', 'fp-forms' ),
    'staff_notification_message' => '',
    'brevo_enabled' => true,
    'brevo_list_id' => '',
    'brevo_event_name' => '',
];

$form_settings = wp_parse_args( $form_settings, $default_settings );
?>

<div class="wrap fp-forms-admin fp-forms-builder">
    <div class="fp-forms-admin__header">
        <h1><?php echo $is_new ? __( 'Nuovo Form', 'fp-forms' ) : __( 'Modifica Form', 'fp-forms' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=fp-forms' ) ); ?>" class="button">
            &larr; <?php esc_html_e( 'Torna ai Form', 'fp-forms' ); ?>
        </a>
    </div>
    
    <form id="fp-form-builder" class="fp-builder-container" method="post" action="" onsubmit="return false;">
        <input type="hidden" name="form_id" id="form_id" value="<?php echo esc_attr( $form_id ); ?>">
        
        <div class="fp-builder-main">
            <div class="fp-builder-header">
                <div class="fp-form-info">
                    <input type="text" 
                           name="form_title" 
                           id="form_title" 
                           placeholder="<?php esc_html_e( 'Titolo del form', 'fp-forms' ); ?>" 
                           value="<?php echo esc_attr( $form_title ); ?>"
                           class="fp-input-large"
                           required>
                    
                    <textarea name="form_description" 
                              id="form_description" 
                              placeholder="<?php esc_html_e( 'Descrizione (opzionale)', 'fp-forms' ); ?>"
                              rows="2"
                              class="fp-textarea-large"><?php echo esc_textarea( $form_description ); ?></textarea>
                </div>
            </div>
            
            <div class="fp-builder-body">
                <div class="fp-fields-container" id="fp-fields-container">
                    <?php if ( ! empty( $form_fields ) ) : ?>
                        <?php foreach ( $form_fields as $index => $field ) : ?>
                            <?php include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/field-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="fp-add-field">
                    <button type="button" class="button" id="fp-add-field-btn">
                        <?php esc_html_e( '+ Aggiungi Campo', 'fp-forms' ); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="fp-builder-sidebar">
            <div class="fp-sidebar-section">
                <h3><?php esc_html_e( 'Tipi di Campo', 'fp-forms' ); ?></h3>
                <div class="fp-field-types">
                <button type="button" class="fp-field-type" data-type="text" data-tooltip="Campo di testo singola linea">
                    <span class="dashicons dashicons-text"></span>
                    <?php esc_html_e( 'Testo', 'fp-forms' ); ?>
                </button>
                <button type="button" class="fp-field-type" data-type="fullname" data-tooltip="Nome e cognome (due campi affiancati)">
                    <span class="dashicons dashicons-admin-users"></span>
                    <?php esc_html_e( 'Nome e cognome', 'fp-forms' ); ?>
                </button>
                <button type="button" class="fp-field-type" data-type="email" data-tooltip="Email con validazione automatica">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e( 'Email', 'fp-forms' ); ?>
                </button>
                    <button type="button" class="fp-field-type" data-type="phone">
                        <span class="dashicons dashicons-phone"></span>
                        <?php esc_html_e( 'Telefono', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="number">
                        <span class="dashicons dashicons-calculator"></span>
                        <?php esc_html_e( 'Numero', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="date">
                        <span class="dashicons dashicons-calendar"></span>
                        <?php esc_html_e( 'Data', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="textarea">
                        <span class="dashicons dashicons-text-page"></span>
                        <?php esc_html_e( 'Area di Testo', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="select">
                        <span class="dashicons dashicons-menu-alt"></span>
                        <?php esc_html_e( 'Select', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="radio">
                        <span class="dashicons dashicons-marker"></span>
                        <?php esc_html_e( 'Radio Button', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="checkbox">
                        <span class="dashicons dashicons-yes"></span>
                        <?php esc_html_e( 'Checkbox', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="privacy-checkbox" data-tooltip="Checkbox GDPR con link a Privacy Policy">
                        <span class="dashicons dashicons-privacy"></span>
                        <?php esc_html_e( 'Privacy Policy', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="marketing-checkbox" data-tooltip="Checkbox opzionale per consenso marketing">
                        <span class="dashicons dashicons-email-alt"></span>
                        <?php esc_html_e( 'Marketing', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="calculated" data-tooltip="Campo calcolato con formula">
                        <span class="dashicons dashicons-calculator"></span>
                        <?php esc_html_e( 'Calcolato', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="recaptcha" data-tooltip="Google reCAPTCHA anti-spam">
                        <span class="dashicons dashicons-shield"></span>
                        <?php esc_html_e( 'reCAPTCHA', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="file">
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e( 'Upload File', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type fp-field-type-step" data-type="step_break" data-tooltip="Separatore per form multi-step">
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <?php esc_html_e( 'Nuovo Step', 'fp-forms' ); ?>
                    </button>
                </div>
            </div>
            
            <div class="fp-sidebar-section">
                <h3><?php esc_html_e( 'Impostazioni Form', 'fp-forms' ); ?></h3>
                
                <h4><?php esc_html_e( 'Badge di Fiducia', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Mostra Badge (opzionale)', 'fp-forms' ); ?></label>
                    <small style="display: block; margin-bottom: 12px;"><?php esc_html_e( 'Seleziona i badge da mostrare in fondo al form, dopo il pulsante Invia, per aumentare fiducia e conversioni', 'fp-forms' ); ?></small>
                    
                    <?php
                    $available_badges = [
                        'instant-response' => [ 'icon' => '⚡', 'text' => __( 'Risposta Immediata', 'fp-forms' ) ],
                        'data-secure' => [ 'icon' => '🔒', 'text' => __( 'I Tuoi Dati Sono Al Sicuro', 'fp-forms' ) ],
                        'no-spam' => [ 'icon' => '🚫', 'text' => __( 'No Spam, Mai', 'fp-forms' ) ],
                        'gdpr-compliant' => [ 'icon' => '✓', 'text' => __( 'GDPR Compliant', 'fp-forms' ) ],
                        'ssl-secure' => [ 'icon' => '🔐', 'text' => __( 'Connessione Sicura SSL', 'fp-forms' ) ],
                        'quick-reply' => [ 'icon' => '💬', 'text' => __( 'Risposta Entro 24h', 'fp-forms' ) ],
                        'free-quote' => [ 'icon' => '💰', 'text' => __( 'Preventivo Gratuito', 'fp-forms' ) ],
                        'trusted' => [ 'icon' => '⭐', 'text' => __( '1000+ Clienti Soddisfatti', 'fp-forms' ) ],
                        'support' => [ 'icon' => '🎯', 'text' => __( 'Supporto Dedicato', 'fp-forms' ) ],
                        'privacy-first' => [ 'icon' => '👤', 'text' => __( 'Privacy Garantita', 'fp-forms' ) ],
                    ];
                    
                    $selected_badges = isset( $form_settings['trust_badges'] ) && is_array( $form_settings['trust_badges'] ) 
                        ? $form_settings['trust_badges'] 
                        : [];
                    
                    foreach ( $available_badges as $badge_key => $badge_data ) :
                        $checked = in_array( $badge_key, $selected_badges );
                    ?>
                        <label style="display: block; margin-bottom: 8px; padding: 8px; background: #f9fafb; border-radius: 4px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                            <input type="checkbox" name="trust_badges[]" value="<?php echo esc_attr( $badge_key ); ?>" <?php checked( $checked, true ); ?>>
                            <span style="font-size: 16px; margin: 0 6px;"><?php echo $badge_data['icon']; ?></span>
                            <span style="font-weight: 500;"><?php echo esc_html( $badge_data['text'] ); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <h4><?php esc_html_e( 'Pulsante Submit', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Testo Pulsante', 'fp-forms' ); ?></label>
                    <input type="text" name="submit_button_text" value="<?php echo esc_attr( $form_settings['submit_button_text'] ); ?>" placeholder="<?php esc_attr_e( 'Invia', 'fp-forms' ); ?>">
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Pulsante', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="submit_button_color" value="<?php echo esc_attr( $form_settings['submit_button_color'] ?? '#3b82f6' ); ?>" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                        <input type="text" name="submit_button_color_text" value="<?php echo esc_attr( $form_settings['submit_button_color'] ?? '#3b82f6' ); ?>" placeholder="#3b82f6" style="width: 100px;" readonly>
                        <button type="button" class="button button-small" onclick="this.previousElementSibling.value = this.previousElementSibling.previousElementSibling.value = '#3b82f6'">Reset</button>
                    </div>
                    <small><?php esc_html_e( 'Colore di sfondo del pulsante', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Dimensione Pulsante', 'fp-forms' ); ?></label>
                    <select name="submit_button_size">
                        <option value="small" <?php selected( $form_settings['submit_button_size'] ?? 'medium', 'small' ); ?>><?php esc_html_e( 'Piccolo', 'fp-forms' ); ?></option>
                        <option value="medium" <?php selected( $form_settings['submit_button_size'] ?? 'medium', 'medium' ); ?>><?php esc_html_e( 'Medio (default)', 'fp-forms' ); ?></option>
                        <option value="large" <?php selected( $form_settings['submit_button_size'] ?? 'medium', 'large' ); ?>><?php esc_html_e( 'Grande', 'fp-forms' ); ?></option>
                    </select>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Stile Pulsante', 'fp-forms' ); ?></label>
                    <select name="submit_button_style">
                        <option value="solid" <?php selected( $form_settings['submit_button_style'] ?? 'solid', 'solid' ); ?>><?php esc_html_e( 'Pieno (Solid)', 'fp-forms' ); ?></option>
                        <option value="outline" <?php selected( $form_settings['submit_button_style'] ?? 'solid', 'outline' ); ?>><?php esc_html_e( 'Bordato (Outline)', 'fp-forms' ); ?></option>
                        <option value="ghost" <?php selected( $form_settings['submit_button_style'] ?? 'solid', 'ghost' ); ?>><?php esc_html_e( 'Trasparente (Ghost)', 'fp-forms' ); ?></option>
                    </select>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Allineamento Pulsante', 'fp-forms' ); ?></label>
                    <select name="submit_button_align">
                        <option value="left" <?php selected( $form_settings['submit_button_align'] ?? 'center', 'left' ); ?>><?php esc_html_e( 'Sinistra', 'fp-forms' ); ?></option>
                        <option value="center" <?php selected( $form_settings['submit_button_align'] ?? 'center', 'center' ); ?>><?php esc_html_e( 'Centro (default)', 'fp-forms' ); ?></option>
                        <option value="right" <?php selected( $form_settings['submit_button_align'] ?? 'center', 'right' ); ?>><?php esc_html_e( 'Destra', 'fp-forms' ); ?></option>
                    </select>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Larghezza Pulsante', 'fp-forms' ); ?></label>
                    <select name="submit_button_width">
                        <option value="auto" <?php selected( $form_settings['submit_button_width'] ?? 'auto', 'auto' ); ?>><?php esc_html_e( 'Automatica (default)', 'fp-forms' ); ?></option>
                        <option value="full" <?php selected( $form_settings['submit_button_width'] ?? 'auto', 'full' ); ?>><?php esc_html_e( 'Larghezza Piena (100%)', 'fp-forms' ); ?></option>
                    </select>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Icona Pulsante (opzionale)', 'fp-forms' ); ?></label>
                    <select name="submit_button_icon">
                        <option value="" <?php selected( $form_settings['submit_button_icon'] ?? '', '' ); ?>><?php esc_html_e( 'Nessuna icona', 'fp-forms' ); ?></option>
                        <option value="paper-plane" <?php selected( $form_settings['submit_button_icon'] ?? '', 'paper-plane' ); ?>>✈️ <?php esc_html_e( 'Paper Plane', 'fp-forms' ); ?></option>
                        <option value="send" <?php selected( $form_settings['submit_button_icon'] ?? '', 'send' ); ?>>📤 <?php esc_html_e( 'Invia', 'fp-forms' ); ?></option>
                        <option value="check" <?php selected( $form_settings['submit_button_icon'] ?? '', 'check' ); ?>>✓ <?php esc_html_e( 'Spunta', 'fp-forms' ); ?></option>
                        <option value="arrow-right" <?php selected( $form_settings['submit_button_icon'] ?? '', 'arrow-right' ); ?>>→ <?php esc_html_e( 'Freccia Destra', 'fp-forms' ); ?></option>
                        <option value="save" <?php selected( $form_settings['submit_button_icon'] ?? '', 'save' ); ?>>💾 <?php esc_html_e( 'Salva', 'fp-forms' ); ?></option>
                    </select>
                    <small><?php esc_html_e( 'Icona mostrata accanto al testo', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Classe CSS Custom', 'fp-forms' ); ?></label>
                    <input type="text" name="custom_css_class" value="<?php echo esc_attr( $form_settings['custom_css_class'] ?? '' ); ?>" placeholder="my-custom-class">
                    <small><?php esc_html_e( 'Aggiungi una classe CSS personalizzata al form', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( '🎨 Colori Personalizzati', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Bordo Campi', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="custom_border_color" value="<?php echo esc_attr( $form_settings['custom_border_color'] ?? '#d1d5db' ); ?>" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onchange="this.nextElementSibling.value = this.value">
                        <input type="text" value="<?php echo esc_attr( $form_settings['custom_border_color'] ?? '#d1d5db' ); ?>" placeholder="#d1d5db" style="width: 100px;" readonly>
                        <button type="button" class="button button-small" onclick="var inputs = this.parentElement.querySelectorAll('input'); inputs[0].value = inputs[1].value = '#d1d5db';">Reset</button>
                    </div>
                    <small><?php esc_html_e( 'Colore del bordo dei campi input, textarea e select', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Focus', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="custom_focus_color" value="<?php echo esc_attr( $form_settings['custom_focus_color'] ?? '#2563eb' ); ?>" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onchange="this.nextElementSibling.value = this.value">
                        <input type="text" value="<?php echo esc_attr( $form_settings['custom_focus_color'] ?? '#2563eb' ); ?>" placeholder="#2563eb" style="width: 100px;" readonly>
                        <button type="button" class="button button-small" onclick="var inputs = this.parentElement.querySelectorAll('input'); inputs[0].value = inputs[1].value = '#2563eb';">Reset</button>
                    </div>
                    <small><?php esc_html_e( 'Colore del bordo e anello quando un campo è in focus', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Testo', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="custom_text_color" value="<?php echo esc_attr( $form_settings['custom_text_color'] ?? '#1f2937' ); ?>" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onchange="this.nextElementSibling.value = this.value">
                        <input type="text" value="<?php echo esc_attr( $form_settings['custom_text_color'] ?? '#1f2937' ); ?>" placeholder="#1f2937" style="width: 100px;" readonly>
                        <button type="button" class="button button-small" onclick="var inputs = this.parentElement.querySelectorAll('input'); inputs[0].value = inputs[1].value = '#1f2937';">Reset</button>
                    </div>
                    <small><?php esc_html_e( 'Colore del testo dei campi e delle label', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Sfondo Form', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="custom_background_color" value="<?php echo esc_attr( $form_settings['custom_background_color'] ?? '#ffffff' ); ?>" style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onchange="this.nextElementSibling.value = this.value">
                        <input type="text" value="<?php echo esc_attr( $form_settings['custom_background_color'] ?? '#ffffff' ); ?>" placeholder="#ffffff" style="width: 100px;" readonly>
                        <button type="button" class="button button-small" onclick="var inputs = this.parentElement.querySelectorAll('input'); inputs[0].value = inputs[1].value = '#ffffff';">Reset</button>
                    </div>
                    <small><?php esc_html_e( 'Colore di sfondo del contenitore del form', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="success_redirect_enabled" value="1" <?php checked( $form_settings['success_redirect_enabled'] ?? false, true ); ?>>
                        <?php esc_html_e( 'Redirect dopo invio', 'fp-forms' ); ?>
                    </label>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'URL Redirect', 'fp-forms' ); ?></label>
                    <input type="url" name="success_redirect_url" value="<?php echo esc_url( $form_settings['success_redirect_url'] ?? '' ); ?>" placeholder="https://example.com/thank-you">
                </div>
                
                <h4><?php esc_html_e( 'Impostazioni Avanzate', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="enable_multistep" value="1" <?php checked( $form_settings['enable_multistep'] ?? false, true ); ?>>
                        <?php esc_html_e( 'Abilita form multi-step', 'fp-forms' ); ?>
                    </label>
                    <small><?php esc_html_e( 'Dividi il form in più step. Usa il campo "Nuovo Step" per separare le sezioni.', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="enable_progressive_save" value="1" <?php checked( $form_settings['enable_progressive_save'] ?? false, true ); ?>>
                        <?php esc_html_e( 'Abilita salvataggio progressivo (auto-save)', 'fp-forms' ); ?>
                    </label>
                    <small><?php esc_html_e( 'Salva automaticamente i dati del form nel browser dell\'utente per evitare perdite', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Messaggio di Conferma', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio di Successo', 'fp-forms' ); ?></label>
                    <textarea name="success_message" rows="4" placeholder="<?php esc_attr_e( 'Grazie! Il tuo messaggio è stato inviato con successo.', 'fp-forms' ); ?>"><?php echo esc_textarea( $form_settings['success_message'] ); ?></textarea>
                    <small><?php esc_html_e( 'Mostrato dopo invio form. Tag disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Tipo Messaggio', 'fp-forms' ); ?></label>
                    <select name="success_message_type">
                        <option value="success" <?php selected( $form_settings['success_message_type'] ?? 'success', 'success' ); ?>><?php esc_html_e( '✓ Successo (verde)', 'fp-forms' ); ?></option>
                        <option value="info" <?php selected( $form_settings['success_message_type'] ?? 'success', 'info' ); ?>><?php esc_html_e( 'ℹ️ Info (blu)', 'fp-forms' ); ?></option>
                        <option value="celebration" <?php selected( $form_settings['success_message_type'] ?? 'success', 'celebration' ); ?>><?php esc_html_e( '🎉 Celebration (festoso)', 'fp-forms' ); ?></option>
                    </select>
                    <small><?php esc_html_e( 'Stile visivo del messaggio di conferma', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Durata Visualizzazione', 'fp-forms' ); ?></label>
                    <select name="success_message_duration">
                        <option value="0" <?php selected( $form_settings['success_message_duration'] ?? '0', '0' ); ?>><?php esc_html_e( 'Sempre visibile', 'fp-forms' ); ?></option>
                        <option value="3000" <?php selected( $form_settings['success_message_duration'] ?? '0', '3000' ); ?>><?php esc_html_e( '3 secondi', 'fp-forms' ); ?></option>
                        <option value="5000" <?php selected( $form_settings['success_message_duration'] ?? '0', '5000' ); ?>><?php esc_html_e( '5 secondi', 'fp-forms' ); ?></option>
                        <option value="10000" <?php selected( $form_settings['success_message_duration'] ?? '0', '10000' ); ?>><?php esc_html_e( '10 secondi', 'fp-forms' ); ?></option>
                    </select>
                    <small><?php esc_html_e( 'Dopo quanto tempo nascondere automaticamente il messaggio', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Notifiche Email', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field" style="background: #fff3cd; padding: 12px; border-left: 4px solid #ffc107; margin-bottom: 15px;">
                    <label style="font-weight: 600; color: #856404;">
                        <input type="checkbox" name="disable_wordpress_emails" value="1" <?php checked( $form_settings['disable_wordpress_emails'] ?? false, true ); ?>>
                        <?php esc_html_e( '🚫 Disabilita TUTTE le email WordPress', 'fp-forms' ); ?>
                    </label>
                    <small style="display: block; margin-top: 8px; color: #856404;">
                        <?php esc_html_e( '⚠️ Se abilitato, NON verranno inviate email (webmaster, cliente, staff). Usa solo se hai configurato Brevo o altro sistema CRM esterno.', 'fp-forms' ); ?>
                    </small>
                    <small style="display: block; margin-top: 4px; color: #856404;">
                        <?php esc_html_e( '... I dati verranno comunque salvati e gli eventi Brevo/Meta continueranno a funzionare.', 'fp-forms' ); ?>
                    </small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Email Destinatario', 'fp-forms' ); ?></label>
                    <input type="text" name="notification_email" value="<?php echo esc_attr( $form_settings['notification_email'] ); ?>" placeholder="admin@example.com, altro@example.com">
                    <small><?php esc_html_e( 'Puoi inserire più indirizzi separati da virgola.', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Oggetto Email Webmaster', 'fp-forms' ); ?></label>
                    <input type="text" name="notification_subject" value="<?php echo esc_attr( $form_settings['notification_subject'] ); ?>" placeholder="Nuova submission da {form_title}">
                    <small><?php esc_html_e( 'Tag disponibili: {form_title}, {site_name}, {date}, {time}', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio Email Webmaster (opzionale)', 'fp-forms' ); ?></label>
                    <textarea name="notification_message" rows="8" placeholder="Lascia vuoto per usare il template automatico con tutti i campi del form..."><?php echo esc_textarea( $form_settings['notification_message'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Template personalizzato per il webmaster. Lascia vuoto per template automatico. Tag disponibili: {nome}, {email}, {form_title}, etc.', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Email di Conferma (Cliente)', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="confirmation_enabled" value="1" <?php checked( $form_settings['confirmation_enabled'], true ); ?>>
                        <?php esc_html_e( 'Invia email di conferma all\'utente', 'fp-forms' ); ?>
                    </label>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Template Email HTML', 'fp-forms' ); ?></label>
                    <?php
                    $available_templates = \FPForms\Email\Templates::get_available_templates();
                    $current_template = $form_settings['confirmation_template'] ?? '';
                    ?>
                    <div class="fp-email-template-selector">
                        <div class="fp-template-cards">
                            <label class="fp-template-card">
                                <input type="radio" name="confirmation_template" value="" <?php checked( $current_template, '' ); ?> class="fp-template-radio">
                                <span class="fp-template-card__icon">&#9993;</span>
                                <span class="fp-template-card__name"><?php esc_html_e( 'Plain Text', 'fp-forms' ); ?></span>
                                <span class="fp-template-card__desc"><?php esc_html_e( 'Classico', 'fp-forms' ); ?></span>
                            </label>
                            <?php
                            $template_icons = [
                                'elegant'      => '&#127968;',
                                'professional' => '&#9879;',
                                'modern'       => '&#9733;',
                                'minimal'      => '&#9634;',
                            ];
                            $template_desc = [
                                'elegant'      => __( 'Hospitality', 'fp-forms' ),
                                'professional' => __( 'Medical/Corp', 'fp-forms' ),
                                'modern'       => __( 'Retail', 'fp-forms' ),
                                'minimal'      => __( 'Universale', 'fp-forms' ),
                            ];
                            foreach ( $available_templates as $slug => $label ) :
                            ?>
                            <label class="fp-template-card">
                                <input type="radio" name="confirmation_template" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $current_template, $slug ); ?> class="fp-template-radio">
                                <span class="fp-template-card__icon"><?php echo $template_icons[ $slug ]; ?></span>
                                <span class="fp-template-card__name"><?php echo esc_html( explode( ' (', $label )[0] ); ?></span>
                                <span class="fp-template-card__desc"><?php echo esc_html( $template_desc[ $slug ] ); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <small><?php esc_html_e( 'Seleziona un template HTML per email di conferma visivamente ricche, oppure Plain Text per il formato classico.', 'fp-forms' ); ?></small>
                    </div>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Accent Email (opzionale)', 'fp-forms' ); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="confirmation_accent_color" value="<?php echo esc_attr( $form_settings['confirmation_accent_color'] ?? '' ); ?>" style="width: 50px; height: 34px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                        <button type="button" class="button button-small" onclick="this.previousElementSibling.value = '';"><?php esc_html_e( 'Usa default', 'fp-forms' ); ?></button>
                    </div>
                    <small><?php esc_html_e( 'Lascia vuoto per usare il colore globale configurato nelle impostazioni plugin.', 'fp-forms' ); ?></small>
                </div>

                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Info Footer Email (opzionale)', 'fp-forms' ); ?></label>
                    <textarea name="confirmation_footer_info" rows="3" placeholder="<?php esc_attr_e( 'Come raggiungerci: ...\nOrari: ...', 'fp-forms' ); ?>"><?php echo esc_textarea( $form_settings['confirmation_footer_info'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Informazioni aggiuntive nel footer email, specifiche per questo form.', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Oggetto Email Conferma', 'fp-forms' ); ?></label>
                    <input type="text" name="confirmation_subject" value="<?php echo esc_attr( $form_settings['confirmation_subject'] ); ?>">
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio Email Conferma', 'fp-forms' ); ?></label>
                    <textarea name="confirmation_message" rows="5" placeholder="<?php esc_attr_e( 'Lascia vuoto per usare il template automatico con saluto personalizzato, riepilogo dati e firma.', 'fp-forms' ); ?>"><?php echo esc_textarea( $form_settings['confirmation_message'] ); ?></textarea>
                    <small><?php esc_html_e( 'Sovrascrive il contenuto del template HTML. Lascia vuoto per il template automatico. Tag disponibili: {form_title}, {site_name}, {date}', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Notifiche Staff', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="staff_notifications_enabled" value="1" <?php checked( $form_settings['staff_notifications_enabled'] ?? false, true ); ?>>
                        <?php esc_html_e( 'Invia notifica a membri dello staff/team', 'fp-forms' ); ?>
                    </label>
                    <small><?php esc_html_e( 'Oltre alla notifica al webmaster, invia email separate ai membri del team', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Email Staff (una per riga)', 'fp-forms' ); ?></label>
                    <textarea name="staff_emails" rows="4" placeholder="mario.rossi@example.com&#10;giulia.bianchi@example.com&#10;support@example.com"><?php echo esc_textarea( $form_settings['staff_emails'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Inserisci un indirizzo email per riga. Supporta anche virgola o punto e virgola come separatore.', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Oggetto Email Staff (opzionale)', 'fp-forms' ); ?></label>
                    <input type="text" name="staff_notification_subject" value="<?php echo esc_attr( $form_settings['staff_notification_subject'] ?? '' ); ?>" placeholder="[STAFF] Nuova submission: {form_title}">
                    <small><?php esc_html_e( 'Lascia vuoto per usare il template di default. Tag disponibili: {form_title}, {site_name}, {date}', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio Email Staff (opzionale)', 'fp-forms' ); ?></label>
                    <textarea name="staff_notification_message" rows="5" placeholder="Lascia vuoto per usare il template standard con tutti i campi..."><?php echo esc_textarea( $form_settings['staff_notification_message'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Template personalizzato per lo staff. Lascia vuoto per usare il template standard. Tag disponibili: {nome_campo}, {form_title}, etc.', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Integrazione Brevo', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="brevo_enabled" value="1" <?php checked( $form_settings['brevo_enabled'] ?? true, true ); ?>>
                        <?php esc_html_e( 'Sincronizza con Brevo CRM', 'fp-forms' ); ?>
                    </label>
                    <small><?php esc_html_e( 'Invia contatti ed eventi a Brevo ad ogni submission (se Brevo è configurato globalmente)', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Lista Brevo (ID)', 'fp-forms' ); ?></label>
                    <input type="number" name="brevo_list_id" value="<?php echo esc_attr( $form_settings['brevo_list_id'] ?? '' ); ?>" placeholder="2" class="small-text">
                    <small><?php esc_html_e( 'Lascia vuoto per usare la lista default configurata nelle impostazioni globali', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Nome Evento Brevo (opzionale)', 'fp-forms' ); ?></label>
                    <input type="text" name="brevo_event_name" value="<?php echo esc_attr( $form_settings['brevo_event_name'] ?? '' ); ?>" placeholder="form_submission">
                    <small><?php esc_html_e( 'Lascia vuoto per usare "form_submission" come default. Esempi: newsletter_signup, contact_request, demo_request', 'fp-forms' ); ?></small>
                </div>
                
                <?php
                // Conditional Logic Builder
                include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/conditional-logic-builder.php';
                
                // Webhooks Settings
                include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/webhooks-settings.php';
                
                // Form Versioning (solo se form esistente)
                if ( ! $is_new && $form_id > 0 ) {
                    include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/form-versioning.php';
                }
                ?>
            </div>
        </div>

        <div class="fp-builder-actions">
            <button type="submit" class="button button-primary button-large">
                <?php esc_html_e( 'Salva Form', 'fp-forms' ); ?>
            </button>
        </div>
    </form>
</div>

<!-- Template campo -->
<script type="text/template" id="fp-field-template">
    <div class="fp-field-item" data-field-index="{{index}}">
        <div class="fp-field-header">
            <span class="fp-field-drag dashicons dashicons-move"></span>
            <span class="fp-field-type-label">{{typeLabel}}</span>
            <span class="fp-field-label-preview">{{label}}</span>
            <div class="fp-field-actions">
                <button type="button" class="fp-field-edit" title="<?php esc_html_e( 'Modifica', 'fp-forms' ); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button type="button" class="fp-field-delete" title="<?php esc_html_e( 'Elimina', 'fp-forms' ); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
        <div class="fp-field-body" style="display:none;">
            <div class="fp-field-row">
                <label><?php esc_html_e( 'Etichetta Campo', 'fp-forms' ); ?></label>
                <input type="text" class="fp-field-input-label" value="{{label}}" required>
            </div>
            <div class="fp-field-row">
                <label><?php esc_html_e( 'Nome Campo', 'fp-forms' ); ?></label>
                <input type="text" class="fp-field-input-name" value="{{name}}" required>
            </div>
            <div class="fp-field-row">
                <label><?php esc_html_e( 'Placeholder', 'fp-forms' ); ?></label>
                <input type="text" class="fp-field-input-placeholder" value="{{placeholder}}">
            </div>
            <div class="fp-field-row">
                <label><?php esc_html_e( 'Descrizione', 'fp-forms' ); ?></label>
                <input type="text" class="fp-field-input-description" value="{{description}}">
            </div>
            <div class="fp-field-row fp-field-choices" style="display:none;">
                <label><?php esc_html_e( 'Opzioni (una per riga)', 'fp-forms' ); ?></label>
                <textarea class="fp-field-input-choices" rows="5">{{choices}}</textarea>
            </div>
            <div class="fp-field-row">
                <label>
                    <input type="checkbox" class="fp-field-input-required" {{required}}>
                    <?php esc_html_e( 'Campo obbligatorio', 'fp-forms' ); ?>
                </label>
            </div>
        </div>
        <input type="hidden" class="fp-field-type" value="{{type}}">
    </div>
</script>
