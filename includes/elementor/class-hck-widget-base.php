<?php
/**
 * Shared Elementor widget helpers.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Base
 */
abstract class HCK_Widget_Base extends \Elementor\Widget_Base {

	/**
	 * Widget category.
	 *
	 * @return string
	 */
	public function get_categories() {
		return array( 'hooshyar' );
	}

	/**
	 * Common "card style" controls (background, border, radius, shadow).
	 *
	 * @param \Elementor\Widget_Base $widget      Widget instance.
	 * @param string                 $selector    CSS selector.
	 * @param array                  $args        Extra args.
	 */
	protected function add_card_style_controls( $widget, $selector, $args = array() ) {
		$widget->start_controls_group(
			'card_style',
			array(
				'label' => __( 'Card Style', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$widget->add_control(
			'card_background',
			array(
				'label'     => __( 'Background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$widget->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$widget->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'label'    => __( 'Border', 'hooshyar-commerce-kit' ),
				'selector' => $selector,
			)
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => __( 'Shadow', 'hooshyar-commerce-kit' ),
				'selector' => $selector,
			)
		);

		$widget->end_controls_group();
	}

	/**
	 * Common button style controls.
	 *
	 * @param \Elementor\Widget_Base $widget   Widget instance.
	 * @param string                 $selector CSS selector.
	 * @param string                 $prefix   Control prefix.
	 */
	protected function add_button_style_controls( $widget, $selector, $prefix = 'btn' ) {
		$widget->start_controls_group(
			$prefix . '_style',
			array(
				'label' => __( 'Button Style', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$widget->add_control(
			$prefix . '_text_color',
			array(
				'label'     => __( 'Text color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			$prefix . '_background',
			array(
				'label'     => __( 'Background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			$prefix . '_hover_color',
			array(
				'label'     => __( 'Hover text color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			$prefix . '_hover_background',
			array(
				'label'     => __( 'Hover background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$selector . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => $prefix . '_typography',
				'label'    => __( 'Typography', 'hooshyar-commerce-kit' ),
				'selector' => $selector,
			)
		);

		$widget->add_responsive_control(
			$prefix . '_padding',
			array(
				'label'      => __( 'Padding', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$widget->add_responsive_control(
			$prefix . '_radius',
			array(
				'label'      => __( 'Border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$widget->end_controls_group();
	}

	/**
	 * Entrance animation control.
	 *
	 * @param \Elementor\Widget_Base $widget   Widget instance.
	 * @param string                 $selector CSS selector.
	 */
	protected function add_animation_controls( $widget, $selector ) {
		$widget->add_control(
			'entrance_animation',
			array(
				'label'   => __( 'Entrance animation', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'        => __( 'None', 'hooshyar-commerce-kit' ),
					'fade-up'     => __( 'Fade up', 'hooshyar-commerce-kit' ),
					'fade-down'   => __( 'Fade down', 'hooshyar-commerce-kit' ),
					'zoom-in'     => __( 'Zoom in', 'hooshyar-commerce-kit' ),
					'zoom-out'    => __( 'Zoom out', 'hooshyar-commerce-kit' ),
					'slide-left'  => __( 'Slide from left', 'hooshyar-commerce-kit' ),
					'slide-right' => __( 'Slide from right', 'hooshyar-commerce-kit' ),
					'rotate-in'   => __( 'Rotate in', 'hooshyar-commerce-kit' ),
				),
				'selectors' => array(
					$selector => '--hck-entrance: {{VALUE}};',
				),
			)
		);
	}
}
