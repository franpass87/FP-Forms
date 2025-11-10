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
        add_action( 'wp_footer', [ $this, 'output_logic_script' ] );
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
}

