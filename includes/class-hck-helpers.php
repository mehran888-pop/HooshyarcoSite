<?php
/**
 * Shared helpers.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Helpers
 */
class HCK_Helpers {

	/**
	 * Render a plugin template.
	 *
	 * Template files receive `$args` extracted into scope.
	 *
	 * @param string $template Template path relative to /templates.
	 * @param array  $args     Variables for the template.
	 * @param bool   $return   Return instead of echo.
	 * @return string|void
	 */
	public static function template( $template, $args = array(), $return = false ) {
		$file = HCK_PLUGIN_DIR . 'templates/' . ltrim( $template, '/' );

		if ( ! is_readable( $file ) ) {
			return '';
		}

		if ( $return ) {
			ob_start();
		}

		// Make args available to the template.
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( $args, EXTR_SKIP ); // NOSONAR

		include $file;

		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Get a setting value with fallbacks.
	 *
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public static function get( $key, $default = '' ) {
		return HCK_Settings::get( $key, $default );
	}

	/**
	 * Collect the global design tokens (CSS custom properties).
	 *
	 * These tokens power all built-in templates so that every colour,
	 * font and radius is controllable from the plugin settings screen.
	 *
	 * @return array
	 */
	public static function design_tokens() {
		$s = array(
			'primary'        => self::get( 'primary_color', '#6c5ce7' ),
			'secondary'      => self::get( 'secondary_color', '#00cec9' ),
			'accent'         => self::get( 'accent_color', '#fd79a8' ),
			'heading_color'  => self::get( 'heading_color', '#1e1b32' ),
			'text_color'     => self::get( 'text_color', '#4b4b62' ),
			'muted_color'    => self::get( 'muted_color', '#8f8fa8' ),
			'bg_color'       => self::get( 'bg_color', '#ffffff' ),
			'surface_color'  => self::get( 'surface_color', '#f6f5fb' ),
			'border_color'   => self::get( 'border_color', '#e6e4f2' ),
			'success_color'  => self::get( 'success_color', '#16a34a' ),
			'danger_color'   => self::get( 'danger_color', '#ef4444' ),
			'warning_color'  => self::get( 'warning_color', '#f59e0b' ),
			'radius'         => self::get( 'border_radius', '14' ),
			'container'      => self::get( 'container_width', '1200' ),
			'font_body'      => self::get( 'font_body', '' ),
			'font_heading'   => self::get( 'font_heading', '' ),
		);

		return $s;
	}

	/**
	 * Print the CSS custom property block.
	 */
	public static function print_css_variables() {
		$t = self::design_tokens();

		$vars = array(
			'--hck-primary'        => $t['primary'],
			'--hck-secondary'      => $t['secondary'],
			'--hck-accent'         => $t['accent'],
			'--hck-heading'        => $t['heading_color'],
			'--hck-text'           => $t['text_color'],
			'--hck-muted'          => $t['muted_color'],
			'--hck-bg'             => $t['bg_color'],
			'--hck-surface'        => $t['surface_color'],
			'--hck-border'         => $t['border_color'],
			'--hck-success'        => $t['success_color'],
			'--hck-danger'         => $t['danger_color'],
			'--hck-warning'        => $t['warning_color'],
			'--hck-radius'         => $t['radius'] . 'px',
			'--hck-radius-sm'      => max( 4, (int) $t['radius'] / 2 ) . 'px',
			'--hck-radius-lg'      => ( (int) $t['radius'] + 8 ) . 'px',
			'--hck-container'      => $t['container'] . 'px',
			'--hck-primary-rgb'    => self::hex_to_rgb( $t['primary'] ),
			'--hck-secondary-rgb'  => self::hex_to_rgb( $t['secondary'] ),
			'--hck-accent-rgb'     => self::hex_to_rgb( $t['accent'] ),
			'--hck-heading-rgb'    => self::hex_to_rgb( $t['heading_color'] ),
			'--hck-text-rgb'       => self::hex_to_rgb( $t['text_color'] ),
		);

		if ( ! empty( $t['font_body'] ) ) {
			$vars['--hck-font-body'] = self::css_font_stack( $t['font_body'] );
		}
		if ( ! empty( $t['font_heading'] ) ) {
			$vars['--hck-font-heading'] = self::css_font_stack( $t['font_heading'] );
		}

		$css = ':root{';
		foreach ( $vars as $prop => $value ) {
			$css .= $prop . ':' . $value . ';';
		}
		$css .= '}';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static CSS built from sanitized colours.
		echo '<style id="hck-css-vars">' . $css . '</style>';
	}

	/**
	 * Build a safe CSS font-family stack from a font name.
	 *
	 * @param string $font Font family name.
	 * @return string
	 */
	public static function css_font_stack( $font ) {
		$font = trim( (string) $font );
		if ( '' === $font ) {
			return '';
		}

		// Load Google font on the fly.
		self::maybe_enqueue_google_font( $font );

		$stack = "'" . esc_attr( $font ) . "'";
		if ( ! in_array( strtolower( $font ), array( 'sans-serif', 'serif', 'monospace', 'tahoma', 'arial', 'verdana', 'roboto' ), true ) ) {
			$stack .= ', -apple-system, "Segoe UI", Tahoma, sans-serif';
		}
		return $stack;
	}

	/**
	 * Enqueue a Google font if the family looks like one.
	 *
	 * @param string $font Font name.
	 */
	public static function maybe_enqueue_google_font( $font ) {
		static $loaded = array();
		if ( isset( $loaded[ $font ] ) ) {
			return;
		}
		$loaded[ $font ] = true;

		$local_fonts = array( 'tahoma', 'arial', 'verdana', 'sans-serif', 'serif', 'monospace', 'roboto', 'iranyekan', 'iransans', 'vazir', 'shabnam', 'estedad' );
		if ( in_array( strtolower( $font ), $local_fonts, true ) ) {
			return;
		}

		$family = rawurlencode( $font );
		wp_enqueue_style(
			'hck-google-font-' . sanitize_title( $font ),
			'https://fonts.googleapis.com/css2?family=' . $family . ':wght@300;400;500;600;700;800&display=swap',
			array(),
			null
		);
	}

	/**
	 * Convert a hex colour to an "r,g,b" string for rgba() usage.
	 *
	 * @param string $hex Hex colour.
	 * @return string
	 */
	public static function hex_to_rgb( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) ) {
			return '0,0,0';
		}
		return intval( substr( $hex, 0, 2 ), 16 ) . ',' . intval( substr( $hex, 2, 2 ), 16 ) . ',' . intval( substr( $hex, 4, 2 ), 16 );
	}

	/**
	 * Inline SVG icon set.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Optional args (size/class).
	 * @return string
	 */
	public static function icon( $name, $args = array() ) {
		$size  = isset( $args['size'] ) ? absint( $args['size'] ) : 22;
		$class = isset( $args['class'] ) ? sanitize_html_class( $args['class'] ) : 'hck-icon';

		$paths = array(
			'home'     => '<path d="M3 10.5 12 3l9 7.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 9.5V21h5v-6h4v6h5V9.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'shop'     => '<path d="M4 8h16l-1.2 12.2a1.5 1.5 0 0 1-1.5 1.3H6.7a1.5 1.5 0 0 1-1.5-1.3L4 8Z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 10V6.5a3.5 3.5 0 1 1 7 0V10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'cart'     => '<path d="M2.5 3h2.6l2.3 12.1a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21.5 7H6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.5" fill="currentColor"/><circle cx="18" cy="20" r="1.5" fill="currentColor"/>',
			'user'     => '<circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20.5c1.4-3.6 4-5.4 7.5-5.4s6.1 1.8 7.5 5.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'search'   => '<circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'heart'    => '<path d="M12 20.5S3.5 15 3.5 9.3A4.3 4.3 0 0 1 12 7.6a4.3 4.3 0 0 1 8.5 1.7c0 5.7-8.5 11.2-8.5 11.2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
			'menu'     => '<path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'close'    => '<path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'grid'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.8"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.8"/>',
			'order'    => '<rect x="5" y="3.5" width="14" height="17" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 8h7M8.5 12h7M8.5 16h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'logout'   => '<path d="M14 4h4.5A1.5 1.5 0 0 1 20 5.5v13a1.5 1.5 0 0 1-1.5 1.5H14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10 8 6 12l4 4M6 12h9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'wallet'   => '<rect x="3" y="6" width="18" height="13" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M16 12.5h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M3 9h13" stroke="currentColor" stroke-width="1.8"/>',
			'truck'    => '<path d="M3 7h10v9H3zM13 10h4.5l2.5 3v3H13z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="7" cy="18" r="1.8" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="18" r="1.8" fill="none" stroke="currentColor" stroke-width="1.8"/>',
			'support'  => '<circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M9.5 9.8a2.6 2.6 0 1 1 3.6 2.4c-.8.4-1.1.9-1.1 1.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="17" r="1" fill="currentColor"/>',
			'box'      => '<path d="m12 3 8 4.2v9.6L12 21l-8-4.2V7.2L12 3Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M4.4 7.4 12 11.5l7.6-4.1M12 11.5V21" fill="none" stroke="currentColor" stroke-width="1.8"/>',
			'location' => '<path d="M12 21s-6.5-5.4-6.5-10.3A6.5 6.5 0 0 1 12 4.2a6.5 6.5 0 0 1 6.5 6.5C18.5 15.6 12 21 12 21Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10.6" r="2.3" fill="none" stroke="currentColor" stroke-width="1.8"/>',
			'filter'   => '<path d="M4 6h16M7 12h10M10 18h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'arrow-l'  => '<path d="M15 5 8 12l7 7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'arrow-r'  => '<path d="m9 5 7 7-7 7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'plus'     => '<path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'trash'    => '<path d="M5 7h14M9 7V5h6v2M7 7l1 13h8l1-13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
			'edit'     => '<path d="m4 20 4.5-1 10-10-3.5-3.5-10 10L4 20Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m13.5 6.5 3.5 3.5" stroke="currentColor" stroke-width="1.8"/>',
			'star'     => '<path d="m12 3.5 2.6 5.4 5.9.8-4.3 4.1 1 5.9-5.2-2.8-5.2 2.8 1-5.9-4.3-4.1 5.9-.8L12 3.5Z" fill="currentColor"/>',
			'chat'     => '<path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7a2.5 2.5 0 0 1-2.5 2.5H10l-4.5 4v-4A2.5 2.5 0 0 1 4 13.5v-7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
			'telegram' => '<path d="m21 4.5-3 15-5.2-3.8-2.6 2.5.3-4.7L19 6.5 8.6 13.2 4 11.7 21 4.5Z" fill="currentColor"/>',
			'bale'     => '<circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8 13.5c1.2 1.6 2.7 2.4 4 2.4s2.8-.8 4-2.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="9" cy="9.8" r="1" fill="currentColor"/><circle cx="15" cy="9.8" r="1" fill="currentColor"/>',
			'settings' => '<circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 3.5v2.2M12 18.3v2.2M20.5 12h-2.2M5.7 12H3.5M18 6l-1.6 1.6M7.6 16.4 6 18M18 18l-1.6-1.6M7.6 7.6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
			'gift'     => '<rect x="4" y="9" width="16" height="11" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M4 13h16M12 9v11" stroke="currentColor" stroke-width="1.8"/><path d="M12 9C10 5 5.5 5.5 6.5 8.2 7.2 10 10 9 12 9Zm0 0c2-4 6.5-3.5 5.5-.8C16.8 10 14 9 12 9Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'percent'  => '<circle cx="8" cy="8" r="2.4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="16" cy="16" r="2.4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M18.5 5.5 5.5 18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		);

		$path = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['check'];

		return sprintf(
			'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%3$s</svg>',
			esc_attr( $class ),
			$size,
			$path // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG markup.
		);
	}

	/**
	 * Price HTML for a product.
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function product_price_html( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}
		return '<span class="hck-price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
	}

	/**
	 * Product badges (sale / new / stock).
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function product_badges( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$badges = array();

		if ( $product->is_on_sale() ) {
			$badges[] = '<span class="hck-badge hck-badge--sale">' . esc_html__( 'Sale', 'hooshyar-commerce-kit' ) . '</span>';
		}

		$created = $product->get_date_created();
		if ( $created && ( time() - $created->getTimestamp() ) < ( 7 * DAY_IN_SECONDS ) ) {
			$badges[] = '<span class="hck-badge hck-badge--new">' . esc_html__( 'New', 'hooshyar-commerce-kit' ) . '</span>';
		}

		if ( ! $product->is_in_stock() ) {
			$badges[] = '<span class="hck-badge hck-badge--out">' . esc_html__( 'Out of stock', 'hooshyar-commerce-kit' ) . '</span>';
		}

		if ( empty( $badges ) ) {
			return '';
		}

		return '<div class="hck-badges">' . implode( '', $badges ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Build args array for the add-to-cart URL.
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function add_to_cart_url( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}
		if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
			return get_permalink( $product->get_id() );
		}
		return $product->add_to_cart_url();
	}

	/**
	 * Fetch products for widgets/shortcodes.
	 *
	 * @param array $args Query arguments.
	 * @return WC_Product[]
	 */
	public static function get_products( $args = array() ) {
		$defaults = array(
			'limit'    => 8,
			'offset'   => 0,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'source'   => 'latest',
			'category' => '',
			'ids'      => '',
			'columns'  => 4,
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'posts_per_page'      => max( 1, absint( $args['limit'] ) ),
			'offset'              => absint( $args['offset'] ),
		);

		switch ( $args['source'] ) {
			case 'best_selling':
				$query_args['meta_key'] = 'total_sales';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;

			case 'on_sale':
				$query_args['post__in']            = array_map( 'absint', wc_get_product_ids_on_sale() );
				$query_args['orderby']             = sanitize_key( $args['orderby'] );
				$query_args['order']               = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
				break;

			case 'featured':
				$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
					),
				);
				$query_args['orderby'] = sanitize_key( $args['orderby'] );
				$query_args['order']   = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
				break;

			case 'ids':
				$ids                 = array_filter( array_map( 'absint', explode( ',', (string) $args['ids'] ) ) );
				$query_args['post__in'] = $ids;
				$query_args['orderby']  = 'post__in';
				break;

			case 'category':
			default:
				$query_args['orderby'] = sanitize_key( $args['orderby'] );
				$query_args['order']   = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
				break;
		}

		if ( ! empty( $args['category'] ) ) {
			$cats                 = array_filter( array_map( 'sanitize_title', explode( ',', $args['category'] ) ) );
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $cats,
				),
			);
		}

		/**
		 * Filter widget product query args.
		 *
		 * @param array $query_args Args.
		 * @param array $args       Original args.
		 */
		$query_args = apply_filters( 'hck_get_products_query_args', $query_args, $args );

		$loop = new WP_Query( $query_args );
		$products = array();

		foreach ( $loop->posts as $post ) {
			$product = wc_get_product( $post->ID );
			if ( $product ) {
				$products[] = $product;
			}
		}

		wp_reset_postdata();

		return $products;
	}

	/**
	 * Format price with WooCommerce settings.
	 *
	 * @param float|int|string $price Price.
	 * @return string
	 */
	public static function format_price( $price ) {
		return wp_kses_post( wc_price( $price ) );
	}

	/**
	 * Escape helper for HTML attributes in templates.
	 *
	 * @param string $value Value.
	 * @return string
	 */
	public static function esc( $value ) {
		return esc_attr( $value );
	}

	/**
	 * Get dashboard endpoints with labels and icons.
	 *
	 * @return array
	 */
	public static function dashboard_endpoints() {
		$endpoints = array(
			''                 => array(
				'label' => __( 'Dashboard', 'hooshyar-commerce-kit' ),
				'icon'  => 'grid',
			),
			'orders'           => array(
				'label' => __( 'Orders', 'hooshyar-commerce-kit' ),
				'icon'  => 'order',
			),
			'downloads'        => array(
				'label' => __( 'Downloads', 'hooshyar-commerce-kit' ),
				'icon'  => 'box',
			),
			'edit-address'     => array(
				'label' => __( 'Addresses', 'hooshyar-commerce-kit' ),
				'icon'  => 'location',
			),
			'edit-account'     => array(
				'label' => __( 'Account details', 'hooshyar-commerce-kit' ),
				'icon'  => 'edit',
			),
			'customer-logout'  => array(
				'label' => __( 'Log out', 'hooshyar-commerce-kit' ),
				'icon'  => 'logout',
			),
		);

		return apply_filters( 'hck_dashboard_endpoints', $endpoints );
	}

	/**
	 * Sanitize a hex colour.
	 *
	 * @param string $color Colour.
	 * @return string
	 */
	public static function sanitize_hex( $color ) {
		$color = sanitize_hex_color( $color );
		return $color ? $color : '';
	}

	/**
	 * Check if Elementor is active.
	 *
	 * @return bool
	 */
	public static function is_elementor_active() {
		return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
	}
}
