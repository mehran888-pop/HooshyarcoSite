<?php
/**
 * Elementor widget: Cart.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Cart
 */
class HCK_Widget_Cart extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-cart';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Cart', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-cart';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'cart', 'basket', 'woocommerce', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Cart', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'template',
			array(
				'label'   => __( 'Template', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''         => __( 'Default (from settings)', 'hooshyar-commerce-kit' ),
					'default'  => __( 'Default', 'hooshyar-commerce-kit' ),
					'modern'   => __( 'Modern', 'hooshyar-commerce-kit' ),
					'minimal'  => __( 'Minimal', 'hooshyar-commerce-kit' ),
					'creative' => __( 'Creative', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''            => __( 'Default (from settings)', 'hooshyar-commerce-kit' ),
					'two-column'  => __( 'Two column', 'hooshyar-commerce-kit' ),
					'summary-left' => __( 'Summary left', 'hooshyar-commerce-kit' ),
					'one-column'  => __( 'One column', 'hooshyar-commerce-kit' ),
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
			'primary_color',
			array(
				'label'     => __( 'Primary color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-cart' => '--hck-primary: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-cart__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-cart__title',
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => __( 'Summary card background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-summary-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Card radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-summary-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_button_style_controls( $this, '{{WRAPPER}} .hck-cart .hck-btn--primary', 'cart_btn' );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo HCK_Cart::instance()->render( // phpcs:ignore WordPress.Security.EscapeOutput
			$settings['template'],
			$settings['layout'],
			true
		);
	}
}
