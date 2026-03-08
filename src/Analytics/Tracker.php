<?php
namespace FPForms\Analytics;

/**
 * Analytics Tracker per form
 */
class Tracker {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Hook per trackare views
        add_action( 'fp_forms_form_rendered', [ $this, 'track_view' ] );
    }
    
    /**
     * Traccia visualizzazione form
     */
    public function track_view( $form_id ) {
        // Non trackare in admin o AJAX
        if ( is_admin() || wp_doing_ajax() ) {
            return;
        }
        
        // Non trackare crawler
        if ( $this->is_crawler() ) {
            return;
        }
        
        // Increment view count
        $this->increment_views( $form_id );
        
        // Track daily views (per grafico)
        $this->track_daily_view( $form_id );
    }
    
    /**
     * Incrementa contatore views con query SQL atomica per evitare race condition.
     * UPDATE ... SET value = value + 1 è atomico a livello di riga in InnoDB.
     */
    private function increment_views( $form_id ) {
        global $wpdb;

        $form_id  = (int) $form_id;
        $meta_key = '_fp_form_views_total';

        // Prova prima l'UPDATE atomico (riga già esistente).
        // Se non aggiorna nessuna riga, il meta non esiste ancora: inseriscilo.
        // Questo evita la race condition SELECT-then-INSERT.
        $updated = $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
            $form_id,
            $meta_key
        ) );

        if ( $updated === 0 ) {
            // Nessuna riga aggiornata: inserisci con unique=true per gestire
            // la race condition nel caso due richieste arrivino contemporaneamente.
            $inserted = add_post_meta( $form_id, $meta_key, 1, true );
            if ( $inserted === false ) {
                // Un'altra richiesta ha già inserito il meta: aggiorna ora.
                $wpdb->query( $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
                    $form_id,
                    $meta_key
                ) );
            }
        }
    }
    
    /**
     * Traccia views giornaliere con query SQL atomica per evitare race condition.
     */
    private function track_daily_view( $form_id ) {
        global $wpdb;

        $form_id  = (int) $form_id;
        $meta_key = '_fp_form_views_' . current_time( 'Y-m-d' );

        // Stesso pattern UPDATE-first per evitare race condition.
        $updated = $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
            $form_id,
            $meta_key
        ) );

        if ( $updated === 0 ) {
            $inserted = add_post_meta( $form_id, $meta_key, 1, true );
            if ( $inserted === false ) {
                $wpdb->query( $wpdb->prepare(
                    "UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
                    $form_id,
                    $meta_key
                ) );
            }
        }
        
        // Cleanup vecchie date (>30 giorni)
        $this->cleanup_old_data( $form_id );
    }
    
    /**
     * Ottiene views totali
     */
    public function get_total_views( $form_id ) {
        return (int) get_post_meta( $form_id, '_fp_form_views_total', true );
    }
    
    /**
     * Ottiene conversion rate
     */
    public function get_conversion_rate( $form_id ) {
        $views = $this->get_total_views( $form_id );
        
        if ( $views === 0 ) {
            return 0;
        }
        
        $submissions = \FPForms\Plugin::instance()->database->count_submissions( $form_id );
        
        return round( ( $submissions / $views ) * 100, 2 );
    }
    
    /**
     * Ottiene views ultimi 7 giorni
     */
    public function get_week_views( $form_id ) {
        $views = [];
        
        for ( $i = 6; $i >= 0; $i-- ) {
            $date = wp_date( 'Y-m-d', strtotime( "-{$i} days" ) );
            $key = '_fp_form_views_' . $date;
            $views[ $date ] = (int) get_post_meta( $form_id, $key, true );
        }
        
        return $views;
    }
    
    /**
     * Ottiene submissions ultimi 7 giorni
     */
    public function get_week_submissions( $form_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'fp_forms_submissions';
        
        $submissions = [];
        
        for ( $i = 6; $i >= 0; $i-- ) {
            $date = wp_date( 'Y-m-d', strtotime( "-{$i} days" ) );
            
            $count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} 
                WHERE form_id = %d 
                AND DATE(created_at) = %s",
                $form_id,
                $date
            ) );
            
            $submissions[ $date ] = (int) $count;
        }
        
        return $submissions;
    }
    
    /**
     * Ottiene statistiche complete
     */
    public function get_stats( $form_id ) {
        $views = $this->get_total_views( $form_id );
        $submissions = \FPForms\Plugin::instance()->database->count_submissions( $form_id );
        
        return [
            'total_views' => $views,
            'total_submissions' => $submissions,
            'conversion_rate' => $this->get_conversion_rate( $form_id ),
            'week_views' => $this->get_week_views( $form_id ),
            'week_submissions' => $this->get_week_submissions( $form_id ),
            'avg_time' => $this->get_average_completion_time( $form_id ),
        ];
    }
    
    /**
     * Ottiene tempo medio compilazione
     */
    private function get_average_completion_time( $form_id ) {
        // Per ora ritorna valore fisso, implementazione completa richiede tracking JS
        return 0;
    }
    
    /**
     * Verifica se è un crawler
     */
    private function is_crawler() {
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        $crawlers = [ 'bot', 'crawl', 'spider', 'slurp', 'yahoo' ];
        
        foreach ( $crawlers as $crawler ) {
            if ( stripos( $user_agent, $crawler ) !== false ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Cleanup dati vecchi
     */
    private function cleanup_old_data( $form_id ) {
        // Solo 1 volta su 100 per non impattare performance
        if ( wp_rand( 1, 100 ) !== 1 ) {
            return;
        }
        
        global $wpdb;
        
        // Elimina meta views più vecchie di 30 giorni (usa wp_date per rispettare il timezone WP)
        $cutoff_date = wp_date( 'Y-m-d', strtotime( '-30 days' ) );
        
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} 
            WHERE post_id = %d 
            AND meta_key LIKE '_fp_form_views_%%' 
            AND meta_key < %s",
            $form_id,
            '_fp_form_views_' . $cutoff_date
        ) );
    }
}

