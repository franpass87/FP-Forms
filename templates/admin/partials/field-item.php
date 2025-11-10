<?php
/**
 * Template: Field Item (usato nel loop del form builder)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$type_labels = [
    'text' => 'Testo',
    'email' => 'Email',
    'phone' => 'Telefono',
    'number' => 'Numero',
    'date' => 'Data',
    'textarea' => 'Area di Testo',
    'select' => 'Select',
    'radio' => 'Radio',
    'checkbox' => 'Checkbox',
    'privacy-checkbox' => 'Privacy Policy',
    'marketing-checkbox' => 'Marketing',
    'recaptcha' => 'reCAPTCHA',
    'file' => 'Upload File',
];

$type_label = isset( $type_labels[ $field['type'] ] ) ? $type_labels[ $field['type'] ] : $field['type'];
$choices = '';
if ( isset( $field['options']['choices'] ) && is_array( $field['options']['choices'] ) ) {
    $choices = implode( "\n", $field['options']['choices'] );
}
?>

<div class="fp-field-item" data-field-index="<?php echo $index; ?>">
    <div class="fp-field-header">
        <span class="fp-field-drag dashicons dashicons-move"></span>
        <span class="fp-field-type-label"><?php echo esc_html( $type_label ); ?></span>
        <span class="fp-field-label-preview"><?php echo esc_html( $field['label'] ); ?></span>
        <div class="fp-field-actions">
            <button type="button" class="fp-field-edit" title="<?php _e( 'Modifica', 'fp-forms' ); ?>">
                <span class="dashicons dashicons-edit"></span>
            </button>
            <button type="button" class="fp-field-delete" title="<?php _e( 'Elimina', 'fp-forms' ); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
    </div>
    <div class="fp-field-body" style="display:none;">
        <div class="fp-field-row">
            <label><?php _e( 'Etichetta Campo', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-label" value="<?php echo esc_attr( $field['label'] ); ?>" required>
        </div>
        <div class="fp-field-row">
            <label><?php _e( 'Nome Campo', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-name" value="<?php echo esc_attr( $field['name'] ); ?>" required>
        </div>
        <div class="fp-field-row">
            <label><?php _e( 'Placeholder', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-placeholder" value="<?php echo esc_attr( $field['options']['placeholder'] ?? '' ); ?>">
        </div>
        <div class="fp-field-row">
            <label><?php _e( 'Descrizione', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-description" value="<?php echo esc_attr( $field['options']['description'] ?? '' ); ?>">
            <small class="fp-field-help"><?php _e( 'Testo di aiuto mostrato sotto il campo', 'fp-forms' ); ?></small>
        </div>
        
        <?php if ( ! in_array( $field['type'], [ 'privacy-checkbox', 'marketing-checkbox', 'recaptcha' ] ) ) : ?>
        <div class="fp-field-row">
            <label><?php _e( 'Messaggio Errore Personalizzato (opzionale)', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-error-message" value="<?php echo esc_attr( $field['options']['error_message'] ?? '' ); ?>" placeholder="<?php _e( 'Questo campo è obbligatorio', 'fp-forms' ); ?>">
            <small class="fp-field-help"><?php _e( 'Messaggio mostrato se il campo non è valido. Lascia vuoto per messaggio predefinito.', 'fp-forms' ); ?></small>
        </div>
        <?php endif; ?>
        
        <?php if ( in_array( $field['type'], [ 'select', 'radio', 'checkbox' ] ) ) : ?>
        <div class="fp-field-row fp-field-choices">
            <label><?php _e( 'Opzioni (una per riga)', 'fp-forms' ); ?></label>
            <textarea class="fp-field-input-choices" rows="5"><?php echo esc_textarea( $choices ); ?></textarea>
        </div>
        <?php endif; ?>
        
        <?php if ( $field['type'] === 'privacy-checkbox' ) : ?>
        <div class="fp-field-row fp-field-privacy-options">
            <label><?php _e( 'Testo Privacy', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-privacy-text" value="<?php echo esc_attr( $field['options']['privacy_text'] ?? 'Ho letto e accetto la' ); ?>" placeholder="Ho letto e accetto la">
            <small class="fp-field-help"><?php _e( 'Il testo verrà seguito dal link automatico alla Privacy Policy', 'fp-forms' ); ?></small>
        </div>
        <?php endif; ?>
        
        <?php if ( $field['type'] === 'marketing-checkbox' ) : ?>
        <div class="fp-field-row fp-field-marketing-options">
            <label><?php _e( 'Testo Consenso Marketing', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-marketing-text" value="<?php echo esc_attr( $field['options']['marketing_text'] ?? 'Acconsento a ricevere comunicazioni marketing e newsletter' ); ?>" placeholder="Acconsento a ricevere comunicazioni marketing">
            <small class="fp-field-help"><?php _e( 'Testo per il consenso marketing/newsletter (opzionale)', 'fp-forms' ); ?></small>
        </div>
        <?php endif; ?>
        
        <?php if ( $field['type'] === 'file' ) : ?>
        <div class="fp-field-row fp-field-file-options">
            <label><?php _e( 'Dimensione Max (MB)', 'fp-forms' ); ?></label>
            <input type="number" class="fp-field-input-max-size" value="<?php echo esc_attr( $field['options']['max_size'] ?? 5 ); ?>" min="1" max="50">
        </div>
        <div class="fp-field-row fp-field-file-options">
            <label><?php _e( 'Tipi File Permessi (separati da virgola)', 'fp-forms' ); ?></label>
            <input type="text" class="fp-field-input-allowed-types" value="<?php echo esc_attr( implode( ',', $field['options']['allowed_types'] ?? [ 'pdf', 'jpg', 'png' ] ) ); ?>" placeholder="pdf,doc,jpg,png">
        </div>
        <div class="fp-field-row fp-field-file-options">
            <label>
                <input type="checkbox" class="fp-field-input-multiple" <?php checked( $field['options']['multiple'] ?? false, true ); ?>>
                <?php _e( 'Permetti upload multipli', 'fp-forms' ); ?>
            </label>
        </div>
        <?php endif; ?>
        <?php if ( $field['type'] === 'recaptcha' ) : ?>
        <div class="fp-field-row">
            <p class="fp-field-notice">
                <span class="dashicons dashicons-shield"></span>
                <?php printf(
                    __( 'Il campo reCAPTCHA usa le impostazioni configurate nella %s. Il widget viene renderizzato automaticamente.', 'fp-forms' ),
                    '<a href="' . admin_url( 'admin.php?page=fp-forms-settings#recaptcha' ) . '">' . __( 'pagina impostazioni', 'fp-forms' ) . '</a>'
                ); ?>
            </p>
        </div>
        <?php elseif ( $field['type'] === 'privacy-checkbox' ) : ?>
        <div class="fp-field-row">
            <p class="fp-field-notice">
                <span class="dashicons dashicons-info"></span>
                <?php _e( 'Il campo Privacy Policy è sempre obbligatorio per GDPR compliance', 'fp-forms' ); ?>
            </p>
        </div>
        <?php elseif ( $field['type'] === 'marketing-checkbox' ) : ?>
        <div class="fp-field-row">
            <label>
                <input type="checkbox" class="fp-field-input-required" <?php checked( $field['required'], true ); ?>>
                <?php _e( 'Campo obbligatorio', 'fp-forms' ); ?>
            </label>
            <small class="fp-field-help"><?php _e( 'Il consenso marketing è generalmente opzionale', 'fp-forms' ); ?></small>
        </div>
        <?php else : ?>
        <div class="fp-field-row">
            <label>
                <input type="checkbox" class="fp-field-input-required" <?php checked( $field['required'], true ); ?>>
                <?php _e( 'Campo obbligatorio', 'fp-forms' ); ?>
            </label>
        </div>
        <?php endif; ?>
    </div>
    <input type="hidden" class="fp-field-type" value="<?php echo esc_attr( $field['type'] ); ?>">
</div>
