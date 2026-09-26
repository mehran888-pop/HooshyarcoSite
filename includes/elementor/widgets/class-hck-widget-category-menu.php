<?php
/**
 * Elementor widget: Professional product-category menu (mega / dropdown).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Category_Menu
 */
class HCK_Widget_Category_Menu extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-category-menu';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Category Menu', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'category', 'menu', 'mega', 'shop', 'header', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Category Menu', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'label',
			array(
				'label'       => __( 'Button label', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Categories', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => __( 'Panel style', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'mega',
				'options' => array(
					'mega'     => __( 'Mega menu (side panel for sub-categories)', 'hooshyar-commerce-kit' ),
					'dropdown' => __( 'Stacked list (inline sub-categories)', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 2,
				'max'     => 6,
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Top-level categories', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
			)
		);

		$this->add_control(
			'show_images',
			array(
				'label'   => __( 'Show category images', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_counts',
			array(
				'label'   => __( 'Show product counts', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_children',
			array(
				'label'   => __( 'Show sub-categories', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			'button_color',
			array(
				'label'     => __( 'Button color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-cats__btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Button background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-cats__btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_background',
			array(
				'label'     => __( 'Panel background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-cats__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => __( 'Item color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-cats__name'                            => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-cats__children a'                      => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => __( 'Accent / hover color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-cats__link:hover .hck-cats__name' => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-cats__children a:hover'           => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-cats__count'                      => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'label'    => __( 'Item typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-cats__name',
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => __( 'Panel radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-cats__panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		echo HCK_Header_Footer::render_category_menu( // phpcs:ignore WordPress.Security.EscapeOutput
			array(
				'enabled'       => 'yes',
				'label'         => $settings['label'],
				'style'         => $settings['style'],
				'columns'       => (int) $settings['columns'],
				'limit'         => (int) $settings['limit'],
				'show_images'   => $settings['show_images'],
				'show_counts'   => $settings['show_counts'],
				'show_children' => $settings['show_children'],
			)
		);
	}
}
