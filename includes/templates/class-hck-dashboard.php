<?php
/**
 * Professional user dashboard (My Account) module.
 *
 * Fully synced with WooCommerce account endpoints: orders, downloads,
 * addresses, account details and logout.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Dashboard
 */
class HCK_Dashboard {

	/**
	 * Singleton.
	 *
	 * @var HCK_Dashboard|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Dashboard
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
		add_shortcode( 'hck_dashboard', array( $this, 'shortcode' ) );

		// Replace the plain WooCommerce my-account navigation with ours.
		add_action( 'woocommerce_account_navigation', array( $this, 'render_navigation' ), 5 );

		// Sync WooCommerce my-account content rendering with our template.
		add_filter( 'the_content', array( $this, 'maybe_wrap_account_content' ), 99 );
	}

	/**
	 * Shortcode [hck_dashboard template="modern" layout="sidebar-right"].
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'template' => HCK_Helpers::get( 'dashboard_template', 'modern' ),
				'layout'   => HCK_Helpers::get( 'dashboard_layout', 'sidebar-right' ),
			),
			$atts,
			'hck_dashboard'
		);

		return $this->render( $atts['template'], $atts['layout'], true );
	}

	/**
	 * Render the dashboard shell.
	 *
	 * @param string $template Template name.
	 * @param string $layout   Layout name.
	 * @param bool   $return   Return string.
	 * @return string|void
	 */
	public function render( $template = '', $layout = '', $return = false ) {
		if ( ! is_user_logged_in() ) {
			return $this->render_login_prompt( $return );
		}

		$template = $template ? sanitize_file_name( $template ) : HCK_Template_Loader::get_template_name( 'dashboard', 'dashboard_template' );
		$layout   = $layout ? sanitize_file_name( $layout ) : sanitize_file_name( HCK_Helpers::get( 'dashboard_layout', 'sidebar-right' ) );

		$file = HCK_PLUGIN_DIR . 'templates/dashboard/dashboard-' . $template . '.php';
		if ( ! is_readable( $file ) ) {
			$file     = HCK_PLUGIN_DIR . 'templates/dashboard/dashboard-modern.php';
			$template = 'modern';
		}

		// Render the WooCommerce endpoint content (orders, downloads, ...) so
		// the dashboard is fully synced with the account pages.
		$account_content = '';
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			ob_start();
			do_action( 'woocommerce_account_content' );
			$account_content = ob_get_clean();
		}

		$dash    = self::instance();
		$content  = $dash->get_welcome_card();
		$content .= $dash->get_stats();
		$content .= $dash->get_recent_orders();
		$content .= $account_content;

		$args = array(
			'template' => $template,
			'layout'   => $layout,
			'user'     => wp_get_current_user(),
			'content'  => $content,
		);

		return HCK_Helpers::template( 'dashboard/dashboard-' . $template . '.php', $args, $return );
	}

	/**
	 * Login prompt for guests.
	 *
	 * @param bool $return Return string.
	 * @return string|void
	 */
	public function render_login_prompt( $return = false ) {
		$html = '<div class="hck-dash hck-dash--guest">';
		$html .= '<div class="hck-dash-login">';
		$html .= '<div class="hck-dash-login__icon">' . HCK_Helpers::icon( 'user', array( 'size' => 48 ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		$html .= '<h2>' . esc_html__( 'Log in to your account', 'hooshyar-commerce-kit' ) . '</h2>';
		$html .= '<p>' . esc_html__( 'Access your orders, downloads and account details.', 'hooshyar-commerce-kit' ) . '</p>';
		$html .= '<a class="hck-btn hck-btn--primary hck-btn--lg" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'Log in / Register', 'hooshyar-commerce-kit' ) . '</a>';
		$html .= '</div></div>';

		if ( $return ) {
			return $html;
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Replace WooCommerce's default account navigation with the HCK sidebar.
	 */
	public function render_navigation() {
		// Prevent the core navigation from printing next to ours.
		remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = isset( $GLOBALS['wp']->query_vars ) && ! empty( $GLOBALS['wp']->query_vars ) ? key( array_intersect_key( $GLOBALS['wp']->query_vars, array_flip( wc_get_account_endpoint_names() ) ) ) : '';

		if ( ! $current ) {
			$current = '';
		}

		echo $this->get_sidebar_nav( $current ); // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * Build the sidebar navigation HTML.
	 *
	 * @param string $current Current endpoint key.
	 * @return string
	 */
	public function get_sidebar_nav( $current = '' ) {
		$user       = wp_get_current_user();
		$endpoints  = HCK_Helpers::dashboard_endpoints();
		$avatar     = get_avatar( $user->ID, 72, '', '', array( 'class' => 'hck-dash-nav__avatar-img' ) );
		$first_name = $user->first_name ? $user->first_name : $user->display_name;

		$html = '<nav class="hck-dash-nav" aria-label="' . esc_attr__( 'Account menu', 'hooshyar-commerce-kit' ) . '">';

		if ( 'yes' === HCK_Helpers::get( 'dashboard_show_avatar', 'yes' ) ) {
			$html .= '<div class="hck-dash-nav__profile">';
			$html .= '<div class="hck-dash-nav__avatar">' . wp_kses_post( $avatar ) . '</div>';
			$html .= '<div class="hck-dash-nav__hello">' . esc_html__( 'Hello', 'hooshyar-commerce-kit' ) . '</div>';
			$html .= '<div class="hck-dash-nav__name">' . esc_html( $first_name ) . '</div>';
			$html .= '</div>';
		}

		$html .= '<ul class="hck-dash-nav__list">';

		foreach ( $endpoints as $endpoint => $data ) {
			// The empty key is the dashboard home (my-account root).
			$url        = wc_get_account_endpoint_url( $endpoint );
			$is_current = ( $current === $endpoint ) || ( '' === $endpoint && '' === $current );

			$html .= sprintf(
				'<li class="hck-dash-nav__item %s"><a href="%s" class="hck-dash-nav__link">%s<span>%s</span></a></li>',
				$is_current ? 'is-active' : '',
				esc_url( $url ),
				HCK_Helpers::icon( $data['icon'], array( 'size' => 19 ) ), // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $data['label'] )
			);
		}

		$html .= '</ul></nav>';

		return $html;
	}

	/**
	 * Welcome card HTML.
	 *
	 * @return string
	 */
	public function get_welcome_card() {
		if ( 'yes' !== HCK_Helpers::get( 'dashboard_welcome', 'yes' ) ) {
			return '';
		}

		$user       = wp_get_current_user();
		$first_name = $user->first_name ? $user->first_name : $user->display_name;
		$orders     = wc_get_orders(
			array(
				'customer_id' => $user->ID,
				'limit'       => 1,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		$html  = '<div class="hck-dash-welcome">';
		$html .= '<div class="hck-dash-welcome__content">';
		$html .= '<h2 class="hck-dash-welcome__title">' . sprintf(
			/* translators: %s: customer first name */
			esc_html__( 'Welcome back, %s 👋', 'hooshyar-commerce-kit' ),
			esc_html( $first_name )
		) . '</h2>';
		$html .= '<p class="hck-dash-welcome__text">' . esc_html__( 'From here you can manage your orders, addresses and account details.', 'hooshyar-commerce-kit' ) . '</p>';

		if ( ! empty( $orders ) ) {
			$html .= '<a class="hck-btn hck-btn--light" href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'Track your latest order', 'hooshyar-commerce-kit' ) . '</a>';
		}

		$html .= '</div>';
		$html .= '<div class="hck-dash-welcome__art">' . HCK_Helpers::icon( 'gift', array( 'size' => 96 ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		$html .= '</div>';

		return $html;
	}

	/**
	 * Stats cards HTML.
	 *
	 * @return string
	 */
	public function get_stats() {
		if ( 'yes' !== HCK_Helpers::get( 'dashboard_stats', 'yes' ) ) {
			return '';
		}

		$user_id   = get_current_user_id();
		$orders    = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => -1,
				'return'      => 'ids',
			)
		);
		$downloads = wc_get_customer_available_downloads( $user_id );

		$stats = array(
			array(
				'icon'  => 'order',
				'label' => __( 'Total orders', 'hooshyar-commerce-kit' ),
				'value' => count( $orders ),
				'link'  => wc_get_account_endpoint_url( 'orders' ),
			),
			array(
				'icon'  => 'box',
				'label' => __( 'Downloads', 'hooshyar-commerce-kit' ),
				'value' => is_array( $downloads ) ? count( $downloads ) : 0,
				'link'  => wc_get_account_endpoint_url( 'downloads' ),
			),
			array(
				'icon'  => 'location',
				'label' => __( 'Addresses', 'hooshyar-commerce-kit' ),
				'value' => ( get_user_meta( $user_id, 'billing_city', true ) || get_user_meta( $user_id, 'shipping_city', true ) ) ? 1 : 0,
				'link'  => wc_get_account_endpoint_url( 'edit-address' ),
			),
		);

		$html = '<div class="hck-dash-stats">';
		foreach ( $stats as $stat ) {
			$html .= '<a class="hck-dash-stat" href="' . esc_url( $stat['link'] ) . '">';
			$html .= '<span class="hck-dash-stat__icon">' . HCK_Helpers::icon( $stat['icon'], array( 'size' => 22 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<span class="hck-dash-stat__value">' . esc_html( $stat['value'] ) . '</span>';
			$html .= '<span class="hck-dash-stat__label">' . esc_html( $stat['label'] ) . '</span>';
			$html .= '</a>';
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Recent orders table HTML.
	 *
	 * @return string
	 */
	public function get_recent_orders() {
		$limit = (int) HCK_Helpers::get( 'dashboard_recent_orders', 5 );
		if ( $limit <= 0 ) {
			return '';
		}

		$orders = wc_get_orders(
			array(
				'customer_id' => get_current_user_id(),
				'limit'       => $limit,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		$html = '<div class="hck-dash-recent">';
		$html .= '<div class="hck-dash-recent__head"><h3>' . esc_html__( 'Recent orders', 'hooshyar-commerce-kit' ) . '</h3>';
		$html .= '<a href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'View all', 'hooshyar-commerce-kit' ) . '</a></div>';

		if ( empty( $orders ) ) {
			$html .= '<p class="hck-dash-recent__empty">' . esc_html__( 'You have not placed any orders yet.', 'hooshyar-commerce-kit' ) . '</p>';
		} else {
			$html .= '<table class="hck-dash-table"><thead><tr>';
			$html .= '<th>' . esc_html__( 'Order', 'hooshyar-commerce-kit' ) . '</th>';
			$html .= '<th>' . esc_html__( 'Date', 'hooshyar-commerce-kit' ) . '</th>';
			$html .= '<th>' . esc_html__( 'Status', 'hooshyar-commerce-kit' ) . '</th>';
			$html .= '<th>' . esc_html__( 'Total', 'hooshyar-commerce-kit' ) . '</th>';
			$html .= '<th></th></tr></thead><tbody>';

			foreach ( $orders as $order ) {
				$html .= '<tr>';
				$html .= '<td data-label="' . esc_attr__( 'Order', 'hooshyar-commerce-kit' ) . '">#' . esc_html( $order->get_order_number() ) . '</td>';
				$html .= '<td data-label="' . esc_attr__( 'Date', 'hooshyar-commerce-kit' ) . '">' . esc_html( wc_format_datetime( $order->get_date_created(), 'Y/m/d' ) ) . '</td>';
				$html .= '<td data-label="' . esc_attr__( 'Status', 'hooshyar-commerce-kit' ) . '"><span class="hck-status hck-status--' . esc_attr( $order->get_status() ) . '">' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</span></td>';
				$html .= '<td data-label="' . esc_attr__( 'Total', 'hooshyar-commerce-kit' ) . '">' . wp_kses_post( $order->get_formatted_order_total() ) . '</td>';
				$html .= '<td><a class="hck-btn hck-btn--ghost hck-btn--sm" href="' . esc_url( $order->get_view_order_url() ) . '">' . esc_html__( 'View', 'hooshyar-commerce-kit' ) . '</a></td>';
				$html .= '</tr>';
			}

			$html .= '</tbody></table>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Header user-area button (login state synced with WooCommerce).
	 *
	 * @param array $args Display args.
	 * @return string
	 */
	public function get_user_button( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_cart'  => true,
				'show_label' => true,
				'layout'     => 'icon-label',
			)
		);

		$html = '<div class="hck-user-area" data-hck-user-area>';

		if ( is_user_logged_in() ) {
			$user       = wp_get_current_user();
			$first_name = $user->first_name ? $user->first_name : $user->display_name;

			$html .= '<button type="button" class="hck-user-area__btn" data-hck-user-toggle aria-expanded="false">';
			$html .= '<span class="hck-user-area__avatar">' . wp_kses_post( get_avatar( $user->ID, 40, '', '', array( 'class' => 'hck-user-area__avatar-img' ) ) ) . '</span>';
			if ( 'icon-label' === $args['layout'] && $args['show_label'] ) {
				$html .= '<span class="hck-user-area__label">' . esc_html( $first_name ) . '</span>';
			}
			$html .= '</button>';

			$html .= '<div class="hck-user-area__menu">';
			$html .= '<a href="' . esc_url( wc_get_account_endpoint_url( '' ) ) . '">' . HCK_Helpers::icon( 'grid', array( 'size' => 17 ) ) . esc_html__( 'Dashboard', 'hooshyar-commerce-kit' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<a href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . HCK_Helpers::icon( 'order', array( 'size' => 17 ) ) . esc_html__( 'My orders', 'hooshyar-commerce-kit' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<a href="' . esc_url( wc_get_account_endpoint_url( 'edit-account' ) ) . '">' . HCK_Helpers::icon( 'settings', array( 'size' => 17 ) ) . esc_html__( 'Account settings', 'hooshyar-commerce-kit' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<a href="' . esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ) . '" class="hck-user-area__logout">' . HCK_Helpers::icon( 'logout', array( 'size' => 17 ) ) . esc_html__( 'Log out', 'hooshyar-commerce-kit' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '</div>';
		} else {
			$html .= '<a class="hck-user-area__btn hck-user-area__btn--login" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">';
			$html .= HCK_Helpers::icon( 'user', array( 'size' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			if ( 'icon-label' === $args['layout'] && $args['show_label'] ) {
				$html .= '<span class="hck-user-area__label">' . esc_html__( 'Login / Register', 'hooshyar-commerce-kit' ) . '</span>';
			}
			$html .= '</a>';
		}

		if ( $args['show_cart'] ) {
			$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
			$html .= '<a class="hck-user-area__cart" href="' . esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' ) . '" data-hck-open-cart aria-label="' . esc_attr__( 'Cart', 'hooshyar-commerce-kit' ) . '">';
			$html .= HCK_Helpers::icon( 'cart', array( 'size' => 21 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			$html .= '<span class="hck-cart-count">' . esc_html( $count ) . '</span>';
			$html .= '</a>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Wrap the plain [woocommerce_my_account] output with our dashboard chrome
	 * so the default WooCommerce page also gets the professional look.
	 *
	 * @param string $content Content.
	 * @return string
	 */
	public function maybe_wrap_account_content( $content ) {
		if ( ! is_page() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return $content;
		}

		// Only wrap when the content is produced by the core shortcode and
		// does not already contain our own dashboard markup.
		if ( false === strpos( $content, 'woocommerce-MyAccount' ) || false !== strpos( $content, 'hck-dash' ) ) {
			return $content;
		}

		$template = HCK_Template_Loader::get_template_name( 'dashboard', 'dashboard_template' );
		$layout   = sanitize_file_name( HCK_Helpers::get( 'dashboard_layout', 'sidebar-right' ) );

		if ( is_user_logged_in() ) {
			$current = '';
			if ( ! empty( $GLOBALS['wp']->query_vars ) ) {
				$matched = array_intersect_key( $GLOBALS['wp']->query_vars, array_flip( array_keys( HCK_Helpers::dashboard_endpoints() ) ) );
				if ( $matched ) {
					$current = key( $matched );
				}
			}

			$dash = self::instance();
			$main  = $dash->get_welcome_card();
			$main .= $dash->get_stats();
			$main .= $dash->get_recent_orders();
			$main .= $content;

			ob_start();
			HCK_Helpers::template(
				'dashboard/dashboard-shell.php',
				array(
					'template' => $template,
					'layout'   => $layout,
					'content'  => $main,
					'current'  => $current,
					'user'     => wp_get_current_user(),
				)
			);
			return ob_get_clean();
		}

		return $content;
	}
}
