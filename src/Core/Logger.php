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
        }
        
        // Ricrea i file di protezione se mancanti (anche dopo aggiornamenti)
        $htaccess = self::$log_dir . '.htaccess';
        if ( ! file_exists( $htaccess ) ) {
            $result = file_put_contents( $htaccess, "deny from all\n" );
            if ( $result === false ) {
                error_log( 'FP Forms: impossibile creare .htaccess nella directory dei log. I log potrebbero essere accessibili pubblicamente.' );
            }
        }
        
        $index = self::$log_dir . 'index.php';
        if ( ! file_exists( $index ) ) {
            file_put_contents( $index, '<?php // Silence is golden' );
        }
        
        // Imposta permessi sicuri (se possibile)
        if ( function_exists( 'chmod' ) ) {
            @chmod( self::$log_dir, 0750 );
        }
    }
    
    /**
     * Log generico.
     * ERROR e WARNING vengono sempre scritti (anche in produzione).
     * INFO e DEBUG vengono scritti solo se WP_DEBUG è attivo.
     */
    public static function log( $message, $level = self::INFO, $context = [] ) {
        $is_critical = in_array( $level, [ self::ERROR, self::WARNING ], true );
        if ( ! $is_critical && ! self::should_log() ) {
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
        
        $log_file = self::$log_dir . 'fp-forms-' . current_time( 'Y-m-d' ) . '.log';
        
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
        $masked = self::mask_email( (string) $to );
        $message = sprintf(
            'Email %s to %s',
            $success ? 'sent successfully' : 'failed',
            $masked
        );

        self::info( $message, [
            'to'      => $masked,
            'subject' => mb_substr( (string) $subject, 0, 80 ),
            'success' => $success,
        ] );
    }

    /**
     * Maschera un indirizzo email per i log (es. jo***@example.com).
     */
    private static function mask_email( string $email ): string {
        $parts = explode( '@', $email, 2 );
        if ( count( $parts ) !== 2 ) {
            return '***';
        }
        $local   = $parts[0];
        $domain  = $parts[1];
        $visible = min( 2, strlen( $local ) );
        return substr( $local, 0, $visible ) . str_repeat( '*', max( 1, strlen( $local ) - $visible ) ) . '@' . $domain;
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
        
        $date = $date ? $date : current_time( 'Y-m-d' );
        
        // Prevenzione path traversal: accetta solo date nel formato YYYY-MM-DD
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return '';
        }
        
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

