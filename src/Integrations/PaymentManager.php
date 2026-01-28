<?php
namespace FPForms\Integrations;

/**
 * Payment Manager Base
 * Gestisce integrazioni pagamenti (base per future implementazioni)
 */
class PaymentManager {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Hook per processare pagamenti dopo submission
        add_action( 'fp_forms_after_save_submission', [ $this, 'maybe_process_payment' ], 20, 3 );
    }
    
    /**
     * Verifica se form richiede pagamento
     */
    public function form_requires_payment( $form_id ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form || ! isset( $form['settings']['payment_enabled'] ) ) {
            return false;
        }
        
        return (bool) $form['settings']['payment_enabled'];
    }
    
    /**
     * Processa pagamento se necessario
     */
    public function maybe_process_payment( $submission_id, $form_id, $data ) {
        if ( ! $this->form_requires_payment( $form_id ) ) {
            return;
        }
        
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        $payment_provider = $form['settings']['payment_provider'] ?? '';
        $amount = $this->calculate_amount( $form_id, $data );
        
        if ( empty( $payment_provider ) || $amount <= 0 ) {
            return;
        }
        
        // Log payment attempt
        \FPForms\Core\Logger::info( 'Payment processing initiated', [
            'form_id' => $form_id,
            'submission_id' => $submission_id,
            'provider' => $payment_provider,
            'amount' => $amount,
        ] );
        
        // Trigger hook per provider specifico
        do_action( 'fp_forms_process_payment_' . $payment_provider, $submission_id, $form_id, $data, $amount );
    }
    
    /**
     * Calcola importo da pagare
     */
    private function calculate_amount( $form_id, $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        // Cerca campo calcolato o campo con importo
        $amount_field = $form['settings']['payment_amount_field'] ?? '';
        
        if ( empty( $amount_field ) ) {
            // Usa importo fisso se configurato
            return floatval( $form['settings']['payment_amount'] ?? 0 );
        }
        
        // Estrai valore da campo
        $field_name = 'fp_field_' . $amount_field;
        $amount = isset( $data[ $field_name ] ) ? floatval( $data[ $field_name ] ) : 0;
        
        return $amount;
    }
    
    /**
     * Registra transazione
     */
    public function log_transaction( $submission_id, $form_id, $provider, $amount, $status, $transaction_id = '', $metadata = [] ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'fp_forms_transactions';
        
        // Crea tabella se non esiste
        $this->maybe_create_transactions_table();
        
        $wpdb->insert(
            $table,
            [
                'submission_id' => $submission_id,
                'form_id' => $form_id,
                'provider' => $provider,
                'amount' => $amount,
                'status' => $status,
                'transaction_id' => $transaction_id,
                'metadata' => wp_json_encode( $metadata ),
                'created_at' => current_time( 'mysql' ),
            ],
            [ '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s' ]
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Crea tabella transazioni se non esiste
     */
    private function maybe_create_transactions_table() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'fp_forms_transactions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            submission_id bigint(20) UNSIGNED NOT NULL,
            form_id bigint(20) UNSIGNED NOT NULL,
            provider varchar(50) NOT NULL,
            amount decimal(10,2) NOT NULL,
            status varchar(20) NOT NULL,
            transaction_id varchar(255) DEFAULT '',
            metadata longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY submission_id (submission_id),
            KEY form_id (form_id),
            KEY status (status)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * Ottiene transazioni per form
     */
    public function get_transactions( $form_id, $args = [] ) {
        global $wpdb;
        
        $this->maybe_create_transactions_table();
        
        $table = $wpdb->prefix . 'fp_forms_transactions';
        
        $defaults = [
            'limit' => 50,
            'offset' => 0,
            'status' => '',
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        // Validazione e sanitizzazione limit/offset per sicurezza
        $args['limit'] = min( absint( $args['limit'] ), 1000 ); // Max 1000 record
        $args['offset'] = absint( $args['offset'] );
        
        $where = $wpdb->prepare( 'WHERE form_id = %d', $form_id );
        
        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
        }
        
        $query = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
        
        return $wpdb->get_results( $wpdb->prepare( $query, $args['limit'], $args['offset'] ) );
    }
}

