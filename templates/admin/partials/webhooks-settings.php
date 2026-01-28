<?php
/**
 * Template: Webhooks Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$webhooks = isset( $form_settings['webhooks'] ) && is_array( $form_settings['webhooks'] ) 
    ? $form_settings['webhooks'] 
    : [];
?>

<div class="fp-webhooks-section">
    <h3>
        <span class="dashicons dashicons-networking"></span>
        <?php _e( 'Webhooks', 'fp-forms' ); ?>
    </h3>
    
    <p class="fp-section-description">
        <?php _e( 'Invia dati a servizi esterni (Zapier, Make, n8n, etc.) dopo ogni submission', 'fp-forms' ); ?>
    </p>
    
    <div id="fp-webhooks-container">
        <?php if ( ! empty( $webhooks ) ) : ?>
            <?php foreach ( $webhooks as $index => $webhook ) : ?>
                <?php include __DIR__ . '/webhook-item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <button type="button" class="button fp-add-webhook-btn" id="fp-add-webhook">
        <span class="dashicons dashicons-plus-alt"></span>
        <?php _e( 'Aggiungi Webhook', 'fp-forms' ); ?>
    </button>
</div>

<!-- Template JavaScript per nuovo webhook -->
<script type="text/template" id="fp-webhook-template">
    <div class="fp-webhook-item" data-webhook-index="{{index}}">
        <div class="fp-webhook-header">
            <span class="fp-webhook-number"><?php _e( 'Webhook', 'fp-forms' ); ?> #{{webhookNumber}}</span>
            <button type="button" class="fp-webhook-delete" title="<?php _e( 'Elimina Webhook', 'fp-forms' ); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        
        <div class="fp-webhook-body">
            <div class="fp-webhook-row">
                <label>
                    <input type="checkbox" class="fp-webhook-enabled" checked>
                    <?php _e( 'Abilita questo webhook', 'fp-forms' ); ?>
                </label>
            </div>
            
            <div class="fp-webhook-row">
                <label><?php _e( 'URL Webhook', 'fp-forms' ); ?> <span class="required">*</span></label>
                <input type="url" class="fp-webhook-url" placeholder="https://hooks.zapier.com/hooks/catch/..." required>
                <small><?php _e( 'URL completo del webhook endpoint', 'fp-forms' ); ?></small>
            </div>
            
            <div class="fp-webhook-row">
                <label><?php _e( 'Secret Key (opzionale)', 'fp-forms' ); ?></label>
                <input type="text" class="fp-webhook-secret" placeholder="<?php _e( 'Chiave segreta per firma HMAC', 'fp-forms' ); ?>">
                <small><?php _e( 'Usa una chiave segreta per verificare l\'autenticità del webhook. La signature sarà inviata nell\'header X-FP-Forms-Signature', 'fp-forms' ); ?></small>
            </div>
            
            <div class="fp-webhook-row">
                <button type="button" class="button button-secondary fp-test-webhook">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e( 'Test Webhook', 'fp-forms' ); ?>
                </button>
                <span class="fp-webhook-test-result"></span>
            </div>
        </div>
        
        <input type="hidden" class="fp-webhook-id" value="{{webhookId}}">
    </div>
</script>

<style>
.fp-webhooks-section {
    margin-top: var(--fp-spacing-xl);
    padding: var(--fp-spacing-lg);
    background: #f9fafb;
    border-radius: var(--fp-radius-md);
    border: 1px solid var(--fp-color-border);
}

.fp-webhook-item {
    background: #fff;
    border: 2px solid var(--fp-color-border);
    border-radius: var(--fp-radius-md);
    margin-bottom: var(--fp-spacing-md);
}

.fp-webhook-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--fp-spacing-sm) var(--fp-spacing-md);
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    border-radius: var(--fp-radius-md) var(--fp-radius-md) 0 0;
}

.fp-webhook-number {
    font-weight: 600;
    font-size: 14px;
}

.fp-webhook-delete {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 4px 8px;
    border-radius: var(--fp-radius-sm);
    cursor: pointer;
    transition: all 0.2s;
}

.fp-webhook-delete:hover {
    background: rgba(255,255,255,0.3);
}

.fp-webhook-body {
    padding: var(--fp-spacing-md);
}

.fp-webhook-row {
    margin-bottom: var(--fp-spacing-md);
}

.fp-webhook-row:last-child {
    margin-bottom: 0;
}

.fp-webhook-row label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: var(--fp-color-text);
    font-size: 13px;
}

.fp-webhook-row input[type="url"],
.fp-webhook-row input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: var(--fp-radius-md);
    font-size: 14px;
}

.fp-webhook-row small {
    display: block;
    margin-top: 4px;
    color: var(--fp-color-muted);
    font-size: 12px;
}

.fp-webhook-test-result {
    margin-left: 10px;
    font-size: 13px;
    font-weight: 500;
}

.fp-webhook-test-result.success {
    color: #059669;
}

.fp-webhook-test-result.error {
    color: #dc2626;
}

.fp-add-webhook-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
</style>

