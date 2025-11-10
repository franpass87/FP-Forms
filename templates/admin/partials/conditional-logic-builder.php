<?php
/**
 * Template: Conditional Logic Builder UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$rules = isset( $form_settings['conditional_rules'] ) ? $form_settings['conditional_rules'] : [];
?>

<div class="fp-conditional-logic-section">
    <h3>
        <span class="dashicons dashicons-randomize"></span>
        <?php _e( 'Logica Condizionale', 'fp-forms' ); ?>
    </h3>
    
    <p class="fp-section-description">
        <?php _e( 'Mostra o nascondi campi in base alle risposte dell\'utente', 'fp-forms' ); ?>
    </p>
    
    <div id="fp-conditional-rules-container">
        <?php if ( ! empty( $rules ) ) : ?>
            <?php foreach ( $rules as $index => $rule ) : ?>
                <?php include __DIR__ . '/rule-item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <button type="button" class="button fp-add-rule-btn" id="fp-add-conditional-rule">
        <span class="dashicons dashicons-plus-alt"></span>
        <?php _e( 'Aggiungi Regola', 'fp-forms' ); ?>
    </button>
</div>

<!-- Template JavaScript per nuova regola -->
<script type="text/template" id="fp-rule-template">
    <div class="fp-rule-item" data-rule-index="{{index}}">
        <div class="fp-rule-header">
            <span class="fp-rule-number"><?php _e( 'Regola', 'fp-forms' ); ?> #{{ruleNumber}}</span>
            <button type="button" class="fp-rule-delete" title="<?php _e( 'Elimina Regola', 'fp-forms' ); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        
        <div class="fp-rule-body">
            <div class="fp-rule-row">
                <label><?php _e( 'Quando il campo', 'fp-forms' ); ?></label>
                <select class="fp-rule-trigger-field" required>
                    <option value=""><?php _e( '-- Seleziona Campo --', 'fp-forms' ); ?></option>
                    <!-- Popolato dinamicamente -->
                </select>
            </div>
            
            <div class="fp-rule-row">
                <select class="fp-rule-condition" required>
                    <option value="equals"><?php _e( 'è uguale a', 'fp-forms' ); ?></option>
                    <option value="not_equals"><?php _e( 'è diverso da', 'fp-forms' ); ?></option>
                    <option value="contains"><?php _e( 'contiene', 'fp-forms' ); ?></option>
                    <option value="not_contains"><?php _e( 'non contiene', 'fp-forms' ); ?></option>
                    <option value="greater_than"><?php _e( 'è maggiore di', 'fp-forms' ); ?></option>
                    <option value="less_than"><?php _e( 'è minore di', 'fp-forms' ); ?></option>
                    <option value="is_empty"><?php _e( 'è vuoto', 'fp-forms' ); ?></option>
                    <option value="is_not_empty"><?php _e( 'non è vuoto', 'fp-forms' ); ?></option>
                </select>
                
                <input type="text" class="fp-rule-value" placeholder="<?php _e( 'valore', 'fp-forms' ); ?>">
            </div>
            
            <div class="fp-rule-row">
                <label><?php _e( 'Allora', 'fp-forms' ); ?></label>
                <select class="fp-rule-action" required>
                    <option value="show"><?php _e( 'Mostra', 'fp-forms' ); ?></option>
                    <option value="hide"><?php _e( 'Nascondi', 'fp-forms' ); ?></option>
                    <option value="require"><?php _e( 'Rendi obbligatorio', 'fp-forms' ); ?></option>
                    <option value="unrequire"><?php _e( 'Rendi facoltativo', 'fp-forms' ); ?></option>
                </select>
            </div>
            
            <div class="fp-rule-row">
                <label><?php _e( 'I seguenti campi', 'fp-forms' ); ?></label>
                <select class="fp-rule-targets" multiple size="5" required>
                    <option value=""><?php _e( '-- Seleziona Campi --', 'fp-forms' ); ?></option>
                    <!-- Popolato dinamicamente -->
                </select>
                <small><?php _e( 'Tieni premuto Ctrl/Cmd per selezionare multipli', 'fp-forms' ); ?></small>
            </div>
        </div>
        
        <input type="hidden" class="fp-rule-id" value="{{ruleId}}">
    </div>
</script>

<style>
.fp-conditional-logic-section {
    margin-top: var(--fp-spacing-xl);
    padding: var(--fp-spacing-lg);
    background: #f9fafb;
    border-radius: var(--fp-radius-md);
    border: 1px solid var(--fp-color-border);
}

.fp-conditional-logic-section h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 var(--fp-spacing-sm);
    font-size: 16px;
}

.fp-section-description {
    color: var(--fp-color-muted);
    font-size: 13px;
    margin: 0 0 var(--fp-spacing-lg);
}

#fp-conditional-rules-container {
    margin-bottom: var(--fp-spacing-md);
}

.fp-rule-item {
    background: #fff;
    border: 2px solid var(--fp-color-border);
    border-radius: var(--fp-radius-md);
    margin-bottom: var(--fp-spacing-md);
}

.fp-rule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--fp-spacing-sm) var(--fp-spacing-md);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: var(--fp-radius-md) var(--fp-radius-md) 0 0;
}

.fp-rule-number {
    font-weight: 600;
    font-size: 14px;
}

.fp-rule-delete {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 4px 8px;
    border-radius: var(--fp-radius-sm);
    cursor: pointer;
    transition: all 0.2s;
}

.fp-rule-delete:hover {
    background: rgba(255,255,255,0.3);
}

.fp-rule-body {
    padding: var(--fp-spacing-md);
}

.fp-rule-row {
    margin-bottom: var(--fp-spacing-md);
}

.fp-rule-row:last-child {
    margin-bottom: 0;
}

.fp-rule-row label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: var(--fp-color-text);
    font-size: 13px;
}

.fp-rule-row select,
.fp-rule-row input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: var(--fp-radius-md);
    font-size: 14px;
}

.fp-rule-row select[multiple] {
    min-height: 120px;
}

.fp-rule-row small {
    display: block;
    margin-top: 4px;
    color: var(--fp-color-muted);
    font-size: 12px;
}

.fp-add-rule-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.fp-add-rule-btn .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}
</style>

