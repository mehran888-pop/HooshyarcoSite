<?php
/**
 * Elementor widget: User dashboard (My Account).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Dashboard
 */
class HCK_Widget_Dashboard extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-dashboard';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK User Dashboard', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'dashboard', 'account', 'my account', 'user', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Dashboard', 'hooshyar-commerce-kit' ),
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
					''             => __( 'Default (from settings)', 'hooshyar-commerce-kit' ),
					'sidebar-right' => __( 'Sidebar right', 'hooshyar-commerce-kit' ),
					'sidebar-left'  => __( 'Sidebar left', 'hooshyar-commerce-kit' ),
					'tabs-top'      => __( 'Horizontal tabs', 'hooshyar-commerce-kit' ),
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
					'{{WRAPPER}} .hck-dash' => '--hck-primary: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_background',
			array(
				'label'     => __( 'Sidebar background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-dash-nav' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_link_color',
			array(
				'label'     => __( 'Menu link color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-dash-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_active_color',
			array(
				'label'     => __( 'Active menu color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-dash-nav__item.is-active .hck-dash-nav__link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-dash-nav__item.is-active' => 'background-color: rgba(var(--hck-primary-rgb), 0.12);',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'label'    => __( 'Menu typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-dash-nav__link',
			)
		);

		$this->add_control(
			'welcome_background',
			array(
				'label'     => __( 'Welcome card background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-dash-welcome' => 'background-image: linear-gradient(120deg, {{VALUE}}, var(--hck-secondary));',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo HCK_Dashboard::instance()->render( // phpcs:ignore WordPress.Security.EscapeOutput
			$settings['template'],
			$settings['layout'],
			true
		);
	}
}
