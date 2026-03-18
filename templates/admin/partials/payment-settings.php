<?php
/**
 * Template: Payment settings (Stripe) in form builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$payment_enabled   = ! empty( $form_settings['payment_enabled'] );
$payment_provider  = isset( $form_settings['payment_provider'] ) && $form_settings['payment_provider'] === 'stripe' ? 'stripe' : '';
$payment_amount    = isset( $form_settings['payment_amount'] ) ? (float) $form_settings['payment_amount'] : 0;
$payment_amount_fld = isset( $form_settings['payment_amount_field'] ) ? sanitize_key( $form_settings['payment_amount_field'] ) : '';
?>

<div class="fp-payment-section fp-conditional-logic-section">
    <h3>
        <span class="dashicons dashicons-money-alt"></span>
        <?php esc_html_e( 'Pagamento', 'fp-forms' ); ?>
    </h3>
    <p class="fp-section-description">
        <?php esc_html_e( 'Abilita il pagamento tramite Stripe Checkout. L\'utente verrà reindirizzato a Stripe per completare il pagamento.', 'fp-forms' ); ?>
    </p>
    <div class="fp-rule-row">
        <label>
            <input type="checkbox" name="payment_enabled" value="1" <?php checked( $payment_enabled ); ?>>
            <?php esc_html_e( 'Richiedi pagamento', 'fp-forms' ); ?>
        </label>
    </div>
    <?php if ( $payment_enabled ) : ?>
    <div class="fp-rule-row">
        <label for="payment_provider"><?php esc_html_e( 'Provider', 'fp-forms' ); ?></label>
        <select id="payment_provider" name="payment_provider">
            <option value=""><?php esc_html_e( '— Nessuno —', 'fp-forms' ); ?></option>
            <option value="stripe" <?php selected( $payment_provider, 'stripe' ); ?>>Stripe</option>
        </select>
    </div>
    <div class="fp-rule-row">
        <label for="payment_amount"><?php esc_html_e( 'Importo fisso (€)', 'fp-forms' ); ?></label>
        <input type="number" id="payment_amount" name="payment_amount" value="<?php echo esc_attr( $payment_amount ); ?>" min="0" step="0.01" class="small-text">
        <p class="description"><?php esc_html_e( 'Usato se non scegli un campo per l\'importo sotto.', 'fp-forms' ); ?></p>
    </div>
    <div class="fp-rule-row">
        <label for="payment_amount_field"><?php esc_html_e( 'Oppure campo per importo', 'fp-forms' ); ?></label>
        <select id="payment_amount_field" name="payment_amount_field">
            <option value=""><?php esc_html_e( '— Importo fisso —', 'fp-forms' ); ?></option>
            <!-- Popolato da JS con campi number/calculated -->
        </select>
    </div>
    <?php endif; ?>
</div>
