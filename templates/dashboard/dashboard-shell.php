<?php
/**
 * Shared dashboard shell (all templates / layouts).
 *
 * Available vars: $template, $layout, $user, $current, $content
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $current ) ) {
	$current = '';
}

if ( ! isset( $content ) || '' === $content ) {
	$dash = HCK_Dashboard::instance();
	$content  = $dash->get_welcome_card();
	$content .= $dash->get_stats();
	$content .= $dash->get_recent_orders();
}
?>
<div class="hck-dash hck-dash--<?php echo esc_attr( $template ); ?> hck-dash--layout-<?php echo esc_attr( $layout ); ?>">

	<?php if ( 'creative' === $template ) : ?>
		<div class="hck-hero hck-hero--dash">
			<div class="hck-hero__content">
				<h2 class="hck-hero__title"><?php esc_html_e( 'My account', 'hooshyar-commerce-kit' ); ?></h2>
				<p class="hck-hero__text"><?php esc_html_e( 'Everything about your orders and profile in one place.', 'hooshyar-commerce-kit' ); ?></p>
			</div>
		</div>
	<?php elseif ( 'modern' === $template ) : ?>
		<header class="hck-dash__header">
			<h2 class="hck-dash__title"><?php esc_html_e( 'My account', 'hooshyar-commerce-kit' ); ?></h2>
			<p class="hck-dash__subtitle"><?php esc_html_e( 'Manage your orders, downloads and personal info', 'hooshyar-commerce-kit' ); ?></p>
		</header>
	<?php elseif ( 'default' === $template ) : ?>
		<h2 class="hck-dash__title hck-dash__title--simple"><?php esc_html_e( 'My account', 'hooshyar-commerce-kit' ); ?></h2>
	<?php endif; ?>

	<?php wc_print_notices(); ?>

	<div class="hck-dash__grid">
		<aside class="hck-dash__side">
			<?php
			$dash = HCK_Dashboard::instance();
			echo $dash->get_sidebar_nav( $current ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</aside>

		<main class="hck-dash__main">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput -- WooCommerce account content.
			echo $content;
			?>
		</main>
	</div>
</div>
