<?php
namespace FPForms\Logic;

/**
 * Gestisce la logica condizionale dei form
 */
class ConditionalLogic {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // output_logic_script è uno stub: lo script è già incluso in frontend.js
    }
    
    /**
     * Valida regole conditional logic
     */
    public function validate_rules( $rules ) {
        if ( ! is_array( $rules ) ) {
            return [];
        }
        
        $validated = [];
        
        foreach ( $rules as $rule ) {
            if ( $this->is_valid_rule( $rule ) ) {
                $validated[] = $rule;
            }
        }
        
        return $validated;
    }
    
    /**
     * Verifica se regola è valida
     */
    private function is_valid_rule( $rule ) {
        $required_keys = [ 'trigger_field', 'condition', 'action', 'target_fields' ];
        
        foreach ( $required_keys as $key ) {
            if ( ! isset( $rule[ $key ] ) ) {
                return false;
            }
        }
        
        // Valida condition
        $valid_conditions = [ 'equals', 'not_equals', 'contains', 'not_contains', 'greater_than', 'less_than', 'is_empty', 'is_not_empty' ];
        if ( ! in_array( $rule['condition'], $valid_conditions ) ) {
            return false;
        }
        
        // Valida action
        $valid_actions = [ 'show', 'hide', 'require', 'unrequire' ];
        if ( ! in_array( $rule['action'], $valid_actions ) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Output script conditional logic
     */
    public function output_logic_script() {
        global $post;
        
        if ( ! $post || ! has_shortcode( $post->post_content, 'fp_form' ) ) {
            return;
        }
        
        // Script già incluso in frontend.js (versione futura)
        // Per ora output inline se ci sono regole
    }
    
    /**
     * Ottiene regole per form
     */
    public function get_form_rules( $form_id ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form || ! isset( $form['settings']['conditional_rules'] ) ) {
            return [];
        }
        
        return $this->validate_rules( $form['settings']['conditional_rules'] );
    }
    
    /**
     * Aggiunge regola
     */
    public function add_rule( $form_id, $rule ) {
        if ( ! $this->is_valid_rule( $rule ) ) {
            return false;
        }
        
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        
        if ( ! $form ) {
            return false;
        }
        
        $rules = isset( $form['settings']['conditional_rules'] ) ? $form['settings']['conditional_rules'] : [];
        
        $rule['id'] = 'rule_' . uniqid();
        $rules[] = $rule;
        
        return \FPForms\Plugin::instance()->forms->update_form( $form_id, [
            'settings' => array_merge( $form['settings'], [
                'conditional_rules' => $rules,
            ] ),
        ] );
    }
    
    /**
     * Rimuove regola
     */
    public function remove_rule( $form_id, $rule_id ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        $rules = isset( $form['settings']['conditional_rules'] ) ? $form['settings']['conditional_rules'] : [];
        
        $rules = array_filter( $rules, function( $rule ) use ( $rule_id ) {
            return isset( $rule['id'] ) && $rule['id'] !== $rule_id;
        } );
        
        return \FPForms\Plugin::instance()->forms->update_form( $form_id, [
            'settings' => array_merge( $form['settings'], [
                'conditional_rules' => array_values( $rules ),
            ] ),
        ] );
    }

    /**
     * Valuta le regole condizionali sui dati inviati e restituisce campi visibili e obbligatori.
     * Logica speculare a conditional-logic.js per validazione server-side.
     *
     * @param int   $form_id ID del form.
     * @param array $data    Dati inviati (chiavi = nomi campo senza prefisso fp_field_).
     * @return array{visible_fields: array<string>, required_fields: array<string>}
     */
    public function evaluate_rules( $form_id, array $data ) {
        $form = \FPForms\Plugin::instance()->forms->get_form( $form_id );
        if ( ! $form || ! is_array( $form ) ) {
            return [ 'visible_fields' => [], 'required_fields' => [] ];
        }

        $rules = isset( $form['settings']['conditional_rules'] ) ? $form['settings']['conditional_rules'] : [];
        $rules = $this->validate_rules( $rules );
        if ( empty( $rules ) ) {
            return [ 'visible_fields' => [], 'required_fields' => [] ];
        }

        $fields = \FPForms\Plugin::instance()->forms->get_fields( $form_id );
        if ( ! is_array( $fields ) ) {
            $fields = [];
        }
        $all_field_names = [];
        foreach ( $fields as $f ) {
            $name = isset( $f['name'] ) ? $f['name'] : '';
            if ( $name !== '' ) {
                $all_field_names[] = $name;
            }
            if ( isset( $f['type'] ) && $f['type'] === 'fullname' ) {
                $all_field_names[] = $name . '_nome';
                $all_field_names[] = $name . '_cognome';
            }
        }

        $visible = array_fill_keys( $all_field_names, true );
        $required = [];
        foreach ( $fields as $f ) {
            if ( ! empty( $f['required'] ) && ! empty( $f['name'] ) ) {
                $required[ $f['name'] ] = true;
            }
            if ( isset( $f['type'] ) && $f['type'] === 'fullname' && ! empty( $f['name'] ) && ! empty( $f['required'] ) ) {
                $required[ $f['name'] . '_nome' ] = true;
                $required[ $f['name'] . '_cognome' ] = true;
            }
        }

        foreach ( $rules as $rule ) {
            $trigger_field = isset( $rule['trigger_field'] ) ? $rule['trigger_field'] : '';
            $condition     = isset( $rule['condition'] ) ? $rule['condition'] : '';
            $expected      = isset( $rule['value'] ) ? $rule['value'] : '';
            $action        = isset( $rule['action'] ) ? $rule['action'] : '';
            $targets       = isset( $rule['target_fields'] ) && is_array( $rule['target_fields'] ) ? $rule['target_fields'] : [];
            if ( $trigger_field === '' || $action === '' || empty( $targets ) ) {
                continue;
            }

            $value = $this->get_field_value_from_data( $trigger_field, $data, $form_id );
            $matches = $this->check_condition( $value, $condition, $expected );
            if ( ! $matches ) {
                continue;
            }

            foreach ( $targets as $target ) {
                $target = is_string( $target ) ? trim( $target ) : '';
                if ( $target === '' ) {
                    continue;
                }
                switch ( $action ) {
                    case 'show':
                        $visible[ $target ] = true;
                        break;
                    case 'hide':
                        $visible[ $target ] = false;
                        unset( $required[ $target ] );
                        break;
                    case 'require':
                        $required[ $target ] = true;
                        break;
                    case 'unrequire':
                        unset( $required[ $target ] );
                        break;
                }
            }
        }

        return [
            'visible_fields'  => array_keys( array_filter( $visible ) ),
            'required_fields' => array_keys( $required ),
        ];
    }

    /**
     * Restituisce il valore di un campo dai dati inviati (come fa il JS).
     *
     * @param string $field_name Nome campo.
     * @param array  $data       Dati submission.
     * @param int    $form_id    ID form (per tipo campo se necessario).
     * @return string
     */
    private function get_field_value_from_data( $field_name, array $data, $form_id ) {
        if ( isset( $data[ $field_name ] ) ) {
            $v = $data[ $field_name ];
            if ( is_array( $v ) ) {
                return implode( ',', $v );
            }
            return is_string( $v ) ? $v : (string) $v;
        }
        return '';
    }

    /**
     * Verifica condizione (stessa logica di conditional-logic.js).
     *
     * @param string $value    Valore campo.
     * @param string $condition Condizione (equals, not_equals, contains, ...).
     * @param string $expected  Valore atteso.
     * @return bool
     */
    private function check_condition( $value, $condition, $expected ) {
        $value    = trim( (string) $value );
        $expected = trim( (string) $expected );
        switch ( $condition ) {
            case 'equals':
                return $value === $expected;
            case 'not_equals':
                return $value !== $expected;
            case 'contains':
                return $value !== '' && strpos( $value, $expected ) !== false;
            case 'not_contains':
                return strpos( $value, $expected ) === false;
            case 'greater_than':
                return is_numeric( $value ) && is_numeric( $expected ) && (float) $value > (float) $expected;
            case 'less_than':
                return is_numeric( $value ) && is_numeric( $expected ) && (float) $value < (float) $expected;
            case 'is_empty':
                return $value === '';
            case 'is_not_empty':
                return $value !== '';
            default:
                return false;
        }
    }
}

