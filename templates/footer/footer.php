<?php
/**
 * Custom footer template (replaces the theme footer.php).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

$hck_template = HCK_Helpers::get( 'footer_template', 'modern' );
?>
</div><!-- #main -->

<?php
$hck_footer_id = (int) HCK_Helpers::get( 'footer_elementor_id', 0 );

if ( $hck_footer_id && HCK_Helpers::is_elementor_active() ) {
	echo HCK_Header_Footer::render_elementor_template( $hck_footer_id ); // phpcs:ignore WordPress.Security.EscapeOutput
} else {
	echo HCK_Header_Footer::render_footer( $hck_template ); // phpcs:ignore WordPress.Security.EscapeOutput
}

wp_footer();
?>
</body>
</html>
