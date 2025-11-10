<?php
/**
 * Plugin Name: FP Forms
 * Plugin URI: https://francescopasseri.com/
 * Description: Form builder professionale per landing page e prenotazioni - Simile a WPForms
 * Version: 1.3.1
 * Author: Francesco Passeri
 * Author URI: https://francescopasseri.com/
 * Text Domain: fp-forms
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Impedisce accesso diretto
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definizione costanti del plugin
define( 'FP_FORMS_VERSION', '1.3.1' );
define( 'FP_FORMS_PLUGIN_FILE', __FILE__ );
define( 'FP_FORMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FP_FORMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FP_FORMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Carica autoloader Composer (con guardia per evitare fatal in ambienti non preparati)
$fp_forms_autoload = FP_FORMS_PLUGIN_DIR . 'vendor/autoload.php';

if ( is_readable( $fp_forms_autoload ) ) {
    require_once $fp_forms_autoload;
} else {
    // Fallback PSR-4 autoloader per ambienti senza Composer (es. installazione da GitHub)
    spl_autoload_register(
        static function ( $class ) {
            $prefix = 'FPForms\\';

            if ( strpos( $class, $prefix ) !== 0 ) {
                return;
            }

            $relative = substr( $class, strlen( $prefix ) );
            $relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );

            $file = FP_FORMS_PLUGIN_DIR . 'src/' . $relative . '.php';

            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }
    );

    add_action(
        'admin_notices',
        static function () {
            if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }

            printf(
                '<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
                esc_html__( 'FP Forms:', 'fp-forms' ),
                esc_html__( 'Autoloader fallback attivo: esegui "composer install" per abilitare il caricamento ottimizzato.', 'fp-forms' )
            );
        }
    );
}

/**
 * Inizializza il plugin
 */
function fp_forms_init() {
    load_plugin_textdomain(
        'fp-forms',
        false,
        dirname(FP_FORMS_PLUGIN_BASENAME) . '/languages'
    );

    if ( function_exists( 'get_role' ) ) {
        \FPForms\Core\Capabilities::ensure_default_capabilities();
    }

    \FPForms\Plugin::instance();
}
add_action( 'init', 'fp_forms_init', 0 );

if ( function_exists( 'add_filter' ) ) {
    \FPForms\Core\Capabilities::register_capability_bridge();
}

/**
 * Hook di attivazione
 */
function fp_forms_activate() {
    require_once FP_FORMS_PLUGIN_DIR . 'includes/Activator.php';
    \FPForms\Activator::activate();
}
register_activation_hook( __FILE__, 'fp_forms_activate' );

/**
 * Hook di disattivazione
 */
function fp_forms_deactivate() {
    require_once FP_FORMS_PLUGIN_DIR . 'includes/Deactivator.php';
    \FPForms\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'fp_forms_deactivate' );

