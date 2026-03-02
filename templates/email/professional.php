<?php
/**
 * Template email: Professional (Medical/Corporate)
 *
 * Variabili disponibili (via extract):
 *   $logo_url, $accent_color, $footer_text, $response_time,
 *   $site_name, $site_url, $user_name, $form_title, $fields_html, $year
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$greeting = $user_name
    ? sprintf( __( 'Gentile %s,', 'fp-forms' ), esc_html( $user_name ) )
    : __( 'Gentile Cliente,', 'fp-forms' );
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo esc_html( $site_name ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">

<!-- Header -->
<tr>
<td style="padding:28px 40px;border-bottom:3px solid <?php echo esc_attr( $accent_color ); ?>;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<?php if ( $logo_url ) : ?>
<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" style="max-height:45px;">
<?php else : ?>
<span style="font-size:20px;font-weight:700;color:#1e293b;"><?php echo esc_html( $site_name ); ?></span>
<?php endif; ?>
</td>
<td style="text-align:right;vertical-align:middle;">
<span style="font-size:12px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;"><?php _e( 'Conferma ricezione', 'fp-forms' ); ?></span>
</td>
</tr>
</table>
</td>
</tr>

<!-- Body -->
<tr>
<td style="padding:36px 40px 20px;">
<p style="font-size:16px;color:#334155;margin:0 0 18px;line-height:1.5;font-weight:600;"><?php echo $greeting; ?></p>
<p style="font-size:15px;color:#475569;margin:0 0 14px;line-height:1.7;">
<?php printf(
    __( 'Confermiamo di aver ricevuto la Sua richiesta inviata tramite il modulo "%s".', 'fp-forms' ),
    esc_html( $form_title )
); ?>
</p>
<p style="font-size:15px;color:#475569;margin:0 0 14px;line-height:1.7;">
<?php _e( 'Il nostro team esaminerà i dettagli e Le risponderà nel più breve tempo possibile.', 'fp-forms' ); ?>
</p>
<?php if ( $response_time ) : ?>
<div style="background:#f0f9ff;border-left:4px solid <?php echo esc_attr( $accent_color ); ?>;padding:14px 18px;margin:20px 0;border-radius:0 6px 6px 0;">
<p style="font-size:14px;color:#0c4a6e;margin:0;line-height:1.5;">
<strong><?php _e( 'Tempi di risposta stimati:', 'fp-forms' ); ?></strong> <?php echo esc_html( $response_time ); ?>
</p>
</div>
<?php endif; ?>
</td>
</tr>

<!-- Data summary -->
<?php if ( $fields_html ) : ?>
<tr>
<td style="padding:0 40px 10px;">
<h3 style="font-size:13px;text-transform:uppercase;letter-spacing:1px;color:#64748b;margin:0 0 12px;font-weight:600;"><?php _e( 'Riepilogo dati inviati', 'fp-forms' ); ?></h3>
<?php echo $fields_html; ?>
</td>
</tr>
<?php endif; ?>

<!-- Closing -->
<tr>
<td style="padding:20px 40px 36px;">
<p style="font-size:15px;color:#475569;margin:0 0 4px;line-height:1.6;"><?php _e( 'Cordiali saluti,', 'fp-forms' ); ?></p>
<p style="font-size:15px;color:#1e293b;margin:0;font-weight:700;"><?php echo esc_html( $site_name ); ?></p>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background:#f8fafc;padding:22px 40px;text-align:center;border-top:1px solid #e2e8f0;">
<?php if ( $footer_text ) : ?>
<p style="font-size:13px;color:#64748b;margin:0 0 8px;line-height:1.5;white-space:pre-line;"><?php echo esc_html( $footer_text ); ?></p>
<?php endif; ?>
<p style="font-size:12px;color:#94a3b8;margin:0;">
<a href="<?php echo esc_url( $site_url ); ?>" style="color:<?php echo esc_attr( $accent_color ); ?>;text-decoration:none;"><?php echo esc_html( $site_url ); ?></a>
</p>
<p style="font-size:11px;color:#cbd5e1;margin:6px 0 0;">&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?></p>
</td>
</tr>

</table>
</td></tr>
</table>
</body>
</html>
