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
    'notification_subject' => __( 'Nuovo invio dal form: {form_title}', 'fp-forms' ),
    'notification_message' => '',
    'confirmation_enabled' => false,
    'confirmation_template' => '',
    'confirmation_accent_color' => '',
    'confirmation_footer_info' => '',
    'confirmation_subject' => __( 'Abbiamo ricevuto il tuo messaggio - {site_name}', 'fp-forms' ),
    'confirmation_message' => '',
    'staff_notifications_enabled' => false,
    'staff_emails' => '',
    'staff_notification_subject' => __( '[STAFF] Nuova richiesta dal form: {form_title}', 'fp-forms' ),
    'staff_notification_message' => '',
    'brevo_enabled' => false,
    'brevo_list_id' => '',
    'brevo_event_name' => '',
];

$form_settings = wp_parse_args( $form_settings, $default_settings );

$fpforms_confirmation_accent = isset( $form_settings['confirmation_accent_color'] ) ? (string) $form_settings['confirmation_accent_color'] : '';
$fpforms_confirmation_accent_custom = ( $fpforms_confirmation_accent !== '' && preg_match( '/^#[0-9A-Fa-f]{6}$/', $fpforms_confirmation_accent ) );
$fpforms_confirmation_accent_preview = $fpforms_confirmation_accent_custom ? $fpforms_confirmation_accent : '#667eea';
?>

<div class="wrap fp-forms-admin fp-forms-builder">
    <?php
    $fp_forms_builder_heading = $is_new ? __( 'Nuovo Form', 'fp-forms' ) : __( 'Modifica Form', 'fp-forms' );
    ?>
    <h1 class="screen-reader-text"><?php echo esc_html( $fp_forms_builder_heading ); ?></h1>
    <div class="fp-forms-admin__header fpforms-page-header">
        <div class="fpforms-page-header-content">
            <h2 class="fp-forms-page-header-title" aria-hidden="true">
                <span class="dashicons dashicons-feedback" aria-hidden="true"></span>
                <?php echo esc_html( $fp_forms_builder_heading ); ?>
            </h2>
            <p class="fpforms-page-header-desc"><?php esc_html_e( 'In alto titolo e descrizione. Sotto: a sinistra componi e riordina i campi (passo 1), a destra aggiungi i tipi di campo (passo 2). Più in basso: aspetto sul sito e impostazioni avanzate.', 'fp-forms' ); ?></p>
        </div>
        <div class="fpforms-page-header-actions">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fp-forms' ) ); ?>" class="button fpforms-btn fpforms-btn-header-ghost">&larr; <?php esc_html_e( 'Torna ai Form', 'fp-forms' ); ?></a>
            <span class="fpforms-page-header-badge">v<?php echo esc_html( defined( 'FP_FORMS_VERSION' ) ? FP_FORMS_VERSION : '0' ); ?></span>
        </div>
    </div>
    
    <form id="fp-form-builder" class="fp-builder-container" method="post" action="" onsubmit="return false;">
        <div class="fp-builder-form-meta fpforms-builder-section">
            <input type="hidden" name="form_id" id="form_id" value="<?php echo esc_attr( $form_id ); ?>">
            <div class="fpforms-builder-section__head">
                <span class="fpforms-builder-section__icon dashicons dashicons-edit-page" aria-hidden="true"></span>
                <div>
                    <h3 class="fpforms-builder-section__title"><?php esc_html_e( 'Titolo e descrizione', 'fp-forms' ); ?></h3>
                    <p class="fpforms-builder-section__subtitle"><?php esc_html_e( 'Nome del form e testo di supporto (opzionale) visibili in questa schermata.', 'fp-forms' ); ?></p>
                </div>
            </div>
            <div class="fpforms-builder-section__body">
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
            </div>
        </div>

        <div class="fp-builder-workspace">
            <div class="fp-builder-fields-hub">
                <div class="fp-builder-body">
                <div class="fp-builder-canvas-head" role="group" aria-label="<?php esc_attr_e( 'Area campi del form', 'fp-forms' ); ?>">
                    <span class="fp-builder-step-badge" aria-hidden="true">1</span>
                    <div class="fp-builder-canvas-head__text">
                        <h3 class="fp-builder-canvas-title"><?php esc_html_e( 'Campi del form', 'fp-forms' ); ?></h3>
                        <p class="fp-builder-canvas-desc"><?php esc_html_e( 'Trascina l’icona ⋮⋮ per riordinare. Clicca la matita per modificare etichetta, nome tecnico e opzioni.', 'fp-forms' ); ?></p>
                    </div>
                </div>

                <div
                    id="fp-builder-empty-state"
                    class="fp-builder-empty-state"
                    role="status"
                    aria-live="polite"
                    <?php echo empty( $form_fields ) ? '' : ' hidden aria-hidden="true"'; ?>
                >
                    <span class="fp-builder-empty-state__icon dashicons dashicons-welcome-widgets-menus" aria-hidden="true"></span>
                    <p class="fp-builder-empty-state__title"><?php esc_html_e( 'Nessun campo ancora', 'fp-forms' ); ?></p>
                    <p class="fp-builder-empty-state__text"><?php esc_html_e( 'A destra, in «Aggiungi campi», scegli un tipo: ogni clic inserisce un campo qui.', 'fp-forms' ); ?></p>
                </div>

                <div class="fp-fields-container" id="fp-fields-container">
                    <?php if ( ! empty( $form_fields ) ) : ?>
                        <?php foreach ( $form_fields as $index => $field ) : ?>
                            <?php include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/field-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                </div>
            </div>

            <div class="fp-builder-palette-column" id="fp-builder-field-palette">
            <div class="fp-sidebar-section fpforms-builder-panel fpforms-builder-panel--palette-sidebar">
                <div class="fpforms-builder-panel__head">
                    <span class="fp-builder-step-badge fp-builder-step-badge--sidebar" aria-hidden="true">2</span>
                    <div>
                        <h3 class="fpforms-builder-panel__title"><?php esc_html_e( 'Aggiungi campi', 'fp-forms' ); ?></h3>
                        <p class="fpforms-builder-panel__hint"><?php esc_html_e( 'Clicca un tipo: il campo viene aggiunto nell’elenco a sinistra. Puoi riordinarlo trascinando.', 'fp-forms' ); ?></p>
                    </div>
                </div>
                <div class="fp-field-types" id="fp-field-types-palette">
                <button type="button" class="fp-field-type" data-type="text" data-tooltip="<?php esc_attr_e( 'Campo di testo su una riga', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Campo di testo su una riga', 'fp-forms' ); ?>">
                    <span class="dashicons dashicons-text"></span>
                    <?php esc_html_e( 'Testo', 'fp-forms' ); ?>
                </button>
                <button type="button" class="fp-field-type" data-type="fullname" data-tooltip="<?php esc_attr_e( 'Nome e cognome su due colonne', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Nome e cognome su due colonne', 'fp-forms' ); ?>">
                    <span class="dashicons dashicons-admin-users"></span>
                    <?php esc_html_e( 'Nome e cognome', 'fp-forms' ); ?>
                </button>
                <button type="button" class="fp-field-type" data-type="email" data-tooltip="<?php esc_attr_e( 'Indirizzo email con validazione', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Indirizzo email con validazione', 'fp-forms' ); ?>">
                    <span class="dashicons dashicons-email"></span>
                    <?php esc_html_e( 'Email', 'fp-forms' ); ?>
                </button>
                    <button type="button" class="fp-field-type" data-type="phone" data-tooltip="<?php esc_attr_e( 'Numero di telefono', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Numero di telefono', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-phone"></span>
                        <?php esc_html_e( 'Telefono', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="number" data-tooltip="<?php esc_attr_e( 'Solo valori numerici', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Solo valori numerici', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-calculator"></span>
                        <?php esc_html_e( 'Numero', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="date" data-tooltip="<?php esc_attr_e( 'Selezione data', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Selezione data', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-calendar"></span>
                        <?php esc_html_e( 'Data', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="textarea" data-tooltip="<?php esc_attr_e( 'Testo su più righe', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Testo su più righe', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-text-page"></span>
                        <?php esc_html_e( 'Area di Testo', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="select" data-tooltip="<?php esc_attr_e( 'Menu a tendina', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Menu a tendina', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-menu-alt"></span>
                        <?php esc_html_e( 'Select', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="radio" data-tooltip="<?php esc_attr_e( 'Scelta singola tra opzioni', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Scelta singola tra opzioni', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-marker"></span>
                        <?php esc_html_e( 'Radio Button', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="checkbox" data-tooltip="<?php esc_attr_e( 'Scelta multipla o singola', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Scelta multipla o singola', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-yes"></span>
                        <?php esc_html_e( 'Checkbox', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="privacy-checkbox" data-tooltip="<?php esc_attr_e( 'Consenso GDPR con link alla privacy', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Consenso GDPR con link alla privacy', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-privacy"></span>
                        <?php esc_html_e( 'Privacy Policy', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="marketing-checkbox" data-tooltip="<?php esc_attr_e( 'Consenso marketing facoltativo', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Consenso marketing facoltativo', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-email-alt"></span>
                        <?php esc_html_e( 'Marketing', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="calculated" data-tooltip="<?php esc_attr_e( 'Valore calcolato da una formula', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Valore calcolato da una formula', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-calculator"></span>
                        <?php esc_html_e( 'Calcolato', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="recaptcha" data-tooltip="<?php esc_attr_e( 'Protezione anti-spam Google reCAPTCHA', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Protezione anti-spam Google reCAPTCHA', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-shield"></span>
                        <?php esc_html_e( 'reCAPTCHA', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type" data-type="file" data-tooltip="<?php esc_attr_e( 'Caricamento file da parte dell’utente', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Caricamento file da parte dell’utente', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e( 'Upload File', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="fp-field-type fp-field-type-step" data-type="step_break" data-tooltip="<?php esc_attr_e( 'Separa il form in più step (con multi-step attivo)', 'fp-forms' ); ?>" title="<?php esc_attr_e( 'Separa il form in più step (con multi-step attivo)', 'fp-forms' ); ?>">
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <?php esc_html_e( 'Nuovo Step', 'fp-forms' ); ?>
                    </button>
                </div>
            </div>
        </div>
        </div>

        <div class="fp-builder-appearance-settings fpforms-builder-section">
            <div class="fpforms-builder-section__head">
                <span class="fpforms-builder-section__icon dashicons dashicons-admin-appearance" aria-hidden="true"></span>
                <div>
                    <h3 class="fpforms-builder-section__title"><?php esc_html_e( 'Aspetto sul sito', 'fp-forms' ); ?></h3>
                    <p class="fpforms-builder-section__subtitle"><?php esc_html_e( 'Pulsante di invio, colori, badge di fiducia e redirect.', 'fp-forms' ); ?></p>
                </div>
            </div>
            <div class="fpforms-builder-section__body">
            <div class="fp-sidebar-section fpforms-builder-panel fpforms-builder-panel--settings">

                <h4><?php esc_html_e( 'Badge euristici', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field fp-heuristic-badges">
                    <label><?php esc_html_e( 'Mostra Badge (opzionale)', 'fp-forms' ); ?></label>
                    <small class="fp-heuristic-description"><?php esc_html_e( 'Seleziona i badge da mostrare in fondo al form. Ogni badge sfrutta un bias cognitivo (euristica) per aumentare fiducia e conversioni. Consiglio: 2-3 badge massimo.', 'fp-forms' ); ?></small>
                    
                    <?php
                    // Badge raggruppati per euristica/bias cognitivo
                    $badge_groups = [
                        'prova-sociale' => [
                            'label' => __( 'Prova sociale', 'fp-forms' ),
                            'tooltip' => __( 'Gli utenti si fidano di ciò che scelgono gli altri', 'fp-forms' ),
                            'badges' => [
                                'trusted' => [ 'icon' => '⭐', 'text' => __( '1000+ Clienti Soddisfatti', 'fp-forms' ) ],
                                'rated-49' => [ 'icon' => '⭐', 'text' => __( '4,9/5 da recensioni verificate', 'fp-forms' ) ],
                                'daily-handled' => [ 'icon' => '📈', 'text' => __( 'Richieste gestite ogni giorno', 'fp-forms' ) ],
                            ],
                        ],
                        'autorita' => [
                            'label' => __( 'Autorità', 'fp-forms' ),
                            'tooltip' => __( 'Riferimenti a norme e certificazioni riconosciute', 'fp-forms' ),
                            'badges' => [
                                'gdpr-compliant' => [ 'icon' => '✓', 'text' => __( 'GDPR Compliant', 'fp-forms' ) ],
                                'ssl-secure' => [ 'icon' => '🔐', 'text' => __( 'Connessione Sicura SSL', 'fp-forms' ) ],
                                'secure-payments' => [ 'icon' => '💳', 'text' => __( 'Pagamenti sicuri', 'fp-forms' ) ],
                            ],
                        ],
                        'reciprocita' => [
                            'label' => __( 'Reciprocità', 'fp-forms' ),
                            'tooltip' => __( 'Offrire qualcosa di valore incoraggia a ricambiare', 'fp-forms' ),
                            'badges' => [
                                'free-quote' => [ 'icon' => '💰', 'text' => __( 'Preventivo Gratuito', 'fp-forms' ) ],
                                'gift-guide' => [ 'icon' => '📘', 'text' => __( 'Guida omaggio in PDF', 'fp-forms' ) ],
                            ],
                        ],
                        'urgenza' => [
                            'label' => __( 'Urgenza / Immediato', 'fp-forms' ),
                            'tooltip' => __( 'Tempi rapidi riducono l\'attrito e l\'abbandono', 'fp-forms' ),
                            'badges' => [
                                'instant-response' => [ 'icon' => '⚡', 'text' => __( 'Risposta Immediata', 'fp-forms' ) ],
                                'quick-reply' => [ 'icon' => '💬', 'text' => __( 'Risposta Entro 24h', 'fp-forms' ) ],
                                'slots-today' => [ 'icon' => '📅', 'text' => __( 'Disponibilità limitata oggi', 'fp-forms' ) ],
                            ],
                        ],
                        'riduzione-rischio' => [
                            'label' => __( 'Riduzione del rischio', 'fp-forms' ),
                            'tooltip' => __( 'Rassicurare su privacy e sicurezza elimina paure', 'fp-forms' ),
                            'badges' => [
                                'data-secure' => [ 'icon' => '🔒', 'text' => __( 'I Tuoi Dati Sono Al Sicuro', 'fp-forms' ) ],
                                'no-spam' => [ 'icon' => '🚫', 'text' => __( 'No Spam, Mai', 'fp-forms' ) ],
                                'privacy-first' => [ 'icon' => '👤', 'text' => __( 'Privacy Garantita', 'fp-forms' ) ],
                                'satisfaction-guarantee' => [ 'icon' => '↩️', 'text' => __( 'Garanzia soddisfatti o rimborsati', 'fp-forms' ) ],
                                'easy-unsubscribe' => [ 'icon' => '✉️', 'text' => __( 'Disiscrizione in un clic', 'fp-forms' ) ],
                            ],
                        ],
                        'impegno' => [
                            'label' => __( 'Impegno / Supporto', 'fp-forms' ),
                            'tooltip' => __( 'Assistenza dedicata aumenta percezione di valore', 'fp-forms' ),
                            'badges' => [
                                'support' => [ 'icon' => '🎯', 'text' => __( 'Supporto Dedicato', 'fp-forms' ) ],
                                'real-human-reply' => [ 'icon' => '👋', 'text' => __( 'Risposta da persone reali, non bot', 'fp-forms' ) ],
                            ],
                        ],
                        'attrito-ridotto' => [
                            'label' => __( 'Attrito ridotto', 'fp-forms' ),
                            'tooltip' => __( 'Meno passaggi = più completamenti (euristica della semplicità)', 'fp-forms' ),
                            'badges' => [
                                'quick-form' => [ 'icon' => '⏱️', 'text' => __( 'Compila in meno di 1 minuto', 'fp-forms' ) ],
                                'no-credit-card' => [ 'icon' => '🆓', 'text' => __( 'Nessuna carta richiesta', 'fp-forms' ) ],
                            ],
                        ],
                        'trasparenza' => [
                            'label' => __( 'Trasparenza', 'fp-forms' ),
                            'tooltip' => __( 'Chiarezza su prezzi e processo aumenta la fiducia', 'fp-forms' ),
                            'badges' => [
                                'honest-pricing' => [ 'icon' => '🏷️', 'text' => __( 'Prezzi chiari, zero sorprese', 'fp-forms' ) ],
                                'clear-steps' => [ 'icon' => '📋', 'text' => __( 'Processo trasparente in 3 passi', 'fp-forms' ) ],
                            ],
                        ],
                    ];
                    
                    $selected_badges = isset( $form_settings['trust_badges'] ) && is_array( $form_settings['trust_badges'] )
                        ? $form_settings['trust_badges']
                        : [];
                    ?>
                    <div class="fp-heuristic-groups-grid">
                    <?php
                    foreach ( $badge_groups as $group_key => $group ) :
                        $tooltip = isset( $group['tooltip'] ) ? $group['tooltip'] : '';
                    ?>
                        <div class="fp-heuristic-group">
                            <span class="fp-heuristic-group-label"<?php echo $tooltip ? ' title="' . esc_attr( $tooltip ) . '"' : ''; ?>><?php echo esc_html( $group['label'] ); ?></span>
                            <?php foreach ( $group['badges'] as $badge_key => $badge_data ) :
                                $checked = in_array( $badge_key, $selected_badges );
                            ?>
                                <label class="fp-heuristic-badge-item">
                                    <input type="checkbox" name="trust_badges[]" value="<?php echo esc_attr( $badge_key ); ?>" <?php checked( $checked, true ); ?>>
                                    <span class="fp-badge-icon"><?php echo $badge_data['icon']; ?></span>
                                    <span class="fp-badge-text"><?php echo esc_html( $badge_data['text'] ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <h4><?php esc_html_e( 'Pulsante Submit', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Testo Pulsante', 'fp-forms' ); ?></label>
                    <input type="text" name="submit_button_text" value="<?php echo esc_attr( $form_settings['submit_button_text'] ); ?>" placeholder="<?php esc_attr_e( 'Invia', 'fp-forms' ); ?>">
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Pulsante', 'fp-forms' ); ?></label>
                    <div class="fpforms-color-row">
                        <input type="color" class="fpforms-color-row__input" name="submit_button_color" value="<?php echo esc_attr( $form_settings['submit_button_color'] ?? '#3b82f6' ); ?>">
                        <input type="text" class="fpforms-color-row__hex" name="submit_button_color_text" value="<?php echo esc_attr( $form_settings['submit_button_color'] ?? '#3b82f6' ); ?>" placeholder="#3b82f6" readonly>
                        <button type="button" class="button button-small fpforms-color-row__reset" data-reset-color="#3b82f6"><?php esc_html_e( 'Reimposta', 'fp-forms' ); ?></button>
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
                
                <h4><?php esc_html_e( 'Colori personalizzati', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Bordo Campi', 'fp-forms' ); ?></label>
                    <div class="fpforms-color-row">
                        <input type="color" class="fpforms-color-row__input" name="custom_border_color" value="<?php echo esc_attr( $form_settings['custom_border_color'] ?? '#d1d5db' ); ?>">
                        <input type="text" class="fpforms-color-row__hex" value="<?php echo esc_attr( $form_settings['custom_border_color'] ?? '#d1d5db' ); ?>" placeholder="#d1d5db" readonly>
                        <button type="button" class="button button-small fpforms-color-row__reset" data-reset-color="#d1d5db"><?php esc_html_e( 'Reimposta', 'fp-forms' ); ?></button>
                    </div>
                    <small><?php esc_html_e( 'Colore del bordo dei campi input, textarea e select', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Focus', 'fp-forms' ); ?></label>
                    <div class="fpforms-color-row">
                        <input type="color" class="fpforms-color-row__input" name="custom_focus_color" value="<?php echo esc_attr( $form_settings['custom_focus_color'] ?? '#2563eb' ); ?>">
                        <input type="text" class="fpforms-color-row__hex" value="<?php echo esc_attr( $form_settings['custom_focus_color'] ?? '#2563eb' ); ?>" placeholder="#2563eb" readonly>
                        <button type="button" class="button button-small fpforms-color-row__reset" data-reset-color="#2563eb"><?php esc_html_e( 'Reimposta', 'fp-forms' ); ?></button>
                    </div>
                    <small><?php esc_html_e( 'Colore del bordo e anello quando un campo è in focus', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Testo', 'fp-forms' ); ?></label>
                    <div class="fpforms-color-row">
                        <input type="color" class="fpforms-color-row__input" name="custom_text_color" value="<?php echo esc_attr( $form_settings['custom_text_color'] ?? '#1f2937' ); ?>">
                        <input type="text" class="fpforms-color-row__hex" value="<?php echo esc_attr( $form_settings['custom_text_color'] ?? '#1f2937' ); ?>" placeholder="#1f2937" readonly>
                        <button type="button" class="button button-small fpforms-color-row__reset" data-reset-color="#1f2937"><?php esc_html_e( 'Reimposta', 'fp-forms' ); ?></button>
                    </div>
                    <small><?php esc_html_e( 'Colore del testo dei campi e delle label', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Colore Sfondo Form', 'fp-forms' ); ?></label>
                    <div class="fpforms-color-row">
                        <input type="color" class="fpforms-color-row__input" name="custom_background_color" value="<?php echo esc_attr( $form_settings['custom_background_color'] ?? '#ffffff' ); ?>">
                        <input type="text" class="fpforms-color-row__hex" value="<?php echo esc_attr( $form_settings['custom_background_color'] ?? '#ffffff' ); ?>" placeholder="#ffffff" readonly>
                        <button type="button" class="button button-small fpforms-color-row__reset" data-reset-color="#ffffff"><?php esc_html_e( 'Reimposta', 'fp-forms' ); ?></button>
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

            </div>
            </div>
        </div>

        <div class="fp-builder-bottom-settings fpforms-builder-section">
            <div class="fpforms-builder-section__head">
                <span class="fpforms-builder-section__icon dashicons dashicons-admin-settings" aria-hidden="true"></span>
                <div>
                    <h3 class="fpforms-builder-section__title"><?php esc_html_e( 'Comportamento, email e integrazioni', 'fp-forms' ); ?></h3>
                    <p class="fpforms-builder-section__subtitle"><?php esc_html_e( 'Messaggio dopo l’invio, notifiche, Brevo, pagamenti e logica condizionale.', 'fp-forms' ); ?></p>
                </div>
            </div>
            <div class="fpforms-builder-section__body">
            <div class="fp-sidebar-section fpforms-builder-panel fpforms-builder-panel--advanced">

                <h4><?php esc_html_e( 'Comportamento', 'fp-forms' ); ?></h4>
                
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
                
                <div class="fp-setting-field fpforms-alert fpforms-alert--warning">
                    <label class="fpforms-alert__label">
                        <input type="checkbox" name="disable_wordpress_emails" value="1" <?php checked( $form_settings['disable_wordpress_emails'] ?? false, true ); ?>>
                        <?php esc_html_e( 'Disabilita tutte le email WordPress da questo form', 'fp-forms' ); ?>
                    </label>
                    <small class="fpforms-alert__text">
                        <?php esc_html_e( 'Se attivo, non partono email a webmaster, cliente né staff. Usalo solo se invii notifiche altrove (es. Brevo o CRM). Gli invii restano salvati e gli eventi di tracciamento configurati continuano a funzionare.', 'fp-forms' ); ?>
                    </small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Email Destinatario', 'fp-forms' ); ?></label>
                    <input type="text" name="notification_email" value="<?php echo esc_attr( $form_settings['notification_email'] ); ?>" placeholder="admin@example.com, altro@example.com">
                    <small><?php esc_html_e( 'Puoi inserire più indirizzi separati da virgola.', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Oggetto Email Webmaster', 'fp-forms' ); ?></label>
                    <input type="text" name="notification_subject" value="<?php echo esc_attr( $form_settings['notification_subject'] ); ?>" placeholder="<?php esc_attr_e( 'Nuovo invio dal form: {form_title}', 'fp-forms' ); ?>">
                    <small><?php esc_html_e( 'Esempio: "Nuovo contatto da {form_title}". Tag disponibili: {form_title}, {site_name}, {date}, {time}', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio Email Webmaster (opzionale)', 'fp-forms' ); ?></label>
                    <textarea name="notification_message" rows="8" placeholder="<?php esc_attr_e( "Lascia vuoto per un'email chiara con riepilogo dati e dettagli invio.", 'fp-forms' ); ?>"><?php echo esc_textarea( $form_settings['notification_message'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Scrivi un testo semplice e diretto per chi riceve l\'email. Lascia vuoto per il template automatico. Tag disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}.', 'fp-forms' ); ?></small>
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
                    <input type="hidden" id="fpforms-confirmation-accent-custom" value="<?php echo $fpforms_confirmation_accent_custom ? '1' : '0'; ?>">
                    <div class="fpforms-color-row fpforms-color-row--compact">
                        <input type="color" class="fpforms-color-row__input fpforms-color-row__input--sm" name="confirmation_accent_color" value="<?php echo esc_attr( $fpforms_confirmation_accent_preview ); ?>">
                        <button type="button" class="button button-small" id="fpforms-confirmation-accent-reset"><?php esc_html_e( 'Usa default globale', 'fp-forms' ); ?></button>
                    </div>
                    <small><?php esc_html_e( 'Lascia il default globale (impostazioni plugin) oppure scegli un colore dedicato per le email di conferma di questo form.', 'fp-forms' ); ?></small>
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
                    <input type="text" name="staff_notification_subject" value="<?php echo esc_attr( $form_settings['staff_notification_subject'] ?? '' ); ?>" placeholder="<?php esc_attr_e( '[STAFF] Nuova richiesta dal form: {form_title}', 'fp-forms' ); ?>">
                    <small><?php esc_html_e( 'Esempio: "[STAFF] Richiesta urgente da {form_title}". Tag disponibili: {form_title}, {site_name}, {date}, {time}', 'fp-forms' ); ?></small>
                </div>
                
                <div class="fp-setting-field">
                    <label><?php esc_html_e( 'Messaggio Email Staff (opzionale)', 'fp-forms' ); ?></label>
                    <textarea name="staff_notification_message" rows="5" placeholder="<?php esc_attr_e( 'Lascia vuoto per un template staff operativo con checklist e link admin.', 'fp-forms' ); ?>"><?php echo esc_textarea( $form_settings['staff_notification_message'] ?? '' ); ?></textarea>
                    <small><?php esc_html_e( 'Usa frasi brevi e orientate all\'azione (es. "Richiamare entro 2 ore"). Lascia vuoto per il template staff automatico. Tag disponibili: {nome}, {email}, {form_title}, {site_name}, {date}, {time}.', 'fp-forms' ); ?></small>
                </div>
                
                <h4><?php esc_html_e( 'Integrazione Brevo', 'fp-forms' ); ?></h4>
                
                <div class="fp-setting-field">
                    <label>
                        <input type="checkbox" name="brevo_enabled" value="1" <?php checked( $form_settings['brevo_enabled'] ?? false, true ); ?>>
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
                // Payment settings (Stripe)
                include FP_FORMS_PLUGIN_DIR . 'templates/admin/partials/payment-settings.php';
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
        </div>

        <div class="fp-builder-actions">
            <button type="submit" class="button button-primary button-large fpforms-btn fpforms-btn-primary">
                <span class="dashicons dashicons-saved" aria-hidden="true"></span>
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
