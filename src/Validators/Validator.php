<?php
namespace FPForms\Validators;

/**
 * Classe per validazione campi form
 */
class Validator {
    
    /**
     * Errori di validazione
     */
    private $errors = [];
    
    /**
     * Valida campo required
     */
    public function validate_required( $value, $field_name, $field_label, $custom_message = '' ) {
        if ( $this->is_empty( $value ) ) {
            // Usa messaggio custom se fornito, altrimenti default
            $error_message = ! empty( $custom_message ) 
                ? $custom_message 
                : sprintf( __( 'Il campo "%s" è obbligatorio.', 'fp-forms' ), $field_label );
            
            $this->add_error( $field_name, $error_message );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida email
     */
    public function validate_email( $value, $field_name, $field_label, $custom_message = '' ) {
        if ( ! $this->is_empty( $value ) && ! is_email( $value ) ) {
            // Usa messaggio custom se fornito, altrimenti default
            $error_message = ! empty( $custom_message ) 
                ? $custom_message 
                : sprintf( __( 'Inserisci un indirizzo email valido per "%s".', 'fp-forms' ), $field_label );
            
            $this->add_error( $field_name, $error_message );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida telefono
     */
    public function validate_phone( $value, $field_name, $field_label, $custom_message = '' ) {
        if ( ! $this->is_empty( $value ) && ! $this->is_valid_phone( $value ) ) {
            // Usa messaggio custom se fornito, altrimenti default
            $error_message = ! empty( $custom_message ) 
                ? $custom_message 
                : sprintf( __( 'Inserisci un numero di telefono valido per "%s".', 'fp-forms' ), $field_label );
            
            $this->add_error( $field_name, $error_message );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida numero
     */
    public function validate_number( $value, $field_name, $field_label, $options = [] ) {
        if ( $this->is_empty( $value ) ) {
            return true;
        }
        
        if ( ! is_numeric( $value ) ) {
            $this->add_error( $field_name, sprintf(
                __( 'Inserisci un numero valido per "%s".', 'fp-forms' ),
                $field_label
            ) );
            return false;
        }
        
        $num = (float) $value;

        if ( isset( $options['min'] ) && $num < (float) $options['min'] ) {
            $this->add_error( $field_name, sprintf(
                __( 'Il valore di "%s" deve essere almeno %s.', 'fp-forms' ),
                $field_label,
                $options['min']
            ) );
            return false;
        }
        
        if ( isset( $options['max'] ) && $num > (float) $options['max'] ) {
            $this->add_error( $field_name, sprintf(
                __( 'Il valore di "%s" non può superare %s.', 'fp-forms' ),
                $field_label,
                $options['max']
            ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida data
     */
    public function validate_date( $value, $field_name, $field_label, $options = [] ) {
        if ( $this->is_empty( $value ) ) {
            return true;
        }
        
        // Valida formato esatto Y-m-d per evitare stringhe ambigue tipo "next friday"
        $dt = \DateTime::createFromFormat( 'Y-m-d', $value );
        if ( ! $dt || $dt->format( 'Y-m-d' ) !== $value ) {
            $this->add_error( $field_name, sprintf(
                __( 'Inserisci una data valida per "%s" (formato: AAAA-MM-GG).', 'fp-forms' ),
                $field_label
            ) );
            return false;
        }
        
        // Confronta le date come stringhe Y-m-d per evitare disallineamenti di timezone
        // (le date senza orario non hanno un timezone significativo)
        $date_str = $dt->format( 'Y-m-d' );
        
        // Valida data minima
        if ( ! empty( $options['min_date'] ) ) {
            $min_dt = \DateTime::createFromFormat( 'Y-m-d', $options['min_date'] );
            if ( $min_dt && $date_str < $min_dt->format( 'Y-m-d' ) ) {
                $this->add_error( $field_name, sprintf(
                    __( 'La data di "%s" non può essere precedente al %s.', 'fp-forms' ),
                    $field_label,
                    date_i18n( get_option( 'date_format' ), $min_dt->getTimestamp() )
                ) );
                return false;
            }
        }
        
        // Valida data massima
        if ( ! empty( $options['max_date'] ) ) {
            $max_dt = \DateTime::createFromFormat( 'Y-m-d', $options['max_date'] );
            if ( $max_dt && $date_str > $max_dt->format( 'Y-m-d' ) ) {
                $this->add_error( $field_name, sprintf(
                    __( 'La data di "%s" non può essere successiva al %s.', 'fp-forms' ),
                    $field_label,
                    date_i18n( get_option( 'date_format' ), $max_dt->getTimestamp() )
                ) );
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Valida URL
     */
    public function validate_url( $value, $field_name, $field_label ) {
        if ( ! $this->is_empty( $value ) && ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
            $this->add_error( $field_name, sprintf(
                __( 'Inserisci un URL valido per "%s".', 'fp-forms' ),
                $field_label
            ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida lunghezza minima
     */
    public function validate_min_length( $value, $min_length, $field_name, $field_label ) {
        if ( ! $this->is_empty( $value ) && mb_strlen( $value ) < $min_length ) {
            $this->add_error( $field_name, sprintf(
                __( 'Il campo "%s" deve contenere almeno %d caratteri.', 'fp-forms' ),
                $field_label,
                $min_length
            ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida lunghezza massima
     */
    public function validate_max_length( $value, $max_length, $field_name, $field_label ) {
        if ( ! $this->is_empty( $value ) && mb_strlen( $value ) > $max_length ) {
            $this->add_error( $field_name, sprintf(
                __( 'Il campo "%s" non può contenere più di %d caratteri.', 'fp-forms' ),
                $field_label,
                $max_length
            ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida che il valore di un campo select/radio sia tra le opzioni consentite (whitelist).
     * Previene l'invio di valori arbitrari per campi a scelta fissa.
     */
    public function validate_choices( $value, $choices, $field_name, $field_label ) {
        if ( $this->is_empty( $value ) || ! is_array( $choices ) || empty( $choices ) ) {
            return true;
        }
        
        if ( is_array( $value ) ) {
            // Checkbox multipli: ogni valore deve essere nella whitelist
            foreach ( $value as $v ) {
                if ( ! in_array( (string) $v, array_map( 'strval', $choices ), true ) ) {
                    $this->add_error( $field_name, sprintf(
                        __( 'Il valore selezionato per "%s" non è valido.', 'fp-forms' ),
                        $field_label
                    ) );
                    return false;
                }
            }
        } else {
            if ( ! in_array( (string) $value, array_map( 'strval', $choices ), true ) ) {
                $this->add_error( $field_name, sprintf(
                    __( 'Il valore selezionato per "%s" non è valido.', 'fp-forms' ),
                    $field_label
                ) );
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Valida pattern regex
     */
    public function validate_pattern( $value, $pattern, $field_name, $field_label, $message = null ) {
        if ( ! $this->is_empty( $value ) && ! preg_match( $pattern, $value ) ) {
            $error_message = $message ? $message : sprintf(
                __( 'Il formato di "%s" non è valido.', 'fp-forms' ),
                $field_label
            );
            
            $this->add_error( $field_name, $error_message );
            return false;
        }
        
        return true;
    }
    
    /**
     * Aggiunge errore
     */
    public function add_error( $field_name, $message ) {
        $this->errors[ $field_name ] = $message;
    }
    
    /**
     * Ottiene errori
     */
    public function get_errors() {
        return $this->errors;
    }
    
    /**
     * Verifica se ci sono errori
     */
    public function has_errors() {
        return ! empty( $this->errors );
    }
    
    /**
     * Reset errori
     */
    public function reset_errors() {
        $this->errors = [];
    }
    
    /**
     * Verifica se valore è vuoto
     */
    private function is_empty( $value ) {
        if ( is_array( $value ) ) {
            return empty( $value );
        }
        
        return $value === '' || $value === null;
    }
    
    /**
     * Valida numero di telefono
     */
    private function is_valid_phone( $phone ) {
        // Pattern flessibile per telefoni internazionali
        $pattern = '/^[+]?[\d\s\-\(\)\.]+$/';
        return preg_match( $pattern, $phone ) && strlen( preg_replace( '/[^\d]/', '', $phone ) ) >= 6;
    }
}
