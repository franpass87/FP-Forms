<?php
/**
 * Uninstall FP Forms
 * 
 * Pulizia dati quando plugin viene disinstallato
 * GDPR Compliance: Rimuove tutti i dati salvati
 * 
 * @package FPForms
 * @since 1.2.3
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete all plugin data
 */
function fp_forms_uninstall_cleanup() {
    global $wpdb;
    
    // 1. Delete all forms (custom post type: fp_form)
    $forms = get_posts( [
        'post_type' => 'fp_form',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ] );
    
    foreach ( $forms as $form ) {
        wp_delete_post( $form->ID, true ); // Force delete, bypass trash
    }
    
    // 2. Delete all submissions (custom post type: fp_submission)
    $submissions = get_posts( [
        'post_type' => 'fp_submission',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ] );
    
    foreach ( $submissions as $submission ) {
        wp_delete_post( $submission->ID, true ); // Force delete
    }
    
    // 3. Delete all plugin options
    $options_to_delete = [
        'fp_forms_version',
        'fp_forms_recaptcha_settings',
        'fp_forms_tracking_settings',
        'fp_forms_brevo_settings',
        'fp_forms_meta_settings',
        'fp_forms_db_version',
    ];
    
    foreach ( $options_to_delete as $option ) {
        delete_option( $option );
    }
    
    // 4. Delete all transients
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_fp_forms_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_fp_forms_%'" );
    
    // 5. Delete uploaded files (GDPR - remove user data)
    $upload_dir = wp_upload_dir();
    $fp_forms_upload_dir = $upload_dir['basedir'] . '/fp-forms-uploads';
    
    if ( file_exists( $fp_forms_upload_dir ) ) {
        fp_forms_delete_directory( $fp_forms_upload_dir );
    }
    
    // 6. Delete custom tables (se esistono)
    // Attualmente non usiamo custom tables, ma per future-proofing:
    $table_name = $wpdb->prefix . 'fp_forms_submissions';
    $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
    
    // 7. Clear any cached data
    wp_cache_flush();
}

/**
 * Recursively delete directory
 */
function fp_forms_delete_directory( $dir ) {
    if ( ! file_exists( $dir ) ) {
        return;
    }
    
    $files = array_diff( scandir( $dir ), [ '.', '..' ] );
    
    foreach ( $files as $file ) {
        $path = $dir . '/' . $file;
        
        if ( is_dir( $path ) ) {
            fp_forms_delete_directory( $path );
        } else {
            @unlink( $path );
        }
    }
    
    @rmdir( $dir );
}

// Execute cleanup
fp_forms_uninstall_cleanup();








