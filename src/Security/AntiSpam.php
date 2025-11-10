<?php
namespace FPForms\Security;

/**
 * Anti-Spam Manager (Honeypot + Rate Limiting)
 */
class AntiSpam {
    
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
        
        // Check timestamp (minimo 3 secondi per compilare)
        if ( isset( $_POST['fp_ts'] ) ) {
            $elapsed = time() - intval( $_POST['fp_ts'] );
            
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
     */
    private function check_rate_limit( $form_id ) {
        $ip = \FPForms\Helpers\Helper::get_user_ip();
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
        
        // Timestamp field
        $html .= '<input type="hidden" name="fp_ts" value="' . time() . '" />';
        
        return $html;
    }
    
    /**
     * Blacklist IP
     */
    public function blacklist_ip( $ip, $reason = '' ) {
        $blacklist = get_option( 'fp_forms_ip_blacklist', [] );
        
        if ( ! in_array( $ip, $blacklist ) ) {
            $blacklist[] = [
                'ip' => $ip,
                'reason' => $reason,
                'date' => current_time( 'mysql' ),
            ];
            
            update_option( 'fp_forms_ip_blacklist', $blacklist );
            
            \FPForms\Core\Logger::info( 'IP blacklisted', [
                'ip' => $ip,
                'reason' => $reason,
            ] );
        }
    }
    
    /**
     * Check se IP è in blacklist
     */
    public function is_blacklisted( $ip ) {
        $blacklist = get_option( 'fp_forms_ip_blacklist', [] );
        
        foreach ( $blacklist as $entry ) {
            if ( isset( $entry['ip'] ) && $entry['ip'] === $ip ) {
                return true;
            }
        }
        
        return false;
    }
}

