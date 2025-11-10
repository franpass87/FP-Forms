<?php
namespace FPForms\Core;

/**
 * Cache Manager per performance
 */
class Cache {
    
    /**
     * Prefisso cache
     */
    const PREFIX = 'fp_forms_';
    
    /**
     * Gruppo cache
     */
    const GROUP = 'fp_forms';
    
    /**
     * Durata default cache (1 ora)
     */
    const DEFAULT_EXPIRATION = 3600;
    
    /**
     * Ottiene valore dalla cache
     */
    public static function get( $key, $default = null ) {
        $value = wp_cache_get( self::PREFIX . $key, self::GROUP );
        
        if ( $value === false ) {
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Imposta valore in cache
     */
    public static function set( $key, $value, $expiration = self::DEFAULT_EXPIRATION ) {
        return wp_cache_set( self::PREFIX . $key, $value, self::GROUP, $expiration );
    }
    
    /**
     * Elimina valore dalla cache
     */
    public static function delete( $key ) {
        return wp_cache_delete( self::PREFIX . $key, self::GROUP );
    }
    
    /**
     * Flush cache gruppo
     */
    public static function flush() {
        return wp_cache_flush_group( self::GROUP );
    }
    
    /**
     * Ottiene form dalla cache
     */
    public static function get_form( $form_id ) {
        return self::get( 'form_' . $form_id );
    }
    
    /**
     * Salva form in cache
     */
    public static function set_form( $form_id, $form_data ) {
        return self::set( 'form_' . $form_id, $form_data );
    }
    
    /**
     * Invalida cache form
     */
    public static function invalidate_form( $form_id ) {
        self::delete( 'form_' . $form_id );
        self::delete( 'form_fields_' . $form_id );
    }
    
    /**
     * Ottiene fields dalla cache
     */
    public static function get_form_fields( $form_id ) {
        return self::get( 'form_fields_' . $form_id );
    }
    
    /**
     * Salva fields in cache
     */
    public static function set_form_fields( $form_id, $fields ) {
        return self::set( 'form_fields_' . $form_id, $fields );
    }
    
    /**
     * Ottiene submissions count dalla cache
     */
    public static function get_submissions_count( $form_id, $status = '' ) {
        $key = 'submissions_count_' . $form_id . '_' . $status;
        return self::get( $key );
    }
    
    /**
     * Salva submissions count in cache
     */
    public static function set_submissions_count( $form_id, $count, $status = '' ) {
        $key = 'submissions_count_' . $form_id . '_' . $status;
        return self::set( $key, $count, 300 ); // 5 minuti
    }
    
    /**
     * Invalida cache submissions
     */
    public static function invalidate_submissions( $form_id ) {
        self::delete( 'submissions_count_' . $form_id . '_' );
        self::delete( 'submissions_count_' . $form_id . '_unread' );
        self::delete( 'submissions_count_' . $form_id . '_read' );
    }
    
    /**
     * Remember - Ottiene o genera valore
     */
    public static function remember( $key, $callback, $expiration = self::DEFAULT_EXPIRATION ) {
        $value = self::get( $key );
        
        if ( $value !== null ) {
            return $value;
        }
        
        $value = call_user_func( $callback );
        self::set( $key, $value, $expiration );
        
        return $value;
    }
}

