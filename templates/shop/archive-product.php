<?php
/**
 * Creative shop archive template.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$hck_layout    = HCK_Helpers::get( 'shop_layout', 'catalog' );
$hck_sidebar   = HCK_Helpers::get( 'shop_sidebar', 'none' );
$hck_card      = HCK_Helpers::get( 'shop_card_style', 'boxed' );
$hck_hover     = HCK_Helpers::get( 'shop_hover', 'zoom' );
$hck_columns   = (int) HCK_Helpers::get( 'shop_columns', 4 );
?>
<div class="hck-shop hck-shop--<?php echo esc_attr( $hck_layout ); ?> hck-shop--sidebar-<?php echo esc_attr( $hck_sidebar ); ?>">

	<div class="hck-container">

		<header class="hck-shop__header">
			<div class="hck-shop__heading">
				<h1 class="hck-shop__title"><?php woocommerce_page_title(); ?></h1>
				<?php
				$term_desc = term_description();
				if ( $term_desc ) {
					echo '<div class="hck-shop__desc">' . wp_kses_post( $term_desc ) . '</div>';
				}
				?>
			</div>

			<?php if ( 'yes' === HCK_Helpers::get( 'shop_show_result_count', 'yes' ) ) : ?>
				<div class="hck-shop__toolbar-wrap">
					<?php echo HCK_Shop::get_toolbar(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endif; ?>
		</header>

		<div class="hck-shop__grid">

			<?php if ( 'none' !== $hck_sidebar ) : ?>
				<aside class="hck-shop__sidebar">
					<?php if ( is_active_sidebar( 'hck-shop-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'hck-shop-sidebar' ); ?>
					<?php else : ?>
						<div class="hck-widget">
							<h4 class="hck-widget__title"><?php esc_html_e( 'Categories', 'hooshyar-commerce-kit' ); ?></h4>
							<ul>
								<?php
								wp_list_categories(
									array(
										'taxonomy' => 'product_cat',
										'title_li' => '',
										'depth'    => 2,
									)
								);
								?>
							</ul>
						</div>
					<?php endif; ?>
				</aside>
			<?php endif; ?>

			<main class="hck-shop__main">
				<?php
				if ( woocommerce_product_loop() ) {
					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();
							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					/**
					 * Hook: woocommerce_no_products_found.
					 */
					do_action( 'woocommerce_no_products_found' );
				}
				?>
			</main>
		</div>
	</div>
</div>

<?php
get_footer( 'shop' );
