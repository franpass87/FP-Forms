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
     * FIX #1: Usa wp_check_filetype() e validazione immagini WordPress standard
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
        
        // FIX #1: Usa wp_check_filetype() invece di validazione manuale
        $wp_filetype = wp_check_filetype( $file['name'], $this->get_allowed_mime_types_for_wp( $allowed_types ) );
        
        if ( ! $wp_filetype['ext'] || ! $wp_filetype['type'] ) {
            return new \WP_Error(
                'invalid_file_type',
                sprintf( __( 'Tipo file non permesso. Formati accettati: %s', 'fp-forms' ), implode( ', ', $allowed_types ) )
            );
        }
        
        // Verifica che l'estensione sia nella lista permessa
        $file_ext = strtolower( $wp_filetype['ext'] );
        if ( ! in_array( $file_ext, $allowed_types, true ) ) {
            return new \WP_Error(
                'invalid_file_type',
                sprintf( __( 'Tipo file non permesso. Formati accettati: %s', 'fp-forms' ), implode( ', ', $allowed_types ) )
            );
        }
        
        // FIX #1: Per immagini, verifica anche il contenuto reale del file
        $image_extensions = [ 'jpg', 'jpeg', 'png', 'gif', 'webp' ];
        if ( in_array( $file_ext, $image_extensions, true ) ) {
            $image_info = @getimagesize( $file['tmp_name'] );
            if ( $image_info === false ) {
                return new \WP_Error( 
                    'invalid_image', 
                    __( 'Il file non è un\'immagine valida. Il contenuto del file non corrisponde all\'estensione.', 'fp-forms' ) 
                );
            }
            
            // Verifica che il MIME type dell'immagine corrisponda
            $detected_mime = $image_info['mime'];
            if ( $detected_mime !== $wp_filetype['type'] ) {
                return new \WP_Error( 
                    'invalid_mime', 
                    __( 'Il tipo MIME del file non corrisponde all\'estensione.', 'fp-forms' ) 
                );
            }
        }
        
        // Verifica MIME type con finfo (doppia verifica)
        $detected_mime = false;
        if ( function_exists( 'finfo_open' ) ) {
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            if ( $finfo ) {
                $detected_mime = finfo_file( $finfo, $file['tmp_name'] );
                finfo_close( $finfo );
            }
        }
        
        // Se finfo è disponibile, verifica che il MIME type corrisponda
        if ( $detected_mime && $detected_mime !== $wp_filetype['type'] ) {
            return new \WP_Error( 
                'invalid_mime', 
                __( 'Il tipo MIME rilevato non corrisponde al tipo di file dichiarato.', 'fp-forms' ) 
            );
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
            
            // FIX #3: Protezione directory universale (Apache + Nginx + IIS)
            // Protezione Apache (.htaccess)
            file_put_contents( $fp_forms_dir . '/.htaccess', 'deny from all' );
            
            // Protezione universale (index.php)
            file_put_contents( $fp_forms_dir . '/index.php', '<?php // Silence is golden' );
            
            // Imposta permessi sicuri (se possibile)
            if ( function_exists( 'chmod' ) ) {
                @chmod( $fp_forms_dir, 0750 );
            }
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
     * Ottiene MIME types permessi (compatibilità legacy)
     * @deprecated Usa get_allowed_mime_types_for_wp() per wp_check_filetype()
     */
    private function get_allowed_mime_types( $extensions ) {
        $wp_mime_types = get_allowed_mime_types();
        $allowed = [];
        
        foreach ( $extensions as $ext ) {
            // Cerca nell'array WordPress MIME types
            foreach ( $wp_mime_types as $mime_ext => $mime_type ) {
                // Supporta formati come "jpg|jpeg|jpe"
                $mime_ext_array = explode( '|', $mime_ext );
                if ( in_array( $ext, $mime_ext_array, true ) ) {
                    $allowed[] = $mime_type;
                    break;
                }
            }
        }
        
        // Se non trovato in WordPress, usa fallback
        if ( empty( $allowed ) ) {
            $fallback_mimes = [
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
            
            foreach ( $extensions as $ext ) {
                if ( isset( $fallback_mimes[ $ext ] ) ) {
                    $allowed[] = $fallback_mimes[ $ext ];
                }
            }
        }
        
        return array_unique( $allowed );
    }
    
    /**
     * Ottiene MIME types per wp_check_filetype()
     * FIX #1: Usa get_allowed_mime_types() di WordPress
     */
    private function get_allowed_mime_types_for_wp( $extensions ) {
        $wp_mime_types = get_allowed_mime_types();
        $allowed = [];
        
        foreach ( $extensions as $ext ) {
            // Cerca nell'array WordPress MIME types
            foreach ( $wp_mime_types as $mime_ext => $mime_type ) {
                // Supporta formati come "jpg|jpeg|jpe"
                $mime_ext_array = explode( '|', $mime_ext );
                if ( in_array( $ext, $mime_ext_array, true ) ) {
                    // wp_check_filetype() si aspetta un array con estensioni come chiavi
                    $allowed[ $ext ] = $mime_type;
                    break;
                }
            }
        }
        
        // Se non trovato in WordPress, usa fallback
        if ( empty( $allowed ) ) {
            $fallback_mimes = [
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
            
            foreach ( $extensions as $ext ) {
                if ( isset( $fallback_mimes[ $ext ] ) ) {
                    $allowed[ $ext ] = $fallback_mimes[ $ext ];
                }
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

