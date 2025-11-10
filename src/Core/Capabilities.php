<?php
namespace FPForms\Core;

/**
 * Gestione capabilities e permessi
 */
class Capabilities {
    
    /**
     * Capability per gestire form
     */
    const MANAGE_FORMS = 'manage_fp_forms';
    
    /**
     * Capability per vedere submissions
     */
    const VIEW_SUBMISSIONS = 'view_fp_forms_submissions';
    
    /**
     * Capability per gestire impostazioni
     */
    const MANAGE_SETTINGS = 'manage_fp_forms_settings';
    
    /**
     * Verifica se utente può gestire form
     */
    public static function can_manage_forms() {
        return current_user_can( 'manage_options' ) || current_user_can( self::MANAGE_FORMS );
    }
    
    /**
     * Verifica se utente può vedere submissions
     */
    public static function can_view_submissions() {
        return current_user_can( 'manage_options' ) || current_user_can( self::VIEW_SUBMISSIONS );
    }
    
    /**
     * Verifica se utente può gestire impostazioni
     */
    public static function can_manage_settings() {
        return current_user_can( 'manage_options' ) || current_user_can( self::MANAGE_SETTINGS );
    }
    
    /**
     * Verifica capability o muori
     */
    public static function check_or_die( $capability ) {
        if ( ! current_user_can( $capability ) ) {
            wp_die( __( 'Non hai i permessi per eseguire questa azione.', 'fp-forms' ) );
        }
    }
    
    /**
     * Aggiunge capabilities ai ruoli
     */
    public static function add_capabilities() {
        $admin = get_role( 'administrator' );
        
        if ( $admin ) {
            $admin->add_cap( self::MANAGE_FORMS );
            $admin->add_cap( self::VIEW_SUBMISSIONS );
            $admin->add_cap( self::MANAGE_SETTINGS );
        }
        
        $editor = get_role( 'editor' );
        
        if ( $editor ) {
            $editor->add_cap( self::MANAGE_FORMS );
            $editor->add_cap( self::VIEW_SUBMISSIONS );
        }
    }
    
    /**
     * Garantisce che i ruoli principali dispongano delle capability richieste.
     * Serve a coprire i casi in cui il plugin venga aggiornato senza riattivazione.
     */
    public static function ensure_default_capabilities(): void {
        static $checked = false;

        if ( $checked ) {
            return;
        }

        $checked = true;

        if ( ! function_exists( 'wp_roles' ) ) {
            return;
        }

        $wp_roles = wp_roles();

        if ( ! $wp_roles instanceof \WP_Roles ) {
            return;
        }

        $admin  = get_role( 'administrator' );
        $editor = get_role( 'editor' );

        $admin_missing_caps  = ! $admin || ! $admin->has_cap( self::MANAGE_FORMS ) || ! $admin->has_cap( self::VIEW_SUBMISSIONS ) || ! $admin->has_cap( self::MANAGE_SETTINGS );
        $editor_missing_caps = ! $editor || ! $editor->has_cap( self::MANAGE_FORMS ) || ! $editor->has_cap( self::VIEW_SUBMISSIONS );

        if ( $admin_missing_caps || $editor_missing_caps ) {
            self::add_capabilities();
        }
    }
    
    /**
     * Garantisce che gli utenti con privilegi elevati ereditino le capability personalizzate.
     */
    public static function register_capability_bridge(): void {
        add_filter(
            'user_has_cap',
            static function ( $allcaps, $caps ) {
                if ( empty( $caps ) ) {
                    return $allcaps;
                }

                $has_manage_options = isset( $allcaps['manage_options'] ) && $allcaps['manage_options'];

                if ( ! $has_manage_options ) {
                    return $allcaps;
                }

                $targets = [
                    self::MANAGE_FORMS,
                    self::VIEW_SUBMISSIONS,
                    self::MANAGE_SETTINGS,
                ];

                foreach ( $targets as $target ) {
                    if ( in_array( $target, $caps, true ) ) {
                        $allcaps[ $target ] = true;
                    }
                }

                return $allcaps;
            },
            10,
            3
        );
    }
    
    /**
     * Rimuove capabilities dai ruoli
     */
    public static function remove_capabilities() {
        $roles = [ 'administrator', 'editor' ];
        
        foreach ( $roles as $role_name ) {
            $role = get_role( $role_name );
            
            if ( $role ) {
                $role->remove_cap( self::MANAGE_FORMS );
                $role->remove_cap( self::VIEW_SUBMISSIONS );
                $role->remove_cap( self::MANAGE_SETTINGS );
            }
        }
    }
}

