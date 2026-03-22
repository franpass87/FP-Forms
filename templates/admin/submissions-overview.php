<?php
/**
 * Template: Panoramica submissions (tutti i form)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap fp-forms-admin">
    <h1 class="screen-reader-text"><?php esc_html_e( 'Submissions', 'fp-forms' ); ?></h1>
    <div class="fp-forms-admin__header">
        <div class="fpforms-page-header-content">
            <h2 class="fp-forms-page-header-title" aria-hidden="true"><?php esc_html_e( 'Submissions', 'fp-forms' ); ?></h2>
            <p class="fpforms-page-header-desc"><?php esc_html_e( 'Visualizza e gestisci le submission dei form.', 'fp-forms' ); ?></p>
        </div>
        <span class="fpforms-page-header-badge">v<?php echo esc_html( defined( 'FP_FORMS_VERSION' ) ? FP_FORMS_VERSION : '0' ); ?></span>
    </div>

    <?php if ( empty( $forms_data ) ) : ?>
        <div class="fp-forms-empty-state animated">
            <div class="fp-forms-empty-icon bouncing">📬</div>
            <h2><?php _e( 'Nessun form trovato', 'fp-forms' ); ?></h2>
            <p class="fp-empty-subtitle"><?php _e( 'Crea il tuo primo form per iniziare a raccogliere submissions.', 'fp-forms' ); ?></p>
            <div class="fp-empty-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=fp-forms-new' ) ); ?>" class="button button-primary button-hero">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e( 'Crea un Form', 'fp-forms' ); ?>
                </a>
            </div>
        </div>
    <?php else : ?>
        <div class="fp-forms-table-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e( 'Form', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Submissions', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Non Lette', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Ultima Submission', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Azioni', 'fp-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $forms_data as $fd ) : ?>
                <tr>
                    <td>
                        <strong>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $fd['id'] ) ); ?>">
                                <?php echo esc_html( $fd['title'] ); ?>
                            </a>
                        </strong>
                    </td>
                    <td><?php echo (int) $fd['total']; ?></td>
                    <td>
                        <?php if ( $fd['unread'] > 0 ) : ?>
                            <span class="fp-badge-unread"><?php echo (int) $fd['unread']; ?></span>
                        <?php else : ?>
                            <span style="color: var(--fp-color-muted);">&mdash;</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( $fd['last_submission'] ) : ?>
                            <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $fd['last_submission'] ) ) ); ?>
                        <?php else : ?>
                            <span style="color: var(--fp-color-muted);">&mdash;</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $fd['id'] ) ); ?>" class="button button-small">
                            <?php _e( 'Vedi Submissions', 'fp-forms' ); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
