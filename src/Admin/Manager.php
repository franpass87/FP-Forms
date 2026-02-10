<?php
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
        add_action( 'wp_ajax_fp_forms_test_brevo', [ $this, 'ajax_test_brevo' ] );
        add_action( 'wp_ajax_fp_forms_load_brevo_lists', [ $this, 'ajax_load_brevo_lists' ] );
        add_action( 'wp_ajax_fp_forms_test_meta', [ $this, 'ajax_test_meta' ] );
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

        // Sottomenu: Submissions
        add_submenu_page(
            'fp-forms',
            __( 'Submissions', 'fp-forms' ),
            __( 'Submissions', 'fp-forms' ),
            Capabilities::VIEW_SUBMISSIONS,
            'fp-forms-submissions',
            [ $this, 'render_submissions_page' ]
        );
        remove_submenu_page( 'fp-forms', 'fp-forms-submissions' );

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
        // Carica solo nelle nostre pagine
        if ( strpos( $hook, 'fp-forms' ) === false ) {
            return;
        }
        
        // Aggiungi body class per admin shell
        add_filter( 'admin_body_class', function( $classes ) {
            return $classes . ' fp-forms-admin-shell';
        } );
        
        // CSS
        wp_enqueue_style(
            'fp-forms-admin',
            FP_FORMS_PLUGIN_URL . 'assets/css/admin.css',
            [],
            FP_FORMS_VERSION
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
        if ( ! Capabilities::can_view_submissions() ) {
            wp_die( __( 'Non hai i permessi per accedere a questa pagina.', 'fp-forms' ) );
        }

        $form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_die( __( 'Form non specificato.', 'fp-forms' ) );
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
            
            // Salva impostazioni SMTP
            $smtp_settings = [
                'enabled'    => isset( $_POST['smtp_enabled'] ),
                'host'       => sanitize_text_field( $_POST['smtp_host'] ?? '' ),
                'port'       => intval( $_POST['smtp_port'] ?? 587 ),
                'encryption' => sanitize_text_field( $_POST['smtp_encryption'] ?? 'tls' ),
                'auth'       => isset( $_POST['smtp_auth'] ),
                'username'   => sanitize_text_field( $_POST['smtp_username'] ?? '' ),
                'password'   => $_POST['smtp_password'] ?? '',
            ];
            // Validazione encryption
            if ( ! in_array( $smtp_settings['encryption'], [ 'tls', 'ssl', 'none' ], true ) ) {
                $smtp_settings['encryption'] = 'tls';
            }
            // Validazione porta
            if ( $smtp_settings['port'] < 1 || $smtp_settings['port'] > 65535 ) {
                $smtp_settings['port'] = 587;
            }
            update_option( 'fp_forms_smtp_settings', $smtp_settings );
            
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
            ];
            update_option( 'fp_forms_tracking_settings', $tracking_settings );
            
            // Salva impostazioni Brevo
            $brevo_settings = [
                'api_key' => sanitize_text_field( $_POST['brevo_api_key'] ?? '' ),
                'default_list_id' => sanitize_text_field( $_POST['brevo_default_list'] ?? '' ),
                'double_optin' => isset( $_POST['brevo_double_optin'] ),
                'track_events' => isset( $_POST['brevo_track_events'] ),
            ];
            update_option( 'fp_forms_brevo_settings', $brevo_settings );
            
            // Salva impostazioni Meta Pixel
            $meta_settings = [
                'pixel_id' => sanitize_text_field( $_POST['meta_pixel_id'] ?? '' ),
                'access_token' => sanitize_text_field( $_POST['meta_access_token'] ?? '' ),
                'track_views' => isset( $_POST['meta_track_views'] ),
            ];
            update_option( 'fp_forms_meta_settings', $meta_settings );
            
            echo '<div class="notice notice-success"><p>' . __( 'Impostazioni salvate!', 'fp-forms' ) . '</p></div>';
        }
        
        include FP_FORMS_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    /**
     * AJAX: Salva form
     */
    public function ajax_save_form() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $description = isset( $_POST['description'] ) ? wp_kses_post( $_POST['description'] ) : '';
        $fields = isset( $_POST['fields'] ) ? json_decode( stripslashes( $_POST['fields'] ), true ) : [];
        $settings_raw = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : [];
        
        // BUGFIX #25: Sanitize and validate settings before save
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        
        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }
        
        $new_form_id = \FPForms\Plugin::instance()->forms->duplicate_form( $form_id );
        
        if ( $new_form_id ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'csv';
        
        $options = [
            'date_from' => isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '',
            'date_to' => isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '',
            'status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '',
            'fields' => isset( $_POST['fields'] ) ? array_map( 'sanitize_text_field', $_POST['fields'] ) : [],
        ];
        
        if ( ! $form_id ) {
            wp_send_json_error( [ 'message' => __( 'Form non valido.', 'fp-forms' ) ] );
        }
        
        // Export basato sul formato
        switch ( $format ) {
            case 'csv':
                $exporter = new \FPForms\Export\CsvExporter();
                $exporter->export( $form_id, $options );
                break;
                
            case 'xlsx':
            case 'excel':
                $exporter = new \FPForms\Export\ExcelExporter();
                $exporter->export( $form_id, $options );
                break;
                
            default:
                wp_send_json_error( [ 'message' => __( 'Formato export non supportato.', 'fp-forms' ) ] );
        }
        
        // Il metodo export fa exit, quindi questo non verrÃ  mai eseguito
    }
    
    /**
     * AJAX: Importa template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        // Formatta HTML
        $html = '<div class="fp-submission-details">';
        
        // Info submission
        $html .= '<div class="fp-submission-meta">';
        $html .= '<p><strong>' . __( 'Data:', 'fp-forms' ) . '</strong> ' . \FPForms\Helpers\Helper::format_date( $submission->created_at ) . '</p>';
        $html .= '<p><strong>' . __( 'Stato:', 'fp-forms' ) . '</strong> ' . ( $submission->status === 'read' ? 'Letta' : 'Non letta' ) . '</p>';
        $html .= '<p><strong>' . __( 'IP:', 'fp-forms' ) . '</strong> ' . esc_html( $submission->user_ip ) . '</p>';
        $html .= '</div>';
        
        $html .= '<hr style="margin: 20px 0;">';
        
        // Dati form
        $html .= '<div class="fp-submission-data">';
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
                $html .= '<p><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $combined ) . '</p>';
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
            $html .= '<p><strong>' . esc_html( $field_label ) . ':</strong> ' . esc_html( $value ) . '</p>';
        }
        $html .= '</div>';
        
        // File allegati
        if ( ! empty( $files ) ) {
            $html .= '<hr style="margin: 20px 0;">';
            $html .= '<h4>' . __( 'File Allegati', 'fp-forms' ) . '</h4>';
            $html .= '<div class="fp-submission-files">';
            
            foreach ( $files as $file ) {
                $html .= '<div class="fp-file-item">';
                $html .= '<span class="dashicons dashicons-media-default"></span> ';
                $html .= '<a href="' . esc_url( $file->file_url ) . '" target="_blank">' . esc_html( $file->file_name ) . '</a>';
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
     * AJAX: Testa connessione SMTP
     */
    public function ajax_test_smtp() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
            'brevo_event_name', 'notification_email'
        ];
        
        foreach ( $text_fields as $field ) {
            if ( isset( $settings[ $field ] ) ) {
                if ( $field === 'notification_email' ) {
                    // Email validation
                    $sanitized[ $field ] = sanitize_email( $settings[ $field ] );
                } elseif ( $field === 'success_redirect_url' ) {
                    // URL validation
                    $sanitized[ $field ] = esc_url_raw( $settings[ $field ] );
                } elseif ( in_array( $field, [ 'success_message', 'notification_message', 'confirmation_message', 'staff_notification_message' ] ) ) {
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
            'disable_wordpress_emails', 'success_redirect_enabled'
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
                
                // Solo aggiungi se ha URL valido
                if ( ! empty( $sanitized_webhook['url'] ) ) {
                    $sanitized['webhooks'][] = $sanitized_webhook;
                }
            }
        } else {
            $sanitized['webhooks'] = [];
        }
        
        return $sanitized;
    }
    
    /**
     * AJAX: Test webhook
     */
    public function ajax_test_webhook() {
        check_ajax_referer( 'fp_forms_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
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
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permessi insufficienti.', 'fp-forms' ) ] );
        }
        
        $form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;
        $snapshot_id = isset( $_POST['snapshot_id'] ) ? sanitize_text_field( $_POST['snapshot_id'] ) : '';
        
        if ( ! $form_id || ! $snapshot_id ) {
            wp_send_json_error( [ 'message' => __( 'Parametri non validi.', 'fp-forms' ) ] );
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
}
