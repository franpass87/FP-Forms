<?php
namespace FPForms\Integrations;

/**
 * Meta (Facebook) Pixel & Conversions API Integration
 * 
 * Supporta:
 * - Facebook Pixel (client-side)
 * - Conversions API (server-side) per iOS 14.5+ tracking
 * 
 * @since 1.2.0
 */
class MetaPixel {
    
    /**
     * Conversions API endpoint
     * Usa versione più recente dell'API Facebook Graph
     */
    const CAPI_ENDPOINT = 'https://graph.facebook.com/v21.0';
    
    /**
     * Pixel ID
     */
    private $pixel_id;
    
    /**
     * Access Token (per Conversions API)
     */
    private $access_token;
    
    /**
     * Enabled
     */
    private $enabled;
    
    /**
     * Use Conversions API
     */
    private $use_capi;
    
    /**
     * Track form views
     */
    private $track_views;
    
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
     * Carica settings
     */
    private function load_settings() {
        $settings = get_option( 'fp_forms_meta_settings', [] );
        
        $this->pixel_id = $settings['pixel_id'] ?? '';
        $this->access_token = $settings['access_token'] ?? '';
        $this->enabled = ! empty( $this->pixel_id );
        $this->use_capi = ! empty( $this->access_token );
        $this->track_views = $settings['track_views'] ?? true;
    }
    
    /**
     * Inizializza hooks
     */
    private function init_hooks() {
        // Pixel script in head
        add_action( 'wp_head', [ $this, 'render_pixel_script' ], 5 );
        
        // Events script in footer
        add_action( 'wp_footer', [ $this, 'render_events_script' ] );
        
        // Server-side tracking dopo submission
        add_action( 'fp_forms_after_save_submission', [ $this, 'track_conversion_server_side' ], 10, 3 );
    }
    
    /**
     * Verifica se è attivo
     */
    public function is_enabled() {
        return $this->enabled;
    }
    
    /**
     * Renderizza Facebook Pixel base script
     */
    public function render_pixel_script() {
        if ( empty( $this->pixel_id ) ) {
            return;
        }
        
        ?>
<!-- Meta Pixel Code (FP Forms) -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo esc_js( $this->pixel_id ); ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo esc_attr( $this->pixel_id ); ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
        <?php
    }
    
    /**
     * Renderizza script eventi form
     */
    public function render_events_script() {
        if ( ! $this->enabled ) {
            return;
        }
        
        ?>
<script>
(function() {
    'use strict';
    
    if (typeof fbq !== 'function') {
        console.warn('[FP Forms] Meta Pixel not loaded');
        return;
    }
    
    var fpMetaTracking = {
        trackViews: <?php echo $this->track_views ? 'true' : 'false'; ?>,
        trackedForms: [],
        formStarted: {},
        formProgress: {},
        formTimers: {},
        
        /**
         * Track form view
         */
        trackFormView: function(formId, formTitle) {
            if (!this.trackViews) return;
            if (this.trackedForms.indexOf(formId) !== -1) return;
            
            this.trackedForms.push(formId);
            
            // Meta standard event: ViewContent
            fbq('track', 'ViewContent', {
                content_name: formTitle,
                content_category: 'form',
                content_ids: [formId],
                content_type: 'form_view'
            });
            
            console.log('[FP Forms Meta] Form View tracked:', formId);
        },
        
        /**
         * Track form start (primo campo focus)
         */
        trackFormStart: function(formId, formTitle) {
            if (this.formStarted[formId]) return;
            
            this.formStarted[formId] = true;
            this.formTimers[formId] = Date.now();
            
            // Custom event: FormStart
            fbq('trackCustom', 'FormStart', {
                form_id: formId,
                form_title: formTitle,
                event_category: 'engagement'
            });
            
            console.log('[FP Forms Meta] Form Start tracked:', formId);
        },
        
        /**
         * Track form progress (% compilazione)
         */
        trackFormProgress: function(formId, formTitle, progress) {
            var progressKey = formId + '_' + progress;
            
            if (this.formProgress[progressKey]) return;
            this.formProgress[progressKey] = true;
            
            // Custom event: FormProgress (a 25%, 50%, 75%)
            if ([25, 50, 75].indexOf(progress) !== -1) {
                fbq('trackCustom', 'FormProgress', {
                    form_id: formId,
                    form_title: formTitle,
                    progress_percent: progress,
                    event_category: 'engagement'
                });
                
                console.log('[FP Forms Meta] Form Progress tracked:', formId, progress + '%');
            }
        },
        
        /**
         * Track form abandon
         */
        trackFormAbandon: function(formId, formTitle) {
            if (!this.formStarted[formId]) return;
            
            var timeSpent = this.formTimers[formId] ? 
                Math.round((Date.now() - this.formTimers[formId]) / 1000) : 0;
            
            // Custom event: FormAbandoned
            fbq('trackCustom', 'FormAbandoned', {
                form_id: formId,
                form_title: formTitle,
                time_spent_seconds: timeSpent,
                event_category: 'abandonment'
            });
            
            console.log('[FP Forms Meta] Form Abandoned:', formId, timeSpent + 's');
        },
        
        /**
         * Track validation error
         */
        trackValidationError: function(formId, formTitle, fieldName, errorMessage) {
            fbq('trackCustom', 'FormValidationError', {
                form_id: formId,
                form_title: formTitle,
                field_name: fieldName,
                error_message: errorMessage,
                event_category: 'error'
            });
            
            console.log('[FP Forms Meta] Validation Error:', fieldName, errorMessage);
        },
        
        /**
         * Track form submission (Lead)
         */
        trackFormSubmit: function(formId, formTitle, success, submissionId) {
            var timeSpent = this.formTimers[formId] ? 
                Math.round((Date.now() - this.formTimers[formId]) / 1000) : 0;
            
            if (success) {
                // BUGFIX #26: Generate event_id for deduplication with CAPI
                var eventId = 'fp_forms_' + submissionId + '_' + Date.now();
                
                // Meta standard event: Lead (CONVERSIONE PRINCIPALE)
                fbq('track', 'Lead', {
                    content_name: formTitle,
                    content_category: 'form_submission',
                    content_ids: [formId],
                    value: 1.0,
                    currency: 'EUR',
                    status: 'completed'
                }, {
                    eventID: eventId // BUGFIX #26: Deduplication with server-side CAPI
                });
                
                // Custom event: CompleteRegistration (se è signup form)
                if (formTitle.toLowerCase().indexOf('registr') !== -1 || 
                    formTitle.toLowerCase().indexOf('signup') !== -1 ||
                    formTitle.toLowerCase().indexOf('iscriz') !== -1) {
                    fbq('track', 'CompleteRegistration', {
                        content_name: formTitle,
                        status: 'completed',
                        value: 1.0,
                        currency: 'EUR'
                    }, {
                        eventID: eventId + '_reg' // BUGFIX #26: Separate event ID for registration
                    });
                }
                
                // Custom event per tracking avanzato
                fbq('trackCustom', 'FormSubmission', {
                    form_id: formId,
                    form_title: formTitle,
                    submission_id: submissionId,
                    time_spent_seconds: timeSpent,
                    event_category: 'conversion'
                });
                
                console.log('[FP Forms Meta] Lead tracked:', formId, '(' + timeSpent + 's)');
            }
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
        },
        
        /**
         * Inizializza tracking
         */
        init: function() {
            var self = this;
            
            // Track form views
            var forms = document.querySelectorAll('.fp-forms-container form');
            
            forms.forEach(function(form) {
                var formIdElement = form.querySelector('[name="form_id"]');
                var formId = formIdElement ? formIdElement.value : null;
                var formTitle = form.closest('.fp-forms-container').dataset.formTitle || 'Untitled Form';
                
                if (!formId) return;
                
                // Track view
                setTimeout(function() {
                    self.trackFormView(formId, formTitle);
                }, 100);
                
                var hasStarted = false;
                var inputs = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
                
                // Track form start (first interaction)
                inputs.forEach(function(input) {
                    input.addEventListener('focus', function() {
                        if (!hasStarted) {
                            hasStarted = true;
                            self.trackFormStart(formId, formTitle);
                        }
                    }, { once: true });
                    
                    // Track progress on input
                    input.addEventListener('input', function() {
                        var progress = self.calculateProgress(form);
                        self.trackFormProgress(formId, formTitle, progress);
                    });
                });
                
                // Track abandon (page unload senza submit)
                window.addEventListener('beforeunload', function() {
                    if (hasStarted && !form.dataset.submitted) {
                        self.trackFormAbandon(formId, formTitle);
                    }
                });
                
                // Track submission success
                form.addEventListener('fpFormSubmitSuccess', function(e) {
                    form.dataset.submitted = 'true';
                    self.trackFormSubmit(
                        formId, 
                        formTitle, 
                        true, 
                        (e.detail && e.detail.submissionId ? e.detail.submissionId : null)
                    );
                });
                
                // Track validation errors
                form.addEventListener('fpFormSubmitError', function(e) {
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
        }
    };
    
    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            fpMetaTracking.init();
        });
    } else {
        fpMetaTracking.init();
    }
    
})();
</script>
        <?php
    }
    
    /**
     * Track conversion via Conversions API (server-side)
     * Più affidabile di pixel per iOS 14.5+ e ad blockers
     */
    public function track_conversion_server_side( $submission_id, $form_id, $data ) {
        if ( ! $this->use_capi ) {
            return;
        }
        
        // Ottieni form
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return;
        }
        
        // Estrai dati utente
        $user_data = $this->prepare_user_data( $data );
        
        // BUGFIX #26: Add event_id for deduplication (prevent Pixel + CAPI double counting)
        $event_id = 'fp_forms_' . $submission_id . '_' . time();
        
        // Prepara event data
        $event_data = [
            'event_name' => 'Lead',
            'event_time' => time(),
            'event_id' => $event_id, // BUGFIX #26: Deduplication ID
            'action_source' => 'website',
            'event_source_url' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : get_site_url(),
            'user_data' => $user_data,
            'custom_data' => [
                'form_id' => $form_id,
                'form_title' => $form['title'],
                'submission_id' => $submission_id,
                'currency' => 'EUR',
                'value' => 1.0,
            ],
        ];
        
        // Aggiungi fbp e fbc se disponibili (from cookies)
        if ( isset( $_COOKIE['_fbp'] ) ) {
            $event_data['user_data']['fbp'] = sanitize_text_field( $_COOKIE['_fbp'] );
        }
        
        if ( isset( $_COOKIE['_fbc'] ) ) {
            $event_data['user_data']['fbc'] = sanitize_text_field( $_COOKIE['_fbc'] );
        }
        
        // Invia a Conversions API
        $result = $this->send_conversion_event( $event_data );
        
        if ( $result['success'] ) {
            \FPForms\Core\Logger::info( 'Meta CAPI event sent', [
                'form_id' => $form_id,
                'event' => 'Lead',
            ] );
        } else {
            \FPForms\Core\Logger::error( 'Meta CAPI failed', [
                'form_id' => $form_id,
                'error' => $result['error'],
            ] );
        }
    }
    
    /**
     * Prepara user_data per Conversions API
     */
    private function prepare_user_data( $data ) {
        $user_data = [
            'client_ip_address' => $this->get_user_ip(),
            'client_user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
        ];
        
        // Estrai email (hashed)
        foreach ( $data as $key => $value ) {
            // Skip se array (checkbox multipli, etc.)
            if ( is_array( $value ) ) {
                continue;
            }
            
            $clean_key = str_replace( 'fp_field_', '', $key );
            
            if ( stripos( $clean_key, 'email' ) !== false && is_email( $value ) ) {
                $user_data['em'] = hash( 'sha256', strtolower( trim( $value ) ) );
                break;
            }
        }
        
        // Estrai nome e cognome (hashed)
        foreach ( $data as $key => $value ) {
            // Skip se array
            if ( is_array( $value ) ) {
                continue;
            }
            
            // Converti a stringa per sicurezza
            $value = (string) $value;
            
            $clean_key = str_replace( 'fp_field_', '', $key );
            
            if ( in_array( strtolower( $clean_key ), [ 'nome', 'name', 'first_name', 'firstname' ] ) ) {
                $user_data['fn'] = hash( 'sha256', strtolower( trim( $value ) ) );
            }
            
            if ( in_array( strtolower( $clean_key ), [ 'cognome', 'surname', 'last_name', 'lastname' ] ) ) {
                $user_data['ln'] = hash( 'sha256', strtolower( trim( $value ) ) );
            }
            
            if ( in_array( strtolower( $clean_key ), [ 'telefono', 'phone', 'tel' ] ) ) {
                // Rimuovi spazi e caratteri non numerici
                $phone = preg_replace( '/[^0-9+]/', '', $value );
                if ( ! empty( $phone ) ) {
                    $user_data['ph'] = hash( 'sha256', $phone );
                }
            }
        }
        
        return $user_data;
    }
    
    /**
     * Invia evento a Conversions API
     */
    private function send_conversion_event( $event_data ) {
        if ( ! $this->use_capi ) {
            return [ 'success' => false, 'error' => 'CAPI not configured' ];
        }
        
        $url = self::CAPI_ENDPOINT . '/' . $this->pixel_id . '/events';
        
        $payload = [
            'data' => [ $event_data ],
            'test_event_code' => apply_filters( 'fp_forms_meta_test_event_code', '' ),
        ];
        
        // Invia a Conversions API con access token
        $response = wp_remote_post( 
            $url . '?access_token=' . urlencode( $this->access_token ),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => wp_json_encode( $payload ),
                'timeout' => 15,
            ]
        );
        
        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'error' => $response->get_error_message(),
            ];
        }
        
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );
        
        // Verifica errori API
        if ( isset( $result['error'] ) ) {
            return [
                'success' => false,
                'error' => $result['error']['message'] ?? 'Unknown error',
            ];
        }
        
        return [
            'success' => true,
            'error' => null,
            'events_received' => $result['events_received'] ?? 0,
        ];
    }
    
    /**
     * Test connessione Conversions API
     */
    public function test_connection() {
        if ( empty( $this->pixel_id ) ) {
            return [
                'success' => false,
                'message' => __( 'Inserisci Pixel ID', 'fp-forms' ),
            ];
        }
        
        if ( ! $this->use_capi ) {
            return [
                'success' => true,
                'message' => __( 'Facebook Pixel configurato (solo client-side). Aggiungi Access Token per Conversions API server-side.', 'fp-forms' ),
            ];
        }
        
        // Test event
        $test_event = [
            'event_name' => 'PageView',
            'event_time' => time(),
            'action_source' => 'website',
            'event_source_url' => get_site_url(),
            'user_data' => [
                'client_ip_address' => $this->get_user_ip(),
                'client_user_agent' => 'FP-Forms-Test',
            ],
        ];
        
        $result = $this->send_conversion_event( $test_event );
        
        if ( $result['success'] ) {
            return [
                'success' => true,
                'message' => sprintf(
                    __( 'Connessione attiva! Eventi ricevuti: %d. Facebook Pixel + Conversions API configurati correttamente.', 'fp-forms' ),
                    $result['events_received'] ?? 0
                ),
            ];
        }
        
        return [
            'success' => false,
            'message' => __( 'Errore connessione: ', 'fp-forms' ) . $result['error'],
        ];
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
}
