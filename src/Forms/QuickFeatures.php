<?php
namespace FPForms\Forms;

/**
 * Quick Features aggiuntive per migliorare UX
 */
class QuickFeatures {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Success redirect
        add_filter( 'fp_forms_success_message', [ $this, 'maybe_redirect' ], 10, 3 );
        
        // Custom CSS class sul form
        add_filter( 'fp_forms_form_html', [ $this, 'add_custom_class' ], 10, 3 );
    }
    
    /**
     * Gestisce redirect dopo success
     */
    public function maybe_redirect( $message, $form_id, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return $message;
        }
        
        // Controlla se redirect è abilitato
        if ( isset( $form['settings']['success_redirect_enabled'] ) && $form['settings']['success_redirect_enabled'] ) {
            $redirect_url = isset( $form['settings']['success_redirect_url'] ) ? $form['settings']['success_redirect_url'] : '';
            
            if ( ! empty( $redirect_url ) ) {
                // Sostituisci tag dinamici nella URL
                $redirect_url = $this->replace_tags( $redirect_url, $data, $form );
                
                // Aggiungi info per JavaScript redirect
                add_filter( 'fp_forms_ajax_response', function( $response ) use ( $redirect_url ) {
                    $response['redirect'] = esc_url( $redirect_url );
                    return $response;
                } );
            }
        }
        
        return $message;
    }
    
    /**
     * Aggiunge classe CSS custom al form
     */
    public function add_custom_class( $html, $form_id, $form ) {
        if ( isset( $form['settings']['custom_css_class'] ) && ! empty( $form['settings']['custom_css_class'] ) ) {
            $custom_class = sanitize_html_class( $form['settings']['custom_css_class'] );
            $html = str_replace( 
                'class="fp-forms-container"', 
                'class="fp-forms-container ' . $custom_class . '"', 
                $html 
            );
        }
        
        return $html;
    }
    
    /**
     * Sostituisce tag dinamici
     */
    private function replace_tags( $text, $data, $form ) {
        // Tag per i campi del form
        foreach ( $data as $key => $value ) {
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            $text = str_replace( '{' . $key . '}', $value, $text );
        }
        
        // Tag generali
        $text = str_replace( '{form_id}', $form['id'], $text );
        $text = str_replace( '{form_title}', $form['title'], $text );
        
        return $text;
    }
    
    /**
     * Ottiene classe width per campo
     */
    public static function get_field_width_class( $field ) {
        if ( ! isset( $field['options']['width'] ) ) {
            return 'fp-field-width-full';
        }
        
        $width = $field['options']['width'];
        
        $classes = [
            'full' => 'fp-field-width-full',
            'half' => 'fp-field-width-half',
            'third' => 'fp-field-width-third',
            'quarter' => 'fp-field-width-quarter',
        ];
        
        return $classes[ $width ] ?? 'fp-field-width-full';
    }
}

