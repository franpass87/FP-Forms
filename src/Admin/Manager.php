<?php
declare(strict_types=1);

namespace FPForms\Admin;

use FPForms\Core\Capabilities;

/**
 * Gestisce l'area admin del plugin
 */
class Manager {
    
    /**
     * Costruttore
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu_pages' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        
        // AJAX handlers
        add_action( 'wp_ajax_fp_forms_save_form', [ $this, 'ajax_save_form' ] );
        add_action( 'wp_ajax_fp_forms_delete_form', [ $this, 'ajax_delete_form' ] );
        add_action( 'wp_ajax_fp_forms_duplicate_form', [ $this, 'ajax_duplicate_form' ] );
        add_action( 'wp_ajax_fp_forms_delete_submission', [ $this, 'ajax_delete_submission' ] );
        
        // Nuovi AJAX handlers v1.1
        add_action( 'wp_ajax_fp_forms_export_submissions', [ $this, 'ajax_export_submissions' ] );
        add_action( 'wp_ajax_fp_forms_import_template', [ $this, 'ajax_import_template' ] );
        add_action( 'wp_ajax_fp_forms_get_templates', [ $this, 'ajax_get_templates' ] );
        add_action( 'wp_ajax_fp_forms_get_submission_details', [ $this, 'ajax_get_submission_details' ] );
        
        // AJAX handlers v1.2 (migliorie)
        add_action( 'wp_ajax_fp_forms_bulk_action_submissions', [ $this, 'ajax_bulk_action_submissions' ] );
        add_action( 'wp_ajax_fp_forms_export_form_config', [ $this, 'ajax_export_form_config' ] );
        add_action( 'wp_ajax_fp_forms_test_recaptcha', [ $this, 'ajax_test_recaptcha' ] );
        add_action( 'wp_ajax_fp_forms_test_meta', [ $this, 'ajax_test_meta' ] );
        add_action( 'wp_ajax_fp_forms_run_simulation', [ $this, 'ajax_run_simulation' ] );
        add_action( 'wp_ajax_fp_forms_test_webhook', [ $this, 'ajax_test_webhook' ] );
        add_action( 'wp_ajax_fp_forms_restore_snapshot', [ $this, 'ajax_restore_snapshot' ] );
        add_action( 'wp_ajax_fp_forms_import_form_config', [ $this, 'ajax_import_form_config' ] );
        add_action( 'wp_ajax_fp_forms_test_smtp', [ $this, 'ajax_test_smtp' ] );
    }
    
    /**
     * Aggiunge le pagine menu in admin
     */
    public function add_menu_pages() {
        // Menu principale
        add_menu_page(
            __( 'FP Forms', 'fp-forms' ),
            __( 'FP Forms', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms',
            [ $this, 'render_forms_page' ],
            'dashicons-feedback',
            30
        );
        
        // Sottomenu: Tutti i form
        add_submenu_page(
            'fp-forms',
            __( 'Tutti i Form', 'fp-forms' ),
            __( 'Tutti i Form', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms',
            [ $this, 'render_forms_page' ]
        );
        
        // Sottomenu: Nuovo form
        add_submenu_page(
            'fp-forms',
            __( 'Nuovo Form', 'fp-forms' ),
            __( 'Nuovo Form', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms-new',
            [ $this, 'render_new_form_page' ]
        );
        
        // Sottomenu: Template Library
        add_submenu_page(
            'fp-forms',
            __( 'Template Library', 'fp-forms' ),
            __( 'Template', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms-templates',
            [ $this, 'render_templates_page' ]
        );
        
        // Sottomenu: Modifica form (nascosto)
        add_submenu_page(
            'fp-forms',
            __( 'Modifica Form', 'fp-forms' ),
            __( 'Modifica Form', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms-edit',
            [ $this, 'render_edit_form_page' ]
        );

        add_action(
            'admin_head',
            static function () {
                echo '<style>#toplevel_page_fp-forms .wp-submenu a[href="admin.php?page=fp-forms-edit"]{display:none;}</style>';
            }
        );

        // Sottomenu: Submissions (stessa capability del menu Form: chi gestisce i form vede le submissions)
        add_submenu_page(
            'fp-forms',
            __( 'Submissions', 'fp-forms' ),
            __( 'Submissions', 'fp-forms' ),
            Capabilities::MANAGE_FORMS,
            'fp-forms-submissions',
            [ $this, 'render_submissions_page' ]
        );

        // Sottomenu: Analytics (nascosto)
        add_submenu_page(
            'fp-forms',
            __( 'Analytics', 'fp-forms' ),
            __( 'Analytics', 'fp-forms' ),
            Capabilities::VIEW_SUBMISSIONS,
            'fp-forms-analytics',
            [ $this, 'render_analytics_page' ]
        );
        remove_submenu_page( 'fp-forms', 'fp-forms-analytics' );
        
        // Sottomenu: Impostazioni
        add_submenu_page(
            'fp-forms',
            __( 'Impostazioni', 'fp-forms' ),
            __( 'Impostazioni', 'fp-forms' ),
            Capabilities::MANAGE_SETTINGS,
            'fp-forms-settings',
            [ $this, 'render_settings_page' ]
        );
    }
    
    /**
     * Carica assets admin
     */
    public function enqueue_assets( $hook ) {
        // Carica solo nelle nostre pagine (compat menu parent + fallback $_GET)
        $is_our_page = ( strpos( $hook, 'fp-forms' ) !== false )
            || ( isset( $_GET['page'] ) && strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'fp-forms' ) === 0 );
        if ( ! $is_our_page ) {
            return;
        }
        
        // Aggiungi body class per admin shell
        add_filter( 'admin_body_class', function( $classes ) {
            return $classes . ' fp-forms-admin-shell';
        } );
        
        wp_enqueue_media();

        $admin_css_path = FP_FORMS_PLUGIN_DIR . 'assets/css/admin.css';
        $admin_css_version = file_exists( $admin_css_path ) ? (string) filemtime( $admin_css_path ) : FP_FORMS_VERSION;

        // CSS
        wp_enqueue_style(
            'fp-forms-admin',
            FP_FORMS_PLUGIN_URL . 'assets/css/admin.css',
            [],
            $admin_css_version
        );
        
        wp_enqueue_style(
            'fp-forms-toast',
            FP_FORMS_PLUGIN_URL . 'assets/css/toast.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_enqueue_style(
            'fp-forms-loading',
            FP_FORMS_PLUGIN_URL . 'assets/css/loading-states.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_enqueue_style(
            'fp-forms-ui-enhancements',
            FP_FORMS_PLUGIN_URL . 'assets/css/ui-enhancements.css',
            [],
            FP_FORMS_VERSION
        );
        
        wp_enqueue_style(
            'fp-forms-modal',
            FP_FORMS_PLUGIN_URL . 'assets/css/modal-confirm.css',
            [],
            FP_FORMS_VERSION
        );
        
        // JS
        wp_register_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            [],
            '4.4.0',
            true
        );
        
        // Carica Chart.js solo nella pagina analytics
        if ( strpos( $hook, 'fp-forms-analytics' ) !== false ) {
            wp_enqueue_script( 'chartjs' );
        }
        
        wp_enqueue_script(
            'fp-forms-toast',
            FP_FORMS_PLUGIN_URL . 'assets/js/toast.js',
            [ 'jquery' ],
            FP_FORMS_VERSION,
            true
        );
        
        wp_enqueue_script(
            'fp-forms-loading',
            FP_FORMS_PLUGIN_URL . 'assets/js/loading-states.js',
            [ 'jquery' ],
            FP_FORMS_VERSION,
            true
        );
        
        wp_enqueue_script(
            'fp-forms-modal',
            FP_FORMS_PLUGIN_URL . 'assets/js/modal-confirm.js',
            [ 'jquery' ],
            FP_FORMS_VERSION,
            true
        );
        
        wp_enqueue_script(
            'fp-forms-admin',
            FP_FORMS_PLUGIN_URL . 'assets/js/admin.js',
            [ 'jquery', 'jquery-ui-sortable', 'fp-forms-toast', 'fp-forms-loading', 'fp-forms-modal' ],
            FP_FORMS_VERSION,
            true
        );
        
        // Localizza script
        wp_localize_script( 'fp-forms-admin', 'fpFormsAdmin', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'fp_forms_admin' ),
            'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
            'editFormUrlBase' => admin_url( 'admin.php?page=fp-forms-edit&form_id=' ),
            'strings' => [
                'confirmDelete' => __( 'Sei sicuro di voler eliminare questo elemento?', 'fp-forms' ),
                'error' => __( 'Si è verificato un errore. Riprova.', 'fp-forms' ),
                'saved' => __( 'Salvato con successo!', 'fp-forms' ),
            ],
        ] );
    }
    
    /**
     * Renderizza pagina lista form
     */
    public function render_forms_page() {
        if ( ! Capabilities::can_manage_forms() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $forms = \FPForms\Plugin::instance()->forms->get_forms();
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/forms-list.php';
    }
    
    /**
     * Renderizza pagina nuovo form
     */
    public function render_new_form_page() {
        if ( ! Capabilities::can_manage_forms() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        include FP_FORMS_PLUGIN_DIR . 'templates/admin/form-builder.php';
    }
    
    /**
     * Renderizza pagina modifica form
     */
    public function render_edit_form_page() {
        if ( ! Capabilities::can_manage_forms() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_die( __( 'Form non trovato.', 'fp-forms' ) );
        }
        
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            wp_die( __( 'Form non trovato.', 'fp-forms' ) );
        }
        
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/form-builder.php';
    }
    
    /**
     * Renderizza pagina submissions
     */
    public function render_submissions_page() {
        if ( ! Capabilities::can_manage_forms() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            $forms = \FPForms\Plugin::instance()->forms->get_forms();
            $db    = \FPForms\Plugin::instance()->database;

            $forms_data = [];
            foreach ( $forms as $form ) {
                $total  = $db->count_submissions( $form['id'] );
                $unread = $db->count_submissions( $form['id'], 'unread' );
                $last   = $db->get_last_submission_date( $form['id'] );

                $forms_data[] = [
                    'id'               => $form['id'],
                    'title'            => $form['title'],
                    'total'            => $total,
                    'unread'           => $unread,
                    'last_submission'  => $last,
                ];
            }

            include FP_FORMS_PLUGIN_DIR . 'templates/admin/submissions-overview.php';
            return;
        }
        
        // Pagination
        $per_page = 20;
        $page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $page - 1 ) * $per_page;
        
        // Search
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        
        // Filter status
        $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
        
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        // Prepara args per get_submissions()
        $args = [
            'status' => $status_filter,
            'limit' => $per_page,
            'offset' => $offset,
            'search' => $search,
        ];
        
        $submissions = \FPForms\Plugin::instance()->database->get_submissions( $form_id, $args );
        $total_submissions = \FPForms\Plugin::instance()->database->count_submissions( $form_id, $status_filter, $search );
        $total_pages = ceil( $total_submissions / $per_page );
        
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/submissions-list.php';
    }
    
    /**
     * Renderizza pagina template library
     */
    public function render_templates_page() {
        if ( ! Capabilities::can_manage_forms() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $library = new \FPForms\Templates\Library();
        $templates = $library->get_templates();
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/templates-library.php';
    }
    
    /**
     * Renderizza pagina analytics
     */
    public function render_analytics_page() {
        if ( ! Capabilities::can_view_submissions() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_die( __( 'Form non specificato.', 'fp-forms' ) );
        }
        
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        $tracker = \FPForms\Plugin::instance()->analytics;
        $stats = $tracker->get_stats( $form_id );
        
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/analytics.php';
    }
    
    /**
     * Renderizza pagina impostazioni
     */
    public function render_settings_page() {
        if ( ! Capabilities::can_manage_settings() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        // Salva impostazioni se submit
        if ( isset( $_POST['fp_forms_settings_submit'] ) ) {
            check_admin_referer( 'fp_forms_settings' );
            
            update_option( 'fp_forms_email_from_name', sanitize_text_field( $_POST['email_from_name'] ) );
            update_option( 'fp_forms_email_from_address', sanitize_email( $_POST['email_from_address'] ) );
            
            // Salva impostazioni template email
            update_option( 'fp_forms_email_logo_url', esc_url_raw( $_POST['email_logo_url'] ?? '' ) );
            $accent = sanitize_text_field( $_POST['email_accent_color'] ?? '#3b82f6' );
            update_option( 'fp_forms_email_accent_color', preg_match( '/^#[0-9A-Fa-f]{6}$/', $accent ) ? $accent : '#3b82f6' );
            update_option( 'fp_forms_email_response_time', sanitize_text_field( $_POST['email_response_time'] ?? '' ) );
            update_option( 'fp_forms_email_footer_text', sanitize_textarea_field( $_POST['email_footer_text'] ?? '' ) );
            
            // Salva impostazioni SMTP
            $smtp_settings = [
                'enabled'    => isset( $_POST['smtp_enabled'] ),
                'host'       => sanitize_text_field( $_POST['smtp_host'] ?? '' ),
                'port'       => intval( $_POST['smtp_port'] ?? 587 ),
                'encryption' => sanitize_text_field( $_POST['smtp_encryption'] ?? 'tls' ),
                'auth'       => isset( $_POST['smtp_auth'] ),
                'username'   => sanitize_text_field( $_POST['smtp_username'] ?? '' ),
                'password'   => sanitize_text_field( $_POST['smtp_password'] ?? '' ),
            ];
            // Validazione encryption
            if ( ! in_array( $smtp_settings['encryption'], [ 'tls', 'ssl', 'none' ], true ) ) {
                $smtp_settings['encryption'] = 'tls';
            }
            // Validazione porta
            if ( $smtp_settings['port'] < 1 || $smtp_settings['port'] > 65535 ) {
                $smtp_settings['port'] = 587;
            }
            // Prevenzione SSRF: blocca host SMTP che risolvono a IP privati/riservati
            $smtp_host = $smtp_settings['host'];
            if ( ! empty( $smtp_host ) ) {
                if ( filter_var( $smtp_host, FILTER_VALIDATE_IP ) ) {
                    // IP letterale: blocca se privato/riservato
                    if ( filter_var( $smtp_host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
                        $smtp_settings['host'] = '';
                    }
                } else {
                    // Hostname: risolvi e verifica
                    $resolved = gethostbyname( $smtp_host );
                    if ( $resolved !== $smtp_host && filter_var( $resolved, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
                        $smtp_settings['host'] = '';
                    }
                }
            }
            update_option( 'fp_forms_smtp_settings', $smtp_settings );

            update_option( 'fp_forms_email_queue_enabled', isset( $_POST['email_queue_enabled'] ) ? '1' : '0' );
            $rate_max = isset( $_POST['email_rate_limit_max'] ) ? absint( $_POST['email_rate_limit_max'] ) : 50;
            update_option( 'fp_forms_email_rate_limit_max', $rate_max > 0 ? $rate_max : 0 );
            $spam_threshold = isset( $_POST['spam_score_threshold'] ) ? absint( $_POST['spam_score_threshold'] ) : 80;
            $spam_threshold = max( 1, min( 100, $spam_threshold ) );
            update_option( 'fp_forms_spam_score_threshold', $spam_threshold );

            $stripe_settings = [
                'secret_key'     => isset( $_POST['stripe_secret_key'] ) ? sanitize_text_field( wp_unslash( $_POST['stripe_secret_key'] ) ) : '',
                'publishable_key' => isset( $_POST['stripe_publishable_key'] ) ? sanitize_text_field( wp_unslash( $_POST['stripe_publishable_key'] ) ) : '',
                'webhook_secret' => isset( $_POST['stripe_webhook_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['stripe_webhook_secret'] ) ) : '',
            ];
            update_option( 'fp_forms_stripe_settings', $stripe_settings );
            
            // Salva impostazioni reCAPTCHA (nuovo formato 2025)
            $recaptcha_settings = [
                'version' => sanitize_text_field( $_POST['recaptcha_version'] ?? 'v2' ),
                'site_key' => sanitize_text_field( $_POST['recaptcha_site_key'] ?? '' ),
                'secret_key' => sanitize_text_field( $_POST['recaptcha_secret_key'] ?? '' ),
                'min_score' => floatval( $_POST['recaptcha_min_score'] ?? 0.5 ),
            ];
            update_option( 'fp_forms_recaptcha_settings', $recaptcha_settings );
            
            // Salva impostazioni Tracking (GTM & GA4)
            $tracking_settings = [
                'gtm_id' => sanitize_text_field( $_POST['gtm_id'] ?? '' ),
                'ga4_id' => sanitize_text_field( $_POST['ga4_id'] ?? '' ),
                'track_views' => isset( $_POST['track_views'] ),
                'track_interactions' => isset( $_POST['track_interactions'] ),
                'debug_mode' => isset( $_POST['track_debug_mode'] ),
            ];
            update_option( 'fp_forms_tracking_settings', $tracking_settings );
            
            // Salva impostazioni Brevo (solo opzioni Forms-specifiche; API key e liste sono in FP Tracking)
            $existing_brevo = get_option( 'fp_forms_brevo_settings', [] );
            $existing_brevo = is_array( $existing_brevo ) ? $existing_brevo : [];
            $brevo_settings = array_merge( $existing_brevo, [
                'double_optin' => isset( $_POST['brevo_double_optin'] ),
                'track_events' => isset( $_POST['brevo_track_events'] ),
            ] );
            update_option( 'fp_forms_brevo_settings', $brevo_settings );
            
            // Salva impostazioni Meta Pixel
            $meta_settings = [
                'pixel_id' => sanitize_text_field( $_POST['meta_pixel_id'] ?? '' ),
                'access_token' => sanitize_text_field( $_POST['meta_access_token'] ?? '' ),
                'track_views' => isset( $_POST['meta_track_views'] ),
            ];
            update_option( 'fp_forms_meta_settings', $meta_settings );
            
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Impostazioni salvate!', 'fp-forms' ) . '</p></div>';
        }
        
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    /**
     * AJAX: Salva form
     */
    public function ajax_save_form() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id   = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
        $title     = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $description = isset( $_POST['description'] ) ? wp_kses_post( $_POST['description'] ) : '';
        $fields_json = isset( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : '[]';
        $fields_raw  = json_decode( $fields_json, true, 20 );
        if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $fields_raw ) ) {
            \FPForms\Core\Logger::error( 'ajax_save_form: fields JSON non valido', [ 'json_error' => json_last_error_msg() ] );
            wp_send_json_error( [ 'message' => __( 'Dati campi non validi. Ricarica la pagina e riprova.', 'fp-forms' ) ] );
        }
        // Sanitizza i campi con la stessa funzione usata per l'import
        $fields = $this->sanitize_imported_fields( $fields_raw );

        if ( $form_id > 0 && empty( $fields ) ) {
            $existing = \FPForms\Plugin::instance()->forms->get_fields( $form_id );
            if ( ! empty( $existing ) ) {
                \FPForms\Core\Logger::warning( 'ajax_save_form: campi vuoti su form con campi esistenti', [ 'form_id' => $form_id, 'raw_count' => count( $fields_raw ) ] );
                wp_send_json_error( [ 'message' => __( 'I campi non sono stati inviati. Ricarica la pagina e riprova.', 'fp-forms' ) ] );
            }
        }

        $settings_json = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '{}';
        $settings_raw  = json_decode( $settings_json, true, 20 );
        if ( ! is_array( $settings_raw ) ) {
            $settings_raw = [];
        }
        
        // Sanitize and validate settings before save
        $settings = $this->sanitize_form_settings( $settings_raw );
        
        $forms_manager = \FPForms\Plugin::instance()->forms;
        
        if ( $form_id ) {
            // Aggiorna form esistente
            $result = $forms_manager->update_form( $form_id, [
                'title' => $title,
                'description' => $description,
                'fields' => $fields,
                'settings' => $settings,
            ] );
            
            if ( $result ) {
                wp_send_json_success( [
                    'message' => __( 'Form aggiornato!', 'fp-forms' ),
                    'form_id' => $form_id,
                ] );
            }
            global $wpdb;
            $db_error = $wpdb->last_error ?: \FPForms\Database\Manager::$last_save_error;
            if ( $db_error ) {
                \FPForms\Core\Logger::error( 'ajax_save_form: update fallito', [ 'error' => $db_error, 'form_id' => $form_id ] );
            }
            $err_msg = $db_error
                ? sprintf( __( 'Errore DB: %s', 'fp-forms' ), esc_html( $db_error ) )
                : __( 'Errore nel salvare il form. Verifica che il form esista.', 'fp-forms' );
            wp_send_json_error( [ 'message' => $err_msg ] );
            return;
        } else {
            // Crea nuovo form
            $new_form_id = $forms_manager->create_form( $title, [
                'description' => $description,
                'fields' => $fields,
                'settings' => $settings,
            ] );
            
            if ( $new_form_id ) {
                wp_send_json_success( [
                    'message' => __( 'Form creato!', 'fp-forms' ),
                    'form_id' => $new_form_id,
                ] );
            }
        }
        
        wp_send_json_error( [ 'message' => __( 'Errore nel salvare il form.', 'fp-forms' ) ] );
    }
    
    /**
     * AJAX: Elimina form
     */
    public function ajax_delete_form() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }
        
        $result = \FPForms\Plugin::instance()->forms->delete_form( $form_id );
        
        if ( $result ) {
            wp_send_json_success( [ 'message' => __( 'Form eliminato!', 'fp-forms' ) ] );
        }
        
        wp_send_json_error( [ 'message' => __( 'Errore nell\'eliminare il form.', 'fp-forms' ) ] );
    }
    
    /**
     * AJAX: Duplica form
     */
    public function ajax_duplicate_form() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }
        
        $new_form_id = \FPForms\Plugin::instance()->forms->duplicate_form( $form_id );
        
        if ( $new_form_id ) {
            // Rimuovi webhook (con i loro secret) dal form duplicato per evitare invii duplicati
            // e per non propagare credenziali sensibili a contesti diversi.
            $new_form = \FPForms\Plugin::instance()->forms->get_form( $new_form_id );
            if ( $new_form ) {
                $new_settings             = $new_form['settings'] ?? [];
                $new_settings['webhooks'] = [];
                \FPForms\Plugin::instance()->forms->update_form( $new_form_id, [ 'settings' => $new_settings ] );
            }

            wp_send_json_success( [
                'message' => __( 'Form duplicato!', 'fp-forms' ),
                'form_id' => $new_form_id,
            ] );
        }
        
        wp_send_json_error( [ 'message' => __( 'Errore nel duplicare il form.', 'fp-forms' ) ] );
    }
    
    /**
     * AJAX: Elimina submission
     */
    public function ajax_delete_submission() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $submission_id = isset( $_POST['submission_id'] ) ? intval( $_POST['submission_id'] ) : 0;
        
        if ( ! $submission_id ) {
            wp_send_json_error( [ 'message' => __( 'Submission non valida.', 'fp-forms' ) ] );
        }
        
        $result = \FPForms\Plugin::instance()->submissions->delete_submission( $submission_id );
        
        if ( $result ) {
            wp_send_json_success( [ 'message' => __( 'Submission eliminata!', 'fp-forms' ) ] );
        }
        
        wp_send_json_error( [ 'message' => __( 'Errore nell\'eliminare la submission.', 'fp-forms' ) ] );
    }
    
    /**
     * AJAX: Export submissions
     */
    public function ajax_export_submissions() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'csv';
        
        $options = [
            'date_from' => isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '',
            'date_to' => isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '',
            'status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '',
            'fields' => isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? array_map( 'sanitize_text_field', $_POST['fields'] ) : [],
        ];
        
        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }
        
        // Export basato sul formato
        switch ( $format ) {
            case 'csv':
                $exporter = new \FPForms\Export\CsvExporter();
                $result   = $exporter->export( $form_id, $options );
                break;
                
            case 'xlsx':
            case 'excel':
                $exporter = new \FPForms\Export\ExcelExporter();
                $result   = $exporter->export( $form_id, $options );
                break;
                
            default:
                wp_send_json_error( [ 'message' => __( 'Formato export non supportato.', 'fp-forms' ) ] );
                return;
        }
        
        // Se l'export ha restituito WP_Error (es. nessun dato), invia l'errore al client
        if ( isset( $result ) && is_wp_error( $result ) ) {
            wp_send_json_error( [ 'message' => $result->get_error_message() ] );
        }
        
        // Il metodo export fa exit, quindi questo non verrÃ  mai eseguito
    }
    
    /**
     * AJAX: Importa template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        $custom_title = isset( $_POST['custom_title'] ) ? sanitize_text_field( $_POST['custom_title'] ) : '';
        
        if ( ! $template_id ) {
            wp_send_json_error( [ 'message' => __( 'Template non specificato.', 'fp-forms' ) ] );
        }
        
        $library = new \FPForms\Templates\Library();
        $form_id = $library->import_template( $template_id, $custom_title );
        
        if ( is_wp_error( $form_id ) ) {
            wp_send_json_error( [ 'message' => $form_id->get_error_message() ] );
        }
        
        if ( $form_id ) {
            wp_send_json_success( [
                'message' => __( 'Template importato con successo!', 'fp-forms' ),
                'form_id' => $form_id,
                'redirect' => admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form_id ),
            ] );
        }
        
        wp_send_json_error( [ 'message' => __( 'Errore nell\'importare il template.', 'fp-forms' ) ] );
    }
    
    /**
     * AJAX: Ottiene lista template
     */
    public function ajax_get_templates() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : null;
        
        $library = new \FPForms\Templates\Library();
        $templates = $library->get_templates( $category );
        
        wp_send_json_success( [ 'templates' => array_values( $templates ) ] );
    }
    
    /**
     * AJAX: Ottiene dettagli submission
     */
    public function ajax_get_submission_details() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $submission_id = isset( $_POST['submission_id'] ) ? intval( $_POST['submission_id'] ) : 0;
        
        if ( ! $submission_id ) {
            wp_send_json_error( [ 'message' => __( 'Submission non valida.', 'fp-forms' ) ] );
        }
        
        $submission = \FPForms\Plugin::instance()->submissions->get_submission( $submission_id );
        
        if ( ! $submission ) {
            wp_send_json_error( [ 'message' => __( 'Submission non trovata.', 'fp-forms' ) ] );
        }
        
        // Ottieni file allegati
        $files = \FPForms\Plugin::instance()->submissions->get_submission_files( $submission_id );
        
        // Ottieni form per nomi campi
        $form = \FPForms\Plugin::instance()->forms->get_form( $submission->form_id );

        if ( ! $form || ! is_array( $form ) ) {
            $form = [ 'fields' => [], 'title' => __( 'Form eliminato', 'fp-forms' ) ];
        }
        
        // Formatta HTML
        $html = '<div class="fp-submission-details">';
        $html .= '<div class="fp-submission-meta-grid">';
        $html .= '<div class="fp-submission-meta-card"><span class="fp-submission-meta-label">' . esc_html__( 'Data', 'fp-forms' ) . '</span><strong class="fp-submission-meta-value">' . esc_html( \FPForms\Helpers\Helper::format_date( $submission->created_at ) ) . '</strong></div>';
        $html .= '<div class="fp-submission-meta-card"><span class="fp-submission-meta-label">' . esc_html__( 'Stato', 'fp-forms' ) . '</span><strong class="fp-submission-meta-value">' . ( $submission->status === 'read' ? esc_html__( 'Letta', 'fp-forms' ) : esc_html__( 'Non letta', 'fp-forms' ) ) . '</strong></div>';
        $html .= '<div class="fp-submission-meta-card"><span class="fp-submission-meta-label">' . esc_html__( 'IP', 'fp-forms' ) . '</span><strong class="fp-submission-meta-value">' . esc_html( $submission->user_ip ) . '</strong></div>';
        $html .= '</div>';

        // Dati form
        $html .= '<div class="fp-submission-data-list">';
        $fullname_bases = [];
        foreach ( $form['fields'] as $f ) {
            if ( isset( $f['type'] ) && $f['type'] === 'fullname' && ! empty( $f['name'] ) ) {
                $fullname_bases[ $f['name'] ] = $f['label'];
            }
        }
        $shown_keys = [];
        foreach ( $submission->data as $key => $value ) {
            if ( in_array( $key, $shown_keys, true ) ) {
                continue;
            }
            $clean = str_replace( 'fp_field_', '', $key );
            $is_nome = preg_match( '/^(.+)_nome$/', $clean, $m ) && isset( $fullname_bases[ $m[1] ] );
            $is_cognome = preg_match( '/^(.+)_cognome$/', $clean, $m ) && isset( $fullname_bases[ $m[1] ] );
            if ( $is_nome || $is_cognome ) {
                $base = $m[1];
                $label = $fullname_bases[ $base ];
                $key_nome = $base . '_nome';
                $key_cognome = $base . '_cognome';
                $val_nome = isset( $submission->data[ $key_nome ] ) ? ( is_array( $submission->data[ $key_nome ] ) ? implode( ', ', $submission->data[ $key_nome ] ) : $submission->data[ $key_nome ] ) : '';
                $val_cognome = isset( $submission->data[ $key_cognome ] ) ? ( is_array( $submission->data[ $key_cognome ] ) ? implode( ', ', $submission->data[ $key_cognome ] ) : $submission->data[ $key_cognome ] ) : '';
                $combined = trim( $val_nome . ' ' . $val_cognome );
                $html .= '<div class="fp-submission-data-row"><span class="fp-submission-data-key">' . esc_html( $label ) . '</span><span class="fp-submission-data-value">' . esc_html( $combined ) . '</span></div>';
                $shown_keys[] = $key_nome;
                $shown_keys[] = $key_cognome;
                continue;
            }
            $field_label = $key;
            foreach ( $form['fields'] as $field ) {
                if ( $field['name'] === $clean ) {
                    $field_label = $field['label'];
                    break;
                }
            }
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            $html .= '<div class="fp-submission-data-row"><span class="fp-submission-data-key">' . esc_html( $field_label ) . '</span><span class="fp-submission-data-value">' . esc_html( (string) $value ) . '</span></div>';
        }
        $html .= '</div>';
        
        // File allegati
        if ( ! empty( $files ) ) {
            $html .= '<h4 class="fp-submission-files-title">' . esc_html__( 'File Allegati', 'fp-forms' ) . '</h4>';
            $html .= '<div class="fp-submission-files">';
            
            foreach ( $files as $file ) {
                $html .= '<div class="fp-file-item">';
                $html .= '<span class="dashicons dashicons-media-default"></span> ';
                $html .= '<a href="' . esc_url( $file->file_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $file->file_name ) . '</a>';
                $html .= ' <small>(' . \FPForms\Helpers\Helper::format_bytes( $file->file_size ) . ')</small>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        wp_send_json_success( [ 'html' => $html ] );
    }
    
    /**
     * AJAX: Testa connessione reCAPTCHA
     */
    public function ajax_test_recaptcha() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti', 'fp-forms' ) ] );
        }
        
        $recaptcha = new \FPForms\Security\ReCaptcha();
        $result = $recaptcha->test_connection();
        
        if ( $result['success'] ) {
            wp_send_json_success( [ 'message' => $result['message'] ] );
        } else {
            wp_send_json_error( [ 'message' => $result['message'] ] );
        }
    }
    
    /**
     * AJAX: Testa connessione Brevo
     */
    public function ajax_test_brevo() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti', 'fp-forms' ) ] );
        }
        
        $brevo = new \FPForms\Integrations\Brevo();
        $result = $brevo->test_connection();
        
        if ( $result['success'] ) {
            wp_send_json_success( [ 'message' => $result['message'], 'account' => $result['account'] ] );
        } else {
            wp_send_json_error( [ 'message' => $result['message'] ] );
        }
    }
    
    /**
     * AJAX: Carica liste Brevo
     */
    public function ajax_load_brevo_lists() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti', 'fp-forms' ) ] );
        }
        
        $brevo = new \FPForms\Integrations\Brevo();
        $result = $brevo->get_lists();
        
        if ( $result['success'] ) {
            wp_send_json_success( [ 'lists' => $result['lists'] ] );
        } else {
            wp_send_json_error( [ 'message' => $result['error'] ] );
        }
    }
    
    /**
     * AJAX: Testa connessione Meta Pixel
     */
    public function ajax_test_meta() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti', 'fp-forms' ) ] );
        }
        
        $meta = new \FPForms\Integrations\MetaPixel();
        $result = $meta->test_connection();
        
        if ( $result['success'] ) {
            wp_send_json_success( [ 'message' => $result['message'] ] );
        } else {
            wp_send_json_error( [ 'message' => $result['message'] ] );
        }
    }

    /**
     * AJAX: Simula i flussi principali del form senza invii reali.
     *
     * Il simulatore esegue un dry-run dei controlli operativi per:
     * email, tracking, Brevo, Meta, webhook e reCAPTCHA.
     */
    public function ajax_run_simulation() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );

        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }

        $form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
        if ( $form_id <= 0 ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }

        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        if ( ! is_array( $form ) ) {
            wp_send_json_error( [ 'message' => __( 'Form non trovato.', 'fp-forms' ) ] );
        }

        $sample_data = $this->build_simulation_sample_data( $form );
        $report      = $this->build_simulation_report( $form, $sample_data );

        wp_send_json_success(
            [
                'message' => __( 'Simulazione completata con successo.', 'fp-forms' ),
                'report'  => $report,
            ]
        );
    }

    /**
     * Costruisce dati demo coerenti con i campi del form.
     *
     * @param array $form Dati form.
     * @return array Dati submission simulata.
     */
    private function build_simulation_sample_data( array $form ): array {
        $sample_data = [];
        $fields      = $form['fields'] ?? [];

        foreach ( $fields as $field ) {
            if ( ! is_array( $field ) || empty( $field['name'] ) ) {
                continue;
            }

            $name = (string) $field['name'];
            $type = (string) ( $field['type'] ?? 'text' );

            switch ( $type ) {
                case 'email':
                    $sample_data[ $name ] = 'simulazione@example.com';
                    break;
                case 'phone':
                    $sample_data[ $name ] = '+39 333 1234567';
                    break;
                case 'number':
                    $sample_data[ $name ] = '42';
                    break;
                case 'date':
                    $sample_data[ $name ] = gmdate( 'Y-m-d' );
                    break;
                case 'checkbox':
                    $choices              = $field['options']['choices'] ?? [];
                    $sample_data[ $name ] = is_array( $choices ) && ! empty( $choices ) ? [ (string) $choices[0] ] : [ 'opzione-1' ];
                    break;
                case 'select':
                case 'radio':
                    $choices              = $field['options']['choices'] ?? [];
                    $sample_data[ $name ] = is_array( $choices ) && ! empty( $choices ) ? (string) $choices[0] : 'opzione-1';
                    break;
                case 'fullname':
                    $sample_data[ $name . '_nome' ]    = 'Mario';
                    $sample_data[ $name . '_cognome' ] = 'Rossi';
                    break;
                default:
                    $sample_data[ $name ] = 'Valore di test';
                    break;
            }
        }

        return $sample_data;
    }

    /**
     * Genera il report completo della simulazione.
     *
     * @param array $form Dati form.
     * @param array $sample_data Dati demo usati nel dry-run.
     * @return array
     */
    private function build_simulation_report( array $form, array $sample_data ): array {
        $form_settings = is_array( $form['settings'] ?? null ) ? $form['settings'] : [];
        $form_id       = (int) ( $form['id'] ?? 0 );

        $emails_disabled = ! empty( $form_settings['disable_wordpress_emails'] );
        $notification_to = isset( $form_settings['notification_email'] ) && trim( (string) $form_settings['notification_email'] ) !== ''
            ? (string) $form_settings['notification_email']
            : (string) get_option( 'admin_email' );

        $staff_raw   = (string) ( $form_settings['staff_emails'] ?? '' );
        $staff_emails = preg_split( '/[,;\n\r]+/', $staff_raw ) ?: [];
        $staff_emails = array_values( array_filter( array_map( 'trim', $staff_emails ), 'is_email' ) );

        $has_user_email_field = false;
        foreach ( (array) ( $form['fields'] ?? [] ) as $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }
            if ( ( $field['type'] ?? '' ) === 'email' ) {
                $has_user_email_field = true;
                break;
            }
        }

        $tracking_hook_registered = has_action( 'fp_forms_after_save_submission' ) !== false;
        $tracking_listeners       = has_action( 'fp_tracking_event' ) !== false;
        $confirmation_enabled     = ! empty( $form_settings['confirmation_enabled'] );
        $staff_enabled            = ! empty( $form_settings['staff_notifications_enabled'] );

        $brevo_enabled_for_form = ! empty( $form_settings['brevo_enabled'] );
        $brevo_list_id = $form_settings['brevo_list_id'] ?? '';
        $brevo_has_key = false;
        if ( function_exists( 'fp_tracking_get_brevo_settings' ) ) {
            $central = fp_tracking_get_brevo_settings();
            $brevo_has_key = ! empty( $central['enabled'] );
            $forms_list_it = 0;
            $forms_list_en = 0;
            if ( function_exists( 'fp_tracking_get_brevo_list_id' ) ) {
                $forms_list_it = (int) fp_tracking_get_brevo_list_id( 'forms', 'it' );
                $forms_list_en = (int) fp_tracking_get_brevo_list_id( 'forms', 'en' );
            } else {
                $forms_lists = is_array( $central['source_lists']['forms'] ?? null ) ? $central['source_lists']['forms'] : [];
                $forms_list_it = (int) ( $forms_lists['it'] ?? 0 );
                $forms_list_en = (int) ( $forms_lists['en'] ?? 0 );
            }
            if ( $forms_list_it <= 0 ) {
                $forms_list_it = (int) ( $central['list_id_it'] ?? 0 );
            }
            if ( $forms_list_en <= 0 ) {
                $forms_list_en = (int) ( $central['list_id_en'] ?? 0 );
            }
            if ( empty( $brevo_list_id ) ) {
                $brevo_list_id = $forms_list_en ?: $forms_list_it;
            }
            if ( empty( $brevo_list_id ) ) {
                $brevo_list_id = $forms_list_it ?: ( $central['list_id_it'] ?? '' );
            }
        } else {
            $brevo_settings = get_option( 'fp_forms_brevo_settings', [] );
            $brevo_has_key = ! empty( $brevo_settings['api_key'] ?? '' );
            if ( empty( $brevo_list_id ) ) {
                $brevo_list_id = $brevo_settings['default_list_id_it'] ?? ( $brevo_settings['default_list_id'] ?? ( $brevo_settings['default_list_id_en'] ?? '' ) );
            }
        }

        $meta_settings   = get_option( 'fp_forms_meta_settings', [] );
        $meta_has_pixel  = ! empty( $meta_settings['pixel_id'] ?? '' );
        $meta_has_token  = ! empty( $meta_settings['access_token'] ?? '' );

        $webhooks       = is_array( $form_settings['webhooks'] ?? null ) ? $form_settings['webhooks'] : [];
        $enabled_hooks  = array_filter(
            $webhooks,
            static function ( $wh ) {
                return is_array( $wh ) && ! empty( $wh['enabled'] ) && ! empty( $wh['url'] );
            }
        );
        $webhook_manager = \FPForms\Plugin::instance()->webhooks;
        $safe_webhooks   = 0;
        foreach ( $enabled_hooks as $wh ) {
            if ( $webhook_manager->is_safe_url_for_save( (string) $wh['url'] ) ) {
                $safe_webhooks++;
            }
        }

        $has_recaptcha_field = false;
        foreach ( (array) ( $form['fields'] ?? [] ) as $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }
            if ( ( $field['type'] ?? '' ) === 'recaptcha' ) {
                $has_recaptcha_field = true;
                break;
            }
        }
        $recaptcha_settings   = get_option( 'fp_forms_recaptcha_settings', [] );
        $recaptcha_configured = ! empty( $recaptcha_settings['site_key'] ?? '' ) && ! empty( $recaptcha_settings['secret_key'] ?? '' );

        $checks = [
            [
                'key'     => 'email_webmaster',
                'label'   => __( 'Email Webmaster', 'fp-forms' ),
                'status'  => $emails_disabled ? 'disabled' : 'ok',
                'details' => $emails_disabled
                    ? __( 'Disabilitata dal toggle "Disabilita email WordPress".', 'fp-forms' )
                    : sprintf( __( 'Invio previsto verso: %s', 'fp-forms' ), $notification_to ),
            ],
            [
                'key'     => 'email_confirmation',
                'label'   => __( 'Email Conferma Cliente', 'fp-forms' ),
                'status'  => $emails_disabled ? 'disabled' : ( ! $confirmation_enabled ? 'disabled' : ( $has_user_email_field ? 'ok' : 'warning' ) ),
                'details' => $emails_disabled
                    ? __( 'Disabilitata dal toggle "Disabilita email WordPress".', 'fp-forms' )
                    : ( ! $confirmation_enabled
                        ? __( 'Conferma cliente non abilitata per questo form.', 'fp-forms' )
                        : ( $has_user_email_field ? __( 'Campo email rilevato: conferma inviabile.', 'fp-forms' ) : __( 'Nessun campo email nel form: conferma cliente non inviabile.', 'fp-forms' ) ) ),
            ],
            [
                'key'     => 'email_staff',
                'label'   => __( 'Email Staff', 'fp-forms' ),
                'status'  => $emails_disabled ? 'disabled' : ( ! $staff_enabled ? 'disabled' : ( ! empty( $staff_emails ) ? 'ok' : 'warning' ) ),
                'details' => $emails_disabled
                    ? __( 'Disabilitata dal toggle "Disabilita email WordPress".', 'fp-forms' )
                    : ( ! $staff_enabled
                        ? __( 'Notifiche staff non abilitate per questo form.', 'fp-forms' )
                        : ( ! empty( $staff_emails )
                            ? sprintf( __( 'Notifica staff attiva verso %d destinatari validi.', 'fp-forms' ), count( $staff_emails ) )
                            : __( 'Notifiche staff abilitate ma senza email valide configurate.', 'fp-forms' ) ) ),
            ],
            [
                'key'     => 'tracking',
                'label'   => __( 'Tracking Eventi', 'fp-forms' ),
                'status'  => $tracking_hook_registered ? 'ok' : 'warning',
                'details' => $tracking_hook_registered
                    ? ( $tracking_listeners
                        ? __( 'Hook tracking registrati e listener evento disponibili.', 'fp-forms' )
                        : __( 'Hook registrati, ma nessun listener su fp_tracking_event (controlla plugin tracking layer).', 'fp-forms' ) )
                    : __( 'Hook tracking non registrati correttamente.', 'fp-forms' ),
            ],
            [
                'key'     => 'brevo',
                'label'   => __( 'Brevo CRM', 'fp-forms' ),
                'status'  => ! $brevo_enabled_for_form ? 'disabled' : ( $brevo_has_key && ! empty( $brevo_list_id ) ? 'ok' : 'warning' ),
                'details' => ! $brevo_enabled_for_form
                    ? __( 'Integrazione Brevo non abilitata per questo form.', 'fp-forms' )
                    : ( $brevo_has_key && ! empty( $brevo_list_id )
                        ? __( 'Configurazione completa per sync contatti/eventi.', 'fp-forms' )
                        : __( 'Configurazione incompleta (serve API key globale e lista per il form).', 'fp-forms' ) ),
            ],
            [
                'key'     => 'meta',
                'label'   => __( 'Meta Pixel/CAPI', 'fp-forms' ),
                'status'  => ! $meta_has_pixel ? 'disabled' : ( $meta_has_token ? 'ok' : 'warning' ),
                'details' => ! $meta_has_pixel
                    ? __( 'Meta Pixel non configurato.', 'fp-forms' )
                    : ( $meta_has_token ? __( 'Pixel ID e token CAPI configurati.', 'fp-forms' ) : __( 'Pixel ID presente, token CAPI mancante (tracciamento server-side ridotto).', 'fp-forms' ) ),
            ],
            [
                'key'     => 'webhooks',
                'label'   => __( 'Webhooks', 'fp-forms' ),
                'status'  => empty( $enabled_hooks ) ? 'disabled' : ( $safe_webhooks === count( $enabled_hooks ) ? 'ok' : 'warning' ),
                'details' => empty( $enabled_hooks )
                    ? __( 'Nessun webhook attivo nel form.', 'fp-forms' )
                    : sprintf(
                        __( 'Webhook attivi: %1$d, URL sicuri: %2$d.', 'fp-forms' ),
                        count( $enabled_hooks ),
                        $safe_webhooks
                    ),
            ],
            [
                'key'     => 'recaptcha',
                'label'   => __( 'reCAPTCHA', 'fp-forms' ),
                'status'  => ! $has_recaptcha_field ? 'disabled' : ( $recaptcha_configured ? 'ok' : 'warning' ),
                'details' => ! $has_recaptcha_field
                    ? __( 'Nessun campo reCAPTCHA nel form.', 'fp-forms' )
                    : ( $recaptcha_configured ? __( 'Chiavi reCAPTCHA configurate.', 'fp-forms' ) : __( 'Campo reCAPTCHA presente ma chiavi mancanti.', 'fp-forms' ) ),
            ],
        ];

        $ok_count      = 0;
        $warning_count = 0;
        $disabled_count = 0;
        foreach ( $checks as $check ) {
            if ( $check['status'] === 'ok' ) {
                $ok_count++;
            } elseif ( $check['status'] === 'disabled' ) {
                $disabled_count++;
            } else {
                $warning_count++;
            }
        }

        return [
            'form_id'      => $form_id,
            'form_title'   => (string) ( $form['title'] ?? '' ),
            'sample_data'  => $sample_data,
            'checks'       => $checks,
            'summary'      => [
                'ok'       => $ok_count,
                'warning'  => $warning_count,
                'disabled' => $disabled_count,
            ],
            'simulated_at' => current_time( 'mysql' ),
        ];
    }
    
    /**
     * AJAX: Testa connessione SMTP
     */
    public function ajax_test_smtp() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti', 'fp-forms' ) ] );
        }
        
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        
        if ( ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => __( 'Indirizzo email non valido.', 'fp-forms' ) ] );
        }
        
        $result = \FPForms\Email\Smtp::send_test( $email );
        
        if ( $result['success'] ) {
            wp_send_json_success( [ 'message' => $result['message'] ] );
        } else {
            wp_send_json_error( [ 'message' => $result['message'] ] );
        }
    }
    
    /**
     * Sanitize and validate form settings
     * BUGFIX #25: Prevent malicious/invalid values in settings
     */
    private function sanitize_form_settings( $settings ) {
        if ( ! is_array( $settings ) ) {
            return [];
        }
        
        $sanitized = [];
        
        // Text fields
        $text_fields = [
            'submit_button_text', 'success_message', 'notification_subject', 'notification_message',
            'confirmation_subject', 'confirmation_message', 'staff_notification_subject', 
            'staff_notification_message', 'custom_css_class', 'success_redirect_url',
            'brevo_event_name', 'notification_email', 'confirmation_footer_info'
        ];
        
        foreach ( $text_fields as $field ) {
            if ( isset( $settings[ $field ] ) ) {
                if ( $field === 'notification_email' ) {
                    $raw_emails = array_map( 'trim', explode( ',', $settings[ $field ] ) );
                    $valid = array_filter( $raw_emails, 'is_email' );
                    $sanitized[ $field ] = implode( ', ', $valid );
                } elseif ( $field === 'success_redirect_url' ) {
                    // URL validation
                    $sanitized[ $field ] = esc_url_raw( $settings[ $field ] );
                } elseif ( in_array( $field, [ 'success_message', 'notification_message', 'confirmation_message', 'staff_notification_message', 'confirmation_footer_info' ] ) ) {
                    // Textarea - allow line breaks
                    $sanitized[ $field ] = sanitize_textarea_field( $settings[ $field ] );
                } else {
                    // Standard text
                    $sanitized[ $field ] = sanitize_text_field( $settings[ $field ] );
                }
            }
        }
        
        // Textarea fields (multi-line)
        if ( isset( $settings['staff_emails'] ) ) {
            $sanitized['staff_emails'] = sanitize_textarea_field( $settings['staff_emails'] );
        }
        
        // Boolean fields
        $boolean_fields = [
            'confirmation_enabled', 'staff_notifications_enabled', 'brevo_enabled',
            'disable_wordpress_emails', 'success_redirect_enabled',
            'enable_multistep', 'enable_progressive_save',
        ];
        
        foreach ( $boolean_fields as $field ) {
            $sanitized[ $field ] = isset( $settings[ $field ] ) && $settings[ $field ] ? true : false;
        }
        
        // Numeric fields
        if ( isset( $settings['brevo_list_id'] ) ) {
            $sanitized['brevo_list_id'] = absint( $settings['brevo_list_id'] );
        }
        
        // Color (HEX validation)
        if ( isset( $settings['submit_button_color'] ) ) {
            $color = $settings['submit_button_color'];
            if ( preg_match( '/^#[0-9A-Fa-f]{6}$/', $color ) ) {
                $sanitized['submit_button_color'] = $color;
            } else {
                $sanitized['submit_button_color'] = '#3b82f6'; // default
            }
        }
        
        // Confirmation accent color (HEX, empty = use global)
        if ( isset( $settings['confirmation_accent_color'] ) ) {
            $ca = $settings['confirmation_accent_color'];
            $sanitized['confirmation_accent_color'] = preg_match( '/^#[0-9A-Fa-f]{6}$/', $ca ) ? $ca : '';
        }

        // Custom colors (HEX validation)
        $custom_colors = [
            'custom_border_color' => '#d1d5db',
            'custom_focus_color' => '#2563eb',
            'custom_text_color' => '#1f2937',
            'custom_background_color' => '#ffffff',
        ];
        
        foreach ( $custom_colors as $field => $default ) {
            if ( isset( $settings[ $field ] ) ) {
                $color = $settings[ $field ];
                if ( preg_match( '/^#[0-9A-Fa-f]{6}$/', $color ) ) {
                    $sanitized[ $field ] = $color;
                } else {
                    $sanitized[ $field ] = $default;
                }
            }
        }
        
        // Whitelist validations
        $whitelist_fields = [
            'submit_button_size' => [ 'small', 'medium', 'large' ],
            'submit_button_style' => [ 'solid', 'outline', 'ghost' ],
            'submit_button_align' => [ 'left', 'center', 'right' ],
            'submit_button_width' => [ 'auto', 'full' ],
            'submit_button_icon' => [ '', 'paper-plane', 'send', 'check', 'arrow-right', 'save' ],
            'success_message_type' => [ 'success', 'info', 'celebration' ],
            'confirmation_template' => [ '', 'elegant', 'professional', 'modern', 'minimal' ],
        ];
        
        foreach ( $whitelist_fields as $field => $allowed_values ) {
            if ( isset( $settings[ $field ] ) ) {
                $value = $settings[ $field ];
                $sanitized[ $field ] = in_array( $value, $allowed_values, true ) ? $value : $allowed_values[0];
            }
        }
        
        // Duration (whitelist numeric)
        if ( isset( $settings['success_message_duration'] ) ) {
            $duration = intval( $settings['success_message_duration'] );
            $allowed_durations = [ 0, 3000, 5000, 10000 ];
            $sanitized['success_message_duration'] = in_array( $duration, $allowed_durations, true ) ? $duration : 0;
        }
        
        // Trust badges (whitelist array)
        if ( isset( $settings['trust_badges'] ) && is_array( $settings['trust_badges'] ) ) {
            $allowed_badges = [ 
                'instant-response', 'data-secure', 'no-spam', 'gdpr-compliant', 
                'ssl-secure', 'quick-reply', 'free-quote', 'trusted', 
                'support', 'privacy-first' 
            ];
            
            $sanitized['trust_badges'] = array_values( array_intersect( $settings['trust_badges'], $allowed_badges ) );
        } else {
            $sanitized['trust_badges'] = [];
        }
        
        // Webhooks (array di webhook)
        if ( isset( $settings['webhooks'] ) && is_array( $settings['webhooks'] ) ) {
            $sanitized['webhooks'] = [];
            foreach ( $settings['webhooks'] as $webhook ) {
                if ( ! is_array( $webhook ) ) {
                    continue;
                }
                
                $sanitized_webhook = [
                    'enabled' => isset( $webhook['enabled'] ) ? (bool) $webhook['enabled'] : true,
                    'url' => isset( $webhook['url'] ) ? esc_url_raw( $webhook['url'] ) : '',
                    'secret' => isset( $webhook['secret'] ) ? sanitize_text_field( $webhook['secret'] ) : '',
                    'id' => isset( $webhook['id'] ) ? sanitize_text_field( $webhook['id'] ) : 'webhook_' . uniqid(),
                ];
                
                // Solo aggiungi se ha URL valido e supera il check SSRF (inclusa risoluzione DNS)
                // La risoluzione DNS viene fatta qui (al salvataggio) e non ad ogni submission.
                if ( ! empty( $sanitized_webhook['url'] ) && \FPForms\Plugin::instance()->webhooks->is_safe_url_for_save( $sanitized_webhook['url'] ) ) {
                    $sanitized['webhooks'][] = $sanitized_webhook;
                }
            }
        } else {
            $sanitized['webhooks'] = [];
        }

        // Conditional logic: operator globale AND/OR
        if ( isset( $settings['conditional_operator_global'] ) ) {
            $op = $settings['conditional_operator_global'];
            $sanitized['conditional_operator_global'] = in_array( $op, [ 'and', 'or' ], true ) ? $op : 'or';
        } else {
            $sanitized['conditional_operator_global'] = 'or';
        }

        // Conditional rules (struttura validata da ConditionalLogic::validate_rules)
        if ( isset( $settings['conditional_rules'] ) && is_array( $settings['conditional_rules'] ) ) {
            $valid_conditions = [ 'equals', 'not_equals', 'contains', 'not_contains', 'greater_than', 'less_than', 'is_empty', 'is_not_empty' ];
            $valid_actions    = [ 'show', 'hide', 'require', 'unrequire' ];
            $sanitized['conditional_rules'] = [];
            foreach ( $settings['conditional_rules'] as $rule ) {
                if ( ! is_array( $rule ) || empty( $rule['trigger_field'] ) || empty( $rule['action'] ) || empty( $rule['target_fields'] ) ) {
                    continue;
                }
                $cond = isset( $rule['condition'] ) && in_array( $rule['condition'], $valid_conditions, true ) ? $rule['condition'] : 'equals';
                $act  = isset( $rule['action'] ) && in_array( $rule['action'], $valid_actions, true ) ? $rule['action'] : 'show';
                $targets = is_array( $rule['target_fields'] ) ? array_map( 'sanitize_key', $rule['target_fields'] ) : [];
                $targets = array_values( array_filter( $targets ) );
                if ( empty( $targets ) ) {
                    continue;
                }
                $sanitized['conditional_rules'][] = [
                    'id'            => isset( $rule['id'] ) ? sanitize_text_field( $rule['id'] ) : 'rule_' . uniqid(),
                    'trigger_field' => sanitize_key( $rule['trigger_field'] ),
                    'condition'     => $cond,
                    'value'         => isset( $rule['value'] ) ? sanitize_text_field( $rule['value'] ) : '',
                    'action'        => $act,
                    'target_fields' => $targets,
                ];
            }
        } else {
            $sanitized['conditional_rules'] = [];
        }

        $sanitized['payment_enabled'] = isset( $settings['payment_enabled'] ) && $settings['payment_enabled'] ? true : false;
        $sanitized['payment_provider'] = isset( $settings['payment_provider'] ) && $settings['payment_provider'] === 'stripe' ? 'stripe' : '';
        $sanitized['payment_amount'] = isset( $settings['payment_amount'] ) ? max( 0, floatval( $settings['payment_amount'] ) ) : 0;
        $sanitized['payment_amount_field'] = isset( $settings['payment_amount_field'] ) ? sanitize_key( $settings['payment_amount_field'] ) : '';
        
        return $sanitized;
    }
    
    /**
     * Sanitizza i campi importati da JSON esterno.
     * Applica whitelist dei tipi ammessi e sanitizzazione su ogni attributo.
     */
    private function sanitize_imported_fields( $fields ) {
        if ( ! is_array( $fields ) ) {
            return [];
        }
        
        $allowed_types = [
            'text', 'fullname', 'email', 'phone', 'number', 'date', 'textarea',
            'select', 'radio', 'checkbox', 'privacy-checkbox', 'marketing-checkbox',
            'recaptcha', 'file', 'calculated', 'step_break',
        ];
        
        $sanitized = [];
        foreach ( $fields as $order => $field ) {
            if ( ! is_array( $field ) ) {
                continue;
            }
            
            $type = sanitize_key( $field['type'] ?? '' );
            if ( ! in_array( $type, $allowed_types, true ) ) {
                continue;
            }
            
            $name = sanitize_key( $field['name'] ?? '' );
            if ( $name === '' ) {
                $name = 'field_' . $order;
            }
            
            $clean = [
                'type'     => $type,
                'label'    => sanitize_text_field( $field['label'] ?? '' ),
                'name'     => $name,
                'required' => ! empty( $field['required'] ),
            ];
            
            // Sanitizza le opzioni ricorsivamente (solo scalari)
            $options = [];
            if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
                $options = array_map( function( $v ) {
                    return is_array( $v )
                        ? array_map( 'sanitize_text_field', $v )
                        : sanitize_text_field( (string) $v );
                }, $field['options'] );
            }
            
            // step_title va in options per step_break (usato da field-item e MultiStep)
            if ( $type === 'step_break' && isset( $field['step_title'] ) ) {
                $options['step_title'] = sanitize_text_field( (string) $field['step_title'] );
            }
            
            $clean['options'] = $options;
            $sanitized[]      = $clean;
        }
        
        return $sanitized;
    }
    
    /**
     * AJAX: Test webhook
     */
    public function ajax_test_webhook() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_settings() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';
        $secret = isset( $_POST['secret'] ) ? sanitize_text_field( $_POST['secret'] ) : '';
        
        if ( empty( $url ) ) {
            wp_send_json_error( [ 'message' => __( 'URL webhook non valido.', 'fp-forms' ) ] );
        }
        
        $webhook_manager = \FPForms\Plugin::instance()->webhooks;
        $result = $webhook_manager->test_webhook( $url, $secret );
        
        if ( $result['success'] ) {
            wp_send_json_success( [
                'message' => $result['message'],
                'status_code' => $result['status_code'],
            ] );
        } else {
            wp_send_json_error( [
                'message' => $result['message'],
            ] );
        }
    }
    
    /**
     * AJAX: Ripristina snapshot
     */
    public function ajax_restore_snapshot() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $snapshot_id = isset( $_POST['snapshot_id'] ) ? sanitize_text_field( $_POST['snapshot_id'] ) : '';
        
        if ( ! $form_id || ! $snapshot_id ) {
            wp_send_json_error( [ 'message' => __( 'Parametri non validi.', 'fp-forms' ) ] );
        }

        // Verifica che il form esista e che l'utente abbia il diritto di modificarlo
        $form_post = get_post( $form_id );
        if ( ! $form_post ) {
            wp_send_json_error( [ 'message' => __( 'Form non trovato.', 'fp-forms' ) ] );
        }
        if ( ! current_user_can( 'manage_options' ) && (int) $form_post->post_author !== get_current_user_id() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti per questo form.', 'fp-forms' ) ] );
        }

        $versioning = \FPForms\Plugin::instance()->versioning;
        $result = $versioning->restore_snapshot( $form_id, $snapshot_id );
        
        if ( $result ) {
            wp_send_json_success( [
                'message' => __( 'Snapshot ripristinato con successo!', 'fp-forms' ),
                'redirect' => admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form_id ),
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'Errore nel ripristinare lo snapshot.', 'fp-forms' ) ] );
        }
    }

    /**
     * AJAX: Azioni di massa sulle submissions
     */
    public function ajax_bulk_action_submissions() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );

        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }

        $bulk_action    = isset( $_POST['bulk_action'] ) ? sanitize_text_field( $_POST['bulk_action'] ) : '';
        $submission_ids = isset( $_POST['submission_ids'] ) ? array_map( 'intval', (array) $_POST['submission_ids'] ) : [];
        $submission_ids = array_filter( $submission_ids );

        if ( empty( $bulk_action ) || empty( $submission_ids ) ) {
            wp_send_json_error( [ 'message' => __( 'Parametri non validi.', 'fp-forms' ) ] );
        }

        $allowed_actions = [ 'delete', 'mark-read', 'mark-unread', 'export' ];
        if ( ! in_array( $bulk_action, $allowed_actions, true ) ) {
            wp_send_json_error( [ 'message' => __( 'Azione non valida.', 'fp-forms' ) ] );
        }

        $submissions = \FPForms\Plugin::instance()->submissions;
        $db          = \FPForms\Plugin::instance()->database;
        $processed   = 0;

        switch ( $bulk_action ) {
            case 'delete':
                foreach ( $submission_ids as $id ) {
                    if ( $submissions->delete_submission( $id ) ) {
                        $processed++;
                    }
                }
                break;

            case 'mark-read':
                foreach ( $submission_ids as $id ) {
                    if ( $db->update_submission_status( $id, 'read' ) !== false ) {
                        $processed++;
                    }
                }
                break;

            case 'mark-unread':
                foreach ( $submission_ids as $id ) {
                    if ( $db->update_submission_status( $id, 'unread' ) !== false ) {
                        $processed++;
                    }
                }
                break;

            case 'export':
                $rows        = [];
                $all_keys    = [];
                $parsed_subs = [];

                // Prima passata: raccoglie tutte le chiavi uniche da tutti i record
                foreach ( $submission_ids as $id ) {
                    $sub = $db->get_submission( $id );
                    if ( ! $sub ) {
                        continue;
                    }
                    $data = json_decode( $sub->data, true );
                    if ( ! is_array( $data ) ) {
                        $data = [];
                    }
                    foreach ( array_keys( $data ) as $key ) {
                        if ( ! in_array( $key, $all_keys, true ) ) {
                            $all_keys[] = $key;
                        }
                    }
                    $parsed_subs[] = [ 'sub' => $sub, 'data' => $data ];
                }

                $headers = array_merge( [ 'ID', 'Data', 'Stato' ], $all_keys );

                // Seconda passata: costruisce le righe con colonne allineate agli header
                foreach ( $parsed_subs as $item ) {
                    $sub  = $item['sub'];
                    $data = $item['data'];
                    $row  = [
                        $this->escape_csv_formula( (string) $sub->id ),
                        $this->escape_csv_formula( (string) $sub->created_at ),
                        $this->escape_csv_formula( (string) $sub->status ),
                    ];
                    foreach ( $all_keys as $key ) {
                        $val   = isset( $data[ $key ] ) ? $data[ $key ] : '';
                        $raw   = is_array( $val ) ? implode( ', ', $val ) : (string) $val;
                        $row[] = $this->escape_csv_formula( $raw );
                    }
                    $rows[] = $row;
                }

                $csv = fopen( 'php://temp', 'r+' );
                fputcsv( $csv, $headers );
                foreach ( $rows as $row ) {
                    fputcsv( $csv, $row );
                }
                rewind( $csv );
                $csv_content = stream_get_contents( $csv );
                fclose( $csv );

                wp_send_json_success( [
                    'message'  => sprintf( __( '%d submissions esportate.', 'fp-forms' ), count( $rows ) ),
                    'csv'      => base64_encode( $csv_content ),
                    'filename' => 'fp-forms-export-' . wp_date( 'Y-m-d' ) . '.csv',
                ] );
                return;
        }

        wp_send_json_success( [
            'message' => sprintf( __( 'Operazione completata: %d submissions aggiornate.', 'fp-forms' ), $processed ),
        ] );
    }

    /**
     * AJAX: Export configurazione form come JSON
     */
    public function ajax_export_form_config() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );

        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }

        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }

        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );

        if ( ! $form ) {
            wp_send_json_error( [ 'message' => __( 'Form non trovato.', 'fp-forms' ) ] );
        }

        // Rimuovi dati sensibili dall'export (webhook secret, credenziali integrazione)
        $safe_settings = $form['settings'];
        if ( isset( $safe_settings['webhooks'] ) && is_array( $safe_settings['webhooks'] ) ) {
            foreach ( $safe_settings['webhooks'] as &$wh ) {
                unset( $wh['secret'] );
            }
            unset( $wh );
        }
        unset( $safe_settings['brevo_api_key'] );
        unset( $safe_settings['meta_pixel_token'] );

        $export = [
            'fp_forms_version' => FP_FORMS_VERSION,
            'title'            => $form['title'],
            'description'      => $form['description'],
            'fields'           => $form['fields'],
            'settings'         => $safe_settings,
        ];

        wp_send_json_success( [
            'config'   => $export,
            'filename' => sanitize_file_name( $form['title'] ) . '-config.json',
        ] );
    }

    /**
     * AJAX: Import configurazione form da JSON
     */
    public function ajax_import_form_config() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );

        if ( ! \FPForms\Core\Capabilities::can_manage_forms() ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }

        $config_json = isset( $_POST['config'] ) ? wp_unslash( $_POST['config'] ) : '';
        $config      = json_decode( $config_json, true );

        if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $config ) ) {
            wp_send_json_error( [ 'message' => __( 'JSON non valido.', 'fp-forms' ) ] );
        }

        if ( empty( $config['title'] ) || ! isset( $config['fields'] ) ) {
            wp_send_json_error( [ 'message' => __( 'Configurazione incompleta: mancano titolo o campi.', 'fp-forms' ) ] );
        }

        $forms = \FPForms\Plugin::instance()->forms;

        $form_id = $forms->create_form(
            sanitize_text_field( $config['title'] ) . ' (' . __( 'Importato', 'fp-forms' ) . ')',
            [
                'description' => isset( $config['description'] ) ? sanitize_textarea_field( $config['description'] ) : '',
                'fields'      => $this->sanitize_imported_fields( $config['fields'] ),
                'settings'    => isset( $config['settings'] ) ? $this->sanitize_form_settings( $config['settings'] ) : [],
            ]
        );

        if ( $form_id ) {
            wp_send_json_success( [
                'message'  => __( 'Form importato con successo!', 'fp-forms' ),
                'form_id'  => $form_id,
                'redirect' => admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form_id ),
            ] );
        }

        wp_send_json_error( [ 'message' => __( 'Errore durante l\'importazione.', 'fp-forms' ) ] );
    }

    /**
     * Previene CSV/Formula Injection nelle celle.
     * Prefissa con apostrofo le celle che iniziano con =, +, -, @, TAB, CR.
     */
    private function escape_csv_formula( string $value ): string {
        if ( preg_match( '/^[=+\-@\t\r]/', $value ) ) {
            return "'" . $value;
        }
        return $value;
    }
}
