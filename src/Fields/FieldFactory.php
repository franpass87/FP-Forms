<?php
namespace FPForms\Fields;

/**
 * Factory per creare field renderer
 */
class FieldFactory {
    
    /**
     * Renderer registrati
     */
    private static $renderers = [];
    
    /**
     * Inizializza renderers di default
     */
    public static function init() {
        self::register_defaults();
    }
    
    /**
     * Registra renderer di default
     */
    private static function register_defaults() {
        self::$renderers = [
            'text' => [ __CLASS__, 'render_text' ],
            'email' => [ __CLASS__, 'render_email' ],
            'phone' => [ __CLASS__, 'render_phone' ],
            'number' => [ __CLASS__, 'render_number' ],
            'date' => [ __CLASS__, 'render_date' ],
            'textarea' => [ __CLASS__, 'render_textarea' ],
            'select' => [ __CLASS__, 'render_select' ],
            'radio' => [ __CLASS__, 'render_radio' ],
            'checkbox' => [ __CLASS__, 'render_checkbox' ],
            'privacy-checkbox' => [ __CLASS__, 'render_privacy_checkbox' ],
            'marketing-checkbox' => [ __CLASS__, 'render_marketing_checkbox' ],
            'recaptcha' => [ __CLASS__, 'render_recaptcha' ],
            'file' => [ __CLASS__, 'render_file' ],
        ];
    }
    
    /**
     * Registra renderer custom
     */
    public static function register( $type, $callback ) {
        self::$renderers[ $type ] = $callback;
    }
    
    /**
     * Renderizza field
     */
    public static function render( $field, $form_id ) {
        $type = $field['type'];
        
        if ( ! isset( self::$renderers[ $type ] ) ) {
            return '';
        }
        
        return call_user_func( self::$renderers[ $type ], $field, $form_id );
    }
    
    /**
     * Ottiene attributi comuni
     */
    private static function get_common_attrs( $field, $form_id ) {
        $field_name = 'fp_field_' . $field['name'];
        $field_id = 'fp_field_' . $form_id . '_' . $field['name'];
        $required = $field['required'] ? 'required' : '';
        $placeholder = isset( $field['options']['placeholder'] ) ? $field['options']['placeholder'] : '';
        
        return [
            'name' => $field_name,
            'id' => $field_id,
            'required' => $required,
            'placeholder' => $placeholder,
        ];
    }
    
    /**
     * Renderizza wrapper
     */
    private static function wrap_field( $field, $content ) {
        $required_mark = $field['required'] ? ' <span class="fp-forms-required">*</span>' : '';
        $description = isset( $field['options']['description'] ) ? $field['options']['description'] : '';
        
        $html = '<div class="fp-forms-field fp-forms-field-' . esc_attr( $field['type'] ) . '">';
        $html .= '<label for="' . esc_attr( $field['id'] ?? '' ) . '" class="fp-forms-label">' . esc_html( $field['label'] ) . $required_mark . '</label>';
        $html .= $content;
        
        if ( $description ) {
            $html .= '<p class="fp-forms-description">' . esc_html( $description ) . '</p>';
        }
        
        $html .= '<span class="fp-forms-error" style="display:none;"></span>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render text field
     */
    public static function render_text( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $html = sprintf(
            '<input type="text" id="%s" name="%s" class="fp-forms-input" %s placeholder="%s" />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required'],
            esc_attr( $attrs['placeholder'] )
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render email field
     */
    public static function render_email( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $html = sprintf(
            '<input type="email" id="%s" name="%s" class="fp-forms-input" %s placeholder="%s" />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required'],
            esc_attr( $attrs['placeholder'] )
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render phone field
     */
    public static function render_phone( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $html = sprintf(
            '<input type="tel" id="%s" name="%s" class="fp-forms-input" %s placeholder="%s" />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required'],
            esc_attr( $attrs['placeholder'] )
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render number field
     */
    public static function render_number( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $html = sprintf(
            '<input type="number" id="%s" name="%s" class="fp-forms-input" %s placeholder="%s" />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required'],
            esc_attr( $attrs['placeholder'] )
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render date field
     */
    public static function render_date( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $html = sprintf(
            '<input type="date" id="%s" name="%s" class="fp-forms-input" %s />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required']
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render textarea field
     */
    public static function render_textarea( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        $rows = isset( $field['options']['rows'] ) ? $field['options']['rows'] : 5;
        
        $html = sprintf(
            '<textarea id="%s" name="%s" class="fp-forms-textarea" rows="%d" %s placeholder="%s"></textarea>',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            (int) $rows,
            $attrs['required'],
            esc_attr( $attrs['placeholder'] )
        );
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render select field
     */
    public static function render_select( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        $choices = isset( $field['options']['choices'] ) ? $field['options']['choices'] : [];
        
        $html = sprintf(
            '<select id="%s" name="%s" class="fp-forms-select" %s>',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $attrs['required']
        );
        
        $html .= '<option value="">' . __( '-- Seleziona --', 'fp-forms' ) . '</option>';
        
        foreach ( $choices as $choice ) {
            $html .= '<option value="' . esc_attr( $choice ) . '">' . esc_html( $choice ) . '</option>';
        }
        
        $html .= '</select>';
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render radio field
     */
    public static function render_radio( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        $choices = isset( $field['options']['choices'] ) ? $field['options']['choices'] : [];
        
        $html = '<div class="fp-forms-radio-group">';
        
        foreach ( $choices as $index => $choice ) {
            $choice_id = $attrs['id'] . '_' . $index;
            $html .= '<label class="fp-forms-radio-label">';
            $html .= sprintf(
                '<input type="radio" id="%s" name="%s" value="%s" class="fp-forms-radio" %s />',
                esc_attr( $choice_id ),
                esc_attr( $attrs['name'] ),
                esc_attr( $choice ),
                $attrs['required']
            );
            $html .= ' ' . esc_html( $choice );
            $html .= '</label>';
        }
        
        $html .= '</div>';
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render checkbox field
     */
    public static function render_checkbox( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        $choices = isset( $field['options']['choices'] ) ? $field['options']['choices'] : [];
        
        $html = '<div class="fp-forms-checkbox-group">';
        
        foreach ( $choices as $index => $choice ) {
            $choice_id = $attrs['id'] . '_' . $index;
            $html .= '<label class="fp-forms-checkbox-label">';
            $html .= sprintf(
                '<input type="checkbox" id="%s" name="%s[]" value="%s" class="fp-forms-checkbox" />',
                esc_attr( $choice_id ),
                esc_attr( $attrs['name'] ),
                esc_attr( $choice )
            );
            $html .= ' ' . esc_html( $choice );
            $html .= '</label>';
        }
        
        $html .= '</div>';
        
        $field['id'] = $attrs['id'];
        return self::wrap_field( $field, $html );
    }
    
    /**
     * Render privacy checkbox field
     * Campo dedicato per GDPR compliance con link a privacy policy
     */
    public static function render_privacy_checkbox( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        // Ottieni URL privacy policy
        $privacy_url = self::get_privacy_policy_url();
        $privacy_text = isset( $field['options']['privacy_text'] ) 
            ? $field['options']['privacy_text'] 
            : __( 'Ho letto e accetto la', 'fp-forms' );
        $privacy_link_text = __( 'Privacy Policy', 'fp-forms' );
        
        // Il campo privacy è sempre obbligatorio per GDPR
        $field['required'] = true;
        
        $html = '<div class="fp-forms-privacy-checkbox">';
        $html .= '<label class="fp-forms-checkbox-label">';
        $html .= sprintf(
            '<input type="checkbox" id="%s" name="%s" value="1" class="fp-forms-checkbox" required />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] )
        );
        $html .= ' <span class="fp-forms-privacy-text">';
        $html .= esc_html( $privacy_text ) . ' ';
        
        if ( $privacy_url ) {
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="fp-forms-privacy-link">%s</a>',
                esc_url( $privacy_url ),
                esc_html( $privacy_link_text )
            );
        } else {
            $html .= '<strong>' . esc_html( $privacy_link_text ) . '</strong>';
        }
        
        $html .= ' <span class="fp-forms-required">*</span>';
        $html .= '</span>';
        $html .= '</label>';
        $html .= '</div>';
        
        $field['id'] = $attrs['id'];
        
        // Wrap con logica custom (senza label duplicata)
        $description = isset( $field['options']['description'] ) ? $field['options']['description'] : '';
        
        $wrapper = '<div class="fp-forms-field fp-forms-field-privacy-checkbox">';
        $wrapper .= $html;
        
        if ( $description ) {
            $wrapper .= '<p class="fp-forms-description">' . esc_html( $description ) . '</p>';
        }
        
        $wrapper .= '<span class="fp-forms-error" style="display:none;"></span>';
        $wrapper .= '</div>';
        
        return $wrapper;
    }
    
    /**
     * Ottiene URL della privacy policy
     * Controlla nell'ordine: FP-Privacy, WP Privacy Page, URL custom
     */
    private static function get_privacy_policy_url() {
        // 1. Controlla se FP-Privacy è attivo
        if ( function_exists( 'fp_privacy_get_policy_url' ) ) {
            $url = fp_privacy_get_policy_url();
            if ( $url ) {
                return $url;
            }
        }
        
        // 2. Controlla se esiste FP-Privacy come plugin
        if ( class_exists( 'FP\Privacy\Plugin' ) ) {
            // Ottieni URL dalla pagina privacy di FP-Privacy
            $privacy_page_id = get_option( 'fp_privacy_policy_page_id' );
            if ( $privacy_page_id ) {
                return get_permalink( $privacy_page_id );
            }
        }
        
        // 3. Fallback: Privacy Policy page di WordPress
        $privacy_page_id = get_option( 'wp_page_for_privacy_policy' );
        if ( $privacy_page_id ) {
            return get_permalink( $privacy_page_id );
        }
        
        // 4. Nessuna privacy policy trovata
        return '';
    }
    
    /**
     * Render marketing checkbox field
     * Campo opzionale per consenso marketing/newsletter
     */
    public static function render_marketing_checkbox( $field, $form_id ) {
        $attrs = self::get_common_attrs( $field, $form_id );
        
        $marketing_text = isset( $field['options']['marketing_text'] ) 
            ? $field['options']['marketing_text'] 
            : __( 'Acconsento a ricevere comunicazioni marketing e newsletter', 'fp-forms' );
        
        // Marketing checkbox è sempre opzionale (a differenza del privacy)
        $required = ! empty( $field['required'] ) ? 'required' : '';
        
        $html = '<div class="fp-forms-marketing-checkbox">';
        $html .= '<label class="fp-forms-checkbox-label">';
        $html .= sprintf(
            '<input type="checkbox" id="%s" name="%s" value="1" class="fp-forms-checkbox" %s />',
            esc_attr( $attrs['id'] ),
            esc_attr( $attrs['name'] ),
            $required
        );
        $html .= ' <span class="fp-forms-marketing-text">';
        $html .= esc_html( $marketing_text );
        
        if ( $required ) {
            $html .= ' <span class="fp-forms-required">*</span>';
        }
        
        $html .= '</span>';
        $html .= '</label>';
        $html .= '</div>';
        
        $field['id'] = $attrs['id'];
        
        // Wrap con logica custom (senza label duplicata)
        $description = isset( $field['options']['description'] ) ? $field['options']['description'] : '';
        
        $wrapper = '<div class="fp-forms-field fp-forms-field-marketing-checkbox">';
        $wrapper .= $html;
        
        if ( $description ) {
            $wrapper .= '<p class="fp-forms-description">' . esc_html( $description ) . '</p>';
        }
        
        $wrapper .= '<span class="fp-forms-error" style="display:none;"></span>';
        $wrapper .= '</div>';
        
        return $wrapper;
    }
    
    /**
     * Render reCAPTCHA field
     */
    public static function render_recaptcha( $field, $form_id ) {
        $recaptcha = new \FPForms\Security\ReCaptcha();
        
        $html = $recaptcha->render_field( $form_id );
        
        // Wrap minimo (senza label)
        $wrapper = '<div class="fp-forms-field fp-forms-field-recaptcha">';
        $wrapper .= $html;
        $wrapper .= '<span class="fp-forms-error" style="display:none;"></span>';
        $wrapper .= '</div>';
        
        return $wrapper;
    }
    
    /**
     * Render file field
     */
    public static function render_file( $field, $form_id ) {
        $file_field = new \FPForms\Fields\FileField();
        return $file_field->render( $field, $form_id );
    }
}
