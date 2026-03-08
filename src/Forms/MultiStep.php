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
     * Raggruppa campi per step.
     * Il titolo di ogni step viene letto dal campo step_break che lo precede,
     * non dal primo campo dello step (che non ha la proprietà step_title).
     */
    private function group_fields_by_step( $fields ) {
        $steps           = [];
        $current_step    = 0;
        $next_step_title = null;
        $has_fields      = false; // Traccia se abbiamo già incontrato almeno un campo
        
        foreach ( $fields as $field ) {
            // step_break segna l'inizio di un nuovo step e porta il titolo
            if ( isset( $field['type'] ) && $field['type'] === 'step_break' ) {
                // Ignora step_break iniziali (prima di qualsiasi campo)
                if ( $has_fields ) {
                    $current_step++;
                }
                $next_step_title = isset( $field['label'] ) && $field['label'] !== '' ? $field['label'] : null;
                continue;
            }
            
            $has_fields = true;
            
            if ( ! isset( $steps[ $current_step ] ) ) {
                $steps[ $current_step ] = [
                    'title'  => $next_step_title ?? sprintf( __( 'Step %d', 'fp-forms' ), count( $steps ) + 1 ),
                    'fields' => [],
                ];
                $next_step_title = null;
            }
            
            $steps[ $current_step ]['fields'][] = $field;
        }
        
        // Riordina gli step come array indicizzato da 0 per il template
        return array_values( $steps );
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

