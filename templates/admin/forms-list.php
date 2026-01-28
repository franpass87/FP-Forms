<?php
/**
 * Template: Lista form in admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap fp-forms-admin">
    <div class="fp-forms-admin__header">
        <h1><?php _e( 'I tuoi Form', 'fp-forms' ); ?></h1>
        <a href="<?php echo admin_url( 'admin.php?page=fp-forms-new' ); ?>" class="page-title-action">
            <?php _e( 'Aggiungi Nuovo', 'fp-forms' ); ?>
        </a>
    </div>
    
    <?php if ( empty( $forms ) ) : ?>
        <div class="fp-forms-empty-state animated">
            <div class="fp-forms-empty-icon bouncing">📋</div>
            <h2><?php _e( 'Benvenuto in FP Forms!', 'fp-forms' ); ?></h2>
            <p class="fp-empty-subtitle"><?php _e( 'Il tuo form builder professionale per landing page e prenotazioni', 'fp-forms' ); ?></p>
            
            <div class="fp-empty-features">
                <div class="fp-empty-feature">
                    <span class="fp-feature-icon">🎨</span>
                    <strong><?php _e( 'Design Moderno', 'fp-forms' ); ?></strong>
                    <p><?php _e( 'UI/UX professionale e responsive', 'fp-forms' ); ?></p>
                </div>
                <div class="fp-empty-feature">
                    <span class="fp-feature-icon">⚡</span>
                    <strong><?php _e( 'Drag & Drop', 'fp-forms' ); ?></strong>
                    <p><?php _e( 'Costruisci form in pochi minuti', 'fp-forms' ); ?></p>
                </div>
                <div class="fp-empty-feature">
                    <span class="fp-feature-icon">📊</span>
                    <strong><?php _e( 'Analytics', 'fp-forms' ); ?></strong>
                    <p><?php _e( 'Traccia conversioni e performance', 'fp-forms' ); ?></p>
                </div>
            </div>
            
            <div class="fp-empty-actions">
                <a href="<?php echo admin_url( 'admin.php?page=fp-forms-new' ); ?>" class="button button-primary button-hero">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e( 'Crea il tuo primo Form', 'fp-forms' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=fp-forms-templates' ); ?>" class="button button-secondary button-hero">
                    <span class="dashicons dashicons-book"></span>
                    <?php _e( 'Usa un Template', 'fp-forms' ); ?>
                </a>
            </div>
        </div>
    <?php else : ?>
        <div class="fp-forms-table-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e( 'Titolo', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Shortcode', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Submissions', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Conversione', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Data Creazione', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Azioni', 'fp-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $forms as $form ) : 
                    $submissions_count = \FPForms\Plugin::instance()->database->count_submissions( $form['id'] );
                    $unread_count = \FPForms\Plugin::instance()->database->count_submissions( $form['id'], 'unread' );
                    $tracker = \FPForms\Plugin::instance()->analytics;
                    $views = $tracker->get_total_views( $form['id'] );
                    $conversion = $tracker->get_conversion_rate( $form['id'] );
                ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form['id'] ); ?>">
                                    <?php echo esc_html( $form['title'] ); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <code>[fp_form id="<?php echo esc_attr( $form['id'] ); ?>"]</code>
                            <button class="button button-small fp-copy-shortcode" data-shortcode='[fp_form id="<?php echo esc_attr( $form['id'] ); ?>"]' title="<?php _e( 'Copia shortcode', 'fp-forms' ); ?>">
                                <?php _e( 'Copia', 'fp-forms' ); ?>
                            </button>
                        </td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] ); ?>">
                                <?php echo $submissions_count; ?>
                                <?php if ( $unread_count > 0 ) : ?>
                                    <span class="fp-badge-unread"><?php echo $unread_count; ?> non lette</span>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td>
                            <div class="fp-analytics-mini">
                                <span class="fp-views-count" title="<?php _e( 'Visualizzazioni', 'fp-forms' ); ?>">
                                    <span class="dashicons dashicons-visibility"></span> <?php echo $views; ?>
                                </span>
                                <span class="fp-conversion-badge <?php echo $conversion > 5 ? 'good' : ( $conversion > 0 ? 'medium' : 'low' ); ?>" title="<?php _e( 'Tasso di conversione', 'fp-forms' ); ?>">
                                    <?php echo $conversion; ?>%
                                </span>
                            </div>
                        </td>
                        <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $form['created_at'] ) ); ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form['id'] ); ?>" class="button button-small">
                                <?php _e( 'Modifica', 'fp-forms' ); ?>
                            </a>
                            <button class="button button-small fp-duplicate-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>" data-tooltip="Crea una copia di questo form">
                                <?php _e( 'Duplica', 'fp-forms' ); ?>
                            </button>
                            <button class="button button-small button-link-delete fp-delete-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
                                <?php _e( 'Elimina', 'fp-forms' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

