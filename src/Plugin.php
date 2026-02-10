<?php
namespace FPForms;

/**
 * Classe principale del plugin FP Forms
 */
class Plugin {
    
    /**
     * Istanza singleton
     */
    private static $instance = null;
    
    /**
     * Admin instance
     */
    public $admin;
    
    /**
     * Frontend instance
     */
    public $frontend;
    
    /**
     * Database instance
     */
    public $database;
    
    /**
     * Forms manager
     */
    public $forms;
    
    /**
     * Submissions manager
     */
    public $submissions;
    
    /**
     * Email notifications
     */
    public $email;
    
    /**
     * Quick features
     */
    public $quick_features;
    
    /**
     * Templates library
     */
    public $templates;
    
    /**
     * Conditional logic
     */
    public $conditional_logic;
    
    /**
     * Analytics tracker
     */
    public $analytics;
    
    /**
     * GTM & GA4 Tracking
     */
    public $tracking;
    
    /**
     * Brevo Integration
     */
    public $brevo;
    
    /**
     * Meta Pixel Integration
     */
    public $meta_pixel;
    
    /**
     * Webhook Manager
     */
    public $webhooks;
    
    /**
     * Payment Manager
     */
    public $payments;
    
    /**
     * Form Versioning
     */
    public $versioning;
    
    /**
     * Anti-spam
     */
    public $anti_spam;
    
    /**
     * Ottiene l'istanza singleton
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Costruttore
     */
    private function __construct() {
        // Inizializza core components
        $this->init_core();
        
        // Inizializza components
        $this->init_components();
        
        // Inizializza hooks
        $this->init_hooks();
    }
    
    /**
     * Inizializza componenti core
     */
    private function init_core() {
        // Inizializza Logger
        \FPForms\Core\Logger::init();
        
        // Inizializza traduttore (filtra gettext in base a lingua URL)
        \FPForms\I18n\Translator::init();
        
        // Registra hooks del plugin
        \FPForms\Core\Hooks::register();
    }
    
    /**
     * Inizializza i componenti del plugin
     */
    private function init_components() {
        // Database
        $this->database = new Database\Manager();
        
        // Forms manager
        $this->forms = new Forms\Manager();
        
        // Submissions manager
        $this->submissions = new Submissions\Manager();
        
        // Email notifications
        $this->email = new Email\Manager();
        
        // Quick features
        $this->quick_features = new Forms\QuickFeatures();
        
        // Templates library
        $this->templates = new Templates\Library();
        
        // Conditional logic
        $this->conditional_logic = new Logic\ConditionalLogic();
        
        // Multi-step forms
        new Forms\MultiStep();
        
        // Form versioning
        $this->versioning = new Versioning\FormHistory();
        
        // Analytics tracker
        $this->analytics = new Analytics\Tracker();
        
        // GTM & GA4 Tracking
        $this->tracking = new Analytics\Tracking();
        
        // Brevo Integration
        $this->brevo = new Integrations\Brevo();
        
        // Meta Pixel Integration
        $this->meta_pixel = new Integrations\MetaPixel();
        
        // Webhook Manager
        $this->webhooks = new Integrations\WebhookManager();
        
        // Payment Manager (base per future integrazioni)
        $this->payments = new Integrations\PaymentManager();
        
        // Anti-spam
        $this->anti_spam = new Security\AntiSpam();
        
        // SMTP (configura PHPMailer se abilitato)
        Email\Smtp::init();
        
        // Admin (solo se siamo in admin)
        if ( is_admin() ) {
            $this->admin = new Admin\Manager();
            
            // Dashboard widget
            new Admin\DashboardWidget();
        }
        
        // Frontend (solo se non siamo in admin)
        if ( ! is_admin() ) {
            $this->frontend = new Frontend\Manager();
        }
    }
    
    /**
     * Inizializza gli hooks
     */
    private function init_hooks() {
        // Carica text domain per traduzioni
        add_action( 'init', [ $this, 'load_textdomain' ] );
        
        // Registra shortcode
        add_action( 'init', [ $this, 'register_shortcodes' ] );
        
        // Registra custom post type per i form
        add_action( 'init', [ $this, 'register_post_types' ] );
    }
    
    /**
     * Carica il text domain per le traduzioni
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'fp-forms',
            false,
            dirname( FP_FORMS_PLUGIN_BASENAME ) . '/languages'
        );
    }
    
    /**
     * Registra gli shortcode
     */
    public function register_shortcodes() {
        add_shortcode( 'fp_form', [ $this, 'form_shortcode' ] );
    }
    
    /**
     * Shortcode per visualizzare un form
     */
    public function form_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => 0,
        ], $atts, 'fp_form' );
        
        if ( empty( $atts['id'] ) ) {
            return '';
        }
        
        // Inizializza frontend se non presente (per shortcode in admin/preview)
        if ( ! $this->frontend ) {
            $this->frontend = new Frontend\Manager();
        }
        
        return $this->frontend->render_form( $atts['id'] );
    }
    
    /**
     * Registra custom post type per i form
     */
    public function register_post_types() {
        register_post_type( 'fp_form', [
            'labels' => [
                'name' => __( 'Forms', 'fp-forms' ),
                'singular_name' => __( 'Form', 'fp-forms' ),
            ],
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'supports' => [ 'title' ],
            'rewrite' => false,
        ] );
    }
}

