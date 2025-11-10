<?php
namespace FPForms\Security;

/**
 * Google reCAPTCHA Integration
 * Supporta v2 (checkbox) e v3 (invisible/score)
 * 
 * @since 1.2.0
 */
class ReCaptcha {
    
    /**
     * reCAPTCHA API endpoint
     */
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    
    /**
     * Versione reCAPTCHA
     */
    private $version;
    
    /**
     * Site key
     */
    private $site_key;
    
    /**
     * Secret key
     */
    private $secret_key;
    
    /**
     * Score minimo per v3 (0.0 - 1.0)
     */
    private $min_score;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->load_settings();
    }
    
    /**
     * Carica impostazioni
     */
    private function load_settings() {
        $settings = get_option( 'fp_forms_recaptcha_settings', [] );
        
        $this->version = $settings['version'] ?? 'v2';
        $this->site_key = $settings['site_key'] ?? '';
        $this->secret_key = $settings['secret_key'] ?? '';
        $this->min_score = (float) ( $settings['min_score'] ?? 0.5 );
    }
    
    /**
     * Verifica se reCAPTCHA è configurato
     */
    public function is_configured() {
        return ! empty( $this->site_key ) && ! empty( $this->secret_key );
    }
    
    /**
     * Ottiene la versione reCAPTCHA
     */
    public function get_version() {
        return $this->version;
    }
    
    /**
     * Ottiene la site key
     */
    public function get_site_key() {
        return $this->site_key;
    }
    
    /**
     * Renderizza il campo reCAPTCHA
     */
    public function render_field( $form_id ) {
        if ( ! $this->is_configured() ) {
            return $this->render_not_configured_notice();
        }
        
        $html = '<div class="fp-forms-recaptcha-field">';
        
        if ( $this->version === 'v2' ) {
            // reCAPTCHA v2 (checkbox)
            $html .= sprintf(
                '<div class="g-recaptcha" data-sitekey="%s" data-theme="light"></div>',
                esc_attr( $this->site_key )
            );
        } else {
            // reCAPTCHA v3 (invisible)
            $html .= sprintf(
                '<input type="hidden" id="fp-recaptcha-token-%s" name="fp_recaptcha_token" value="">',
                esc_attr( $form_id )
            );
            $html .= '<p class="fp-forms-recaptcha-info">';
            $html .= '<small>';
            $html .= __( 'Questo sito è protetto da reCAPTCHA e si applicano le ', 'fp-forms' );
            $html .= '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener">' . __( 'Privacy Policy', 'fp-forms' ) . '</a>';
            $html .= ' e i ';
            $html .= '<a href="https://policies.google.com/terms" target="_blank" rel="noopener">' . __( 'Termini di Servizio', 'fp-forms' ) . '</a>';
            $html .= ' di Google.';
            $html .= '</small>';
            $html .= '</p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Notice quando reCAPTCHA non è configurato
     */
    private function render_not_configured_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return '';
        }
        
        $settings_url = admin_url( 'admin.php?page=fp-forms-settings#recaptcha' );
        
        return sprintf(
            '<div class="fp-forms-notice fp-forms-notice-warning">
                <span class="dashicons dashicons-warning"></span>
                <p>%s <a href="%s">%s</a></p>
            </div>',
            esc_html__( 'reCAPTCHA non configurato.', 'fp-forms' ),
            esc_url( $settings_url ),
            esc_html__( 'Configura ora', 'fp-forms' )
        );
    }
    
    /**
     * Enqueue scripts reCAPTCHA
     */
    public function enqueue_scripts( $form_id = null ) {
        if ( ! $this->is_configured() ) {
            return;
        }
        
        $script_url = 'https://www.google.com/recaptcha/api.js';
        
        if ( $this->version === 'v3' ) {
            $script_url = add_query_arg( 'render', $this->site_key, $script_url );
            
            // Enqueue script v3
            wp_enqueue_script(
                'google-recaptcha-v3',
                $script_url,
                [],
                null,
                true
            );
            
            // Script per generare token
            $inline_script = "
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(function() {
                        var forms = document.querySelectorAll('.fp-forms-container form');
                        forms.forEach(function(form) {
                            form.addEventListener('submit', function(e) {
                                var tokenField = form.querySelector('[name=\"fp_recaptcha_token\"]');
                                if (tokenField && !tokenField.value) {
                                    e.preventDefault();
                                    grecaptcha.execute('" . esc_js( $this->site_key ) . "', {action: 'fp_forms_submit'}).then(function(token) {
                                        tokenField.value = token;
                                        form.submit();
                                    });
                                }
                            });
                        });
                    });
                }
            });
            ";
            
            wp_add_inline_script( 'google-recaptcha-v3', $inline_script );
        } else {
            // Enqueue script v2
            wp_enqueue_script(
                'google-recaptcha-v2',
                $script_url,
                [],
                null,
                true
            );
        }
    }
    
    /**
     * Verifica la risposta reCAPTCHA
     * 
     * @param string $token Token reCAPTCHA dalla submission
     * @return array ['success' => bool, 'error' => string|null, 'score' => float|null]
     */
    public function verify( $token ) {
        if ( ! $this->is_configured() ) {
            return [
                'success' => false,
                'error' => __( 'reCAPTCHA non configurato', 'fp-forms' ),
                'score' => null,
            ];
        }
        
        if ( empty( $token ) ) {
            return [
                'success' => false,
                'error' => __( 'Verifica reCAPTCHA richiesta', 'fp-forms' ),
                'score' => null,
            ];
        }
        
        // Prepara richiesta
        $data = [
            'secret' => $this->secret_key,
            'response' => $token,
            'remoteip' => $this->get_user_ip(),
        ];
        
        // Chiamata API Google
        $api_response = wp_remote_post( self::VERIFY_URL, [
            'body' => $data,
            'timeout' => 10,
        ] );
        
        if ( is_wp_error( $api_response ) ) {
            return [
                'success' => false,
                'error' => __( 'Errore di connessione reCAPTCHA', 'fp-forms' ),
                'score' => null,
            ];
        }
        
        $body = wp_remote_retrieve_body( $api_response );
        $result = json_decode( $body, true );
        
        if ( ! $result || ! isset( $result['success'] ) ) {
            return [
                'success' => false,
                'error' => __( 'Risposta reCAPTCHA non valida', 'fp-forms' ),
                'score' => null,
            ];
        }
        
        // Verifica successo
        if ( ! $result['success'] ) {
            $error_codes = $result['error-codes'] ?? [];
            return [
                'success' => false,
                'error' => $this->get_error_message( $error_codes ),
                'score' => null,
            ];
        }
        
        // Per v3, verifica lo score
        if ( $this->version === 'v3' ) {
            $score = $result['score'] ?? 0.0;
            
            if ( $score < $this->min_score ) {
                return [
                    'success' => false,
                    'error' => __( 'Verifica reCAPTCHA fallita. Riprova.', 'fp-forms' ),
                    'score' => $score,
                ];
            }
            
            return [
                'success' => true,
                'error' => null,
                'score' => $score,
            ];
        }
        
        // v2 successo
        return [
            'success' => true,
            'error' => null,
            'score' => null,
        ];
    }
    
    /**
     * Ottiene IP dell'utente
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
     * Ottiene messaggio di errore user-friendly
     */
    private function get_error_message( $error_codes ) {
        if ( empty( $error_codes ) ) {
            return __( 'Verifica reCAPTCHA fallita', 'fp-forms' );
        }
        
        $code = is_array( $error_codes ) ? $error_codes[0] : $error_codes;
        
        $messages = [
            'missing-input-secret' => __( 'Configurazione reCAPTCHA non valida', 'fp-forms' ),
            'invalid-input-secret' => __( 'Chiave segreta reCAPTCHA non valida', 'fp-forms' ),
            'missing-input-response' => __( 'Verifica reCAPTCHA richiesta', 'fp-forms' ),
            'invalid-input-response' => __( 'Risposta reCAPTCHA non valida', 'fp-forms' ),
            'bad-request' => __( 'Richiesta reCAPTCHA malformata', 'fp-forms' ),
            'timeout-or-duplicate' => __( 'Verifica reCAPTCHA scaduta o duplicata', 'fp-forms' ),
        ];
        
        return $messages[ $code ] ?? __( 'Verifica reCAPTCHA fallita', 'fp-forms' );
    }
    
    /**
     * Test connessione reCAPTCHA (per admin settings)
     */
    public function test_connection() {
        if ( ! $this->is_configured() ) {
            return [
                'success' => false,
                'message' => __( 'Inserisci Site Key e Secret Key', 'fp-forms' ),
            ];
        }
        
        // Test con una risposta vuota (deve fallire ma rispondere)
        $response = wp_remote_post( self::VERIFY_URL, [
            'body' => [
                'secret' => $this->secret_key,
                'response' => 'test',
            ],
            'timeout' => 10,
        ] );
        
        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'message' => __( 'Errore di connessione: ', 'fp-forms' ) . $response->get_error_message(),
            ];
        }
        
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );
        
        if ( ! $result ) {
            return [
                'success' => false,
                'message' => __( 'Risposta API non valida', 'fp-forms' ),
            ];
        }
        
        // Se riceviamo una risposta valida (anche se fallita), la connessione funziona
        return [
            'success' => true,
            'message' => __( 'Connessione reCAPTCHA attiva! Le chiavi sembrano valide.', 'fp-forms' ),
        ];
    }
}
