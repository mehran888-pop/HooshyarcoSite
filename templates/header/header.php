<?php
/**
 * Custom header template (replaces the theme header.php).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$hck_template = HCK_Helpers::get( 'header_template', 'modern' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'hooshyar-commerce-kit' ); ?></a>

<?php
// Elementor template mode: render the selected template inside our chrome.
$hck_header_id = (int) HCK_Helpers::get( 'header_elementor_id', 0 );

if ( $hck_header_id && HCK_Helpers::is_elementor_active() ) {
	echo HCK_Header_Footer::render_elementor_template( $hck_header_id ); // phpcs:ignore WordPress.Security.EscapeOutput
} else {
	echo HCK_Header_Footer::render_header( $hck_template ); // phpcs:ignore WordPress.Security.EscapeOutput
}
?>

<div id="main" class="hck-site-main">
