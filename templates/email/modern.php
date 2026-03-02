<?php
/**
 * Template email: Modern (Retail/E-commerce)
 *
 * Variabili disponibili (via extract):
 *   $logo_url, $accent_color, $footer_text, $response_time,
 *   $site_name, $site_url, $user_name, $form_title, $fields_html, $year
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$greeting = $user_name
    ? sprintf( __( 'Ciao %s!', 'fp-forms' ), esc_html( $user_name ) )
    : __( 'Ciao!', 'fp-forms' );
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo esc_html( $site_name ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">

<!-- Header -->
<tr>
<td style="background:<?php echo esc_attr( $accent_color ); ?>;padding:30px 40px;text-align:center;">
<?php if ( $logo_url ) : ?>
<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" style="max-height:50px;">
<?php else : ?>
<span style="font-size:24px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;"><?php echo esc_html( $site_name ); ?></span>
<?php endif; ?>
</td>
</tr>

<!-- Hero -->
<tr>
<td style="padding:36px 40px 8px;text-align:center;">
<div style="width:56px;height:56px;margin:0 auto 16px;background:<?php echo esc_attr( $accent_color ); ?>18;border-radius:50%;line-height:56px;font-size:28px;">&#10003;</div>
<h1 style="font-size:22px;color:#18181b;margin:0 0 8px;font-weight:700;"><?php _e( 'Richiesta ricevuta!', 'fp-forms' ); ?></h1>
<p style="font-size:15px;color:#71717a;margin:0;"><?php echo $greeting; ?></p>
</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:16px 40px 20px;">
<p style="font-size:15px;color:#3f3f46;margin:0 0 14px;line-height:1.7;text-align:center;">
<?php printf(
    __( 'Abbiamo ricevuto la tua richiesta dal modulo "%s". Grazie per averci scelto!', 'fp-forms' ),
    esc_html( $form_title )
); ?>
</p>
<?php if ( $response_time ) : ?>
<div style="background:<?php echo esc_attr( $accent_color ); ?>0d;border:1px solid <?php echo esc_attr( $accent_color ); ?>30;padding:14px 20px;margin:16px 0;border-radius:10px;text-align:center;">
<p style="font-size:14px;color:#3f3f46;margin:0;">
<?php printf( __( 'Ti risponderemo %s', 'fp-forms' ), '<strong>' . esc_html( $response_time ) . '</strong>' ); ?>
</p>
</div>
<?php endif; ?>
</td>
</tr>

<!-- Data summary -->
<?php if ( $fields_html ) : ?>
<tr>
<td style="padding:0 40px 10px;">
<h3 style="font-size:13px;text-transform:uppercase;letter-spacing:1px;color:#a1a1aa;margin:0 0 12px;font-weight:700;text-align:center;"><?php _e( 'I tuoi dati', 'fp-forms' ); ?></h3>
<?php echo $fields_html; ?>
</td>
</tr>
<?php endif; ?>

<!-- Closing -->
<tr>
<td style="padding:20px 40px 36px;text-align:center;">
<p style="font-size:15px;color:#3f3f46;margin:0 0 4px;"><?php _e( 'A presto!', 'fp-forms' ); ?></p>
<p style="font-size:15px;color:#18181b;margin:0;font-weight:700;"><?php printf( __( 'Il team %s', 'fp-forms' ), esc_html( $site_name ) ); ?></p>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background:#fafafa;padding:22px 40px;text-align:center;border-top:1px solid #e4e4e7;">
<?php if ( $footer_text ) : ?>
<p style="font-size:13px;color:#71717a;margin:0 0 8px;line-height:1.5;white-space:pre-line;"><?php echo esc_html( $footer_text ); ?></p>
<?php endif; ?>
<p style="font-size:12px;color:#a1a1aa;margin:0;">
<a href="<?php echo esc_url( $site_url ); ?>" style="color:<?php echo esc_attr( $accent_color ); ?>;text-decoration:none;font-weight:600;"><?php echo esc_html( $site_url ); ?></a>
</p>
<p style="font-size:11px;color:#d4d4d8;margin:6px 0 0;">&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?></p>
</td>
</tr>

</table>
</td></tr>
</table>
</body>
</html>
