<?php
namespace FPForms\Database;

/**
 * Gestisce le operazioni sul database
 */
class Manager {
    
    /**
     * Nome tabella submissions
     */
    private $table_submissions;
    
    /**
     * Nome tabella fields
     */
    private $table_fields;
    
    /**
     * Costruttore
     */
    public function __construct() {
        global $wpdb;
        $this->table_submissions = $wpdb->prefix . 'fp_forms_submissions';
        $this->table_fields = $wpdb->prefix . 'fp_forms_fields';
    }
    
    /**
     * Ottiene nome tabella submissions
     */
    public function get_submissions_table() {
        return $this->table_submissions;
    }
    
    /**
     * Ottiene nome tabella fields
     */
    public function get_fields_table() {
        return $this->table_fields;
    }
    
    /**
     * Salva una submission
     */
    public function save_submission( $form_id, $data, $args = [] ) {
        global $wpdb;
        
        $defaults = [
            'user_id' => get_current_user_id(),
            'user_ip' => \FPForms\Helpers\Helper::get_user_ip(),
            'user_agent' => \FPForms\Helpers\Helper::get_user_agent(),
            'status' => 'unread',
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        $result = $wpdb->insert(
            $this->table_submissions,
            [
                'form_id' => $form_id,
                'data' => \FPForms\Helpers\Helper::safe_json_encode( $data ),
                'user_id' => $args['user_id'],
                'user_ip' => $args['user_ip'],
                'user_agent' => $args['user_agent'],
                'status' => $args['status'],
            ],
            [ '%d', '%s', '%d', '%s', '%s', '%s' ]
        );
        
        if ( $result ) {
            $submission_id = $wpdb->insert_id;
            
            // Invalida cache submissions
            \FPForms\Core\Cache::invalidate_submissions( $form_id );
            
            // Log submission
            \FPForms\Core\Logger::log_submission( $form_id, $submission_id, true );
            
            return $submission_id;
        }
        
        \FPForms\Core\Logger::log_submission( $form_id, 0, false );
        
        return false;
    }
    
    /**
     * Ottiene le submissions di un form
     */
    public function get_submissions( $form_id, $args = [] ) {
        global $wpdb;
        
        $defaults = [
            'status' => '',
            'limit' => 50,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        // Sanitizza orderby e order per prevenire SQL injection
        $allowed_orderby = [ 'id', 'created_at', 'status', 'form_id' ];
        $orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
        
        $order = strtoupper( $args['order'] );
        $order = in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'DESC';
        
        $where = $wpdb->prepare( 'WHERE form_id = %d', $form_id );
        
        if ( ! empty( $args['status'] ) ) {
            $where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
        }
        
        // Aggiungi search se presente (colonna si chiama 'data' nel database)
        if ( ! empty( $args['search'] ) ) {
            $where .= $wpdb->prepare( ' AND data LIKE %s', '%' . $wpdb->esc_like( $args['search'] ) . '%' );
        }
        
        $query = "SELECT * FROM {$this->table_submissions} {$where} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        
        return $wpdb->get_results( $wpdb->prepare( $query, $args['limit'], $args['offset'] ) );
    }
    
    /**
     * Conta le submissions (con cache e search)
     */
    public function count_submissions( $form_id, $status = '', $search = '' ) {
        // Non usare cache se c'è search (risultati dinamici)
        if ( empty( $search ) ) {
            $cached = \FPForms\Core\Cache::get_submissions_count( $form_id, $status );
            if ( $cached !== null ) {
                return $cached;
            }
        }
        
        global $wpdb;
        
        $where = $wpdb->prepare( 'WHERE form_id = %d', $form_id );
        
        if ( ! empty( $status ) ) {
            $where .= $wpdb->prepare( ' AND status = %s', $status );
        }
        
        // Aggiungi search se presente
        if ( ! empty( $search ) ) {
            $where .= $wpdb->prepare( ' AND data LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' );
        }
        
        $query = "SELECT COUNT(*) FROM {$this->table_submissions} {$where}";
        
        $count = (int) $wpdb->get_var( $query );
        
        // Salva in cache solo se non c'è search
        if ( empty( $search ) ) {
            \FPForms\Core\Cache::set_submissions_count( $form_id, $count, $status );
        }
        
        return $count;
    }
    
    /**
     * Ottiene una submission per ID
     */
    public function get_submission( $id ) {
        global $wpdb;
        
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_submissions} WHERE id = %d",
            $id
        ) );
    }
    
    /**
     * Aggiorna status di una submission
     */
    public function update_submission_status( $id, $status ) {
        global $wpdb;
        
        return $wpdb->update(
            $this->table_submissions,
            [ 'status' => $status ],
            [ 'id' => $id ],
            [ '%s' ],
            [ '%d' ]
        );
    }
    
    /**
     * Elimina una submission (e i file associati)
     */
    public function delete_submission( $id ) {
        global $wpdb;
        
        // Ottieni form_id prima di eliminare
        $submission = $this->get_submission( $id );
        
        if ( ! $submission ) {
            return false;
        }
        
        // Elimina file associati (BUGFIX: prevenzione memory leak)
        $this->delete_submission_files( $id );
        
        // Elimina submission
        $result = $wpdb->delete(
            $this->table_submissions,
            [ 'id' => $id ],
            [ '%d' ]
        );
        
        if ( $result ) {
            // Invalida cache
            \FPForms\Core\Cache::invalidate_submissions( $submission->form_id );
        }
        
        return $result;
    }
    
    /**
     * Elimina file associati a una submission
     */
    private function delete_submission_files( $submission_id ) {
        global $wpdb;
        $table_files = $wpdb->prefix . 'fp_forms_files';
        
        // Ottieni file da eliminare
        $files = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_files} WHERE submission_id = %d",
            $submission_id
        ) );
        
        // Elimina file fisici
        foreach ( $files as $file ) {
            if ( file_exists( $file->file_path ) ) {
                @unlink( $file->file_path );
            }
        }
        
        // Elimina record database
        $wpdb->delete(
            $table_files,
            [ 'submission_id' => $submission_id ],
            [ '%d' ]
        );
        
        return true;
    }
    
    /**
     * Salva i campi di un form
     */
    public function save_form_fields( $form_id, $fields ) {
        global $wpdb;
        
        // Elimina i campi esistenti
        $wpdb->delete( $this->table_fields, [ 'form_id' => $form_id ], [ '%d' ] );
        
        // Salva i nuovi campi
        foreach ( $fields as $order => $field ) {
            $wpdb->insert(
                $this->table_fields,
                [
                    'form_id' => $form_id,
                    'field_type' => $field['type'],
                    'field_label' => $field['label'],
                    'field_name' => $field['name'],
                    'field_options' => isset( $field['options'] ) ? wp_json_encode( $field['options'] ) : null,
                    'field_order' => $order,
                    'is_required' => isset( $field['required'] ) ? (int) $field['required'] : 0,
                ],
                [ '%d', '%s', '%s', '%s', '%s', '%d', '%d' ]
            );
        }
        
        // Invalida cache
        \FPForms\Core\Cache::invalidate_form( $form_id );
        
        return true;
    }
    
    /**
     * Ottiene i campi di un form (con cache)
     */
    public function get_form_fields( $form_id ) {
        // Prova a ottenere dalla cache
        $cached = \FPForms\Core\Cache::get_form_fields( $form_id );
        if ( $cached !== null ) {
            return $cached;
        }
        
        global $wpdb;
        
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_fields} WHERE form_id = %d ORDER BY field_order ASC",
            $form_id
        ) );
        
        // Salva in cache
        \FPForms\Core\Cache::set_form_fields( $form_id, $results );
        
        return $results;
    }
    
    /**
     * Ottiene IP dell'utente
     */
    private function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field( $ip );
    }
}

