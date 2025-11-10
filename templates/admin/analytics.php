<?php
/**
 * Template: Analytics Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap fp-forms-admin">
    <div class="fp-forms-admin__header">
        <h1><?php printf( __( 'Analytics: %s', 'fp-forms' ), $form['title'] ); ?></h1>
        <div class="fp-header-actions">
            <a href="<?php echo admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] ); ?>" class="button">
                <?php _e( 'Vedi Submissions', 'fp-forms' ); ?>
            </a>
            <a href="<?php echo admin_url( 'admin.php?page=fp-forms' ); ?>" class="button">
                &larr; <?php _e( 'Torna ai Form', 'fp-forms' ); ?>
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="fp-stats-grid">
        <div class="fp-stat-card">
            <div class="fp-stat-icon">👁️</div>
            <div class="fp-stat-content">
                <div class="fp-stat-value"><?php echo number_format( $stats['total_views'] ); ?></div>
                <div class="fp-stat-label"><?php _e( 'Visualizzazioni Totali', 'fp-forms' ); ?></div>
            </div>
        </div>
        
        <div class="fp-stat-card">
            <div class="fp-stat-icon">📝</div>
            <div class="fp-stat-content">
                <div class="fp-stat-value"><?php echo number_format( $stats['total_submissions'] ); ?></div>
                <div class="fp-stat-label"><?php _e( 'Submissions Totali', 'fp-forms' ); ?></div>
            </div>
        </div>
        
        <div class="fp-stat-card <?php echo $stats['conversion_rate'] > 5 ? 'good' : 'needs-improvement'; ?>">
            <div class="fp-stat-icon">📊</div>
            <div class="fp-stat-content">
                <div class="fp-stat-value"><?php echo $stats['conversion_rate']; ?>%</div>
                <div class="fp-stat-label"><?php _e( 'Tasso di Conversione', 'fp-forms' ); ?></div>
                <?php if ( $stats['conversion_rate'] < 5 ) : ?>
                <small class="fp-stat-tip"><?php _e( 'Sotto la media (5-10%)', 'fp-forms' ); ?></small>
                <?php elseif ( $stats['conversion_rate'] > 10 ) : ?>
                <small class="fp-stat-tip"><?php _e( 'Ottimo! Sopra la media', 'fp-forms' ); ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="fp-stat-card">
            <div class="fp-stat-icon">📬</div>
            <div class="fp-stat-content">
                <div class="fp-stat-value"><?php echo \FPForms\Plugin::instance()->database->count_submissions( $form['id'], 'unread' ); ?></div>
                <div class="fp-stat-label"><?php _e( 'Non Lette', 'fp-forms' ); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Chart -->
    <div class="fp-chart-container">
        <h3><?php _e( 'Trend Ultimi 7 Giorni', 'fp-forms' ); ?></h3>
        <canvas id="fp-analytics-chart" width="400" height="150"></canvas>
    </div>
</div>

<style>
.fp-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: var(--fp-spacing-lg);
    margin: var(--fp-spacing-lg) 0;
}

.fp-stat-card {
    background: var(--fp-color-surface);
    border: 1px solid var(--fp-color-border);
    border-radius: var(--fp-radius-lg);
    padding: var(--fp-spacing-lg);
    box-shadow: var(--fp-shadow-md);
    display: flex;
    align-items: center;
    gap: var(--fp-spacing-md);
}

.fp-stat-card.good {
    border-color: var(--fp-color-success);
    background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%);
}

.fp-stat-card.needs-improvement {
    border-color: var(--fp-color-warning);
    background: linear-gradient(135deg, #fff 0%, #fffbeb 100%);
}

.fp-stat-icon {
    font-size: 48px;
    opacity: 0.8;
}

.fp-stat-content {
    flex: 1;
}

.fp-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--fp-color-text);
    line-height: 1;
    margin-bottom: 4px;
}

.fp-stat-label {
    font-size: 13px;
    color: var(--fp-color-muted);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.fp-stat-tip {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    font-style: italic;
}

.fp-chart-container {
    background: var(--fp-color-surface);
    border: 1px solid var(--fp-color-border);
    border-radius: var(--fp-radius-lg);
    padding: var(--fp-spacing-lg);
    box-shadow: var(--fp-shadow-md);
    margin-top: var(--fp-spacing-lg);
}

.fp-chart-container h3 {
    margin: 0 0 var(--fp-spacing-lg);
    font-size: 18px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function($) {
    var ctx = document.getElementById('fp-analytics-chart');
    if (!ctx) return;
    
    var weekViews = <?php echo wp_json_encode( array_values( $stats['week_views'] ) ); ?>;
    var weekSubmissions = <?php echo wp_json_encode( array_values( $stats['week_submissions'] ) ); ?>;
    var labels = <?php echo wp_json_encode( array_keys( $stats['week_views'] ) ); ?>;
    
    // Formatta date
    labels = labels.map(function(date) {
        var d = new Date(date);
        return d.toLocaleDateString('it-IT', { weekday: 'short', day: 'numeric', month: 'short' });
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visualizzazioni',
                data: weekViews,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4
            }, {
                label: 'Submissions',
                data: weekSubmissions,
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>

