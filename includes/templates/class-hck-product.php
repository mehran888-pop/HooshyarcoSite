<?php
/**
 * Professional single product page module.
 *
 * Layouts are applied through body classes and hook re-ordering so the
 * module works with every WooCommerce-compatible theme.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Product
 */
class HCK_Product {

	/**
	 * Singleton.
	 *
	 * @var HCK_Product|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Product
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
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'open_wrapper' ), 1 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'close_wrapper' ), 1 );

		// Trust badges below the add-to-cart button.
		add_action( 'woocommerce_single_product_summary', array( $this, 'trust_badges' ), 31 );

		// Layout-specific re-ordering.
		add_action( 'template_redirect', array( $this, 'maybe_reorder' ), 5 );

		// Tabs style classes.
		add_filter( 'woocommerce_product_tabs', array( $this, 'filter_tabs' ), 99 );

		// Custom product card extras (sold count etc).
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'loop_rating' ), 4 );
	}

	/**
	 * Re-order gallery/summary for specific layouts.
	 */
	public function maybe_reorder() {
		if ( ! is_product() ) {
			return;
		}

		$layout = HCK_Helpers::get( 'product_layout', 'gallery-right' );

		if ( 'gallery-left' === $layout ) {
			// CSS handles the visual order; no hook changes needed.
			return;
		}

		if ( 'centered' === $layout ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_images', 5 );
		}
	}

	/**
	 * Open custom wrapper around product summary columns.
	 */
	public function open_wrapper() {
		echo '<div class="hck-product hck-product--' . esc_attr( HCK_Helpers::get( 'product_layout', 'gallery-right' ) ) . '">';

		if ( 'yes' === HCK_Helpers::get( 'product_sticky_info', 'yes' ) ) {
			// Wrapper class only; stickiness is pure CSS.
			echo '<div class="hck-product__inner">';
		}
	}

	/**
	 * Close custom wrapper.
	 */
	public function close_wrapper() {
		if ( 'yes' === HCK_Helpers::get( 'product_sticky_info', 'yes' ) ) {
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Trust badges (delivery / support / authentic).
	 */
	public function trust_badges() {
		if ( 'yes' !== HCK_Helpers::get( 'product_trust_badges', 'yes' ) ) {
			return;
		}

		$badges = array(
			array( 'icon' => 'truck', 'text' => __( 'Fast & insured delivery', 'hooshyar-commerce-kit' ) ),
			array( 'icon' => 'check', 'text' => __( 'Originality guarantee', 'hooshyar-commerce-kit' ) ),
			array( 'icon' => 'support', 'text' => __( '24/7 support', 'hooshyar-commerce-kit' ) ),
		);

		echo '<div class="hck-trust">';
		foreach ( $badges as $badge ) {
			echo '<div class="hck-trust__item">';
			echo HCK_Helpers::icon( $badge['icon'], array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<span>' . esc_html( $badge['text'] ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Add classes to the tabs.
	 *
	 * @param array $tabs Tabs.
	 * @return array
	 */
	public function filter_tabs( $tabs ) {
		// Class injection happens through CSS on .woocommerce-tabs.
		return $tabs;
	}

	/**
	 * Rating under loop titles.
	 */
	public function loop_rating() {
		global $product;

		if ( $product instanceof WC_Product && $product->get_average_rating() > 0 ) {
			echo '<div class="hck-loop-rating">';
			// phpcs:ignore WordPress.Security.EscapeOutput -- core rating HTML.
			echo wc_get_rating_html( $product->get_average_rating() );
			echo '</div>';
		}
	}

	/**
	 * Product card HTML (shared with Elementor widgets & shortcodes).
	 *
	 * @param WC_Product $product Product.
	 * @param array      $args    Display args.
	 * @return string
	 */
	public static function render_card( $product, $args = array() ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$args = wp_parse_args(
			$args,
			array(
				'show_image'   => true,
				'show_rating'  => true,
				'show_price'   => true,
				'show_excerpt' => false,
				'show_badges'  => true,
				'show_button'  => true,
				'hover'        => HCK_Helpers::get( 'shop_hover', 'zoom' ),
				'card_style'   => HCK_Helpers::get( 'shop_card_style', 'boxed' ),
				'effect'       => 'none',
			)
		);

		$permalink = get_permalink( $product->get_id() );
		$html      = '<div class="hck-card hck-card--' . esc_attr( $args['card_style'] ) . ' hck-card--hover-' . esc_attr( $args['hover'] ) . ' hck-card--effect-' . esc_attr( $args['effect'] ) . '">';
		$html     .= '<div class="hck-card__inner">';

		// Image.
		if ( $args['show_image'] ) {
			$html .= '<a class="hck-card__media" href="' . esc_url( $permalink ) . '">';
			$html .= '<span class="hck-card__image">' . wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ) . '</span>';
			if ( $args['show_badges'] ) {
				$html .= HCK_Helpers::product_badges( $product );
			}
			$html .= '</a>';
		}

		$html .= '<div class="hck-card__body">';

		if ( $args['show_rating'] ) {
			$html .= '<div class="hck-card__rating">' . wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ) . '</div>';
		}

		$html .= '<h3 class="hck-card__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $product->get_name() ) . '</a></h3>';

		if ( $args['show_excerpt'] ) {
			$html .= '<p class="hck-card__excerpt">' . esc_html( wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 12 ) ) . '</p>';
		}

		if ( $args['show_price'] ) {
			$html .= HCK_Helpers::product_price_html( $product );
		}

		if ( $args['show_button'] ) {
			if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
				$html .= '<a class="hck-btn hck-btn--primary hck-btn--sm hck-card__btn" href="' . esc_url( $permalink ) . '">' . esc_html__( 'Select options', 'hooshyar-commerce-kit' ) . '</a>';
			} elseif ( $product->is_in_stock() && $product->is_purchasable() ) {
				$html .= sprintf(
					'<a href="%s" class="hck-btn hck-btn--primary hck-btn--sm hck-card__btn add_to_cart_button ajax_add_to_cart" data-product_id="%d" data-quantity="1" rel="nofollow">%s</a>',
					esc_url( HCK_Helpers::add_to_cart_url( $product ) ),
					absint( $product->get_id() ),
					esc_html( $product->add_to_cart_text() )
				);
			} else {
				$html .= '<span class="hck-btn hck-btn--disabled hck-btn--sm hck-card__btn">' . esc_html__( 'Out of stock', 'hooshyar-commerce-kit' ) . '</span>';
			}
		}

		$html .= '</div>'; // body.
		$html .= '</div></div>';

		return $html;
	}
}
