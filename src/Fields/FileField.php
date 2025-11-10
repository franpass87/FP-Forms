<?php
namespace FPForms\Fields;

/**
 * Gestisce il campo File Upload
 */
class FileField {
    
    /**
     * Upload directory
     */
    private $upload_dir = 'fp-forms';
    
    /**
     * Max file size di default (5MB)
     */
    private $default_max_size = 5242880;
    
    /**
     * Tipi file permessi di default
     */
    private $default_allowed_types = [ 'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif' ];
    
    /**
     * Renderizza campo file
     */
    public function render( $field, $form_id ) {
        $field_name = 'fp_field_' . $field['name'];
        $field_id = 'fp_field_' . $form_id . '_' . $field['name'];
        $required = $field['required'] ? 'required' : '';
        $required_mark = $field['required'] ? ' <span class="fp-forms-required">*</span>' : '';
        
        $max_size = isset( $field['options']['max_size'] ) ? $field['options']['max_size'] : 5;
        $allowed_types = isset( $field['options']['allowed_types'] ) ? $field['options']['allowed_types'] : $this->default_allowed_types;
        $multiple = isset( $field['options']['multiple'] ) && $field['options']['multiple'];
        
        $accept = '.' . implode( ',.', $allowed_types );
        
        $html = '<div class="fp-forms-field fp-forms-field-file">';
        $html .= '<label for="' . esc_attr( $field_id ) . '" class="fp-forms-label">' . esc_html( $field['label'] ) . $required_mark . '</label>';
        
        $html .= '<div class="fp-forms-file-wrapper">';
        $html .= '<input type="file" ';
        $html .= 'id="' . esc_attr( $field_id ) . '" ';
        $html .= 'name="' . esc_attr( $field_name ) . ( $multiple ? '[]' : '' ) . '" ';
        $html .= 'class="fp-forms-file-input" ';
        $html .= 'accept="' . esc_attr( $accept ) . '" ';
        $html .= 'data-max-size="' . esc_attr( $max_size * 1024 * 1024 ) . '" ';
        $html .= $required . ' ';
        
        if ( $multiple ) {
            $html .= 'multiple ';
        }
        
        $html .= '/>';
        
        $html .= '<div class="fp-forms-file-info">';
        $html .= '<span class="fp-forms-file-limit">';
        $html .= sprintf( 
            __( 'Dimensione max: %dMB | Formati: %s', 'fp-forms' ),
            $max_size,
            strtoupper( implode( ', ', $allowed_types ) )
        );
        $html .= '</span>';
        $html .= '</div>';
        
        $html .= '<div class="fp-forms-file-preview" style="display:none;"></div>';
        $html .= '</div>';
        
        if ( isset( $field['options']['description'] ) && ! empty( $field['options']['description'] ) ) {
            $html .= '<p class="fp-forms-description">' . esc_html( $field['options']['description'] ) . '</p>';
        }
        
        $html .= '<span class="fp-forms-error" style="display:none;"></span>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Gestisce upload file
     */
    public function handle_upload( $files, $field_name, $field_options = [] ) {
        if ( empty( $files ) ) {
            return [];
        }
        
        $max_size = isset( $field_options['max_size'] ) ? $field_options['max_size'] * 1024 * 1024 : $this->default_max_size;
        $allowed_types = isset( $field_options['allowed_types'] ) ? $field_options['allowed_types'] : $this->default_allowed_types;
        
        // Se non è array (singolo file), converti in array
        if ( ! isset( $files['name'][0] ) ) {
            $files = [ $files ];
        } else {
            // Riorganizza array per file multipli
            $files = $this->reorganize_files_array( $files );
        }
        
        $uploaded_files = [];
        
        foreach ( $files as $file ) {
            // Validazione
            $validation = $this->validate_file( $file, $max_size, $allowed_types );
            
            if ( is_wp_error( $validation ) ) {
                \FPForms\Core\Logger::error( 'File upload validation failed', [
                    'field' => $field_name,
                    'error' => $validation->get_error_message(),
                ] );
                continue;
            }
            
            // Upload
            $upload = $this->upload_file( $file );
            
            if ( ! is_wp_error( $upload ) ) {
                $uploaded_files[] = [
                    'name' => $file['name'],
                    'url' => $upload['url'],
                    'path' => $upload['file'],
                    'type' => $file['type'],
                    'size' => $file['size'],
                ];
            }
        }
        
        return $uploaded_files;
    }
    
    /**
     * Valida file
     */
    private function validate_file( $file, $max_size, $allowed_types ) {
        // Check errori upload
        if ( $file['error'] !== UPLOAD_ERR_OK ) {
            return new \WP_Error( 'upload_error', __( 'Errore durante l\'upload del file.', 'fp-forms' ) );
        }
        
        // Check dimensione
        if ( $file['size'] > $max_size ) {
            return new \WP_Error( 
                'file_too_large', 
                sprintf( __( 'Il file è troppo grande. Dimensione massima: %sMB', 'fp-forms' ), $max_size / 1024 / 1024 )
            );
        }
        
        // Check tipo file
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        
        if ( ! in_array( $file_ext, $allowed_types ) ) {
            return new \WP_Error(
                'invalid_file_type',
                sprintf( __( 'Tipo file non permesso. Formati accettati: %s', 'fp-forms' ), implode( ', ', $allowed_types ) )
            );
        }
        
        // Verifica MIME type (con error handling per server senza finfo)
        if ( function_exists( 'finfo_open' ) ) {
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            if ( $finfo ) {
                $mime_type = finfo_file( $finfo, $file['tmp_name'] );
                finfo_close( $finfo );
            } else {
                // Fallback a mime_content_type
                $mime_type = mime_content_type( $file['tmp_name'] );
            }
        } else {
            // Fallback per server senza finfo
            $mime_type = isset( $file['type'] ) ? $file['type'] : 'application/octet-stream';
        }
        
        $allowed_mimes = $this->get_allowed_mime_types( $allowed_types );
        
        if ( ! in_array( $mime_type, $allowed_mimes ) ) {
            return new \WP_Error( 'invalid_mime', __( 'Tipo MIME non valido.', 'fp-forms' ) );
        }
        
        return true;
    }
    
    /**
     * Upload file
     */
    private function upload_file( $file ) {
        // Setup upload directory
        $upload_dir = wp_upload_dir();
        $fp_forms_dir = $upload_dir['basedir'] . '/' . $this->upload_dir;
        
        if ( ! file_exists( $fp_forms_dir ) ) {
            wp_mkdir_p( $fp_forms_dir );
            
            // Proteggi directory
            file_put_contents( $fp_forms_dir . '/.htaccess', 'deny from all' );
            file_put_contents( $fp_forms_dir . '/index.php', '<?php // Silence is golden' );
        }
        
        // Sanitize filename
        $filename = sanitize_file_name( $file['name'] );
        $filename = wp_unique_filename( $fp_forms_dir, $filename );
        
        // Move uploaded file
        $destination = $fp_forms_dir . '/' . $filename;
        
        if ( move_uploaded_file( $file['tmp_name'], $destination ) ) {
            return [
                'file' => $destination,
                'url' => $upload_dir['baseurl'] . '/' . $this->upload_dir . '/' . $filename,
                'type' => $file['type'],
            ];
        }
        
        return new \WP_Error( 'upload_failed', __( 'Impossibile caricare il file.', 'fp-forms' ) );
    }
    
    /**
     * Ottiene MIME types permessi
     */
    private function get_allowed_mime_types( $extensions ) {
        $mime_types = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'txt' => 'text/plain',
        ];
        
        $allowed = [];
        
        foreach ( $extensions as $ext ) {
            if ( isset( $mime_types[ $ext ] ) ) {
                $allowed[] = $mime_types[ $ext ];
            }
        }
        
        return $allowed;
    }
    
    /**
     * Riorganizza array file multipli
     */
    private function reorganize_files_array( $files ) {
        $result = [];
        
        foreach ( $files['name'] as $key => $value ) {
            $result[] = [
                'name' => $files['name'][ $key ],
                'type' => $files['type'][ $key ],
                'tmp_name' => $files['tmp_name'][ $key ],
                'error' => $files['error'][ $key ],
                'size' => $files['size'][ $key ],
            ];
        }
        
        return $result;
    }
}

