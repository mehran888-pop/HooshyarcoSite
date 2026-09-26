<?php
/**
 * Mobile navigation module.
 *
 * A fixed bottom navigation bar on mobile that can replace the footer
 * navigation, with configurable items, styles and colours.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Mobile_Nav
 */
class HCK_Mobile_Nav {

	/**
	 * Singleton.
	 *
	 * @var HCK_Mobile_Nav|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Mobile_Nav
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
		add_action( 'wp_footer', array( $this, 'render' ), 20 );
		add_shortcode( 'hck_mobile_nav', array( $this, 'shortcode' ) );

		if ( 'yes' === HCK_Helpers::get( 'mobile_nav_replace_footer', 'yes' ) ) {
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}
	}

	/**
	 * Body class that hides footer menus on mobile.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( 'yes' === HCK_Helpers::get( 'mobile_nav_enable', 'yes' ) ) {
			$classes[] = 'hck-mobile-nav-active';
			if ( 'yes' === HCK_Helpers::get( 'mobile_nav_replace_footer', 'yes' ) ) {
				$classes[] = 'hck-mobile-nav-replaces-footer';
			}
		}
		return $classes;
	}

	/**
	 * Shortcode for manual placement.
	 *
	 * @return string
	 */
	public function shortcode() {
		return $this->get_markup();
	}

	/**
	 * Available nav items.
	 *
	 * @return array
	 */
	public static function get_item_defs() {
		$items = array(
			'home'       => array(
				'label' => __( 'Home', 'hooshyar-commerce-kit' ),
				'icon'  => 'home',
				'url'   => home_url( '/' ),
			),
			'shop'       => array(
				'label' => __( 'Shop', 'hooshyar-commerce-kit' ),
				'icon'  => 'shop',
				'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
			),
			'cart'       => array(
				'label' => __( 'Cart', 'hooshyar-commerce-kit' ),
				'icon'  => 'cart',
				'url'   => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#',
				'badge' => true,
			),
			'account'    => array(
				'label' => __( 'Account', 'hooshyar-commerce-kit' ),
				'icon'  => 'user',
				'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '#',
			),
			'search'     => array(
				'label'  => __( 'Search', 'hooshyar-commerce-kit' ),
				'icon'   => 'search',
				'action' => 'search',
			),
			'categories' => array(
				'label'  => __( 'Categories', 'hooshyar-commerce-kit' ),
				'icon'   => 'grid',
				'action' => 'categories',
			),
			'wishlist'   => array(
				'label' => __( 'Wishlist', 'hooshyar-commerce-kit' ),
				'icon'  => 'heart',
				'url'   => '#',
			),
		);

		return apply_filters( 'hck_mobile_nav_items_defs', $items );
	}

	/**
	 * Build the mobile nav markup.
	 *
	 * @return string
	 */
	public function get_markup() {
		if ( 'yes' !== HCK_Helpers::get( 'mobile_nav_enable', 'yes' ) ) {
			return '';
		}

		$style = sanitize_html_class( HCK_Helpers::get( 'mobile_nav_style', 'floating' ) );
		$keys  = array_filter( array_map( 'sanitize_key', explode( ',', (string) HCK_Helpers::get( 'mobile_nav_items', 'home,shop,cart,account,search' ) ) ) );
		$defs  = self::get_item_defs();
		$show_count = 'yes' === HCK_Helpers::get( 'mobile_nav_cart_count', 'yes' );
		$count      = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

		$html = '<nav class="hck-mnav hck-mnav--' . esc_attr( $style ) . '" id="hck-mobile-nav" aria-label="' . esc_attr__( 'Mobile navigation', 'hooshyar-commerce-kit' ) . '">';

		foreach ( $keys as $key ) {
			if ( ! isset( $defs[ $key ] ) ) {
				continue;
			}
			$item = $defs[ $key ];

			if ( isset( $item['action'] ) && 'search' === $item['action'] ) {
				$html .= '<button type="button" class="hck-mnav__item hck-mnav__item--action" data-hck-search-toggle>';
				$html .= HCK_Helpers::icon( $item['icon'], array( 'size' => 22, 'class' => 'hck-mnav__icon' ) );
				$html .= '<span class="hck-mnav__label">' . esc_html( $item['label'] ) . '</span>';
				$html .= '</button>';
				continue;
			}

			if ( isset( $item['action'] ) && 'categories' === $item['action'] ) {
				$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
				$item['url'] = $shop_url;
			}

			$is_cart = 'cart' === $key;
			$html   .= '<a class="hck-mnav__item' . ( $is_cart ? ' hck-mnav__item--cart' : '' ) . '" href="' . esc_url( isset( $item['url'] ) ? $item['url'] : '#' ) . '"' . ( $is_cart ? ' data-hck-open-cart' : '' ) . '>';
			$html   .= HCK_Helpers::icon( $item['icon'], array( 'size' => 22, 'class' => 'hck-mnav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput

			if ( ! empty( $item['badge'] ) && $show_count ) {
				$html .= '<span class="hck-cart-count hck-mnav__badge">' . esc_html( $count ) . '</span>';
			}

			$html .= '<span class="hck-mnav__label">' . esc_html( $item['label'] ) . '</span>';
			$html .= '</a>';
		}

		$html .= '</nav>';

		// Search overlay used by the search item.
		$html .= '<div class="hck-mnav-search" id="hck-mnav-search" hidden>';
		$html .= '<form role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
		$html .= '<input type="search" name="s" placeholder="' . esc_attr__( 'Search products…', 'hooshyar-commerce-kit' ) . '" />';
		$html .= '<input type="hidden" name="post_type" value="product" />';
		$html .= '<button type="submit">' . esc_html__( 'Search', 'hooshyar-commerce-kit' ) . '</button>';
		$html .= '</form>';
		$html .= '<button type="button" class="hck-mnav-search__close" data-hck-search-toggle aria-label="' . esc_attr__( 'Close', 'hooshyar-commerce-kit' ) . '">' . HCK_Helpers::icon( 'close' ) . '</button>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Print the mobile nav on the frontend.
	 */
	public function render() {
		echo $this->get_markup(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
