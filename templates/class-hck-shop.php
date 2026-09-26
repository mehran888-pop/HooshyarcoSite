<?php
/**
 * Creative shop (product archive) module.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Shop
 */
class HCK_Shop {

	/**
	 * Singleton.
	 *
	 * @var HCK_Shop|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Shop
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'widgets_init', array( $this, 'register_sidebar' ) );
		add_filter( 'template_include', array( $this, 'filter_template' ), 20 );

		// Loop classes for card styles / columns.
		add_filter( 'woocommerce_product_loop_start', array( $this, 'loop_start' ) );
		add_filter( 'loop_shop_columns', array( $this, 'loop_columns' ) );
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_args' ) );
		add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'thumbnail_columns' ) );
	}

	/**
	 * Shop sidebar.
	 */
	public function register_sidebar() {
		register_sidebar(
			array(
				'name'          => __( 'HCK Shop Sidebar', 'hooshyar-commerce-kit' ),
				'id'            => 'hck-shop-sidebar',
				'description'   => __( 'Widgets shown on shop / product archive pages.', 'hooshyar-commerce-kit' ),
				'before_widget' => '<div class="hck-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="hck-widget__title">',
				'after_title'   => '</h4>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'HCK Footer Widgets', 'hooshyar-commerce-kit' ),
				'id'            => 'hck-footer-1',
				'description'   => __( 'Fourth column of the built-in footer.', 'hooshyar-commerce-kit' ),
				'before_widget' => '<div class="hck-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="hck-footer__title">',
				'after_title'   => '</h4>',
			)
		);
	}

	/**
	 * Use our shop archive template.
	 *
	 * @param string $template Template.
	 * @return string
	 */
	public function filter_template( $template ) {
		if ( ! is_post_type_archive( 'product' ) && ! is_product_category() && ! is_product_tag() ) {
			return $template;
		}

		// Only replace when the shop module is active (always, but keep WC pages intact).
		$file = HCK_PLUGIN_DIR . 'templates/shop/archive-product.php';
		return is_readable( $file ) ? $file : $template;
	}

	/**
	 * Add classes to the product loop.
	 *
	 * @param string $html Loop start HTML.
	 * @return string
	 */
	public function loop_start( $html ) {
		$card_style = sanitize_html_class( HCK_Helpers::get( 'shop_card_style', 'boxed' ) );
		$hover      = sanitize_html_class( HCK_Helpers::get( 'shop_hover', 'zoom' ) );

		return str_replace(
			'class="products',
			'class="products hck-products hck-products--card-' . $card_style . ' hck-products--hover-' . $hover . ' ',
			$html
		);
	}

	/**
	 * Grid columns.
	 *
	 * @param int $columns Columns.
	 * @return int
	 */
	public function loop_columns( $columns ) {
		$setting = (int) HCK_Helpers::get( 'shop_columns', 4 );
		return $setting > 0 ? $setting : $columns;
	}

	/**
	 * Related products args.
	 *
	 * @param array $args Args.
	 * @return array
	 */
	public function related_args( $args ) {
		if ( 'yes' !== HCK_Helpers::get( 'product_related', 'yes' ) ) {
			$args['posts_per_page'] = 0;
			$args['columns']        = 0;
			return $args;
		}

		$args['posts_per_page'] = 4;
		$args['columns']        = 4;
		return $args;
	}

	/**
	 * Thumbnail columns on product page.
	 *
	 * @param int $cols Columns.
	 * @return int
	 */
	public function thumbnail_columns( $cols ) {
		return 4;
	}

	/**
	 * Shop toolbar (result count + ordering).
	 *
	 * @return string
	 */
	public static function get_toolbar() {
		if ( 'yes' !== HCK_Helpers::get( 'shop_show_result_count', 'yes' ) ) {
			return '';
		}

		ob_start();
		?>
		<div class="hck-shop-toolbar">
			<div class="hck-shop-toolbar__count">
				<?php woocommerce_result_count(); ?>
			</div>
			<div class="hck-shop-toolbar__sort">
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
