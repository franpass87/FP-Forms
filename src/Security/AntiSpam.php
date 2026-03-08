<?php
namespace FPForms\Security;

/**
 * Anti-Spam Manager (Honeypot + Rate Limiting)
 */
class AntiSpam {
    
    /**
     * Cache in memoria della blacklist IP (evita query ripetute nella stessa request)
     */
    private $_blacklist_cache = null;
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Hook prima della validazione
        add_action( 'fp_forms_before_validate_submission', [ $this, 'check_spam' ], 5, 2 );
    }
    
    /**
     * Verifica spam
     */
    public function check_spam( $form_id, $data ) {
        // Honeypot check
        $honeypot = $this->check_honeypot( $form_id );
        if ( is_wp_error( $honeypot ) ) {
            \FPForms\Core\Logger::warning( 'Honeypot triggered', [
                'form_id' => $form_id,
                'ip' => \FPForms\Helpers\Helper::get_user_ip(),
            ] );
            
            wp_send_json_error( [
                'message' => __( 'Errore di sicurezza. Riprova.', 'fp-forms' ),
            ] );
        }
        
        // Rate limiting check
        $rate_limit = $this->check_rate_limit( $form_id );
        if ( is_wp_error( $rate_limit ) ) {
            \FPForms\Core\Logger::warning( 'Rate limit exceeded', [
                'form_id' => $form_id,
                'ip' => \FPForms\Helpers\Helper::get_user_ip(),
            ] );
            
            wp_send_json_error( [
                'message' => $rate_limit->get_error_message(),
            ] );
        }
    }
    
    /**
     * Honeypot check
     */
    private function check_honeypot( $form_id ) {
        $hp_field = 'fp_hp_' . $form_id;
        
        // Check campo honeypot
        if ( isset( $_POST[ $hp_field ] ) && $_POST[ $hp_field ] !== '' ) {
            return new \WP_Error( 'spam_honeypot', 'Spam detected via honeypot' );
        }
        
        // Check timestamp firmato (minimo 3 secondi per compilare)
        if ( isset( $_POST['fp_ts'], $_POST['fp_ts_token'] ) ) {
            $ts    = intval( $_POST['fp_ts'] );
            $token = sanitize_text_field( $_POST['fp_ts_token'] );
            $expected = hash_hmac( 'sha256', $ts, wp_salt( 'auth' ) );
            
            if ( ! hash_equals( $expected, $token ) ) {
                return new \WP_Error( 'spam_invalid_token', 'Invalid timestamp token' );
            }
            
            $elapsed = time() - $ts;
            
            if ( $elapsed < 3 ) {
                return new \WP_Error( 'spam_too_fast', 'Form submitted too fast' );
            }
            
            // Max 1 ora (probabilmente tab dimenticata)
            if ( $elapsed > 3600 ) {
                return new \WP_Error( 'spam_too_slow', 'Form session expired' );
            }
        }
        
        return true;
    }
    
    /**
     * Rate limiting check
     * FIX #4: Controlla blacklist IP prima di processare rate limit
     */
    private function check_rate_limit( $form_id ) {
        $ip = \FPForms\Helpers\Helper::get_user_ip();
        
        // FIX #4: Controlla blacklist PRIMA del rate limit (evita spreco di risorse)
        if ( $this->is_blacklisted( $ip ) ) {
            \FPForms\Core\Logger::warning( 'Blacklisted IP attempted submission', [
                'form_id' => $form_id,
                'ip' => $ip,
            ] );
            
            return new \WP_Error( 
                'ip_blacklisted',
                __( 'Il tuo indirizzo IP è stato bloccato. Contatta l\'amministratore se ritieni che si tratti di un errore.', 'fp-forms' )
            );
        }
        
        $key = 'fp_forms_rate_' . $form_id . '_' . md5( $ip );
        
        $attempts = get_transient( $key );
        
        // Max 5 submissions per ora per IP
        $max_attempts = apply_filters( 'fp_forms_rate_limit_max', 5, $form_id );
        
        if ( $attempts !== false && $attempts >= $max_attempts ) {
            return new \WP_Error( 
                'rate_limit_exceeded',
                sprintf(
                    __( 'Hai raggiunto il limite massimo di %d invii per ora. Riprova più tardi.', 'fp-forms' ),
                    $max_attempts
                )
            );
        }
        
        // Increment counter
        $new_attempts = $attempts === false ? 1 : $attempts + 1;
        set_transient( $key, $new_attempts, HOUR_IN_SECONDS );
        
        return true;
    }
    
    /**
     * Ottiene campo honeypot HTML
     */
    public function get_honeypot_field( $form_id ) {
        $hp_field = 'fp_hp_' . $form_id;
        
        $html = '<div style="position:absolute;left:-5000px;top:-5000px;" aria-hidden="true">';
        $html .= '<label for="' . $hp_field . '">Lascia vuoto questo campo</label>';
        $html .= '<input type="text" id="' . $hp_field . '" name="' . $hp_field . '" value="" tabindex="-1" autocomplete="off" />';
        $html .= '</div>';
        
        // Timestamp field firmato con HMAC per prevenire spoofing
        $ts    = time();
        $token = hash_hmac( 'sha256', $ts, wp_salt( 'auth' ) );
        $html .= '<input type="hidden" name="fp_ts" value="' . esc_attr( $ts ) . '" />';
        $html .= '<input type="hidden" name="fp_ts_token" value="' . esc_attr( $token ) . '" />';
        
        return $html;
    }
    
    /**
     * Blacklist IP
     */
    public function blacklist_ip( $ip, $reason = '' ) {
        $blacklist = $this->get_blacklist();
        
        $already_listed = array_filter( $blacklist, fn( $e ) => isset( $e['ip'] ) && $e['ip'] === $ip );
        if ( empty( $already_listed ) ) {
            $blacklist[] = [
                'ip'     => $ip,
                'reason' => $reason,
                'date'   => current_time( 'mysql' ),
            ];
            
            update_option( 'fp_forms_ip_blacklist', $blacklist );
            
            // Invalida la cache statica dopo l'aggiornamento
            $this->invalidate_blacklist_cache();
            
            \FPForms\Core\Logger::info( 'IP blacklisted', [
                'ip'     => $ip,
                'reason' => $reason,
            ] );
        }
    }
    
    /**
     * Invalida la cache della blacklist (chiamata dopo aggiornamento).
     */
    private function invalidate_blacklist_cache() {
        $this->_blacklist_cache = null;
    }
    
    /**
     * Check se IP è in blacklist.
     * La blacklist viene caricata una sola volta per request tramite cache in memoria.
     */
    public function is_blacklisted( $ip ) {
        $blacklist = $this->get_blacklist();
        
        foreach ( $blacklist as $entry ) {
            if ( isset( $entry['ip'] ) && $entry['ip'] === $ip ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Carica la blacklist IP con cache in memoria (una sola query per request).
     */
    private function get_blacklist() {
        if ( $this->_blacklist_cache === null ) {
            $this->_blacklist_cache = get_option( 'fp_forms_ip_blacklist', [] );
            if ( ! is_array( $this->_blacklist_cache ) ) {
                $this->_blacklist_cache = [];
            }
        }
        
        return $this->_blacklist_cache;
    }
}

