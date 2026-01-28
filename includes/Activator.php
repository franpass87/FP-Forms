<?php
namespace FPForms;

/**
 * Gestisce l'attivazione del plugin
 */
class Activator {
    
    /**
     * Azioni da eseguire all'attivazione
     */
    public static function activate() {
        // Crea le tabelle del database
        self::create_tables();
        
        // Imposta le opzioni di default
        self::set_default_options();
        
        // Aggiungi capabilities
        \FPForms\Core\Capabilities::add_capabilities();
        
        // Inizializza Logger
        \FPForms\Core\Logger::init();
        
        // Log attivazione
        \FPForms\Core\Logger::info( 'FP Forms activated successfully' );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Crea le tabelle del database
     */
    private static function create_tables() {
        self::create_transactions_table();
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabella per le submissions
        $table_submissions = $wpdb->prefix . 'fp_forms_submissions';
        
        $sql_submissions = "CREATE TABLE IF NOT EXISTS {$table_submissions} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            data longtext NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_ip varchar(100) DEFAULT NULL,
            user_agent varchar(255) DEFAULT NULL,
            status varchar(20) DEFAULT 'unread',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY form_id (form_id),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";
        
        // Tabella per i campi dei form
        $table_fields = $wpdb->prefix . 'fp_forms_fields';
        
        $sql_fields = "CREATE TABLE IF NOT EXISTS {$table_fields} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            field_type varchar(50) NOT NULL,
            field_label varchar(255) NOT NULL,
            field_name varchar(255) NOT NULL,
            field_options longtext DEFAULT NULL,
            field_order int(11) DEFAULT 0,
            is_required tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY form_id (form_id),
            KEY field_order (field_order)
        ) {$charset_collate};";
        
        // Tabella per i file upload (v1.1)
        $table_files = $wpdb->prefix . 'fp_forms_files';
        
        $sql_files = "CREATE TABLE IF NOT EXISTS {$table_files} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            submission_id bigint(20) NOT NULL,
            field_name varchar(255) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_url varchar(500) NOT NULL,
            file_type varchar(100) NOT NULL,
            file_size bigint(20) NOT NULL,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY submission_id (submission_id),
            KEY field_name (field_name)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_submissions );
        dbDelta( $sql_fields );
        dbDelta( $sql_files );
    }
    
    /**
     * Crea tabella transazioni per pagamenti
     */
    private static function create_transactions_table() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'fp_forms_transactions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            submission_id bigint(20) UNSIGNED NOT NULL,
            form_id bigint(20) UNSIGNED NOT NULL,
            provider varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL,
            transaction_id varchar(255) DEFAULT '',
            metadata longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY submission_id (submission_id),
            KEY form_id (form_id),
            KEY status (status)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * Imposta le opzioni di default
     */
    private static function set_default_options() {
        $default_options = [
            'fp_forms_version' => FP_FORMS_VERSION,
            'fp_forms_recaptcha_enabled' => false,
            'fp_forms_email_from_name' => get_bloginfo( 'name' ),
            'fp_forms_email_from_address' => get_bloginfo( 'admin_email' ),
        ];
        
        foreach ( $default_options as $key => $value ) {
            if ( get_option( $key ) === false ) {
                add_option( $key, $value );
            }
        }
    }
}

