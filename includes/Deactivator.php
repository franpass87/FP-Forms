<?php
namespace FPForms;

/**
 * Gestisce la disattivazione del plugin
 */
class Deactivator {
    
    /**
     * Azioni da eseguire alla disattivazione
     */
    public static function deactivate() {
        // Log disattivazione
        \FPForms\Core\Logger::info( 'FP Forms deactivated' );
        
        // Flush cache
        \FPForms\Core\Cache::flush();
        
        // Pulisci vecchi log (opzionale, solo se più vecchi di 90 giorni)
        \FPForms\Core\Logger::clean_old_logs( 90 );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

