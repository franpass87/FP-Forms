<?php
namespace FPForms\Forms;

/**
 * Multi-Step Forms Manager
 * Gestisce form wizard multi-step
 */
class MultiStep {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Hook per renderizzare multi-step forms
        add_filter( 'fp_forms_render_form', [ $this, 'maybe_render_multistep' ], 10, 2 );
    }
    
    /**
     * Verifica se il form è multi-step e renderizza di conseguenza
     */
    public function maybe_render_multistep( $html, $form ) {
        if ( ! isset( $form['settings']['enable_multistep'] ) || ! $form['settings']['enable_multistep'] ) {
            return $html;
        }
        
        // Raggruppa campi per step
        $steps = $this->group_fields_by_step( $form['fields'] );
        
        if ( count( $steps ) <= 1 ) {
            return $html;
        }
        
        // Renderizza wizard
        return $this->render_multistep_form( $form, $steps );
    }
    
    /**
     * Raggruppa campi per step
     */
    private function group_fields_by_step( $fields ) {
        $steps = [];
        $current_step = 0;
        
        foreach ( $fields as $field ) {
            // "step_break" è un campo speciale che indica nuovo step
            if ( isset( $field['type'] ) && $field['type'] === 'step_break' ) {
                $current_step++;
                continue;
            }
            
            if ( ! isset( $steps[ $current_step ] ) ) {
                $steps[ $current_step ] = [
                    'title' => $field['step_title'] ?? sprintf( __( 'Step %d', 'fp-forms' ), $current_step + 1 ),
                    'fields' => [],
                ];
            }
            
            $steps[ $current_step ]['fields'][] = $field;
        }
        
        return $steps;
    }
    
    /**
     * Renderizza form multi-step
     */
    private function render_multistep_form( $form, $steps ) {
        ob_start();
        include FP_FORMS_PLUGIN_DIR . 'templates/frontend/multistep-form.php';
        return ob_get_clean();
    }
}

