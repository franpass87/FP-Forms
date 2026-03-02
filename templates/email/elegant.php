<?php
/**
 * Template email: Elegant (Hospitality/Luxury)
 *
 * Variabili disponibili (via extract):
 *   $logo_url, $accent_color, $footer_text, $response_time,
 *   $site_name, $site_url, $user_name, $form_title, $fields_html, $year
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$greeting = $user_name
    ? sprintf( __( 'Gentile %s,', 'fp-forms' ), esc_html( $user_name ) )
    : __( 'Gentile Ospite,', 'fp-forms' );
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo esc_html( $site_name ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f5f0eb;font-family:Georgia,'Times New Roman',serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f0eb;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">

<!-- Header -->
<tr>
<td style="background:<?php echo esc_attr( $accent_color ); ?>;padding:32px 40px;text-align:center;">
<?php if ( $logo_url ) : ?>
<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" style="max-height:55px;margin-bottom:12px;">
<br>
<?php endif; ?>
<span style="color:#ffffff;font-size:22px;letter-spacing:2px;text-transform:uppercase;font-weight:normal;"><?php echo esc_html( $site_name ); ?></span>
</td>
</tr>

<!-- Decorative line -->
<tr>
<td style="background:linear-gradient(90deg,transparent,<?php echo esc_attr( $accent_color ); ?>40,transparent);height:2px;font-size:0;">&nbsp;</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:40px 40px 20px;">
<p style="font-size:18px;color:#4a3728;margin:0 0 20px;line-height:1.6;"><?php echo $greeting; ?></p>
<p style="font-size:16px;color:#5c4a3a;margin:0 0 16px;line-height:1.7;">
<?php printf(
    __( 'La ringraziamo per averci contattato. Abbiamo ricevuto la Sua richiesta tramite il modulo "%s" e la prenderemo in carico con la massima attenzione.', 'fp-forms' ),
    esc_html( $form_title )
); ?>
</p>
<?php if ( $response_time ) : ?>
<p style="font-size:15px;color:#7c6a5a;margin:0 0 16px;line-height:1.6;font-style:italic;">
<?php printf( __( 'Riceverà una nostra risposta %s.', 'fp-forms' ), esc_html( $response_time ) ); ?>
</p>
<?php endif; ?>
</td>
</tr>

<!-- Data summary -->
<?php if ( $fields_html ) : ?>
<tr>
<td style="padding:0 40px 10px;">
<h3 style="font-size:14px;text-transform:uppercase;letter-spacing:1.5px;color:<?php echo esc_attr( $accent_color ); ?>;margin:0 0 14px;font-weight:normal;"><?php _e( 'Riepilogo della Sua richiesta', 'fp-forms' ); ?></h3>
<?php echo $fields_html; ?>
</td>
</tr>
<?php endif; ?>

<!-- Closing -->
<tr>
<td style="padding:20px 40px 40px;">
<p style="font-size:16px;color:#5c4a3a;margin:0 0 8px;line-height:1.7;"><?php _e( 'Restiamo a Sua completa disposizione.', 'fp-forms' ); ?></p>
<p style="font-size:16px;color:#5c4a3a;margin:0;line-height:1.7;"><?php _e( 'Cordiali saluti,', 'fp-forms' ); ?></p>
<p style="font-size:16px;color:#4a3728;margin:8px 0 0;font-weight:bold;"><?php printf( __( 'Il team di %s', 'fp-forms' ), esc_html( $site_name ) ); ?></p>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background:#f5f0eb;padding:24px 40px;text-align:center;border-top:1px solid #e8ddd2;">
<?php if ( $footer_text ) : ?>
<p style="font-size:13px;color:#8c7a6a;margin:0 0 8px;line-height:1.5;white-space:pre-line;"><?php echo esc_html( $footer_text ); ?></p>
<?php endif; ?>
<p style="font-size:12px;color:#a89888;margin:0;">
<a href="<?php echo esc_url( $site_url ); ?>" style="color:<?php echo esc_attr( $accent_color ); ?>;text-decoration:none;"><?php echo esc_html( $site_url ); ?></a>
</p>
<p style="font-size:11px;color:#bfb0a0;margin:8px 0 0;">&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?></p>
</td>
</tr>

</table>
</td></tr>
</table>
</body>
</html>
