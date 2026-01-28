<?php
namespace FPForms\Admin;

/**
 * Dashboard Widget WordPress
 */
class DashboardWidget {
    
    /**
     * Costruttore
     */
    public function __construct() {
        add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
    }
    
    /**
     * Registra dashboard widget
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'fp_forms_dashboard_widget',
            '📊 FP Forms - Statistiche Rapide',
            [ $this, 'render_widget' ]
        );
    }
    
    /**
     * Renderizza widget
     */
    public function render_widget() {
        $forms = [];

        if (method_exists(\FPForms\Plugin::instance()->forms, 'get_forms')) {
            $forms = \FPForms\Plugin::instance()->forms->get_forms();
        }
        $total_forms = count( $forms );
        $total_submissions = 0;
        $unread_submissions = 0;
        
        $tracker = \FPForms\Plugin::instance()->analytics;
        $total_views = 0;
        
        foreach ( $forms as $form ) {
            $db = \FPForms\Plugin::instance()->database;
            $total_submissions += $db->count_submissions( $form['id'] );
            $unread_submissions += $db->count_submissions( $form['id'], 'unread' );
            $total_views += $tracker->get_total_views( $form['id'] );
        }
        
        $overall_conversion = $total_views > 0 ? round( ( $total_submissions / $total_views ) * 100, 2 ) : 0;
        
        ?>
        <div class="fp-dashboard-widget">
            <div class="fp-widget-stats">
                <div class="fp-widget-stat">
                    <div class="fp-widget-stat-value"><?php echo $total_forms; ?></div>
                    <div class="fp-widget-stat-label"><?php _e( 'Form Attivi', 'fp-forms' ); ?></div>
                </div>
                
                <div class="fp-widget-stat">
                    <div class="fp-widget-stat-value"><?php echo $total_submissions; ?></div>
                    <div class="fp-widget-stat-label"><?php _e( 'Submissions Totali', 'fp-forms' ); ?></div>
                </div>
                
                <div class="fp-widget-stat">
                    <div class="fp-widget-stat-value"><?php echo $total_views; ?></div>
                    <div class="fp-widget-stat-label"><?php _e( 'Visualizzazioni', 'fp-forms' ); ?></div>
                </div>
                
                <div class="fp-widget-stat highlight">
                    <div class="fp-widget-stat-value"><?php echo $unread_submissions; ?></div>
                    <div class="fp-widget-stat-label"><?php _e( 'Non Lette', 'fp-forms' ); ?></div>
                </div>
            </div>
            
            <?php if ( $total_views > 0 ) : ?>
            <div class="fp-widget-conversion">
                <div class="fp-conversion-bar">
                    <div class="fp-conversion-label">
                        <span><?php _e( 'Tasso Conversione Globale', 'fp-forms' ); ?></span>
                        <strong><?php echo $overall_conversion; ?>%</strong>
                    </div>
                    <div class="fp-conversion-progress">
                        <div class="fp-conversion-fill" style="width: <?php echo min( 100, $overall_conversion ); ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            </div>
            
            <?php if ( ! empty( $forms ) ) : ?>
            <div class="fp-widget-forms">
                <h4><?php _e( 'Form più attivi', 'fp-forms' ); ?></h4>
                <ul>
                    <?php
                    // Ordina per submissions
                    usort( $forms, function( $a, $b ) {
                        $count_a = \FPForms\Plugin::instance()->database->count_submissions( $a['id'] );
                        $count_b = \FPForms\Plugin::instance()->database->count_submissions( $b['id'] );
                        return $count_b - $count_a;
                    } );
                    
                    $top_forms = array_slice( $forms, 0, 3 );
                    
                    foreach ( $top_forms as $form ) :
                        $count = \FPForms\Plugin::instance()->database->count_submissions( $form['id'] );
                        $form_views = $tracker->get_total_views( $form['id'] );
                        $form_conversion = $tracker->get_conversion_rate( $form['id'] );
                    ?>
                    <li>
                        <a href="<?php echo admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] ); ?>">
                            <strong><?php echo esc_html( $form['title'] ); ?></strong>
                            <div class="fp-widget-form-stats">
                                <span class="fp-widget-count"><?php echo $count; ?> submissions</span>
                                <?php if ( $form_views > 0 ) : ?>
                                <span class="fp-widget-views"><?php echo $form_views; ?> views</span>
                                <span class="fp-widget-conversion-mini <?php echo $form_conversion > 5 ? 'good' : 'low'; ?>"><?php echo $form_conversion; ?>%</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="fp-widget-actions">
                <a href="<?php echo admin_url( 'admin.php?page=fp-forms-add' ); ?>" class="button button-primary">
                    <?php _e( '+ Nuovo Form', 'fp-forms' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=fp-forms' ); ?>" class="button">
                    <?php _e( 'Tutti i Form', 'fp-forms' ); ?>
                </a>
            </div>
        </div>
        
        <style>
        .fp-dashboard-widget {
            padding: 12px 0;
        }
        
        .fp-widget-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .fp-widget-conversion {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .fp-conversion-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #374151;
        }
        
        .fp-conversion-label strong {
            font-size: 16px;
            color: #1f2937;
        }
        
        .fp-conversion-progress {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .fp-conversion-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            transition: width 0.3s ease;
        }
        
        .fp-widget-form-stats {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 4px;
        }
        
        .fp-widget-views {
            font-size: 11px;
            color: #6b7280;
        }
        
        .fp-widget-conversion-mini {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .fp-widget-conversion-mini.good {
            background: #d1fae5;
            color: #065f46;
        }
        
        .fp-widget-conversion-mini.low {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .fp-widget-stat {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .fp-widget-stat.highlight {
            background: linear-gradient(135deg, #fef2f2 0%, #ffe4e6 100%);
            border-color: #fca5a5;
        }
        
        .fp-widget-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }
        
        .fp-widget-stat.highlight .fp-widget-stat-value {
            color: #991b1b;
        }
        
        .fp-widget-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            margin-top: 5px;
            font-weight: 600;
        }
        
        .fp-widget-forms {
            margin-bottom: 20px;
        }
        
        .fp-widget-forms h4 {
            margin: 0 0 10px;
            font-size: 13px;
            color: #374151;
        }
        
        .fp-widget-forms ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .fp-widget-forms li {
            margin: 0;
            padding: 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .fp-widget-forms li:last-child {
            border-bottom: none;
        }
        
        .fp-widget-forms a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            text-decoration: none;
            color: #374151;
            transition: color 0.2s;
        }
        
        .fp-widget-forms a:hover {
            color: #2563eb;
        }
        
        .fp-widget-count {
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .fp-widget-actions {
            display: flex;
            gap: 10px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }
        
        .fp-widget-actions .button {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
        </style>
        <?php
    }
}

