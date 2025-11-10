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
        
        // File upload enhancement
        wp_register_script(
            'fp-forms-file-upload',
            FP_FORMS_PLUGIN_URL . 'assets/js/file-upload.js',
            [ 'jquery', 'fp-forms-frontend' ],
            FP_FORMS_VERSION,
            true
        );
        
        wp_localize_script( 'fp-forms-frontend', 'fpForms', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'fp_forms_submit' ),
            'strings' => [
                'submitting' => __( 'Invio in corso...', 'fp-forms' ),
                'error' => __( 'Si è verificato un errore. Riprova.', 'fp-forms' ),
                'error_connection' => __( 'Si è verificato un errore di connessione. Riprova.', 'fp-forms' ),
                'error_timeout' => __( 'La richiesta ha impiegato troppo tempo. Verifica la connessione e riprova.', 'fp-forms' ),
                'error_abort' => __( 'Richiesta annullata. Riprova.', 'fp-forms' ),
                'required' => __( 'Questo campo è obbligatorio.', 'fp-forms' ),
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
        wp_enqueue_script( 'fp-forms-frontend' );
        
        // Carica file upload se form ha campo file
        $has_recaptcha = false;
        foreach ( $form['fields'] as $field ) {
            if ( $field['type'] === 'file' ) {
                wp_enqueue_script( 'fp-forms-file-upload' );
            }
            if ( $field['type'] === 'recaptcha' ) {
                $has_recaptcha = true;
            }
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
            
            // Passa regole al JavaScript
            wp_localize_script( 'fp-forms-conditional-logic', 'fpFormsRules_' . $form_id, $form['settings']['conditional_rules'] );
        }
        
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
    
    /**
     * Ottiene HTML di un campo (legacy - mantenuto per compatibilità)
     */
    private function get_field_html_legacy( $field, $form_id ) {
        $field_type = $field['type'];
        $field_name = 'fp_field_' . $field['name'];
        $field_id = 'fp_field_' . $form_id . '_' . $field['name'];
        $required = $field['required'] ? 'required' : '';
        $required_mark = $field['required'] ? ' <span class="fp-forms-required">*</span>' : '';
        
        $html = '<div class="fp-forms-field fp-forms-field-' . esc_attr( $field_type ) . '">';
        $html .= '<label for="' . esc_attr( $field_id ) . '" class="fp-forms-label">' . esc_html( $field['label'] ) . $required_mark . '</label>';
        
        switch ( $field_type ) {
            case 'text':
            case 'email':
            case 'phone':
            case 'number':
            case 'date':
                $html .= '<input type="' . esc_attr( $field_type ) . '" ';
                $html .= 'id="' . esc_attr( $field_id ) . '" ';
                $html .= 'name="' . esc_attr( $field_name ) . '" ';
                $html .= 'class="fp-forms-input" ';
                $html .= $required . ' ';
                
                if ( isset( $field['options']['placeholder'] ) ) {
                    $html .= 'placeholder="' . esc_attr( $field['options']['placeholder'] ) . '" ';
                }
                
                $html .= '/>';
                break;
                
            case 'textarea':
                $html .= '<textarea ';
                $html .= 'id="' . esc_attr( $field_id ) . '" ';
                $html .= 'name="' . esc_attr( $field_name ) . '" ';
                $html .= 'class="fp-forms-textarea" ';
                $html .= $required . ' ';
                
                if ( isset( $field['options']['placeholder'] ) ) {
                    $html .= 'placeholder="' . esc_attr( $field['options']['placeholder'] ) . '" ';
                }
                
                if ( isset( $field['options']['rows'] ) ) {
                    $html .= 'rows="' . esc_attr( $field['options']['rows'] ) . '" ';
                }
                
                $html .= '></textarea>';
                break;
                
            case 'select':
                $html .= '<select ';
                $html .= 'id="' . esc_attr( $field_id ) . '" ';
                $html .= 'name="' . esc_attr( $field_name ) . '" ';
                $html .= 'class="fp-forms-select" ';
                $html .= $required . ' ';
                $html .= '>';
                
                $html .= '<option value="">' . __( '-- Seleziona --', 'fp-forms' ) . '</option>';
                
                if ( isset( $field['options']['choices'] ) && is_array( $field['options']['choices'] ) ) {
                    foreach ( $field['options']['choices'] as $choice ) {
                        $html .= '<option value="' . esc_attr( $choice ) . '">' . esc_html( $choice ) . '</option>';
                    }
                }
                
                $html .= '</select>';
                break;
                
            case 'radio':
                if ( isset( $field['options']['choices'] ) && is_array( $field['options']['choices'] ) ) {
                    $html .= '<div class="fp-forms-radio-group">';
                    foreach ( $field['options']['choices'] as $index => $choice ) {
                        $choice_id = $field_id . '_' . $index;
                        $html .= '<label class="fp-forms-radio-label">';
                        $html .= '<input type="radio" ';
                        $html .= 'id="' . esc_attr( $choice_id ) . '" ';
                        $html .= 'name="' . esc_attr( $field_name ) . '" ';
                        $html .= 'value="' . esc_attr( $choice ) . '" ';
                        $html .= 'class="fp-forms-radio" ';
                        $html .= $required . ' ';
                        $html .= '/>';
                        $html .= ' ' . esc_html( $choice );
                        $html .= '</label>';
                    }
                    $html .= '</div>';
                }
                break;
                
            case 'checkbox':
                if ( isset( $field['options']['choices'] ) && is_array( $field['options']['choices'] ) ) {
                    $html .= '<div class="fp-forms-checkbox-group">';
                    foreach ( $field['options']['choices'] as $index => $choice ) {
                        $choice_id = $field_id . '_' . $index;
                        $html .= '<label class="fp-forms-checkbox-label">';
                        $html .= '<input type="checkbox" ';
                        $html .= 'id="' . esc_attr( $choice_id ) . '" ';
                        $html .= 'name="' . esc_attr( $field_name ) . '[]" ';
                        $html .= 'value="' . esc_attr( $choice ) . '" ';
                        $html .= 'class="fp-forms-checkbox" ';
                        $html .= '/>';
                        $html .= ' ' . esc_html( $choice );
                        $html .= '</label>';
                    }
                    $html .= '</div>';
                }
                break;
        }
        
        if ( isset( $field['options']['description'] ) && ! empty( $field['options']['description'] ) ) {
            $html .= '<p class="fp-forms-description">' . esc_html( $field['options']['description'] ) . '</p>';
        }
        
        $html .= '<span class="fp-forms-error" style="display:none;"></span>';
        $html .= '</div>';
        
        return $html;
    }
}
