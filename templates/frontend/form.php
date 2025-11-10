<?php
/**
 * Template: Form Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$submit_text = isset( $form['settings']['submit_button_text'] ) && ! empty( $form['settings']['submit_button_text'] )
    ? $form['settings']['submit_button_text']
    : __( 'Invia', 'fp-forms' );

// Submit button settings with validation
$btn_color = $form['settings']['submit_button_color'] ?? '#3b82f6';

// BUGFIX #11: Validate HEX color (prevent XSS via CSS injection)
if ( ! preg_match( '/^#[0-9A-Fa-f]{6}$/', $btn_color ) ) {
    $btn_color = '#3b82f6'; // fallback to default
}

// BUGFIX #12: Whitelist size
$btn_size = $form['settings']['submit_button_size'] ?? 'medium';
$allowed_sizes = [ 'small', 'medium', 'large' ];
if ( ! in_array( $btn_size, $allowed_sizes, true ) ) {
    $btn_size = 'medium';
}

// BUGFIX #13: Whitelist style
$btn_style = $form['settings']['submit_button_style'] ?? 'solid';
$allowed_styles = [ 'solid', 'outline', 'ghost' ];
if ( ! in_array( $btn_style, $allowed_styles, true ) ) {
    $btn_style = 'solid';
}

// BUGFIX #14: Whitelist align
$btn_align = $form['settings']['submit_button_align'] ?? 'center';
$allowed_aligns = [ 'left', 'center', 'right' ];
if ( ! in_array( $btn_align, $allowed_aligns, true ) ) {
    $btn_align = 'center';
}

// BUGFIX #15: Whitelist width
$btn_width = $form['settings']['submit_button_width'] ?? 'auto';
$allowed_widths = [ 'auto', 'full' ];
if ( ! in_array( $btn_width, $allowed_widths, true ) ) {
    $btn_width = 'auto';
}

// BUGFIX #16: Whitelist icon
$btn_icon = $form['settings']['submit_button_icon'] ?? '';
$allowed_icons = [ '', 'paper-plane', 'send', 'check', 'arrow-right', 'save' ];
if ( ! in_array( $btn_icon, $allowed_icons, true ) ) {
    $btn_icon = '';
}

// Icon mapping
$icon_map = [
    'paper-plane' => '✈️',
    'send' => '📤',
    'check' => '✓',
    'arrow-right' => '→',
    'save' => '💾',
];
$icon_html = isset( $icon_map[ $btn_icon ] ) ? '<span class="fp-btn-icon">' . $icon_map[ $btn_icon ] . '</span>' : '';

// Trust badges
$trust_badges = isset( $form['settings']['trust_badges'] ) && is_array( $form['settings']['trust_badges'] ) 
    ? $form['settings']['trust_badges'] 
    : [];

$badges_config = [
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

// Check se ci sono campi file
$has_file_field = false;
foreach ( $form['fields'] as $field ) {
    if ( $field['type'] === 'file' ) {
        $has_file_field = true;
        break;
    }
}

// Custom colors
$c_border = $form['settings']['custom_border_color'] ?? '';
$c_focus = $form['settings']['custom_focus_color'] ?? '';
$c_text = $form['settings']['custom_text_color'] ?? '';
$c_bg = $form['settings']['custom_background_color'] ?? '';

// Validate HEX colors (security)
$hex = '/^#[0-9A-Fa-f]{6}$/';
if ( $c_border && ! preg_match( $hex, $c_border ) ) $c_border = '';
if ( $c_focus && ! preg_match( $hex, $c_focus ) ) $c_focus = '';
if ( $c_text && ! preg_match( $hex, $c_text ) ) $c_text = '';
if ( $c_bg && ! preg_match( $hex, $c_bg ) ) $c_bg = '';

$has_custom_colors = $c_border || $c_focus || $c_text || $c_bg;
?>

<?php if ( $has_custom_colors ) : ?>
<style id="fp-forms-colors-<?php echo esc_attr( $form['id'] ); ?>">
#fp-forms-container-<?php echo esc_attr( $form['id'] ); ?> {
    <?php if ( $c_border ) : ?>--fp-forms-border: <?php echo esc_attr( $c_border ); ?>;<?php endif; ?>
    <?php if ( $c_focus ) :
        $r = hexdec( substr( $c_focus, 1, 2 ) );
        $g = hexdec( substr( $c_focus, 3, 2 ) );
        $b = hexdec( substr( $c_focus, 5, 2 ) );
    ?>--fp-forms-primary: <?php echo esc_attr( $c_focus ); ?>;
    --fp-forms-focus-ring: rgba(<?php echo $r; ?>, <?php echo $g; ?>, <?php echo $b; ?>, 0.5);<?php endif; ?>
    <?php if ( $c_text ) : ?>--fp-forms-text: <?php echo esc_attr( $c_text ); ?>;<?php endif; ?>
    <?php if ( $c_bg ) : ?>--fp-forms-surface: <?php echo esc_attr( $c_bg ); ?>;<?php endif; ?>
}
</style>
<?php endif; ?>
<div class="fp-forms-container" 
     id="fp-forms-container-<?php echo esc_attr( $form['id'] ); ?>"
     data-form-title="<?php echo esc_attr( $form['title'] ); ?>">
    <form class="fp-forms-form" 
          id="fp-form-<?php echo esc_attr( $form['id'] ); ?>" 
          method="POST"
          action=""
          data-form-id="<?php echo esc_attr( $form['id'] ); ?>"
          <?php echo $has_file_field ? 'enctype="multipart/form-data"' : ''; ?>>
        
        <?php if ( ! empty( $form['description'] ) ) : ?>
            <div class="fp-forms-description">
                <?php echo wp_kses_post( $form['description'] ); ?>
            </div>
        <?php endif; ?>
        
        <?php if ( ! empty( $trust_badges ) ) : ?>
            <div class="fp-forms-trust-badges">
                <?php foreach ( $trust_badges as $badge_key ) : ?>
                    <?php if ( isset( $badges_config[ $badge_key ] ) ) : ?>
                        <div class="fp-trust-badge">
                            <span class="fp-badge-icon"><?php echo $badges_config[ $badge_key ]['icon']; ?></span>
                            <span class="fp-badge-text"><?php echo esc_html( $badges_config[ $badge_key ]['text'] ); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="fp-forms-fields">
            <?php 
            $frontend = \FPForms\Plugin::instance()->frontend;
            foreach ( $form['fields'] as $field ) {
                echo $frontend->get_field_html( $field, $form['id'] );
            }
            ?>
        </div>
        
        <div class="fp-forms-submit" style="text-align: <?php echo esc_attr( $btn_align ); ?>;">
            <button type="submit" 
                    class="fp-forms-submit-btn fp-btn-<?php echo esc_attr( $btn_size ); ?> fp-btn-<?php echo esc_attr( $btn_style ); ?> fp-btn-<?php echo esc_attr( $btn_width ); ?>"
                    data-color="<?php echo esc_attr( $btn_color ); ?>"
                    style="<?php echo $btn_style === 'solid' ? 'background-color: ' . esc_attr( $btn_color ) . '; border-color: ' . esc_attr( $btn_color ) . ';' : 'color: ' . esc_attr( $btn_color ) . '; border-color: ' . esc_attr( $btn_color ) . ';'; ?>">
                <?php echo esc_html( $submit_text ); ?>
                <?php echo $icon_html; ?>
            </button>
        </div>
        
        <div class="fp-forms-messages">
            <div class="fp-forms-message fp-forms-success" style="display:none;" role="status" aria-live="polite"></div>
            <div class="fp-forms-message fp-forms-error" style="display:none;" role="alert" aria-live="assertive"></div>
        </div>
        
        <?php
        // Honeypot anti-spam
        $anti_spam = new \FPForms\Security\AntiSpam();
        echo $anti_spam->get_honeypot_field( $form['id'] );
        ?>
        
        <input type="hidden" name="action" value="fp_forms_submit">
        <input type="hidden" name="form_id" value="<?php echo esc_attr( $form['id'] ); ?>">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'fp_forms_submit' ); ?>">
    </form>
</div>
