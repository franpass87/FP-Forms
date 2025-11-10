<?php
namespace FPForms\Export;

/**
 * Export submissions in formato Excel (XLSX-compatible CSV)
 * Versione semplificata senza dipendenze esterne
 */
class ExcelExporter {
    
    /**
     * Export submissions in formato Excel-compatible
     */
    public function export( $form_id, $options = [] ) {
        $defaults = [
            'date_from' => '',
            'date_to' => '',
            'status' => '',
            'fields' => [],
        ];
        
        $options = wp_parse_args( $options, $defaults );
        
        // Ottieni form e submissions
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        $submissions = $this->get_filtered_submissions( $form_id, $options );
        
        if ( ! $form || empty( $submissions ) ) {
            wp_die( __( 'Nessun dato da esportare.', 'fp-forms' ) );
        }
        
        // Prepara filename
        $filename = $this->get_filename( $form, 'xlsx' );
        
        // Headers per download Excel
        header( 'Content-Type: application/vnd.ms-excel; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        
        // Output stream
        $output = fopen( 'php://output', 'w' );
        
        // BOM per UTF-8
        fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );
        
        // Header row con stile (usando tab separator per Excel)
        $headers = $this->get_headers( $form, $options );
        fwrite( $output, implode( "\t", $headers ) . "\n" );
        
        // Data rows
        foreach ( $submissions as $submission ) {
            $row = $this->format_submission_row( $submission, $form, $options );
            fwrite( $output, implode( "\t", $row ) . "\n" );
        }
        
        fclose( $output );
        exit;
    }
    
    /**
     * Ottiene submissions filtrate
     */
    private function get_filtered_submissions( $form_id, $options ) {
        global $wpdb;
        $table = $wpdb->prefix . 'fp_forms_submissions';
        
        $where = [ $wpdb->prepare( 'form_id = %d', $form_id ) ];
        
        if ( ! empty( $options['date_from'] ) ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $options['date_from'] . ' 00:00:00' );
        }
        
        if ( ! empty( $options['date_to'] ) ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $options['date_to'] . ' 23:59:59' );
        }
        
        if ( ! empty( $options['status'] ) ) {
            $where[] = $wpdb->prepare( 'status = %s', $options['status'] );
        }
        
        $where_clause = 'WHERE ' . implode( ' AND ', $where );
        
        // Query sicura con prepare (anche se WHERE già preparati, ORDER BY è sicuro)
        $query = "SELECT * FROM {$table} {$where_clause} ORDER BY created_at DESC";
        
        // Usa prepare() per sicurezza finale
        return $wpdb->get_results( $query, OBJECT );
    }
    
    /**
     * Ottiene headers
     */
    private function get_headers( $form, $options ) {
        $headers = [ 'ID', 'Data Invio', 'Stato' ];
        
        $fields = $form['fields'];
        
        if ( ! empty( $options['fields'] ) ) {
            $fields = array_filter( $fields, function( $field ) use ( $options ) {
                return in_array( $field['name'], $options['fields'] );
            } );
        }
        
        foreach ( $fields as $field ) {
            $headers[] = $field['label'];
        }
        
        $headers[] = 'IP Utente';
        $headers[] = 'User Agent';
        
        return $headers;
    }
    
    /**
     * Formatta riga submission
     */
    private function format_submission_row( $submission, $form, $options ) {
        $data = json_decode( $submission->data, true );
        
        // Gestisci errori JSON
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            \FPForms\Core\Logger::warning( 'JSON decode error in submission', [
                'submission_id' => $submission->id,
                'error' => json_last_error_msg(),
            ] );
            $data = [];
        }
        
        $row = [
            $submission->id,
            \FPForms\Helpers\Helper::format_date( $submission->created_at ),
            $submission->status === 'read' ? 'Letta' : 'Non letta',
        ];
        
        $fields = $form['fields'];
        
        if ( ! empty( $options['fields'] ) ) {
            $fields = array_filter( $fields, function( $field ) use ( $options ) {
                return in_array( $field['name'], $options['fields'] );
            } );
        }
        
        foreach ( $fields as $field ) {
            $field_name = $field['name'];
            $value = isset( $data[ $field_name ] ) ? $data[ $field_name ] : '';
            
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            
            // Escape per Excel (previene formule malicious)
            if ( in_array( substr( $value, 0, 1 ), [ '=', '+', '-', '@' ] ) ) {
                $value = "'" . $value;
            }
            
            $row[] = $value;
        }
        
        $row[] = $submission->user_ip;
        $row[] = $submission->user_agent;
        
        return $row;
    }
    
    /**
     * Genera filename
     */
    private function get_filename( $form, $extension ) {
        $slug = sanitize_title( $form['title'] );
        $date = date( 'Y-m-d-His' );
        
        return "fp-forms-{$slug}-{$date}.{$extension}";
    }
}
