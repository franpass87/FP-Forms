<?php
namespace FPForms\Core;

/**
 * Logger per debugging e monitoraggio
 */
class Logger {
    
    /**
     * Log directory
     */
    private static $log_dir = null;
    
    /**
     * Livelli di log
     */
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    /**
     * Inizializza logger
     */
    public static function init() {
        self::$log_dir = WP_CONTENT_DIR . '/uploads/fp-forms-logs/';
        
        if ( ! file_exists( self::$log_dir ) ) {
            wp_mkdir_p( self::$log_dir );
            
            // FIX #3: Protezione directory universale (Apache + Nginx + IIS)
            // Protezione Apache (.htaccess)
            file_put_contents( 
                self::$log_dir . '.htaccess', 
                "deny from all\n" 
            );
            
            // Protezione universale (index.php)
            file_put_contents( 
                self::$log_dir . 'index.php', 
                '<?php // Silence is golden' 
            );
            
            // Imposta permessi sicuri (se possibile)
            if ( function_exists( 'chmod' ) ) {
                @chmod( self::$log_dir, 0750 );
            }
        }
    }
    
    /**
     * Log generico
     */
    public static function log( $message, $level = self::INFO, $context = [] ) {
        if ( ! self::should_log() ) {
            return;
        }
        
        if ( self::$log_dir === null ) {
            self::init();
        }
        
        $timestamp = current_time( 'Y-m-d H:i:s' );
        $log_entry = sprintf(
            "[%s] [%s] %s\n",
            $timestamp,
            $level,
            $message
        );
        
        if ( ! empty( $context ) ) {
            $log_entry .= "Context: " . wp_json_encode( $context ) . "\n";
        }
        
        $log_file = self::$log_dir . 'fp-forms-' . date( 'Y-m-d' ) . '.log';
        
        error_log( $log_entry, 3, $log_file );
    }
    
    /**
     * Log errore
     */
    public static function error( $message, $context = [] ) {
        self::log( $message, self::ERROR, $context );
    }
    
    /**
     * Log warning
     */
    public static function warning( $message, $context = [] ) {
        self::log( $message, self::WARNING, $context );
    }
    
    /**
     * Log info
     */
    public static function info( $message, $context = [] ) {
        self::log( $message, self::INFO, $context );
    }
    
    /**
     * Log debug
     */
    public static function debug( $message, $context = [] ) {
        self::log( $message, self::DEBUG, $context );
    }
    
    /**
     * Log submission
     */
    public static function log_submission( $form_id, $submission_id, $success = true ) {
        $message = sprintf(
            'Submission %s for form #%d',
            $success ? 'saved successfully' : 'failed',
            $form_id
        );
        
        self::info( $message, [
            'form_id' => $form_id,
            'submission_id' => $submission_id,
            'success' => $success,
        ] );
    }
    
    /**
     * Log email
     */
    public static function log_email( $to, $subject, $success = true ) {
        $message = sprintf(
            'Email %s to %s',
            $success ? 'sent successfully' : 'failed',
            $to
        );
        
        self::info( $message, [
            'to' => $to,
            'subject' => $subject,
            'success' => $success,
        ] );
    }
    
    /**
     * Verifica se loggare
     */
    private static function should_log() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }
    
    /**
     * Ottiene logs
     */
    public static function get_logs( $date = null ) {
        if ( self::$log_dir === null ) {
            self::init();
        }
        
        $date = $date ? $date : date( 'Y-m-d' );
        $log_file = self::$log_dir . 'fp-forms-' . $date . '.log';
        
        if ( ! file_exists( $log_file ) ) {
            return '';
        }
        
        return file_get_contents( $log_file );
    }
    
    /**
     * Pulisce vecchi log
     */
    public static function clean_old_logs( $days = 30 ) {
        if ( self::$log_dir === null ) {
            self::init();
        }
        
        $files = glob( self::$log_dir . '*.log' );
        $now = time();
        
        foreach ( $files as $file ) {
            if ( is_file( $file ) ) {
                if ( $now - filemtime( $file ) >= 60 * 60 * 24 * $days ) {
                    unlink( $file );
                }
            }
        }
    }
}

