<?php
namespace FPForms\Integrations;

/**
 * Brevo (ex Sendinblue) Integration
 * API v3 - Contacts & Events Tracking
 * 
 * @since 1.2.0
 */
class Brevo {
    
    /**
     * API Base URL
     */
    const API_BASE = 'https://api.brevo.com/v3';
    
    /**
     * API Key
     */
    private $api_key;
    
    /**
     * Enabled
     */
    private $enabled;
    
    /**
     * Default list ID
     */
    private $default_list_id;
    
    /**
     * Enable double opt-in
     */
    private $double_optin;
    
    /**
     * Track events
     */
    private $track_events;
    
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
        $settings = get_option( 'fp_forms_brevo_settings', [] );
        
        $this->api_key = $settings['api_key'] ?? '';
        $this->enabled = ! empty( $this->api_key );
        $this->default_list_id = $settings['default_list_id'] ?? '';
        $this->double_optin = $settings['double_optin'] ?? false;
        $this->track_events = $settings['track_events'] ?? true;
    }
    
    /**
     * Inizializza hooks
     */
    private function init_hooks() {
        // Sync dopo submission
        add_action( 'fp_forms_after_save_submission', [ $this, 'sync_contact_after_submission' ], 10, 3 );
    }
    
    /**
     * Verifica se integrazione è attiva
     */
    public function is_enabled() {
        return $this->enabled;
    }
    
    /**
     * Sync contatto dopo submission
     */
    public function sync_contact_after_submission( $submission_id, $form_id, $data ) {
        // Ottieni form settings
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return;
        }
        
        // Check se Brevo è abilitato per questo form
        // Se non specificato nel form, usa la configurazione globale
        $brevo_enabled = $form['settings']['brevo_enabled'] ?? true; // Default: sync sempre se Brevo configurato
        
        if ( ! $brevo_enabled ) {
            return;
        }
        
        // Ottieni lista ID (form-specific o default)
        $list_id = $form['settings']['brevo_list_id'] ?? $this->default_list_id;
        
        if ( empty( $list_id ) ) {
            \FPForms\Core\Logger::warning( 'Brevo sync skipped: no list ID', [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
            return;
        }
        
        // Estrai email dai dati
        $email = $this->extract_email( $data );
        
        if ( ! $email ) {
            \FPForms\Core\Logger::warning( 'Brevo sync skipped: no email found', [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
            ] );
            return;
        }
        
        // Prepara attributi contatto
        $attributes = $this->prepare_contact_attributes( $data, $form );
        
        // Crea/aggiorna contatto
        $contact_result = $this->create_or_update_contact( $email, $attributes, [ $list_id ] );
        
        if ( ! $contact_result['success'] ) {
            \FPForms\Core\Logger::error( 'Brevo sync failed', [
                'form_id' => $form_id,
                'email' => $email,
                'error' => $contact_result['error'],
            ] );
            return;
        }
        
        \FPForms\Core\Logger::info( 'Brevo contact synced', [
            'form_id' => $form_id,
            'email' => $email,
            'list_id' => $list_id,
        ] );
        
        // Track evento se abilitato
        if ( $this->track_events ) {
            $event_name = $form['settings']['brevo_event_name'] ?? 'form_submission';
            $event_data = [
                'form_id' => $form_id,
                'form_title' => $form['title'],
                'submission_id' => $submission_id,
                'submission_date' => current_time( 'mysql' ),
            ];
            
            $this->track_event( $email, $event_name, $event_data );
        }
    }
    
    /**
     * Crea o aggiorna contatto in Brevo
     * 
     * @param string $email Email contatto
     * @param array $attributes Attributi personalizzati
     * @param array $list_ids Liste a cui aggiungere il contatto
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function create_or_update_contact( $email, $attributes = [], $list_ids = [] ) {
        $endpoint = '/contacts';
        
        $data = [
            'email' => $email,
            'updateEnabled' => true, // Aggiorna se esiste già
        ];
        
        // Attributi
        if ( ! empty( $attributes ) ) {
            $data['attributes'] = $attributes;
        }
        
        // Liste
        if ( ! empty( $list_ids ) ) {
            $data['listIds'] = array_map( 'intval', $list_ids );
        }
        
        // Double opt-in
        if ( $this->double_optin ) {
            $data['updateEnabled'] = false; // Non aggiornare se già esiste
            $data['emailBlacklisted'] = false;
            $data['smsBlacklisted'] = false;
        }
        
        $response = $this->make_request( 'POST', $endpoint, $data );
        
        if ( ! $response['success'] ) {
            return [
                'success' => false,
                'error' => $response['error'],
            ];
        }
        
        return [
            'success' => true,
            'error' => null,
            'contact_id' => $response['body']['id'] ?? null,
        ];
    }
    
    /**
     * Traccia evento in Brevo
     * 
     * @param string $email Email contatto
     * @param string $event_name Nome evento
     * @param array $event_data Dati evento (opzionale)
     * @return array ['success' => bool, 'error' => string|null]
     */
    public function track_event( $email, $event_name, $event_data = [] ) {
        $endpoint = '/contacts/' . urlencode( $email ) . '/events';
        
        $data = [
            'event' => $event_name,
        ];
        
        // Aggiungi event data se presente
        if ( ! empty( $event_data ) ) {
            $data['properties'] = $event_data;
        }
        
        $response = $this->make_request( 'POST', $endpoint, $data );
        
        if ( ! $response['success'] ) {
            \FPForms\Core\Logger::error( 'Brevo event tracking failed', [
                'email' => $email,
                'event' => $event_name,
                'error' => $response['error'],
            ] );
            
            return [
                'success' => false,
                'error' => $response['error'],
            ];
        }
        
        \FPForms\Core\Logger::info( 'Brevo event tracked', [
            'email' => $email,
            'event' => $event_name,
        ] );
        
        return [
            'success' => true,
            'error' => null,
        ];
    }
    
    /**
     * Ottiene liste disponibili
     * 
     * @return array ['success' => bool, 'lists' => array, 'error' => string|null]
     */
    public function get_lists() {
        $endpoint = '/contacts/lists';
        
        $response = $this->make_request( 'GET', $endpoint );
        
        if ( ! $response['success'] ) {
            return [
                'success' => false,
                'lists' => [],
                'error' => $response['error'],
            ];
        }
        
        $lists = [];
        if ( isset( $response['body']['lists'] ) ) {
            foreach ( $response['body']['lists'] as $list ) {
                $lists[] = [
                    'id' => $list['id'],
                    'name' => $list['name'],
                    'total_subscribers' => $list['totalSubscribers'] ?? 0,
                ];
            }
        }
        
        return [
            'success' => true,
            'lists' => $lists,
            'error' => null,
        ];
    }
    
    /**
     * Test connessione API
     * 
     * @return array ['success' => bool, 'message' => string, 'account' => array|null]
     */
    public function test_connection() {
        $endpoint = '/account';
        
        $response = $this->make_request( 'GET', $endpoint );
        
        if ( ! $response['success'] ) {
            return [
                'success' => false,
                'message' => __( 'Connessione fallita: ', 'fp-forms' ) . $response['error'],
                'account' => null,
            ];
        }
        
        $account = $response['body'];
        
        return [
            'success' => true,
            'message' => sprintf(
                __( 'Connesso! Account: %s (%s)', 'fp-forms' ),
                $account['companyName'] ?? $account['email'] ?? 'Unknown',
                $account['plan'][0]['type'] ?? 'Free'
            ),
            'account' => [
                'email' => $account['email'] ?? '',
                'company' => $account['companyName'] ?? '',
                'plan' => $account['plan'][0]['type'] ?? '',
            ],
        ];
    }
    
    /**
     * Esegue chiamata API Brevo
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint (es: /contacts)
     * @param array $data Dati da inviare (opzionale)
     * @return array ['success' => bool, 'body' => array, 'error' => string|null]
     */
    private function make_request( $method, $endpoint, $data = [] ) {
        if ( ! $this->enabled ) {
            return [
                'success' => false,
                'body' => [],
                'error' => __( 'Brevo non configurato', 'fp-forms' ),
            ];
        }
        
        $url = self::API_BASE . $endpoint;
        
        $args = [
            'method' => $method,
            'headers' => [
                'api-key' => $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 15,
        ];
        
        if ( ! empty( $data ) && in_array( $method, [ 'POST', 'PUT', 'PATCH' ] ) ) {
            $args['body'] = wp_json_encode( $data );
        }
        
        $response = wp_remote_request( $url, $args );
        
        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'body' => [],
                'error' => $response->get_error_message(),
            ];
        }
        
        $status_code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $decoded = json_decode( $body, true );
        
        // Status codes di successo: 200-299
        if ( $status_code >= 200 && $status_code < 300 ) {
            return [
                'success' => true,
                'body' => $decoded ?? [],
                'error' => null,
            ];
        }
        
        // Errore
        $error_message = $decoded['message'] ?? $decoded['error'] ?? 'Unknown error';
        
        return [
            'success' => false,
            'body' => $decoded ?? [],
            'error' => sprintf( 'HTTP %d: %s', $status_code, $error_message ),
        ];
    }
    
    /**
     * Estrae email dai dati submission
     */
    private function extract_email( $data ) {
        // Cerca campo con "email" nel nome
        foreach ( $data as $key => $value ) {
            $clean_key = str_replace( 'fp_field_', '', $key );
            
            if ( stripos( $clean_key, 'email' ) !== false ) {
                return sanitize_email( $value );
            }
        }
        
        return null;
    }
    
    /**
     * Prepara attributi contatto da dati form
     */
    private function prepare_contact_attributes( $data, $form ) {
        $attributes = [];
        
        // Mappa campi comuni
        $field_mapping = [
            'nome' => 'FIRSTNAME',
            'name' => 'FIRSTNAME',
            'first_name' => 'FIRSTNAME',
            'cognome' => 'LASTNAME',
            'surname' => 'LASTNAME',
            'last_name' => 'LASTNAME',
            'telefono' => 'SMS',
            'phone' => 'SMS',
            'azienda' => 'COMPANY',
            'company' => 'COMPANY',
        ];
        
        foreach ( $data as $key => $value ) {
            $clean_key = strtolower( str_replace( 'fp_field_', '', $key ) );
            
            // Salta email (già usata come identifier)
            if ( stripos( $clean_key, 'email' ) !== false ) {
                continue;
            }
            
            // Converti array in stringa (checkbox multipli, etc.)
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            
            // Converti a stringa per Brevo API (accetta solo stringhe/numeri)
            $value = (string) $value;
            
            // Mappa campo se esiste mapping
            if ( isset( $field_mapping[ $clean_key ] ) ) {
                $attributes[ $field_mapping[ $clean_key ] ] = $value;
            } else {
                // Usa campo custom con prefisso
                $custom_key = 'FP_' . strtoupper( $clean_key );
                $attributes[ $custom_key ] = $value;
            }
        }
        
        // Aggiungi metadata
        $attributes['FP_FORM_ID'] = $form['id'];
        $attributes['FP_FORM_TITLE'] = $form['title'];
        $attributes['FP_SUBMISSION_DATE'] = current_time( 'Y-m-d H:i:s' );
        
        return $attributes;
    }
}
