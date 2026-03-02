<?php
/**
 * Template email: Minimal (Universale)
 *
 * Variabili disponibili (via extract):
 *   $logo_url, $accent_color, $footer_text, $response_time,
 *   $site_name, $site_url, $user_name, $form_title, $fields_html, $year
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$greeting = $user_name
    ? sprintf( __( 'Ciao %s,', 'fp-forms' ), esc_html( $user_name ) )
    : __( 'Ciao,', 'fp-forms' );
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo esc_html( $site_name ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:6px;overflow:hidden;border:1px solid #e5e7eb;">

<!-- Header -->
<tr>
<td style="padding:24px 36px;border-bottom:1px solid #e5e7eb;">
<?php if ( $logo_url ) : ?>
<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" style="max-height:40px;">
<?php else : ?>
<span style="font-size:18px;font-weight:600;color:#111827;"><?php echo esc_html( $site_name ); ?></span>
<?php endif; ?>
</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:32px 36px 20px;">
<p style="font-size:16px;color:#111827;margin:0 0 16px;line-height:1.5;"><?php echo $greeting; ?></p>
<p style="font-size:15px;color:#374151;margin:0 0 14px;line-height:1.7;">
<?php printf(
    __( 'grazie per averci contattato tramite il modulo "%s". Abbiamo ricevuto correttamente la tua richiesta.', 'fp-forms' ),
    esc_html( $form_title )
); ?>
</p>
<?php if ( $response_time ) : ?>
<p style="font-size:14px;color:#6b7280;margin:0 0 14px;line-height:1.6;">
<?php printf( __( 'Ti risponderemo %s.', 'fp-forms' ), esc_html( $response_time ) ); ?>
</p>
<?php endif; ?>
</td>
</tr>

<!-- Data summary -->
<?php if ( $fields_html ) : ?>
<tr>
<td style="padding:0 36px 10px;">
<h3 style="font-size:14px;color:#374151;margin:0 0 12px;font-weight:600;"><?php _e( 'Riepilogo', 'fp-forms' ); ?></h3>
<?php echo $fields_html; ?>
</td>
</tr>
<?php endif; ?>

<!-- Closing -->
<tr>
<td style="padding:20px 36px 32px;">
<p style="font-size:15px;color:#374151;margin:0 0 4px;"><?php _e( 'Cordiali saluti,', 'fp-forms' ); ?></p>
<p style="font-size:15px;color:#111827;margin:0;font-weight:600;"><?php printf( __( 'Il team di %s', 'fp-forms' ), esc_html( $site_name ) ); ?></p>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background:#f9fafb;padding:20px 36px;text-align:center;border-top:1px solid #e5e7eb;">
<?php if ( $footer_text ) : ?>
<p style="font-size:13px;color:#6b7280;margin:0 0 6px;line-height:1.5;white-space:pre-line;"><?php echo esc_html( $footer_text ); ?></p>
<?php endif; ?>
<p style="font-size:12px;color:#9ca3af;margin:0;">
<a href="<?php echo esc_url( $site_url ); ?>" style="color:<?php echo esc_attr( $accent_color ); ?>;text-decoration:none;"><?php echo esc_html( $site_url ); ?></a>
&nbsp;&middot;&nbsp; &copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>
</p>
</td>
</tr>

</table>
</td></tr>
</table>
</body>
</html>
