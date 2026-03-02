<?php
namespace FPForms\Email;

/**
 * Gestisce il rendering dei template email HTML.
 */
class Templates {

    private static array $templates = [
        'elegant'      => 'Elegant (Hospitality)',
        'professional' => 'Professional (Medical/Corporate)',
        'modern'       => 'Modern (Retail/E-commerce)',
        'minimal'      => 'Minimal (Universale)',
    ];

    /**
     * @return array<string,string> slug => label
     */
    public static function get_available_templates(): array {
        return self::$templates;
    }

    /**
     * Renderizza un template email HTML.
     *
     * @param string $template_slug  elegant|professional|modern|minimal
     * @param array  $vars           Variabili da iniettare nel template.
     * @return string HTML dell'email.
     */
    public static function render( string $template_slug, array $vars ): string {
        if ( ! isset( self::$templates[ $template_slug ] ) ) {
            $template_slug = 'minimal';
        }

        $file = FP_FORMS_PLUGIN_DIR . 'templates/email/' . $template_slug . '.php';

        if ( ! file_exists( $file ) ) {
            $file = FP_FORMS_PLUGIN_DIR . 'templates/email/minimal.php';
        }

        $vars = wp_parse_args( $vars, self::get_global_vars() );

        ob_start();
        extract( $vars, EXTR_SKIP );
        include $file;
        return (string) ob_get_clean();
    }

    /**
     * Variabili globali (da wp_options) condivise da tutti i template.
     */
    private static function get_global_vars(): array {
        return [
            'logo_url'       => get_option( 'fp_forms_email_logo_url', '' ),
            'accent_color'   => get_option( 'fp_forms_email_accent_color', '#3b82f6' ),
            'footer_text'    => get_option( 'fp_forms_email_footer_text', '' ),
            'response_time'  => get_option( 'fp_forms_email_response_time', '' ),
            'site_name'      => get_bloginfo( 'name' ),
            'site_url'       => get_bloginfo( 'url' ),
            'user_name'      => '',
            'form_title'     => '',
            'fields_html'    => '',
            'year'           => date( 'Y' ),
        ];
    }

    /**
     * Costruisce la tabella HTML con i dati della submission.
     *
     * @param array $form  Configurazione del form (con 'fields').
     * @param array $data  Dati submission.
     * @param string $accent_color Colore accent per l'header della tabella.
     * @return string HTML della tabella.
     */
    public static function build_fields_table( array $form, array $data, string $accent_color = '#3b82f6' ): string {
        $rows = '';
        $i    = 0;

        foreach ( $form['fields'] as $field ) {
            $type = $field['type'] ?? 'text';
            if ( in_array( $type, [ 'hidden', 'honeypot', 'recaptcha', 'step_break', 'privacy-checkbox', 'marketing-checkbox' ], true ) ) {
                continue;
            }

            $name  = $field['name'];
            $label = $field['label'] ?? $name;

            if ( $type === 'fullname' ) {
                $n = isset( $data[ $name . '_nome' ] ) ? $data[ $name . '_nome' ] : '';
                $c = isset( $data[ $name . '_cognome' ] ) ? $data[ $name . '_cognome' ] : '';
                $value = trim( $n . ' ' . $c );
            } else {
                $value = isset( $data[ $name ] ) ? $data[ $name ] : '';
            }

            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
            if ( $value === '' ) {
                continue;
            }

            $bg = ( $i % 2 === 0 ) ? '#f9fafb' : '#ffffff';
            $rows .= sprintf(
                '<tr style="background:%s;"><td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;color:#6b7280;font-weight:600;width:40%%;vertical-align:top;">%s</td><td style="padding:10px 14px;border-bottom:1px solid #e5e7eb;color:#1f2937;">%s</td></tr>',
                $bg,
                esc_html( $label ),
                nl2br( esc_html( $value ) )
            );
            $i++;
        }

        if ( ! $rows ) {
            return '';
        }

        return sprintf(
            '<table width="100%%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin:20px 0;">%s</table>',
            $rows
        );
    }
}
