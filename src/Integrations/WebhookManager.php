<?php
namespace FPForms\Integrations;

/**
 * Webhook Manager
 * Gestisce invio webhook dopo submission
 */
class WebhookManager {
    
    /**
     * Costruttore
     */
    public function __construct() {
        add_action( 'fp_forms_after_save_submission', [ $this, 'trigger_webhooks' ], 10, 3 );
    }
    
    /**
     * Trigger webhooks per form
     */
    public function trigger_webhooks( $submission_id, $form_id, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form || ! isset( $form['settings']['webhooks'] ) || ! is_array( $form['settings']['webhooks'] ) ) {
            return;
        }
        
        foreach ( $form['settings']['webhooks'] as $webhook ) {
            if ( ! isset( $webhook['enabled'] ) || ! $webhook['enabled'] ) {
                continue;
            }
            
            if ( empty( $webhook['url'] ) ) {
                continue;
            }
            
            $this->send_webhook( $webhook, $form_id, $submission_id, $data );
        }
    }
    
    /**
     * Valida struttura e schema dell'URL (senza risoluzione DNS).
     * Usato ad ogni submission per evitare blocchi sincroni.
     */
    private function is_safe_webhook_url( $url ) {
        if ( ! wp_http_validate_url( $url ) ) {
            return false;
        }

        $parsed = wp_parse_url( $url );
        if ( ! in_array( $parsed['scheme'] ?? '', [ 'http', 'https' ], true ) ) {
            return false;
        }

        $host = $parsed['host'] ?? '';
        if ( empty( $host ) ) {
            return false;
        }

        // Blocca IP letterali privati/riservati (nessuna risoluzione DNS qui)
        if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
            if ( filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida URL con risoluzione DNS (per uso al salvataggio, non ad ogni submission).
     * Previene SSRF tramite hostname che risolvono a IP interni.
     * @param string $url
     * @return bool
     */
    public function is_safe_url_for_save( $url ) {
        if ( ! $this->is_safe_webhook_url( $url ) ) {
            return false;
        }

        $parsed = wp_parse_url( $url );
        $host   = $parsed['host'] ?? '';

        // Se è già un IP letterale, il check è già stato fatto in is_safe_webhook_url
        if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
            return true;
        }

        // Risolve il hostname: se la risoluzione fallisce, gethostbyname restituisce l'input invariato
        $resolved_ip = gethostbyname( $host );
        if ( $resolved_ip === $host ) {
            return false;
        }

        if ( filter_var( $resolved_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
            return false;
        }

        return true;
    }
    
    private function send_webhook( $webhook, $form_id, $submission_id, $data ) {
        // Prevenzione SSRF: valida URL prima di inviare
        if ( ! $this->is_safe_webhook_url( $webhook['url'] ) ) {
            \FPForms\Core\Logger::warning( 'Webhook URL non sicuro, invio bloccato', [
                'url' => $webhook['url'],
                'form_id' => $form_id,
            ] );
            return false;
        }
        
        $payload = [
            'form_id' => $form_id,
            'submission_id' => $submission_id,
            'data' => $data,
            'timestamp' => time(),
            'site_url' => home_url(),
            'form_title' => get_the_title( $form_id ),
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
        ];
        
        // Aggiungi signature se secret key presente
        if ( ! empty( $webhook['secret'] ) ) {
            $signature = $this->generate_signature( $payload, $webhook['secret'] );
            $headers['X-FP-Forms-Signature'] = $signature;
        }
        
        $response = wp_remote_post( $webhook['url'], [
            'body' => wp_json_encode( $payload ),
            'headers' => $headers,
            'timeout' => 30,
            'blocking' => false, // Non bloccare la risposta al form
        ] );
        
        // Con blocking:false wp_remote_post restituisce true, non la risposta HTTP.
        // Il log del response code non è disponibile in modalità non-bloccante.
        if ( is_wp_error( $response ) ) {
            \FPForms\Core\Logger::warning( 'Webhook error', [
                'url' => $webhook['url'],
                'error' => $response->get_error_message(),
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
        } else {
            \FPForms\Core\Logger::info( 'Webhook inviato (non-blocking)', [
                'url' => $webhook['url'],
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
        }
        
        return $response;
    }
    
    /**
     * Genera signature HMAC
     */
    private function generate_signature( $payload, $secret ) {
        $payload_json = wp_json_encode( $payload );
        return hash_hmac( 'sha256', $payload_json, $secret );
    }
    
    /**
     * Verifica signature webhook
     */
    public function verify_signature( $payload, $signature, $secret ) {
        $expected = $this->generate_signature( $payload, $secret );
        return hash_equals( $expected, $signature );
    }
    
    /**
     * Test webhook
     */
    public function test_webhook( $url, $secret = '' ) {
        // Il test è un'azione esplicita dell'admin: usa la validazione completa con risoluzione DNS
        // per prevenire SSRF tramite hostname che risolvono a IP interni.
        if ( ! $this->is_safe_url_for_save( $url ) ) {
            return [
                'success' => false,
                'message' => __( 'URL non valido o non sicuro.', 'fp-forms' ),
            ];
        }
        
        $test_payload = [
            'form_id' => 0,
            'submission_id' => 0,
            'data' => [
                'test' => true,
                'message' => 'This is a test webhook from FP Forms',
            ],
            'timestamp' => time(),
            'site_url' => home_url(),
            'form_title' => 'Test Form',
        ];
        
        $headers = [
            'Content-Type' => 'application/json',
        ];
        
        if ( ! empty( $secret ) ) {
            $signature = $this->generate_signature( $test_payload, $secret );
            $headers['X-FP-Forms-Signature'] = $signature;
        }
        
        $response = wp_remote_post( $url, [
            'body' => wp_json_encode( $test_payload ),
            'headers' => $headers,
            'timeout' => 10,
            'blocking' => true,
        ] );
        
        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
            ];
        }
        
        $status_code = wp_remote_retrieve_response_code( $response );
        
        return [
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'message' => $status_code >= 200 && $status_code < 300 
                ? __( 'Webhook inviato con successo!', 'fp-forms' )
                : __( 'Webhook ha ricevuto risposta non valida.', 'fp-forms' ),
        ];
    }
}

