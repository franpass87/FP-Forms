<?php
namespace FPForms\Helpers;

/**
 * Classe Helper con funzioni di utilità
 */
class Helper {
    
    /**
     * Ottiene l'IP dell'utente
     */
    public static function get_user_ip() {
        $ip = '';
        
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field( $ip );
    }
    
    /**
     * Ottiene user agent
     */
    public static function get_user_agent() {
        return isset( $_SERVER['HTTP_USER_AGENT'] ) 
            ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) 
            : '';
    }
    
    /**
     * Genera nome campo univoco
     */
    public static function generate_field_name( $prefix = 'field' ) {
        return $prefix . '_' . wp_generate_password( 8, false );
    }
    
    /**
     * Formatta data per visualizzazione
     */
    public static function format_date( $date, $format = null ) {
        if ( ! $format ) {
            $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        }
        
        return date_i18n( $format, strtotime( $date ) );
    }
    
    /**
     * Tronca testo con ellipsis
     */
    public static function truncate( $text, $length = 100, $suffix = '...' ) {
        if ( mb_strlen( $text ) <= $length ) {
            return $text;
        }
        
        return mb_substr( $text, 0, $length ) . $suffix;
    }
    
    /**
     * Verifica se stringa è JSON valido
     */
    public static function is_json( $string ) {
        if ( ! is_string( $string ) ) {
            return false;
        }
        
        json_decode( $string );
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Converte array in JSON sicuro
     */
    public static function safe_json_encode( $data ) {
        return wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    }
    
    /**
     * Decodifica JSON in modo sicuro
     */
    public static function safe_json_decode( $json, $assoc = true ) {
        if ( ! self::is_json( $json ) ) {
            return $assoc ? [] : null;
        }
        
        return json_decode( $json, $assoc );
    }
    
    /**
     * Verifica se siamo in admin AJAX
     */
    public static function is_admin_ajax() {
        return defined( 'DOING_AJAX' ) && DOING_AJAX && is_admin();
    }
    
    /**
     * Verifica se siamo in frontend AJAX
     */
    public static function is_frontend_ajax() {
        return defined( 'DOING_AJAX' ) && DOING_AJAX && ! is_admin();
    }
    
    /**
     * Ottiene URL admin page
     */
    public static function get_admin_url( $page, $params = [] ) {
        $url = admin_url( 'admin.php?page=' . $page );
        
        if ( ! empty( $params ) ) {
            $url = add_query_arg( $params, $url );
        }
        
        return $url;
    }
    
    /**
     * Genera nonce per azione
     */
    public static function create_nonce( $action ) {
        return wp_create_nonce( 'fp_forms_' . $action );
    }
    
    /**
     * Verifica nonce
     */
    public static function verify_nonce( $nonce, $action ) {
        return wp_verify_nonce( $nonce, 'fp_forms_' . $action );
    }
    
    /**
     * Ottiene valore POST sanitizzato
     */
    public static function get_post( $key, $default = '' ) {
        return isset( $_POST[ $key ] ) ? sanitize_text_field( $_POST[ $key ] ) : $default;
    }
    
    /**
     * Ottiene valore GET sanitizzato
     */
    public static function get_get( $key, $default = '' ) {
        return isset( $_GET[ $key ] ) ? sanitize_text_field( $_GET[ $key ] ) : $default;
    }
    
    /**
     * Ottiene array POST sanitizzato
     */
    public static function get_post_array( $key, $default = [] ) {
        if ( ! isset( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) {
            return $default;
        }
        
        return array_map( 'sanitize_text_field', $_POST[ $key ] );
    }
    
    /**
     * Slugify stringa
     */
    public static function slugify( $text ) {
        $text = sanitize_title( $text );
        $text = str_replace( '-', '_', $text );
        return $text;
    }
    
    /**
     * Converte bytes in formato leggibile
     */
    public static function format_bytes( $bytes, $precision = 2 ) {
        $units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];
        
        for ( $i = 0; $bytes > 1024 && $i < count( $units ) - 1; $i++ ) {
            $bytes /= 1024;
        }
        
        return round( $bytes, $precision ) . ' ' . $units[ $i ];
    }
    
    /**
     * Debug log (solo se WP_DEBUG è attivo)
     */
    public static function log( $message, $data = null ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        
        if ( is_string( $message ) ) {
            $log_message = '[FP Forms] ' . $message;
        } else {
            $log_message = '[FP Forms] ' . var_export( $message, true );
        }
        
        if ( $data !== null ) {
            $log_message .= ' | Data: ' . var_export( $data, true );
        }
        
        error_log( $log_message );
    }
    
    /**
     * Ottiene template path con fallback
     */
    public static function get_template_path( $template_name, $template_path = '' ) {
        // Check in tema
        $theme_template = get_stylesheet_directory() . '/fp-forms/' . $template_name;
        if ( file_exists( $theme_template ) ) {
            return $theme_template;
        }
        
        // Fallback a plugin
        return FP_FORMS_PLUGIN_DIR . 'templates/' . $template_path . $template_name;
    }
    
    /**
     * Include template con variabili
     */
    public static function get_template( $template_name, $args = [], $template_path = '' ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
        }
        
        $template_file = self::get_template_path( $template_name, $template_path );
        
        if ( file_exists( $template_file ) ) {
            include $template_file;
        }
    }
}

