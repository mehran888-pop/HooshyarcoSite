<?php
/**
 * Elementor widget: Mobile navigation bar.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Mobile_Nav
 */
class HCK_Widget_Mobile_Nav extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-mobile-nav';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Mobile Nav', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-mobile';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'mobile', 'nav', 'menu', 'bottom bar', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Mobile Nav', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'The bar is displayed automatically on mobile (configurable in Hooshyar Kit → Settings → Mobile Nav). This widget lets you preview it and place it manually.', 'hooshyar-commerce-kit' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''         => __( 'Default (from settings)', 'hooshyar-commerce-kit' ),
					'default'  => __( 'Solid bar', 'hooshyar-commerce-kit' ),
					'floating' => __( 'Floating pill', 'hooshyar-commerce-kit' ),
					'modern'   => __( 'Modern with center action', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_background',
			array(
				'label'     => __( 'Bar background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-mnav' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-mnav__item' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active / accent color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-mnav__item:active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-mnav__badge'       => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => __( 'Label typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-mnav__label',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$markup   = HCK_Mobile_Nav::instance()->get_markup();

		if ( $settings['style'] ) {
			$markup = preg_replace( '/hck-mnav--\w+/', 'hck-mnav--' . sanitize_html_class( $settings['style'] ), $markup );
		}

		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
