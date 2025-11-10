<?php
/**
 * Template: Multi-Step Form
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$total_steps = count( $steps );
?>

<div class="fp-forms-container fp-multistep-container" id="fp-forms-container-<?php echo esc_attr( $form['id'] ); ?>">
    <!-- Progress Bar -->
    <div class="fp-multistep-progress">
        <div class="fp-progress-bar">
            <div class="fp-progress-fill" style="width: <?php echo ( 1 / $total_steps ) * 100; ?>%"></div>
        </div>
        <div class="fp-progress-steps">
            <?php foreach ( $steps as $index => $step ) : ?>
            <div class="fp-progress-step <?php echo $index === 0 ? 'active' : ''; ?>" data-step="<?php echo esc_attr( $index ); ?>">
                <div class="fp-step-number"><?php echo esc_html( $index + 1 ); ?></div>
                <div class="fp-step-title"><?php echo esc_html( $step['title'] ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <form class="fp-forms-form fp-multistep-form" 
          id="fp-form-<?php echo esc_attr( $form['id'] ); ?>" 
          data-form-id="<?php echo esc_attr( $form['id'] ); ?>"
          data-total-steps="<?php echo esc_attr( $total_steps ); ?>">
        
        <?php foreach ( $steps as $index => $step ) : ?>
        <div class="fp-step-content" data-step="<?php echo esc_attr( $index ); ?>" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>">
            <h3 class="fp-step-heading"><?php echo esc_html( $step['title'] ); ?></h3>
            
            <?php foreach ( $step['fields'] as $field ) : ?>
                <?php echo \FPForms\Fields\FieldFactory::render( $field, $form['id'] ); ?>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <!-- Navigation Buttons -->
        <div class="fp-multistep-nav">
            <button type="button" class="button fp-btn-prev" style="display:none;">
                ← <?php _e( 'Indietro', 'fp-forms' ); ?>
            </button>
            
            <button type="button" class="button button-primary fp-btn-next">
                <?php _e( 'Avanti', 'fp-forms' ); ?> →
            </button>
            
            <button type="submit" class="button button-primary fp-btn-submit" style="display:none;">
                <?php _e( 'Invia Form', 'fp-forms' ); ?>
            </button>
        </div>
        
        <div class="fp-forms-messages">
            <div class="fp-forms-message fp-forms-success" style="display:none;"></div>
            <div class="fp-forms-message fp-forms-error" style="display:none;"></div>
        </div>
        
        <?php
        // Honeypot anti-spam
        $anti_spam = new \FPForms\Security\AntiSpam();
        echo $anti_spam->get_honeypot_field( $form['id'] );
        ?>
        
        <input type="hidden" name="action" value="fp_forms_submit">
        <input type="hidden" name="form_id" value="<?php echo esc_attr( $form['id'] ); ?>">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'fp_forms_submit' ); ?>">
        <input type="hidden" id="fp-current-step" value="0">
    </form>
</div>

<style>
.fp-multistep-progress {
    margin-bottom: var(--fp-spacing-xl);
}

.fp-progress-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
    margin-bottom: var(--fp-spacing-lg);
}

.fp-progress-fill {
    height: 100%;
    background: linear-gradient(135deg, var(--fp-color-primary) 0%, #7c3aed 100%);
    transition: width 0.4s ease;
}

.fp-progress-steps {
    display: flex;
    justify-content: space-between;
    gap: var(--fp-spacing-md);
}

.fp-progress-step {
    flex: 1;
    text-align: center;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.fp-progress-step.active,
.fp-progress-step.completed {
    opacity: 1;
}

.fp-step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin: 0 auto 8px;
    transition: all 0.3s;
}

.fp-progress-step.active .fp-step-number {
    background: var(--fp-color-primary);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
}

.fp-progress-step.completed .fp-step-number {
    background: #10b981;
    color: #fff;
}

.fp-step-title {
    font-size: 12px;
    color: var(--fp-color-muted);
    font-weight: 600;
}

.fp-step-content {
    animation: fadeIn 0.4s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.fp-step-heading {
    font-size: 24px;
    margin-bottom: var(--fp-spacing-lg);
    color: var(--fp-color-text);
}

.fp-multistep-nav {
    display: flex;
    gap: var(--fp-spacing-md);
    justify-content: space-between;
    margin-top: var(--fp-spacing-xl);
    padding-top: var(--fp-spacing-lg);
    border-top: 1px solid var(--fp-color-border);
}
</style>

<script>
jQuery(document).ready(function($) {
    var $form = $('#fp-form-<?php echo esc_js( $form['id'] ); ?>');
    var currentStep = 0;
    var totalSteps = parseInt($form.data('total-steps'));
    
    // Aggiorna UI
    function updateUI() {
        // Nascondi tutti gli step
        $('.fp-step-content').hide();
        $('.fp-step-content[data-step="' + currentStep + '"]').show();
        
        // Aggiorna progress bar
        var progress = ((currentStep + 1) / totalSteps) * 100;
        $('.fp-progress-fill').css('width', progress + '%');
        
        // Aggiorna indicator steps
        $('.fp-progress-step').removeClass('active completed');
        $('.fp-progress-step').each(function(idx) {
            if (idx < currentStep) {
                $(this).addClass('completed');
            } else if (idx === currentStep) {
                $(this).addClass('active');
            }
        });
        
        // Mostra/Nascondi bottoni
        $('.fp-btn-prev').toggle(currentStep > 0);
        $('.fp-btn-next').toggle(currentStep < totalSteps - 1);
        $('.fp-btn-submit').toggle(currentStep === totalSteps - 1);
        
        $('#fp-current-step').val(currentStep);
    }
    
    // Next
    $('.fp-btn-next').on('click', function() {
        if (currentStep < totalSteps - 1) {
            currentStep++;
            updateUI();
        }
    });
    
    // Prev
    $('.fp-btn-prev').on('click', function() {
        if (currentStep > 0) {
            currentStep--;
            updateUI();
        }
    });
    
    updateUI();
});
</script>

