<?php
/**
 * Template: Webhook Item (per webhook esistenti)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="fp-webhook-item" data-webhook-index="<?php echo esc_attr( $index ); ?>">
    <div class="fp-webhook-header">
        <span class="fp-webhook-number"><?php _e( 'Webhook', 'fp-forms' ); ?> #<?php echo esc_html( $index + 1 ); ?></span>
        <button type="button" class="fp-webhook-delete" title="<?php _e( 'Elimina Webhook', 'fp-forms' ); ?>">
            <span class="dashicons dashicons-trash"></span>
        </button>
    </div>
    
    <div class="fp-webhook-body">
        <div class="fp-webhook-row">
            <label>
                <input type="checkbox" class="fp-webhook-enabled" <?php checked( $webhook['enabled'] ?? true, true ); ?>>
                <?php _e( 'Abilita questo webhook', 'fp-forms' ); ?>
            </label>
        </div>
        
        <div class="fp-webhook-row">
            <label><?php _e( 'URL Webhook', 'fp-forms' ); ?> <span class="required">*</span></label>
            <input type="url" class="fp-webhook-url" value="<?php echo esc_attr( $webhook['url'] ?? '' ); ?>" placeholder="https://hooks.zapier.com/hooks/catch/..." required>
            <small><?php _e( 'URL completo del webhook endpoint', 'fp-forms' ); ?></small>
        </div>
        
        <div class="fp-webhook-row">
            <label><?php _e( 'Secret Key (opzionale)', 'fp-forms' ); ?></label>
            <input type="text" class="fp-webhook-secret" value="<?php echo esc_attr( $webhook['secret'] ?? '' ); ?>" placeholder="<?php _e( 'Chiave segreta per firma HMAC', 'fp-forms' ); ?>">
            <small><?php _e( 'Usa una chiave segreta per verificare l\'autenticità del webhook. La signature sarà inviata nell\'header X-FP-Forms-Signature', 'fp-forms' ); ?></small>
        </div>
        
        <div class="fp-webhook-row">
            <button type="button" class="button button-secondary fp-test-webhook" data-webhook-url="<?php echo esc_attr( $webhook['url'] ?? '' ); ?>" data-webhook-secret="<?php echo esc_attr( $webhook['secret'] ?? '' ); ?>">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php _e( 'Test Webhook', 'fp-forms' ); ?>
            </button>
            <span class="fp-webhook-test-result"></span>
        </div>
    </div>
    
    <input type="hidden" class="fp-webhook-id" value="<?php echo esc_attr( $webhook['id'] ?? 'webhook_' . uniqid() ); ?>">
</div>

