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
     * Invia webhook
     */
    private function send_webhook( $webhook, $form_id, $submission_id, $data ) {
        $payload = [
            'form_id' => $form_id,
            'submission_id' => $submission_id,
            'data' => $data,
            'timestamp' => current_time( 'timestamp' ),
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
        
        // Log risultato
        if ( is_wp_error( $response ) ) {
            \FPForms\Core\Logger::warning( 'Webhook error', [
                'url' => $webhook['url'],
                'error' => $response->get_error_message(),
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
        } else {
            $status_code = wp_remote_retrieve_response_code( $response );
            \FPForms\Core\Logger::info( 'Webhook sent', [
                'url' => $webhook['url'],
                'status' => $status_code,
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
        $test_payload = [
            'form_id' => 0,
            'submission_id' => 0,
            'data' => [
                'test' => true,
                'message' => 'This is a test webhook from FP Forms',
            ],
            'timestamp' => current_time( 'timestamp' ),
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

