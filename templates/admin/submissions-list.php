<?php
/**
 * Template: Lista submissions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap fp-forms-admin">
    <div class="fp-forms-admin__header">
        <h1><?php printf( __( 'Submissions: %s', 'fp-forms' ), $form['title'] ); ?></h1>
        <div class="fp-header-actions">
            <button class="button fp-export-submissions-btn" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
                <span class="dashicons dashicons-download"></span>
                <?php _e( 'Export', 'fp-forms' ); ?>
            </button>
            <a href="<?php echo admin_url( 'admin.php?page=fp-forms' ); ?>" class="button">
                &larr; <?php _e( 'Torna ai Form', 'fp-forms' ); ?>
            </a>
        </div>
    </div>
    
    <!-- Search & Filters Bar -->
    <div class="fp-search-filters-bar">
        <form method="get" action="" class="fp-search-form">
            <input type="hidden" name="page" value="fp-forms-submissions">
            <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">
            
            <div class="fp-search-input-wrapper">
                <span class="dashicons dashicons-search"></span>
                <input type="search" name="s" value="<?php echo esc_attr( $search ?? '' ); ?>" placeholder="<?php _e( 'Cerca nelle submissions...', 'fp-forms' ); ?>" class="fp-search-input">
            </div>
            
            <select name="status" class="fp-status-filter">
                <option value=""><?php _e( 'Tutti gli stati', 'fp-forms' ); ?></option>
                <option value="unread" <?php selected( $status_filter ?? '', 'unread' ); ?>><?php _e( 'Non Lette', 'fp-forms' ); ?></option>
                <option value="read" <?php selected( $status_filter ?? '', 'read' ); ?>><?php _e( 'Lette', 'fp-forms' ); ?></option>
            </select>
            
            <button type="submit" class="button"><?php _e( 'Filtra', 'fp-forms' ); ?></button>
            <?php if ( ! empty( $search ) || ! empty( $status_filter ) ) : ?>
            <a href="<?php echo admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] ); ?>" class="button">
                <?php _e( 'Reset', 'fp-forms' ); ?>
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if ( empty( $submissions ) ) : ?>
        <div class="fp-forms-empty-state animated">
            <div class="fp-forms-empty-icon bouncing">📬</div>
            <h2><?php _e( 'Nessuna submission ancora', 'fp-forms' ); ?></h2>
            <p class="fp-empty-subtitle"><?php _e( 'Quando qualcuno compila questo form, vedrai qui i risultati', 'fp-forms' ); ?></p>
            
            <div class="fp-empty-tips">
                <h3><?php _e( '💡 Suggerimenti per ottenere più submissions:', 'fp-forms' ); ?></h3>
                <ul>
                    <li><?php _e( '✓ Inserisci lo shortcode nelle tue landing page', 'fp-forms' ); ?></li>
                    <li><?php _e( '✓ Ottimizza il form per conversioni più alte', 'fp-forms' ); ?></li>
                    <li><?php _e( '✓ Usa la logica condizionale per semplificare la compilazione', 'fp-forms' ); ?></li>
                    <li><?php _e( '✓ Testa il form prima di pubblicarlo', 'fp-forms' ); ?></li>
                </ul>
            </div>
            
            <div class="fp-empty-actions">
                <button class="button button-primary button-large" onclick="navigator.clipboard.writeText('[fp_form id=<?php echo esc_js( $form['id'] ); ?>]')">
                    <span class="dashicons dashicons-admin-page"></span>
                    <?php _e( 'Copia Shortcode', 'fp-forms' ); ?>
                </button>
                <a href="<?php echo admin_url( 'admin.php?page=fp-forms-edit&form_id=' . $form['id'] ); ?>" class="button button-large">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e( 'Modifica Form', 'fp-forms' ); ?>
                </a>
            </div>
        </div>
    <?php else : ?>
        <!-- Bulk Actions -->
        <div class="fp-bulk-actions-bar">
            <input type="checkbox" id="fp-select-all-submissions" />
            <label for="fp-select-all-submissions"><?php _e( 'Seleziona Tutti', 'fp-forms' ); ?></label>
            
            <select id="fp-bulk-action">
                <option value=""><?php _e( 'Azioni di massa', 'fp-forms' ); ?></option>
                <option value="delete"><?php _e( 'Elimina', 'fp-forms' ); ?></option>
                <option value="mark-read"><?php _e( 'Segna come lette', 'fp-forms' ); ?></option>
                <option value="mark-unread"><?php _e( 'Segna come non lette', 'fp-forms' ); ?></option>
                <option value="export"><?php _e( 'Export selezionate', 'fp-forms' ); ?></option>
            </select>
            
            <button type="button" class="button" id="fp-apply-bulk-action">
                <?php _e( 'Applica', 'fp-forms' ); ?>
            </button>
            
            <span class="fp-selected-count" style="margin-left: 15px; display:none;">
                <strong>0</strong> <?php _e( 'selezionate', 'fp-forms' ); ?>
            </span>
        </div>
        
        <div class="fp-forms-table-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="fp-select-all-table" /></th>
                    <th style="width: 50px;"><?php _e( 'ID', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Anteprima Dati', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Data', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Stato', 'fp-forms' ); ?></th>
                    <th><?php _e( 'Azioni', 'fp-forms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $submissions as $submission ) : 
                    $data = json_decode( $submission->data, true );
                    
                    // Gestisci errori JSON
                    if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $data ) ) {
                        $data = [];
                    }
                    
                    $preview = '';
                    $count = 0;
                    foreach ( $data as $key => $value ) {
                        if ( $count++ >= 3 ) break;
                        if ( is_array( $value ) ) {
                            $value = implode( ', ', $value );
                        }
                        $preview .= '<strong>' . esc_html( $key ) . ':</strong> ' . esc_html( substr( $value, 0, 50 ) ) . ' | ';
                    }
                ?>
                    <tr class="<?php echo $submission->status === 'unread' ? 'fp-submission-unread' : ''; ?>" data-submission-id="<?php echo esc_attr( $submission->id ); ?>">
                        <td><input type="checkbox" class="fp-submission-checkbox" value="<?php echo esc_attr( $submission->id ); ?>" /></td>
                        <td><?php echo esc_html( $submission->id ); ?></td>
                        <td>
                            <?php echo rtrim( $preview, ' | ' ); ?>
                            <?php if ( $count >= 3 ) echo '...'; ?>
                        </td>
                        <td><?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $submission->created_at ) ); ?></td>
                        <td>
                            <?php if ( $submission->status === 'unread' ) : ?>
                                <span class="fp-status-unread"><?php _e( 'Non letta', 'fp-forms' ); ?></span>
                            <?php else : ?>
                                <span class="fp-status-read"><?php _e( 'Letta', 'fp-forms' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="button button-small fp-view-submission" data-submission-id="<?php echo esc_attr( $submission->id ); ?>">
                                <?php _e( 'Visualizza', 'fp-forms' ); ?>
                            </button>
                            <button class="button button-small button-link-delete fp-delete-submission" data-submission-id="<?php echo esc_attr( $submission->id ); ?>">
                                <?php _e( 'Elimina', 'fp-forms' ); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
        <!-- Pagination -->
        <?php if ( isset( $total_pages ) && $total_pages > 1 ) : ?>
        <div class="fp-pagination">
            <?php
            $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
            $base_url = admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] );
            
            if ( ! empty( $search ) ) {
                $base_url .= '&s=' . urlencode( $search );
            }
            if ( ! empty( $status_filter ) ) {
                $base_url .= '&status=' . urlencode( $status_filter );
            }
            
            // First
            if ( $current_page > 1 ) :
            ?>
            <a href="<?php echo $base_url . '&paged=1'; ?>" class="fp-page-link">«</a>
            <a href="<?php echo $base_url . '&paged=' . ( $current_page - 1 ); ?>" class="fp-page-link">‹</a>
            <?php endif; ?>
            
            <?php
            // Pages
            $range = 2;
            for ( $i = max( 1, $current_page - $range ); $i <= min( $total_pages, $current_page + $range ); $i++ ) :
            ?>
            <a href="<?php echo $base_url . '&paged=' . $i; ?>" class="fp-page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php
            // Last
            if ( $current_page < $total_pages ) :
            ?>
            <a href="<?php echo $base_url . '&paged=' . ( $current_page + 1 ); ?>" class="fp-page-link">›</a>
            <a href="<?php echo $base_url . '&paged=' . $total_pages; ?>" class="fp-page-link">»</a>
            <?php endif; ?>
            
            <span class="fp-page-info">
                <?php printf( __( 'Pagina %d di %d', 'fp-forms' ), $current_page, $total_pages ); ?>
            </span>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Modal per visualizzare submission -->
<div id="fp-submission-modal" class="fp-modal" style="display:none;">
    <div class="fp-modal-content">
        <div class="fp-modal-header">
            <h2><?php _e( 'Dettagli Submission', 'fp-forms' ); ?></h2>
            <button class="fp-modal-close">&times;</button>
        </div>
        <div class="fp-modal-body" id="fp-submission-details">
            <!-- Contenuto caricato via JS -->
        </div>
    </div>
</div>

<!-- Modal Export -->
<div id="fp-export-modal" class="fp-modal" style="display:none;">
    <div class="fp-modal-content">
        <div class="fp-modal-header">
            <h2><?php _e( 'Export Submissions', 'fp-forms' ); ?></h2>
            <button class="fp-modal-close">&times;</button>
        </div>
        <div class="fp-modal-body">
            <form id="fp-export-form" method="post">
                <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">
                
                <div class="fp-form-group">
                    <label><?php _e( 'Formato Export', 'fp-forms' ); ?></label>
                    <select name="format" class="fp-input">
                        <option value="csv">CSV</option>
                        <option value="xlsx">Excel (XLSX)</option>
                    </select>
                </div>
                
                <div class="fp-form-group">
                    <label><?php _e( 'Data Da', 'fp-forms' ); ?></label>
                    <input type="date" name="date_from" class="fp-input">
                </div>
                
                <div class="fp-form-group">
                    <label><?php _e( 'Data A', 'fp-forms' ); ?></label>
                    <input type="date" name="date_to" class="fp-input">
                </div>
                
                <div class="fp-form-group">
                    <label><?php _e( 'Stato', 'fp-forms' ); ?></label>
                    <select name="status" class="fp-input">
                        <option value=""><?php _e( 'Tutti', 'fp-forms' ); ?></option>
                        <option value="unread"><?php _e( 'Non Lette', 'fp-forms' ); ?></option>
                        <option value="read"><?php _e( 'Lette', 'fp-forms' ); ?></option>
                    </select>
                </div>
                
                <div class="fp-modal-actions">
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e( 'Scarica Export', 'fp-forms' ); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fp-header-actions {
    display: flex;
    gap: var(--fp-spacing-sm);
    align-items: center;
}

.fp-export-submissions-btn .dashicons {
    margin-right: 6px;
}

.fp-form-group {
    margin-bottom: var(--fp-spacing-md);
}

.fp-form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 14px;
}

.fp-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: var(--fp-radius-md);
    font-size: 15px;
}

.fp-modal-actions .dashicons {
    margin-right: 6px;
}
</style>


