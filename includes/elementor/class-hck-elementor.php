<?php
/**
 * Elementor integration bootstrap.
 *
 * Registers the Hooshyar widget category, all dedicated widgets and
 * the custom theme locations used by the header/footer templates.
 * Works with Elementor Free and Elementor Pro.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Elementor
 */
class HCK_Elementor {

	/**
	 * Minimum Elementor version.
	 */
	const MIN_VERSION = '3.5.0';

	/**
	 * Singleton.
	 *
	 * @var HCK_Elementor|null
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * @return HCK_Elementor
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
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		// Elementor 3.5+ registration hook.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		// Legacy hook for older Elementor versions (also serves as a safety net).
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_assets' ) );
		add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_deps' ), 30 );
	}

	/**
	 * Check Elementor version.
	 *
	 * @return bool
	 */
	public function check_requirements() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}
		return version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' );
	}

	/**
	 * Register the widget category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'hooshyar',
			array(
				'title' => __( 'Hooshyar Kit', 'hooshyar-commerce-kit' ),
				'icon'  => 'eicon-shopping-cart',
			)
		);
	}

	/**
	 * Register all widgets.
	 *
	 * Safe to run twice (Elementor 3.5+ `register` and the legacy
	 * `widgets_registered` hook) — duplicates are skipped by Elementor.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! $this->check_requirements() || empty( $widgets_manager ) ) {
			return;
		}

		$widgets = array(
			'HCK_Widget_Products',
			'HCK_Widget_Banner',
			'HCK_Widget_Product_Banner',
			'HCK_Widget_Cart',
			'HCK_Widget_Checkout',
			'HCK_Widget_Dashboard',
			'HCK_Widget_User_Area',
			'HCK_Widget_Mobile_Nav',
			'HCK_Widget_Category_Menu',
		);

		foreach ( $widgets as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$instance = new $class();

			if ( method_exists( $widgets_manager, 'register' ) ) {
				$widgets_manager->register( $instance );
			} elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
				$widgets_manager->register_widget_type( $instance );
			}
		}
	}

	/**
	 * Register editor scripts.
	 */
	public function register_assets() {
		wp_register_script(
			'hck-elementor-editor',
			HCK_PLUGIN_URL . 'assets/js/widgets.js',
			array( 'hck-frontend' ),
			HCK_VERSION,
			true
		);
	}

	/**
	 * Editor-only styles.
	 */
	public function editor_assets() {
		wp_enqueue_style( 'hck-widgets', HCK_PLUGIN_URL . 'assets/css/widgets.css', array(), HCK_VERSION );
	}

	/**
	 * Make sure frontend styles load inside Elementor previews.
	 */
	public function frontend_deps() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() || \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			wp_enqueue_style( 'hck-widgets', HCK_PLUGIN_URL . 'assets/css/widgets.css', array(), HCK_VERSION );
			wp_enqueue_style( 'hck-templates', HCK_PLUGIN_URL . 'assets/css/templates.css', array(), HCK_VERSION );
		}
	}

	/**
	 * Register custom Elementor theme locations (header/footer).
	 *
	 * @param \Elementor\Core\ThemeManager $theme_manager Theme manager.
	 */
	public function register_locations( $theme_manager ) {
		$theme_manager->register_all_core_location();

		if ( method_exists( $theme_manager, 'register_location' ) ) {
			$theme_manager->register_location(
				'hck_header',
				array(
					'hook'         => 'hck/header',
					'edit_in_content' => true,
				)
			);
			$theme_manager->register_location(
				'hck_footer',
				array(
					'hook'         => 'hck/footer',
					'edit_in_content' => true,
				)
			);
		}
	}
}
