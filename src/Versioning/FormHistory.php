<?php
namespace FPForms\Versioning;

/**
 * Form Versioning Manager
 * Gestisce snapshot e cronologia modifiche form
 */
class FormHistory {
    
    /**
     * Costruttore
     */
    public function __construct() {
        // Hook per salvare snapshot automatici
        add_action( 'fp_forms_form_saved', [ $this, 'create_snapshot' ], 10, 2 );
    }
    
    /**
     * Crea snapshot del form
     */
    public function create_snapshot( $form_id, $form_data ) {
        // Crea snapshot solo se form è stato modificato significativamente
        $last_snapshot = $this->get_latest_snapshot( $form_id );
        
        if ( $last_snapshot ) {
            // Verifica se ci sono cambiamenti significativi
            if ( ! $this->has_significant_changes( $last_snapshot['data'], $form_data ) ) {
                return false;
            }
        }
        
        $snapshot = [
            'id' => 'snapshot_' . uniqid(),
            'form_id' => $form_id,
            'data' => $form_data,
            'timestamp' => current_time( 'timestamp' ),
            'user_id' => get_current_user_id(),
            'note' => '', // Può essere aggiunto manualmente
        ];
        
        $snapshots = $this->get_snapshots( $form_id );
        $snapshots[] = $snapshot;
        
        // Mantieni solo ultimi 20 snapshot
        if ( count( $snapshots ) > 20 ) {
            $snapshots = array_slice( $snapshots, -20 );
        }
        
        update_post_meta( $form_id, '_fp_form_snapshots', $snapshots );
        
        \FPForms\Core\Logger::info( 'Form snapshot created', [
            'form_id' => $form_id,
            'snapshot_id' => $snapshot['id'],
        ] );
        
        return $snapshot['id'];
    }
    
    /**
     * Ottiene tutti gli snapshot di un form
     */
    public function get_snapshots( $form_id ) {
        $snapshots = get_post_meta( $form_id, '_fp_form_snapshots', true );
        return is_array( $snapshots ) ? $snapshots : [];
    }
    
    /**
     * Ottiene ultimo snapshot
     */
    public function get_latest_snapshot( $form_id ) {
        $snapshots = $this->get_snapshots( $form_id );
        return ! empty( $snapshots ) ? end( $snapshots ) : null;
    }
    
    /**
     * Verifica se ci sono cambiamenti significativi
     */
    private function has_significant_changes( $old_data, $new_data ) {
        // Confronta campi chiave
        $key_fields = [ 'title', 'description', 'fields', 'settings' ];
        
        foreach ( $key_fields as $field ) {
            $old_value = isset( $old_data[ $field ] ) ? $old_data[ $field ] : null;
            $new_value = isset( $new_data[ $field ] ) ? $new_data[ $field ] : null;
            
            if ( $old_value !== $new_value ) {
                // Serializza per confronto profondo
                if ( serialize( $old_value ) !== serialize( $new_value ) ) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Ripristina form da snapshot
     */
    public function restore_snapshot( $form_id, $snapshot_id ) {
        $snapshots = $this->get_snapshots( $form_id );
        
        foreach ( $snapshots as $snapshot ) {
            if ( $snapshot['id'] === $snapshot_id ) {
                $form_data = $snapshot['data'];
                
                // Aggiorna form con dati snapshot
                $result = \FPForms\Plugin::instance()->forms->update_form( $form_id, $form_data );
                
                if ( $result ) {
                    // Crea nuovo snapshot del restore
                    $this->create_snapshot( $form_id, $form_data );
                    
                    \FPForms\Core\Logger::info( 'Form restored from snapshot', [
                        'form_id' => $form_id,
                        'snapshot_id' => $snapshot_id,
                    ] );
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Elimina snapshot
     */
    public function delete_snapshot( $form_id, $snapshot_id ) {
        $snapshots = $this->get_snapshots( $form_id );
        
        $snapshots = array_filter( $snapshots, function( $snapshot ) use ( $snapshot_id ) {
            return $snapshot['id'] !== $snapshot_id;
        } );
        
        update_post_meta( $form_id, '_fp_form_snapshots', array_values( $snapshots ) );
        
        return true;
    }
    
    /**
     * Ottiene diff tra due snapshot
     */
    public function get_diff( $form_id, $snapshot_id_1, $snapshot_id_2 ) {
        $snapshots = $this->get_snapshots( $form_id );
        
        $snapshot1 = null;
        $snapshot2 = null;
        
        foreach ( $snapshots as $snapshot ) {
            if ( $snapshot['id'] === $snapshot_id_1 ) {
                $snapshot1 = $snapshot;
            }
            if ( $snapshot['id'] === $snapshot_id_2 ) {
                $snapshot2 = $snapshot;
            }
        }
        
        if ( ! $snapshot1 || ! $snapshot2 ) {
            return null;
        }
        
        // Confronto semplice (può essere migliorato)
        $diff = [
            'title_changed' => $snapshot1['data']['title'] !== $snapshot2['data']['title'],
            'fields_changed' => serialize( $snapshot1['data']['fields'] ) !== serialize( $snapshot2['data']['fields'] ),
            'settings_changed' => serialize( $snapshot1['data']['settings'] ) !== serialize( $snapshot2['data']['settings'] ),
        ];
        
        return $diff;
    }
}

