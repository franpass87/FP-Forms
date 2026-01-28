<?php
namespace FPForms\Fields;

/**
 * Campo Calcolato
 * Calcola valori in base a formula
 */
class CalculatedField {
    
    /**
     * Renderizza campo calcolato
     */
    public static function render( $field, $form_id ) {
        $field_name = 'fp_field_' . $field['name'];
        $field_id = 'fp_field_' . $form_id . '_' . $field['name'];
        $formula = isset( $field['options']['formula'] ) ? $field['options']['formula'] : '';
        $format = isset( $field['options']['format'] ) ? $field['options']['format'] : 'number';
        $decimals = isset( $field['options']['decimals'] ) ? (int) $field['options']['decimals'] : 2;
        
        $html = sprintf(
            '<input type="text" 
                   id="%s" 
                   name="%s" 
                   class="fp-forms-input fp-calculated-field" 
                   readonly 
                   data-formula="%s"
                   data-format="%s"
                   data-decimals="%d"
                   value="" />',
            esc_attr( $field_id ),
            esc_attr( $field_name ),
            esc_attr( $formula ),
            esc_attr( $format ),
            $decimals
        );
        
        $field['id'] = $field_id;
        return FieldFactory::wrap_field( $field, $html );
    }
}

