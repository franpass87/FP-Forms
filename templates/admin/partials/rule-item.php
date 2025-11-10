<?php
/**
 * Template: Rule Item (per conditional logic esistenti)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="fp-rule-item" data-rule-index="<?php echo $index; ?>">
    <div class="fp-rule-header">
        <span class="fp-rule-number"><?php printf( __( 'Regola #%d', 'fp-forms' ), $index + 1 ); ?></span>
        <button type="button" class="fp-rule-delete" title="<?php _e( 'Elimina Regola', 'fp-forms' ); ?>">
            <span class="dashicons dashicons-trash"></span>
        </button>
    </div>
    
    <div class="fp-rule-body">
        <div class="fp-rule-row">
            <label><?php _e( 'Quando il campo', 'fp-forms' ); ?></label>
            <select class="fp-rule-trigger-field" required>
                <option value=""><?php _e( '-- Seleziona Campo --', 'fp-forms' ); ?></option>
                <?php foreach ( $form_fields ?? [] as $field ) : ?>
                <option value="<?php echo esc_attr( $field['name'] ); ?>" <?php selected( $rule['trigger_field'] ?? '', $field['name'] ); ?>>
                    <?php echo esc_html( $field['label'] ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="fp-rule-row">
            <select class="fp-rule-condition" required>
                <option value="equals" <?php selected( $rule['condition'] ?? '', 'equals' ); ?>><?php _e( 'è uguale a', 'fp-forms' ); ?></option>
                <option value="not_equals" <?php selected( $rule['condition'] ?? '', 'not_equals' ); ?>><?php _e( 'è diverso da', 'fp-forms' ); ?></option>
                <option value="contains" <?php selected( $rule['condition'] ?? '', 'contains' ); ?>><?php _e( 'contiene', 'fp-forms' ); ?></option>
                <option value="not_contains" <?php selected( $rule['condition'] ?? '', 'not_contains' ); ?>><?php _e( 'non contiene', 'fp-forms' ); ?></option>
                <option value="greater_than" <?php selected( $rule['condition'] ?? '', 'greater_than' ); ?>><?php _e( 'è maggiore di', 'fp-forms' ); ?></option>
                <option value="less_than" <?php selected( $rule['condition'] ?? '', 'less_than' ); ?>><?php _e( 'è minore di', 'fp-forms' ); ?></option>
                <option value="is_empty" <?php selected( $rule['condition'] ?? '', 'is_empty' ); ?>><?php _e( 'è vuoto', 'fp-forms' ); ?></option>
                <option value="is_not_empty" <?php selected( $rule['condition'] ?? '', 'is_not_empty' ); ?>><?php _e( 'non è vuoto', 'fp-forms' ); ?></option>
            </select>
            
            <input type="text" class="fp-rule-value" value="<?php echo esc_attr( $rule['value'] ?? '' ); ?>" placeholder="<?php _e( 'valore', 'fp-forms' ); ?>">
        </div>
        
        <div class="fp-rule-row">
            <label><?php _e( 'Allora', 'fp-forms' ); ?></label>
            <select class="fp-rule-action" required>
                <option value="show" <?php selected( $rule['action'] ?? '', 'show' ); ?>><?php _e( 'Mostra', 'fp-forms' ); ?></option>
                <option value="hide" <?php selected( $rule['action'] ?? '', 'hide' ); ?>><?php _e( 'Nascondi', 'fp-forms' ); ?></option>
                <option value="require" <?php selected( $rule['action'] ?? '', 'require' ); ?>><?php _e( 'Rendi obbligatorio', 'fp-forms' ); ?></option>
                <option value="unrequire" <?php selected( $rule['action'] ?? '', 'unrequire' ); ?>><?php _e( 'Rendi facoltativo', 'fp-forms' ); ?></option>
            </select>
        </div>
        
        <div class="fp-rule-row">
            <label><?php _e( 'I seguenti campi', 'fp-forms' ); ?></label>
            <select class="fp-rule-targets" multiple size="5" required>
                <?php foreach ( $form_fields ?? [] as $field ) : ?>
                <?php 
                $selected = isset( $rule['target_fields'] ) && in_array( $field['name'], $rule['target_fields'] );
                ?>
                <option value="<?php echo esc_attr( $field['name'] ); ?>" <?php selected( $selected, true ); ?>>
                    <?php echo esc_html( $field['label'] ); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small><?php _e( 'Tieni premuto Ctrl/Cmd per selezionare multipli', 'fp-forms' ); ?></small>
        </div>
    </div>
    
    <input type="hidden" class="fp-rule-id" value="<?php echo esc_attr( $rule['id'] ?? 'rule_' . uniqid() ); ?>">
</div>

