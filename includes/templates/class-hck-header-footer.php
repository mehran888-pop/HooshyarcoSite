<?php
/**
 * Header & Footer module.
 *
 * Provides multiple professional header/footer presets (default,
 * modern, minimal, creative) with selectable layouts.
 *
 * WordPress loads header.php / footer.php through locate_template()
 * which has no filter, so the plugin uses the battle-tested technique
 * of hooking `get_header` / `get_footer`: it prints the custom header
 * (with full document chrome) and then includes the theme template
 * inside an output buffer so its markup is discarded (require_once
 * guarantees the theme file is not executed twice).
 *
 * Elementor Pro users can instead assign Elementor templates — the
 * plugin renders the selected template inside the document chrome.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Header_Footer
 */
class HCK_Header_Footer {

	/**
	 * Singleton.
	 *
	 * @var HCK_Header_Footer|null
	 */
	private static $instance = null;

	/**
	 * Whether the custom header was already printed this request.
	 *
	 * @var bool
	 */
	private $header_done = false;

	/**
	 * Whether the custom footer was already printed this request.
	 *
	 * @var bool
	 */
	private $footer_done = false;

	/**
	 * Instance.
	 *
	 * @return HCK_Header_Footer
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
		add_action( 'get_header', array( $this, 'replace_header' ), 5 );
		add_action( 'get_footer', array( $this, 'replace_footer' ), 5 );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
	}

	/**
	 * Should custom header/footer be used on the current request?
	 *
	 * @return bool
	 */
	public function should_use_custom() {
		if ( is_admin() || wp_doing_ajax() ) {
			return false;
		}

		$scope = HCK_Helpers::get( 'header_footer_scope', 'entire_site' );

		switch ( $scope ) {
			case 'front_only':
				return is_front_page();

			case 'shop_only':
				return ( function_exists( 'is_shop' ) && is_shop() )
					|| is_product_category()
					|| is_product_tag()
					|| is_post_type_archive( 'product' );

			case 'custom_ids':
				$ids = array_filter( array_map( 'absint', explode( ',', (string) HCK_Helpers::get( 'header_footer_ids', '' ) ) ) );
				return is_page() && in_array( get_queried_object_id(), $ids, true );

			case 'entire_site':
			default:
				return true;
		}
	}

	/**
	 * Print the custom header and swallow the theme's header.php output.
	 *
	 * @param string|null $name Header name argument passed to get_header().
	 */
	public function replace_header( $name = null ) {
		if ( $this->header_done || ! $this->should_use_custom() ) {
			return;
		}
		$this->header_done = true;

		$file = HCK_PLUGIN_DIR . 'templates/header/header.php';
		if ( ! is_readable( $file ) ) {
			return;
		}

		// Print our header (full document chrome + header markup).
		require $file;

		// Prevent the theme header's wp_head / wp_body_open from running twice.
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_body_open' );

		// Capture the theme's header.php output and discard it. Because
		// load_template() uses require_once, the later load inside
		// get_header() becomes a no-op.
		$templates = array();
		if ( $name ) {
			$templates[] = 'header-' . $name . '.php';
		}
		$templates[] = 'header.php';

		ob_start();
		locate_template( $templates, true, true );
		ob_end_clean();
	}

	/**
	 * Print the custom footer and swallow the theme's footer.php output.
	 */
	public function replace_footer() {
		if ( $this->footer_done || ! $this->should_use_custom() ) {
			return;
		}
		$this->footer_done = true;

		$file = HCK_PLUGIN_DIR . 'templates/footer/footer.php';
		if ( ! is_readable( $file ) ) {
			return;
		}

		require $file;

		remove_all_actions( 'wp_footer' );

		ob_start();
		locate_template( array( 'footer.php' ), true, true );
		ob_end_clean();
	}

	/**
	 * Body classes.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public function body_classes( $classes ) {
		if ( $this->should_use_custom() ) {
			$classes[] = 'hck-custom-header-footer';
			$classes[] = 'hck-header-' . sanitize_html_class( HCK_Helpers::get( 'header_template', 'modern' ) );
			$classes[] = 'hck-header-layout-' . sanitize_html_class( HCK_Helpers::get( 'header_layout', 'inline' ) );
		}

		if ( is_product() ) {
			$classes[] = 'hck-product-layout-' . sanitize_html_class( HCK_Helpers::get( 'product_layout', 'gallery-right' ) );
			if ( 'yes' === HCK_Helpers::get( 'product_sticky_info', 'yes' ) ) {
				$classes[] = 'hck-product-sticky';
			}
		}

		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
			$classes[] = 'hck-shop-layout-' . sanitize_html_class( HCK_Helpers::get( 'shop_layout', 'catalog' ) );
		}

		return $classes;
	}

	/**
	 * Get primary menu items for built-in headers.
	 *
	 * @return array
	 */
	public static function get_menu_items() {
		$locations = get_nav_menu_locations();
		$items     = array();

		$menu_id = 0;
		if ( isset( $locations['primary'] ) ) {
			$menu_id = (int) $locations['primary'];
		} else {
			// wp_get_nav_menus() is available on every WordPress 3.0+ install.
			if ( function_exists( 'wp_get_nav_menus' ) ) {
				$menus = wp_get_nav_menus( array( 'number' => 1 ) );
			} else {
				$menus = array();
			}
			if ( ! empty( $menus ) ) {
				$menu_id = (int) $menus[0]->term_id;
			}
		}

		if ( $menu_id && function_exists( 'wp_get_nav_menu_items' ) ) {
			$items = wp_get_nav_menu_items( $menu_id );
			if ( is_wp_error( $items ) ) {
				$items = array();
			}
		}

		if ( empty( $items ) ) {
			$items = array(
				(object) array(
					'title' => __( 'Home', 'hooshyar-commerce-kit' ),
					'url'   => home_url( '/' ),
				),
				(object) array(
					'title' => __( 'Shop', 'hooshyar-commerce-kit' ),
					'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
				),
			);
		}

		return $items;
	}

	/**
	 * Render the primary navigation markup.
	 *
	 * @return string
	 */
	public static function render_nav() {
		$items = self::get_menu_items();

		$html = '<nav class="hck-nav" aria-label="' . esc_attr__( 'Main navigation', 'hooshyar-commerce-kit' ) . '"><ul class="hck-nav__list">';

		foreach ( $items as $item ) {
			$html .= sprintf(
				'<li class="hck-nav__item"><a class="hck-nav__link" href="%s">%s</a></li>',
				esc_url( $item->url ),
				esc_html( $item->title )
			);
		}

		$html .= '</ul></nav>';

		return $html;
	}

	/**
	 * Render the header actions (search / user / cart).
	 *
	 * @return string
	 */
	public static function render_actions() {
		$html = '<div class="hck-header-actions">';

		if ( 'yes' === HCK_Helpers::get( 'header_search', 'yes' ) ) {
			$html .= '<form role="search" method="get" class="hck-header-search" action="' . esc_url( home_url( '/' ) ) . '">';
			$html .= '<input type="search" name="s" class="hck-header-search__input" placeholder="' . esc_attr__( 'Search products…', 'hooshyar-commerce-kit' ) . '" value="' . esc_attr( get_search_query() ) . '" />';
			$html .= '<input type="hidden" name="post_type" value="product" />';
			$html .= '<button type="submit" class="hck-header-search__btn" aria-label="' . esc_attr__( 'Search', 'hooshyar-commerce-kit' ) . '">' . HCK_Helpers::icon( 'search', array( 'size' => 19 ) ) . '</button>';
			$html .= '</form>';
		}

		if ( 'yes' === HCK_Helpers::get( 'header_user_button', 'yes' ) ) {
			$html .= HCK_Dashboard::instance()->get_user_button(
				array(
					'show_cart' => 'yes' === HCK_Helpers::get( 'header_cart_button', 'yes' ),
				)
			);
		} elseif ( 'yes' === HCK_Helpers::get( 'header_cart_button', 'yes' ) ) {
			$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
			$html .= '<a class="hck-user-area__cart" href="' . esc_url( wc_get_cart_url() ) . '" data-hck-open-cart aria-label="' . esc_attr__( 'Cart', 'hooshyar-commerce-kit' ) . '">';
			$html .= HCK_Helpers::icon( 'cart', array( 'size' => 21 ) );
			$html .= '<span class="hck-cart-count">' . esc_html( $count ) . '</span></a>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render a full header preset.
	 *
	 * @param string $template Template name.
	 * @return string
	 */
	public static function render_header( $template ) {
		$layout = HCK_Helpers::get( 'header_layout', 'inline' );
		$sticky = 'yes' === HCK_Helpers::get( 'header_sticky', 'yes' );
		$topbar = 'yes' === HCK_Helpers::get( 'header_topbar', 'no' );

		$html = '<header class="hck-header hck-header--' . esc_attr( $template ) . ' hck-header--layout-' . esc_attr( $layout ) . ( $sticky ? ' hck-header--sticky' : '' ) . '">';

		if ( $topbar ) {
			$html .= '<div class="hck-topbar">';
			$html .= '<div class="hck-container hck-topbar__inner">';
			$phone = HCK_Helpers::get( 'header_phone', '' );
			if ( $phone ) {
				$html .= '<span class="hck-topbar__item">' . HCK_Helpers::icon( 'support', array( 'size' => 15 ) ) . ' ' . esc_html( $phone ) . '</span>';
			}
			$html .= '<span class="hck-topbar__item">' . esc_html__( 'Free shipping for orders over a certain amount', 'hooshyar-commerce-kit' ) . '</span>';
			$html .= '</div></div>';
		}

		$html .= '<div class="hck-header__main">';
		$html .= '<div class="hck-container hck-header__inner">';

		$logo = '<a class="hck-logo" href="' . esc_url( home_url( '/' ) ) . '">';
		if ( has_custom_logo() ) {
			$logo .= get_custom_logo();
		} else {
			$logo .= '<span class="hck-logo__mark">' . HCK_Helpers::icon( 'shop', array( 'size' => 24 ) ) . '</span>';
			$logo .= '<span class="hck-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
		}
		$logo .= '</a>';

		$nav     = self::render_nav();
		$actions = self::render_actions();
		$burger  = '<button type="button" class="hck-burger" data-hck-mobile-toggle aria-label="' . esc_attr__( 'Menu', 'hooshyar-commerce-kit' ) . '">' . HCK_Helpers::icon( 'menu' ) . '</button>';

		switch ( $layout ) {
			case 'centered':
				$html .= '<div class="hck-header__row hck-header__row--top">' . $logo . $actions . '</div>';
				$html .= '<div class="hck-header__row hck-header__row--bottom">' . $nav . $burger . '</div>';
				break;

			case 'split':
				$html .= '<div class="hck-header__row">';
				$html .= '<div class="hck-header__side hck-header__side--start">' . $nav . '</div>';
				$html .= '<div class="hck-header__side hck-header__side--center">' . $logo . '</div>';
				$html .= '<div class="hck-header__side hck-header__side--end">' . $actions . $burger . '</div>';
				$html .= '</div>';
				break;

			case 'inline':
			default:
				$html .= '<div class="hck-header__row">';
				$html .= $logo;
				$html .= '<div class="hck-header__nav-wrap">' . $nav . $burger . '</div>';
				$html .= $actions;
				$html .= '</div>';
				break;
		}

		$html .= '</div></div>';
		$html .= '</header>';

		return $html;
	}

	/**
	 * Render a full footer preset.
	 *
	 * @param string $template Template name.
	 * @return string
	 */
	public static function render_footer( $template ) {
		$columns   = (int) HCK_Helpers::get( 'footer_columns', 3 );
		$about     = HCK_Helpers::get( 'footer_about', get_bloginfo( 'description' ) );
		$copyright = HCK_Helpers::get( 'footer_copyright', '© ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) );

		$html  = '<footer class="hck-footer hck-footer--' . esc_attr( $template ) . '">';
		$html .= '<div class="hck-container">';
		$html .= '<div class="hck-footer__grid hck-footer__grid--' . esc_attr( $columns ) . '">';

		// Column 1: about.
		$html .= '<div class="hck-footer__col">';
		$html .= '<a class="hck-logo hck-logo--footer" href="' . esc_url( home_url( '/' ) ) . '">';
		if ( has_custom_logo() ) {
			$html .= get_custom_logo();
		} else {
			$html .= '<span class="hck-logo__mark">' . HCK_Helpers::icon( 'shop', array( 'size' => 24 ) ) . '</span>';
			$html .= '<span class="hck-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
		}
		$html .= '</a>';
		if ( $about ) {
			$html .= '<p class="hck-footer__about">' . esc_html( $about ) . '</p>';
		}
		$html .= '<div class="hck-footer__social">';
		$html .= '<a href="#" class="hck-footer__social-link" aria-label="Telegram">' . HCK_Helpers::icon( 'telegram', array( 'size' => 18 ) ) . '</a>';
		$html .= '<a href="#" class="hck-footer__social-link" aria-label="Bale">' . HCK_Helpers::icon( 'bale', array( 'size' => 18 ) ) . '</a>';
		$html .= '</div></div>';

		// Column 2: quick links.
		if ( $columns >= 2 ) {
			$html .= '<div class="hck-footer__col">';
			$html .= '<h4 class="hck-footer__title">' . esc_html__( 'Quick links', 'hooshyar-commerce-kit' ) . '</h4>';
			$html .= '<ul class="hck-footer__links">';
			$html .= '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'hooshyar-commerce-kit' ) . '</a></li>';
			$html .= '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Shop', 'hooshyar-commerce-kit' ) . '</a></li>';
			$html .= '<li><a href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'Cart', 'hooshyar-commerce-kit' ) . '</a></li>';
			$html .= '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'My account', 'hooshyar-commerce-kit' ) . '</a></li>';
			$html .= '</ul></div>';
		}

		// Column 3: contact.
		if ( $columns >= 3 ) {
			$html .= '<div class="hck-footer__col">';
			$html .= '<h4 class="hck-footer__title">' . esc_html__( 'Contact us', 'hooshyar-commerce-kit' ) . '</h4>';
			$phone = HCK_Helpers::get( 'header_phone', '' );
			if ( $phone ) {
				$html .= '<p class="hck-footer__contact">' . HCK_Helpers::icon( 'support', array( 'size' => 16 ) ) . ' ' . esc_html( $phone ) . '</p>';
			}
			$html .= '<p class="hck-footer__contact">' . HCK_Helpers::icon( 'chat', array( 'size' => 16 ) ) . ' ' . esc_html__( 'Support: 24/7', 'hooshyar-commerce-kit' ) . '</p>';
			$html .= '</div>';
		}

		// Column 4: widget area.
		if ( $columns >= 4 ) {
			$html .= '<div class="hck-footer__col">';
			if ( is_active_sidebar( 'hck-footer-1' ) ) {
				ob_start();
				dynamic_sidebar( 'hck-footer-1' );
				$html .= ob_get_clean();
			} else {
				$html .= '<h4 class="hck-footer__title">' . esc_html__( 'Newsletter', 'hooshyar-commerce-kit' ) . '</h4>';
				$html .= '<p class="hck-footer__about">' . esc_html__( 'Subscribe to get the latest offers.', 'hooshyar-commerce-kit' ) . '</p>';
			}
			$html .= '</div>';
		}

		$html .= '</div>'; // grid.

		$html .= '<div class="hck-footer__bottom">';
		$html .= '<p>' . wp_kses_post( $copyright ) . '</p>';
		$html .= '<p class="hck-footer__brand">' . esc_html__( 'Powered by Hooshyar Commerce Kit', 'hooshyar-commerce-kit' ) . '</p>';
		$html .= '</div>';

		$html .= '</div></footer>';

		return $html;
	}

	/**
	 * Render an Elementor template by id (header/footer in template mode).
	 *
	 * @param int $template_id Template ID.
	 * @return string
	 */
	public static function render_elementor_template( $template_id ) {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return '';
		}

		$content = \Elementor\Plugin::instance()->frontend->get_builder_content( (int) $template_id, true );
		return $content ? $content : '';
	}
}
