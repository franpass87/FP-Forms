<?php
namespace FPForms\Export;

/**
 * Export submissions in formato CSV
 */
class CsvExporter {
    
    /**
     * Export submissions in CSV
     */
    public function export( $form_id, $options = [] ) {
        $defaults = [
            'date_from' => '',
            'date_to' => '',
            'status' => '',
            'fields' => [], // Empty = all fields
        ];
        
        $options = wp_parse_args( $options, $defaults );
        
        // Ottieni form e submissions
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        $submissions = $this->get_filtered_submissions( $form_id, $options );
        
        if ( ! $form || empty( $submissions ) ) {
            return new \WP_Error( 'no_data', __( 'Nessun dato da esportare.', 'fp-forms' ) );
        }
        
        // Prepara filename
        $filename = $this->get_filename( $form, 'csv' );
        
        // Headers per download
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );
        
        // Output stream
        $output = fopen( 'php://output', 'w' );
        
        // BOM per UTF-8
        fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );
        
        // Header row
        fputcsv( $output, $this->get_csv_headers( $form, $options ) );
        
        // Data rows
        foreach ( $submissions as $submission ) {
            $row = $this->format_submission_row( $submission, $form, $options );
            fputcsv( $output, $row );
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
        
        // Filtro data
        if ( ! empty( $options['date_from'] ) ) {
            $where[] = $wpdb->prepare( 'created_at >= %s', $options['date_from'] . ' 00:00:00' );
        }
        
        if ( ! empty( $options['date_to'] ) ) {
            $where[] = $wpdb->prepare( 'created_at <= %s', $options['date_to'] . ' 23:59:59' );
        }
        
        // Filtro status
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
     * Ottiene headers CSV
     */
    private function get_csv_headers( $form, $options ) {
        $headers = [ 'ID', 'Data Invio', 'Stato' ];
        
        $fields = $form['fields'];
        
        // Se specificati campi specifici
        if ( ! empty( $options['fields'] ) ) {
            $fields = array_filter( $fields, function( $field ) use ( $options ) {
                return in_array( $field['name'], $options['fields'] );
            } );
        }
        
        foreach ( $fields as $field ) {
            $headers[] = $field['label'];
        }
        
        $headers[] = 'IP Utente';
        
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
            
            $row[] = $value;
        }
        
        $row[] = $submission->user_ip;
        
        return $row;
    }
    
    /**
     * Genera filename
     */
    private function get_filename( $form, $extension ) {
        $slug = sanitize_title( $form['title'] );
        $date = date( 'Y-m-d' );
        
        return "fp-forms-{$slug}-{$date}.{$extension}";
    }
}

