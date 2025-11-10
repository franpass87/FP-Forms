<?php
namespace FPForms\Sanitizers;

/**
 * Classe per sanitizzazione dati
 */
class Sanitizer {
    
    /**
     * Sanitizza campo in base al tipo
     */
    public function sanitize_field( $value, $field_type ) {
        if ( is_array( $value ) ) {
            return $this->sanitize_array( $value );
        }
        
        switch ( $field_type ) {
            case 'email':
                return sanitize_email( $value );
                
            case 'url':
                return esc_url_raw( $value );
                
            case 'number':
                return $this->sanitize_number( $value );
                
            case 'phone':
                return $this->sanitize_phone( $value );
                
            case 'date':
                return $this->sanitize_date( $value );
                
            case 'textarea':
                return sanitize_textarea_field( $value );
                
            case 'html':
                return wp_kses_post( $value );
                
            default:
                return sanitize_text_field( $value );
        }
    }
    
    /**
     * Sanitizza array ricorsivamente
     */
    public function sanitize_array( $array ) {
        $sanitized = [];
        
        foreach ( $array as $key => $value ) {
            $key = sanitize_key( $key );
            
            if ( is_array( $value ) ) {
                $sanitized[ $key ] = $this->sanitize_array( $value );
            } else {
                $sanitized[ $key ] = sanitize_text_field( $value );
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitizza numero
     */
    public function sanitize_number( $value ) {
        // Rimuovi tutto tranne numeri, punto e segno meno
        $value = preg_replace( '/[^0-9.\-]/', '', $value );
        
        // Converti in float se contiene punto, altrimenti in int
        if ( strpos( $value, '.' ) !== false ) {
            return (float) $value;
        }
        
        return (int) $value;
    }
    
    /**
     * Sanitizza telefono
     */
    public function sanitize_phone( $value ) {
        // Mantieni solo numeri, spazi, +, -, (, )
        return preg_replace( '/[^0-9\s\+\-\(\)]/', '', $value );
    }
    
    /**
     * Sanitizza data
     */
    public function sanitize_date( $value ) {
        $timestamp = strtotime( $value );
        
        if ( ! $timestamp ) {
            return '';
        }
        
        return date( 'Y-m-d', $timestamp );
    }
    
    /**
     * Sanitizza boolean
     */
    public function sanitize_boolean( $value ) {
        return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
    }
    
    /**
     * Sanitizza integer
     */
    public function sanitize_int( $value ) {
        return (int) $value;
    }
    
    /**
     * Sanitizza float
     */
    public function sanitize_float( $value ) {
        return (float) $value;
    }
    
    /**
     * Sanitizza slug
     */
    public function sanitize_slug( $value ) {
        return sanitize_title( $value );
    }
    
    /**
     * Sanitizza classe CSS
     */
    public function sanitize_css_class( $value ) {
        return sanitize_html_class( $value );
    }
    
    /**
     * Sanitizza HEX color
     */
    public function sanitize_hex_color( $value ) {
        if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value ) ) {
            return $value;
        }
        
        return '';
    }
    
    /**
     * Sanitizza file name
     */
    public function sanitize_file_name( $value ) {
        return sanitize_file_name( $value );
    }
    
    /**
     * Rimuove HTML tags
     */
    public function strip_tags( $value, $allowed_tags = '' ) {
        return strip_tags( $value, $allowed_tags );
    }
    
    /**
     * Sanitizza data submission completa
     */
    public function sanitize_submission_data( $data, $fields ) {
        $sanitized = [];
        
        foreach ( $fields as $field ) {
            $field_name = $field['name'];
            
            if ( ! isset( $data[ $field_name ] ) ) {
                continue;
            }
            
            $value = $data[ $field_name ];
            $sanitized[ $field_name ] = $this->sanitize_field( $value, $field['type'] );
        }
        
        return $sanitized;
    }
}

