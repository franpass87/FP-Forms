<?php
/**
 * Template: Form Versioning UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$versioning = \FPForms\Plugin::instance()->versioning;
$snapshots = $versioning->get_snapshots( $form_id );
?>

<div class="fp-versioning-section">
    <h3>
        <span class="dashicons dashicons-backup"></span>
        <?php _e( 'Cronologia Versioni', 'fp-forms' ); ?>
    </h3>
    
    <p class="fp-section-description">
        <?php _e( 'Visualizza e ripristina versioni precedenti del form', 'fp-forms' ); ?>
    </p>
    
    <?php if ( empty( $snapshots ) ) : ?>
        <div class="fp-no-snapshots">
            <p><?php _e( 'Nessuno snapshot disponibile. Gli snapshot vengono creati automaticamente quando salvi modifiche significative al form.', 'fp-forms' ); ?></p>
        </div>
    <?php else : ?>
        <div class="fp-snapshots-list">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;"><?php _e( '#', 'fp-forms' ); ?></th>
                        <th><?php _e( 'Data', 'fp-forms' ); ?></th>
                        <th><?php _e( 'Utente', 'fp-forms' ); ?></th>
                        <th><?php _e( 'Note', 'fp-forms' ); ?></th>
                        <th style="width: 150px;"><?php _e( 'Azioni', 'fp-forms' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_reverse( $snapshots ) as $index => $snapshot ) : 
                        $user = get_userdata( $snapshot['user_id'] ?? 0 );
                        $user_name = $user ? $user->display_name : __( 'Sconosciuto', 'fp-forms' );
                        $date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $snapshot['timestamp'] );
                    ?>
                    <tr>
                        <td><?php echo count( $snapshots ) - $index; ?></td>
                        <td>
                            <strong><?php echo esc_html( $date ); ?></strong>
                            <?php if ( $index === 0 ) : ?>
                                <span class="fp-badge-current"><?php _e( 'Corrente', 'fp-forms' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $user_name ); ?></td>
                        <td>
                            <?php if ( ! empty( $snapshot['note'] ) ) : ?>
                                <?php echo esc_html( $snapshot['note'] ); ?>
                            <?php else : ?>
                                <em><?php _e( 'Nessuna nota', 'fp-forms' ); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $index > 0 ) : ?>
                                <button type="button" class="button button-small fp-restore-snapshot" 
                                        data-snapshot-id="<?php echo esc_attr( $snapshot['id'] ); ?>"
                                        data-form-id="<?php echo esc_attr( $form_id ); ?>">
                                    <span class="dashicons dashicons-undo"></span>
                                    <?php _e( 'Ripristina', 'fp-forms' ); ?>
                                </button>
                            <?php else : ?>
                                <span class="button button-small disabled"><?php _e( 'Versione corrente', 'fp-forms' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.fp-versioning-section {
    margin-top: var(--fp-spacing-xl);
    padding: var(--fp-spacing-lg);
    background: #f9fafb;
    border-radius: var(--fp-radius-md);
    border: 1px solid var(--fp-color-border);
}

.fp-no-snapshots {
    padding: var(--fp-spacing-lg);
    text-align: center;
    color: var(--fp-color-muted);
}

.fp-snapshots-list {
    margin-top: var(--fp-spacing-md);
}

.fp-badge-current {
    display: inline-block;
    padding: 2px 8px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.fp-restore-snapshot .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
    margin-right: 4px;
}
</style>

