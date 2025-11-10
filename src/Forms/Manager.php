<?php
namespace FPForms\Forms;

use FPForms\Core\Cache;

/**
 * Gestisce i form
 */
class Manager {
    
    /**
     * Crea un nuovo form
     */
    public function create_form( $title, $args = [] ) {
        $defaults = [
            'description' => '',
            'settings' => [],
            'fields' => [],
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        $post_id = wp_insert_post( [
            'post_title' => $title,
            'post_content' => $args['description'],
            'post_status' => 'publish',
            'post_type' => 'fp_form',
        ] );
        
        if ( $post_id ) {
            // Salva settings
            update_post_meta( $post_id, '_fp_form_settings', $args['settings'] );
            
            // Salva campi se presenti
            if ( ! empty( $args['fields'] ) ) {
                $this->update_fields( $post_id, $args['fields'] );
            }
            
            return $post_id;
        }
        
        return false;
    }
    
    /**
     * Aggiorna un form
     */
    public function update_form( $form_id, $data ) {
        $update_data = [
            'ID' => $form_id,
        ];
        
        if ( isset( $data['title'] ) ) {
            $update_data['post_title'] = $data['title'];
        }
        
        if ( isset( $data['description'] ) ) {
            $update_data['post_content'] = $data['description'];
        }
        
        $result = wp_update_post( $update_data );
        
        if ( $result ) {
            // Aggiorna settings
            if ( isset( $data['settings'] ) ) {
                update_post_meta( $form_id, '_fp_form_settings', $data['settings'] );
            }
            
            // Aggiorna campi
            if ( isset( $data['fields'] ) ) {
                $this->update_fields( $form_id, $data['fields'] );
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Elimina un form (e tutti i dati associati)
     */
    public function delete_form( $form_id ) {
        global $wpdb;
        $db = \FPForms\Plugin::instance()->database;
        
        // BUGFIX: Elimina PRIMA i file delle submissions
        $submissions = $wpdb->get_col( $wpdb->prepare(
            "SELECT id FROM {$db->get_submissions_table()} WHERE form_id = %d",
            $form_id
        ) );
        
        foreach ( $submissions as $submission_id ) {
            // Usa il metodo che elimina anche i file fisici
            $db->delete_submission( $submission_id );
        }
        
        // Elimina campi
        $wpdb->delete( $db->get_fields_table(), [ 'form_id' => $form_id ], [ '%d' ] );
        
        // Invalida cache
        Cache::invalidate_form( $form_id );
        
        // Elimina post
        return wp_delete_post( $form_id, true );
    }
    
    /**
     * Ottiene un form
     */
    public function get_form( $form_id ) {
        $form_id = absint( $form_id );

        if ( ! $form_id ) {
            return null;
        }

        $cached = Cache::get_form( $form_id );
        if ( null !== $cached ) {
            return $cached;
        }

        $post = get_post( $form_id );
        
        if ( ! $post || $post->post_type !== 'fp_form' ) {
            Cache::invalidate_form( $form_id );
            return null;
        }
        
        $form = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'description' => $post->post_content,
            'settings' => get_post_meta( $post->ID, '_fp_form_settings', true ),
            'fields' => $this->get_fields( $post->ID ),
            'created_at' => $post->post_date,
            'modified_at' => $post->post_modified,
        ];

        Cache::set_form( $form_id, $form );

        return $form;
    }
    
    /**
     * Ottiene tutti i form
     */
    public function get_forms( $args = [] ) {
        $defaults = [
            'posts_per_page' => -1,
            'post_type' => 'fp_form',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        
        $args = wp_parse_args( $args, $defaults );
        
        $posts = get_posts( $args );
        
        $forms = [];
        foreach ( $posts as $post ) {
            $forms[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'description' => $post->post_content,
                'created_at' => $post->post_date,
                'modified_at' => $post->post_modified,
            ];
        }
        
        return $forms;
    }
    
    /**
     * Aggiorna i campi di un form
     */
    public function update_fields( $form_id, $fields ) {
        $db = \FPForms\Plugin::instance()->database;
        return $db->save_form_fields( $form_id, $fields );
    }
    
    /**
     * Ottiene i campi di un form
     */
    public function get_fields( $form_id ) {
        $db = \FPForms\Plugin::instance()->database;
        $fields_data = $db->get_form_fields( $form_id );
        
        $fields = [];
        foreach ( $fields_data as $field ) {
            $fields[] = [
                'id' => $field->id,
                'type' => $field->field_type,
                'label' => $field->field_label,
                'name' => $field->field_name,
                'options' => $field->field_options ? json_decode( $field->field_options, true ) : [],
                'required' => (bool) $field->is_required,
                'order' => $field->field_order,
            ];
        }
        
        return $fields;
    }
    
    /**
     * Duplica un form
     */
    public function duplicate_form( $form_id ) {
        $form = $this->get_form( $form_id );
        
        if ( ! $form ) {
            return false;
        }
        
        return $this->create_form(
            $form['title'] . ' (Copia)',
            [
                'description' => $form['description'],
                'settings' => $form['settings'],
                'fields' => $form['fields'],
            ]
        );
    }
}

