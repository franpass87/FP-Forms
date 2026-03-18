<?php
namespace FPForms\Frontend;

/**
 * Gestisce il frontend del plugin
 */
class Manager {
    
    /**
     * Costruttore
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // Inizializza Field Factory
        \FPForms\Fields\FieldFactory::init();
    }
    
    /**
     * Carica assets frontend
     */
    public function enqueue_assets() {
        // CSS
        wp_register_style(
            'fp-forms-frontend',
            FP_FORMS_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            FP_FORMS_VERSION
        );
        
        // JS
        wp_register_script(
            'fp-forms-frontend',
            FP_FORMS_PLUGIN_URL . 'assets/js/frontend.js',
            [ 'jquery' ],
            FP_FORMS_VERSION,
            true
        );
        
        // Toast notifications
        wp_register_style(
            'fp-forms-toast',
            FP_FORMS_PLUGIN_URL . 'assets/css/toast.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_register_script(
            'fp-forms-toast',
            FP_FORMS_PLUGIN_URL . 'assets/js/toast.js',
            [ 'jquery' ],
            FP_FORMS_VERSION,
            true
        );
        
        // File upload enhancement
        wp_register_script(
            'fp-forms-file-upload',
            FP_FORMS_PLUGIN_URL . 'assets/js/file-upload.js',
            [ 'jquery', 'fp-forms-frontend' ],
            FP_FORMS_VERSION,
            true
        );
        
        // Form calculator
        wp_register_script(
            'fp-forms-calculator',
            FP_FORMS_PLUGIN_URL . 'assets/js/calculator.js',
            [ 'jquery', 'fp-forms-frontend' ],
            FP_FORMS_VERSION,
            true
        );
        
        // Progressive save
        wp_register_style(
            'fp-forms-progressive-save',
            FP_FORMS_PLUGIN_URL . 'assets/css/progressive-save.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_register_script(
            'fp-forms-progressive-save',
            FP_FORMS_PLUGIN_URL . 'assets/js/progressive-save.js',
            [ 'jquery', 'fp-forms-frontend' ],
            FP_FORMS_VERSION,
            true
        );
        
        // Voice input
        wp_register_style(
            'fp-forms-voice-input',
            FP_FORMS_PLUGIN_URL . 'assets/css/voice-input.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_register_script(
            'fp-forms-voice-input',
            FP_FORMS_PLUGIN_URL . 'assets/js/voice-input.js',
            [ 'jquery', 'fp-forms-frontend' ],
            FP_FORMS_VERSION,
            true
        );
        
        wp_localize_script( 'fp-forms-frontend', 'fpForms', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'fp_forms_submit' ),
            'strings' => [
                'submitting'          => __( 'Invio in corso...', 'fp-forms' ),
                'error'               => __( 'Si è verificato un errore. Riprova.', 'fp-forms' ),
                'error_connection'    => __( 'Si è verificato un errore di connessione. Riprova.', 'fp-forms' ),
                'error_timeout'       => __( 'La richiesta ha impiegato troppo tempo. Verifica la connessione e riprova.', 'fp-forms' ),
                'error_abort'         => __( 'Richiesta annullata. Riprova.', 'fp-forms' ),
                'required'            => __( 'Questo campo è obbligatorio.', 'fp-forms' ),
                'invalid_email'       => __( 'Inserisci un indirizzo email valido.', 'fp-forms' ),
                'autosave_found'      => __( 'Abbiamo trovato dati salvati.', 'fp-forms' ),
                'autosave_restore'    => __( 'Ripristina', 'fp-forms' ),
                'autosave_dismiss'    => __( 'Ignora', 'fp-forms' ),
                'autosave_restored'   => __( 'Dati ripristinati con successo!', 'fp-forms' ),
            ],
        ] );
    }
    
    /**
     * Renderizza un form
     */
    public function render_form( $form_id ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return '<p>' . __( 'Form non trovato.', 'fp-forms' ) . '</p>';
        }
        
        // Carica assets
        wp_enqueue_style( 'fp-forms-frontend' );
        wp_enqueue_style( 'fp-forms-toast' );
        wp_enqueue_style( 'fp-forms-progressive-save' );
        wp_enqueue_style( 'fp-forms-voice-input' );
        wp_enqueue_script( 'fp-forms-toast' );
        wp_enqueue_script( 'fp-forms-progressive-save' );
        wp_enqueue_script( 'fp-forms-voice-input' );
        wp_enqueue_script( 'fp-forms-frontend' );
        
        // Carica file upload se form ha campo file
        $has_recaptcha = false;
        $has_calculated = false;
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] === 'file' ) {
                wp_enqueue_script( 'fp-forms-file-upload' );
            }
            if ( $field['type'] === 'recaptcha' ) {
                $has_recaptcha = true;
            }
            if ( $field['type'] === 'calculated' ) {
                $has_calculated = true;
            }
        }
        
        // Enqueue calculator se presente
        if ( $has_calculated ) {
            wp_enqueue_script( 'fp-forms-calculator' );
        }
        
        // Enqueue reCAPTCHA se presente nel form
        if ( $has_recaptcha ) {
            $recaptcha = new \FPForms\Security\ReCaptcha();
            $recaptcha->enqueue_scripts( $form_id );
        }
        
        // Carica conditional logic se ci sono regole
        if ( isset( $form['settings']['conditional_rules'] ) && ! empty( $form['settings']['conditional_rules'] ) ) {
            wp_enqueue_script(
                'fp-forms-conditional-logic',
                FP_FORMS_PLUGIN_URL . 'assets/js/conditional-logic.js',
                [ 'jquery', 'fp-forms-frontend' ],
                FP_FORMS_VERSION,
                true
            );
            
            $operator = isset( $form['settings']['conditional_operator_global'] ) && $form['settings']['conditional_operator_global'] === 'and' ? 'and' : 'or';
            wp_localize_script( 'fp-forms-conditional-logic', 'fpFormsRules_' . $form_id, [
                'rules'    => $form['settings']['conditional_rules'],
                'operator' => $operator,
            ] );
        }
        
        // Token one-time per lock submission (anti double-submit)
        $submit_token = function_exists( 'wp_generate_uuid4' ) ? wp_generate_uuid4() : ( 'fp_' . uniqid( '', true ) );
        set_transient( 'fp_forms_submit_lock_' . $submit_token, 0, 120 );
        
        // Track view per analytics
        do_action( 'fp_forms_form_rendered', $form_id );
        
        // Avvia output buffering
        ob_start();
        
        // Include template
        include FP_FORMS_PLUGIN_DIR . 'templates/frontend/form.php';
        
        return ob_get_clean();
    }
    
    /**
     * Ottiene HTML di un campo (usa FieldFactory)
     */
    public function get_field_html( $field, $form_id ) {
        $html = \FPForms\Fields\FieldFactory::render( $field, $form_id );
        
        // Applica filtro per personalizzazione
        return \FPForms\Core\Hooks::filter_field_html( $html, $field, $form_id );
    }
    
}
