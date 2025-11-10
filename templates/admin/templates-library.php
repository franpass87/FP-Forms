<?php
/**
 * Template: Templates Library
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap fp-forms-admin">
    <div class="fp-forms-admin__header">
        <h1><?php _e( 'Template Library', 'fp-forms' ); ?></h1>
        <a href="<?php echo admin_url( 'admin.php?page=fp-forms' ); ?>" class="button">
            &larr; <?php _e( 'Torna ai Form', 'fp-forms' ); ?>
        </a>
    </div>
    
    <div class="fp-templates-intro">
        <p><?php _e( 'Inizia velocemente con uno dei nostri template predefiniti. Puoi personalizzarlo dopo l\'importazione.', 'fp-forms' ); ?></p>
    </div>
    
    <div class="fp-templates-container">
        <div class="fp-templates-grid">
            <?php foreach ( $templates as $template ) : ?>
                <div class="fp-template-card" data-template-id="<?php echo esc_attr( $template['id'] ); ?>">
                    <div class="fp-template-icon">
                        <?php echo $template['icon']; ?>
                    </div>
                    <h3 class="fp-template-title"><?php echo esc_html( $template['name'] ); ?></h3>
                    <p class="fp-template-description"><?php echo esc_html( $template['description'] ); ?></p>
                    <div class="fp-template-meta">
                        <span class="fp-template-category"><?php echo esc_html( ucfirst( $template['category'] ) ); ?></span>
                        <span class="fp-template-fields"><?php echo count( $template['fields'] ); ?> campi</span>
                    </div>
                    <button class="button button-primary fp-import-template-btn">
                        <?php _e( 'Usa Template', 'fp-forms' ); ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal per import template -->
<div id="fp-template-import-modal" class="fp-modal" style="display:none;">
    <div class="fp-modal-content">
        <div class="fp-modal-header">
            <h2><?php _e( 'Importa Template', 'fp-forms' ); ?></h2>
            <button class="fp-modal-close">&times;</button>
        </div>
        <div class="fp-modal-body">
            <form id="fp-template-import-form">
                <div class="fp-form-group">
                    <label for="template-title"><?php _e( 'Titolo Form (opzionale)', 'fp-forms' ); ?></label>
                    <input type="text" 
                           id="template-title" 
                           name="custom_title" 
                           placeholder="<?php _e( 'Lascia vuoto per usare il titolo del template', 'fp-forms' ); ?>"
                           class="fp-input-large">
                </div>
                <input type="hidden" id="template-id" name="template_id">
                <div class="fp-modal-actions">
                    <button type="submit" class="button button-primary button-large">
                        <?php _e( 'Importa Template', 'fp-forms' ); ?>
                    </button>
                    <button type="button" class="button button-large fp-modal-close">
                        <?php _e( 'Annulla', 'fp-forms' ); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Template Library Styles */
.fp-templates-intro {
    margin: 20px 0 30px;
    padding: 20px;
    background: #f0f6fc;
    border-left: 4px solid var(--fp-color-primary);
    border-radius: var(--fp-radius-md);
}

.fp-templates-intro p {
    margin: 0;
    color: var(--fp-color-text);
    font-size: 15px;
}

.fp-templates-container {
    background: var(--fp-color-surface);
    border-radius: var(--fp-radius-lg);
    padding: var(--fp-spacing-lg);
    box-shadow: var(--fp-shadow-md);
}

.fp-templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--fp-spacing-lg);
}

.fp-template-card {
    background: var(--fp-color-background);
    border: 2px solid var(--fp-color-border);
    border-radius: var(--fp-radius-lg);
    padding: var(--fp-spacing-lg);
    transition: all 0.2s ease;
    cursor: pointer;
}

.fp-template-card:hover {
    border-color: var(--fp-color-primary);
    box-shadow: var(--fp-shadow-lg);
    transform: translateY(-4px);
}

.fp-template-icon {
    font-size: 48px;
    text-align: center;
    margin-bottom: var(--fp-spacing-md);
}

.fp-template-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 var(--fp-spacing-sm);
    color: var(--fp-color-text);
}

.fp-template-description {
    font-size: 14px;
    color: var(--fp-color-muted);
    margin: 0 0 var(--fp-spacing-md);
    line-height: 1.5;
}

.fp-template-meta {
    display: flex;
    gap: var(--fp-spacing-sm);
    margin-bottom: var(--fp-spacing-md);
    font-size: 12px;
}

.fp-template-category,
.fp-template-fields {
    padding: 4px 10px;
    background: #e0e7ff;
    color: #3730a3;
    border-radius: var(--fp-radius-full);
    font-weight: 600;
}

.fp-import-template-btn {
    width: 100%;
    justify-content: center;
}

.fp-modal-actions {
    display: flex;
    gap: var(--fp-spacing-sm);
    margin-top: var(--fp-spacing-lg);
}

.fp-modal-actions button {
    flex: 1;
}

@media (max-width: 768px) {
    .fp-templates-grid {
        grid-template-columns: 1fr;
    }
}
</style>

