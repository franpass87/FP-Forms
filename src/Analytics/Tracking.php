<?php
namespace FPForms\Analytics;

/**
 * Google Tag Manager & Analytics 4 Tracking
 * 
 * @since 1.2.0
 */
class Tracking {
    
    /**
     * GTM Container ID
     */
    private $gtm_id;
    
    /**
     * GA4 Measurement ID
     */
    private $ga4_id;
    
    /**
     * Tracking enabled
     */
    private $enabled;
    
    /**
     * Track form views
     */
    private $track_views;
    
    /**
     * Track field interactions
     */
    private $track_interactions;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->load_settings();
        
        if ( $this->enabled ) {
            $this->init_hooks();
        }
    }
    
    /**
     * Carica impostazioni
     */
    private function load_settings() {
        $settings = get_option( 'fp_forms_tracking_settings', [] );
        
        $this->gtm_id = $settings['gtm_id'] ?? '';
        $this->ga4_id = $settings['ga4_id'] ?? '';
        $this->enabled = ! empty( $this->gtm_id ) || ! empty( $this->ga4_id );
        $this->track_views = $settings['track_views'] ?? true;
        $this->track_interactions = $settings['track_interactions'] ?? false;
    }
    
    /**
     * Inizializza hooks
     */
    private function init_hooks() {
        // GTM container in head
        if ( ! empty( $this->gtm_id ) ) {
            add_action( 'wp_head', [ $this, 'render_gtm_head' ], 1 );
            add_action( 'wp_body_open', [ $this, 'render_gtm_body' ] );
        }
        
        // GA4 script
        if ( ! empty( $this->ga4_id ) ) {
            add_action( 'wp_head', [ $this, 'render_ga4_script' ], 2 );
        }
        
        // Tracking eventi form
        add_action( 'wp_footer', [ $this, 'render_tracking_script' ] );
    }
    
    /**
     * Verifica se tracking è attivo
     */
    public function is_enabled() {
        return $this->enabled;
    }
    
    /**
     * Renderizza GTM container (head)
     */
    public function render_gtm_head() {
        if ( empty( $this->gtm_id ) ) {
            return;
        }
        
        ?>
<!-- Google Tag Manager (FP Forms) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $this->gtm_id ); ?>');</script>
<!-- End Google Tag Manager -->
        <?php
    }
    
    /**
     * Renderizza GTM noscript (body)
     */
    public function render_gtm_body() {
        if ( empty( $this->gtm_id ) ) {
            return;
        }
        
        ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $this->gtm_id ); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <?php
    }
    
    /**
     * Renderizza GA4 script
     */
    public function render_ga4_script() {
        if ( empty( $this->ga4_id ) ) {
            return;
        }
        
        ?>
<!-- Google Analytics 4 (FP Forms) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $this->ga4_id ); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo esc_js( $this->ga4_id ); ?>', {
    'send_page_view': true
  });
</script>
<!-- End Google Analytics 4 -->
        <?php
    }
    
    /**
     * Renderizza tracking script per form events
     */
    public function render_tracking_script() {
        if ( ! $this->enabled ) {
            return;
        }
        
        ?>
<script>
(function() {
    'use strict';
    
    var fpFormsTracking = {
        gtmEnabled: <?php echo ! empty( $this->gtm_id ) ? 'true' : 'false'; ?>,
        ga4Enabled: <?php echo ! empty( $this->ga4_id ) ? 'true' : 'false'; ?>,
        trackViews: <?php echo $this->track_views ? 'true' : 'false'; ?>,
        trackInteractions: <?php echo $this->track_interactions ? 'true' : 'false'; ?>,
        trackedForms: [],
        
        /**
         * Push event to dataLayer (GTM)
         */
        pushToDataLayer: function(eventData) {
            if (!this.gtmEnabled) return;
            
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(eventData);
            
            if (window.console && window.console.log) {
                console.log('[FP Forms GTM]', eventData);
            }
        },
        
        /**
         * Send event to GA4
         */
        sendToGA4: function(eventName, eventParams) {
            if (!this.ga4Enabled || typeof gtag !== 'function') return;
            
            gtag('event', eventName, eventParams);
            
            if (window.console && window.console.log) {
                console.log('[FP Forms GA4]', eventName, eventParams);
            }
        },
        
        /**
         * Track form view
         */
        trackFormView: function(formId, formTitle) {
            if (!this.trackViews) return;
            if (this.trackedForms.indexOf(formId) !== -1) return;
            
            this.trackedForms.push(formId);
            
            // GTM dataLayer
            this.pushToDataLayer({
                'event': 'fp_form_view',
                'form_id': formId,
                'form_title': formTitle,
                'form_name': 'FP Form #' + formId
            });
            
            // GA4 event
            this.sendToGA4('form_view', {
                'form_id': formId,
                'form_name': formTitle,
                'form_type': 'fp_forms'
            });
        },
        
        /**
         * Track form start (first field interaction)
         */
        trackFormStart: function(formId, formTitle) {
            // Salva timestamp per calcolare tempo
            this.formTimers = this.formTimers || {};
            this.formTimers[formId] = Date.now();
            
            // GTM dataLayer
            this.pushToDataLayer({
                'event': 'fp_form_start',
                'form_id': formId,
                'form_title': formTitle,
                'event_category': 'engagement'
            });
            
            // GA4 event (standard)
            this.sendToGA4('form_start', {
                'form_id': formId,
                'form_name': formTitle
            });
        },
        
        /**
         * Track form progress
         */
        trackFormProgress: function(formId, formTitle, progress) {
            this.formProgress = this.formProgress || {};
            var progressKey = formId + '_' + progress;
            
            if (this.formProgress[progressKey]) return;
            this.formProgress[progressKey] = true;
            
            // Solo a 25%, 50%, 75%
            if ([25, 50, 75].indexOf(progress) !== -1) {
                this.pushToDataLayer({
                    'event': 'fp_form_progress',
                    'form_id': formId,
                    'form_title': formTitle,
                    'progress_percent': progress,
                    'event_category': 'engagement'
                });
                
                this.sendToGA4('form_progress', {
                    'form_id': formId,
                    'form_name': formTitle,
                    'progress': progress
                });
            }
        },
        
        /**
         * Track form abandon
         */
        trackFormAbandon: function(formId, formTitle) {
            var timeSpent = 0;
            if (this.formTimers && this.formTimers[formId]) {
                timeSpent = Math.round((Date.now() - this.formTimers[formId]) / 1000);
            }
            
            this.pushToDataLayer({
                'event': 'fp_form_abandon',
                'form_id': formId,
                'form_title': formTitle,
                'time_spent_seconds': timeSpent,
                'event_category': 'abandonment'
            });
            
            this.sendToGA4('form_abandon', {
                'form_id': formId,
                'form_name': formTitle,
                'time_spent': timeSpent
            });
        },
        
        /**
         * Track validation error
         */
        trackValidationError: function(formId, formTitle, errorField, errorMessage) {
            this.pushToDataLayer({
                'event': 'fp_form_validation_error',
                'form_id': formId,
                'form_title': formTitle,
                'error_field': errorField,
                'error_message': errorMessage,
                'event_category': 'error'
            });
            
            this.sendToGA4('form_error', {
                'form_id': formId,
                'form_name': formTitle,
                'error_field': errorField,
                'error_type': 'validation'
            });
        },
        
        /**
         * Track form submission
         */
        trackFormSubmit: function(formId, formTitle, success, errorMessage) {
            var timeSpent = 0;
            if (this.formTimers && this.formTimers[formId]) {
                timeSpent = Math.round((Date.now() - this.formTimers[formId]) / 1000);
            }
            
            if (success) {
                // Submission success
                this.pushToDataLayer({
                    'event': 'fp_form_submit',
                    'form_id': formId,
                    'form_title': formTitle,
                    'form_status': 'success',
                    'time_to_complete': timeSpent
                });
                
                this.sendToGA4('form_submit', {
                    'form_id': formId,
                    'form_name': formTitle,
                    'success': true,
                    'engagement_time_msec': timeSpent * 1000
                });
                
                // Conversion event
                this.pushToDataLayer({
                    'event': 'fp_form_conversion',
                    'form_id': formId,
                    'form_title': formTitle,
                    'conversion_type': 'form_submission',
                    'conversion_value': 1.0
                });
                
                this.sendToGA4('conversion', {
                    'send_to': 'AW-CONVERSION_ID', // User configurable
                    'form_id': formId,
                    'value': 1.0,
                    'currency': 'EUR'
                });
                
                // Generate Lead event (standard GA4)
                this.sendToGA4('generate_lead', {
                    'form_id': formId,
                    'form_name': formTitle,
                    'value': 1.0,
                    'currency': 'EUR'
                });
                
            } else {
                // Submission error
                this.pushToDataLayer({
                    'event': 'fp_form_error',
                    'form_id': formId,
                    'form_title': formTitle,
                    'error_message': errorMessage || 'Unknown error',
                    'event_category': 'error'
                });
                
                this.sendToGA4('form_error', {
                    'form_id': formId,
                    'form_name': formTitle,
                    'error_type': errorMessage || 'submission_error'
                });
            }
        },
        
        /**
         * Track field interaction
         */
        trackFieldInteraction: function(formId, fieldName, fieldType) {
            if (!this.trackInteractions) return;
            
            this.pushToDataLayer({
                'event': 'fp_form_field_interaction',
                'form_id': formId,
                'field_name': fieldName,
                'field_type': fieldType
            });
        },
        
        /**
         * Inizializza tracking per tutti i form
         */
        init: function() {
            var self = this;
            
                // Trova tutti i form FP-Forms
                var forms = document.querySelectorAll('.fp-forms-container form');
                
                forms.forEach(function(form) {
                    var formIdElement = form.querySelector('[name="form_id"]');
                    var formId = formIdElement ? formIdElement.value : null;
                    var formTitle = form.closest('.fp-forms-container').dataset.formTitle || 'Untitled Form';
                    
                    if (!formId) return;
                    
                    // Track view on page load
                    setTimeout(function() {
                        self.trackFormView(formId, formTitle);
                    }, 100);
                    
                    // Track form start (first field focus)
                    var hasStarted = false;
                    var inputs = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
                    
                    inputs.forEach(function(input) {
                        // Track form start (once)
                        input.addEventListener('focus', function() {
                            if (!hasStarted) {
                                hasStarted = true;
                                self.trackFormStart(formId, formTitle);
                            }
                            
                            // Track field interaction (se abilitato)
                            if (self.trackInteractions) {
                                var fieldName = input.name.replace('fp_field_', '');
                                var fieldType = input.type || input.tagName.toLowerCase();
                                self.trackFieldInteraction(formId, fieldName, fieldType);
                            }
                        }, { once: true });
                        
                        // Track progress on input
                        input.addEventListener('input', function() {
                            var progress = self.calculateProgress(form);
                            self.trackFormProgress(formId, formTitle, progress);
                        });
                    });
                    
                    // Track abandon (page unload without submit)
                    window.addEventListener('beforeunload', function() {
                        if (hasStarted && !form.dataset.submitted) {
                            self.trackFormAbandon(formId, formTitle);
                        }
                    });
                    
                    // Track submission success
                    form.addEventListener('fpFormSubmitSuccess', function(e) {
                        form.dataset.submitted = 'true';
                        self.trackFormSubmit(formId, formTitle, true);
                    });
                    
                    // Track submission errors
                    form.addEventListener('fpFormSubmitError', function(e) {
                        var errorMsg = (e.detail && e.detail.message) ? e.detail.message : 'Unknown error';
                        self.trackFormSubmit(formId, formTitle, false, errorMsg);
                        
                        // Track validation errors specifici
                        if (e.detail && e.detail.errors) {
                            Object.keys(e.detail.errors).forEach(function(fieldName) {
                                self.trackValidationError(
                                    formId, 
                                    formTitle, 
                                    fieldName, 
                                    e.detail.errors[fieldName]
                                );
                            });
                        }
                    });
                });
            },
            
            /**
             * Calculate form progress
             */
            calculateProgress: function(form) {
                var fields = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
                var filled = 0;
                
                fields.forEach(function(field) {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        if (field.checked) filled++;
                    } else if (field.value && field.value.trim() !== '') {
                        filled++;
                    }
                });
                
                return Math.round((filled / fields.length) * 100);
            }
    };
    
    // Init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            fpFormsTracking.init();
        });
    } else {
        fpFormsTracking.init();
    }
    
})();
</script>
        <?php
    }
    
    /**
     * Track submission server-side (per backup/logging)
     */
    public function track_submission( $form_id, $submission_id, $success = true ) {
        if ( ! $this->enabled ) {
            return;
        }
        
        // Salva tracking data per analytics dashboard
        $tracking_data = [
            'form_id' => $form_id,
            'submission_id' => $submission_id,
            'success' => $success,
            'timestamp' => current_time( 'mysql' ),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'ip' => $this->get_user_ip(),
        ];
        
        // Salva in meta per analisi
        update_post_meta( $submission_id, '_fp_tracking_data', $tracking_data );
        
        // Hook per integrazioni esterne
        do_action( 'fp_forms_tracking_submission', $tracking_data );
    }
    
    /**
     * Ottiene IP utente
     */
    private function get_user_ip() {
        $ip = '';
        
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        return sanitize_text_field( $ip );
    }
    
    /**
     * Ottiene statistiche tracking per form
     */
    public function get_form_stats( $form_id, $days = 30 ) {
        global $wpdb;
        
        $submissions_table = $wpdb->prefix . 'fp_forms_submissions';
        
        $date_from = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
        
        // Total submissions
        $total = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$submissions_table} WHERE form_id = %d AND created_at >= %s",
            $form_id,
            $date_from
        ) );
        
        // Submissions per day
        $per_day = $wpdb->get_results( $wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM {$submissions_table} 
             WHERE form_id = %d AND created_at >= %s
             GROUP BY DATE(created_at)
             ORDER BY date DESC",
            $form_id,
            $date_from
        ) );
        
        return [
            'total' => (int) $total,
            'per_day' => $per_day,
            'average_per_day' => $total > 0 ? round( $total / $days, 2 ) : 0,
        ];
    }
}
