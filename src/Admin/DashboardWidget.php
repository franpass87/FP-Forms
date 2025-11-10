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
        
        foreach ( $forms as $form ) {
            $db = \FPForms\Plugin::instance()->database;
            $total_submissions += $db->count_submissions( $form['id'] );
            $unread_submissions += $db->count_submissions( $form['id'], 'unread' );
        }
        
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
                
                <div class="fp-widget-stat highlight">
                    <div class="fp-widget-stat-value"><?php echo $unread_submissions; ?></div>
                    <div class="fp-widget-stat-label"><?php _e( 'Non Lette', 'fp-forms' ); ?></div>
                </div>
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
                    ?>
                    <li>
                        <a href="<?php echo admin_url( 'admin.php?page=fp-forms-submissions&form_id=' . $form['id'] ); ?>">
                            <strong><?php echo esc_html( $form['title'] ); ?></strong>
                            <span class="fp-widget-count"><?php echo $count; ?> submissions</span>
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

