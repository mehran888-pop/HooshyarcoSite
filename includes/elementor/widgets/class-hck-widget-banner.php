<?php
/**
 * Elementor widget: Banner / Poster with creative effects.
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Banner
 */
class HCK_Widget_Banner extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-banner';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Banner / Poster', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-banner';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'banner', 'poster', 'hero', 'promo', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		/* -----------------------------------------------------------------
		 * Content
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Banner', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'centered',
				'options' => array(
					'centered' => __( 'Centered', 'hooshyar-commerce-kit' ),
					'left'     => __( 'Content left', 'hooshyar-commerce-kit' ),
					'right'    => __( 'Content right', 'hooshyar-commerce-kit' ),
					'split'    => __( 'Split card', 'hooshyar-commerce-kit' ),
					'poster'   => __( 'Poster', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Creative effect', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'kenburns',
				'options' => array(
					'none'         => __( 'None', 'hooshyar-commerce-kit' ),
					'kenburns'     => __( 'Ken Burns (slow zoom)', 'hooshyar-commerce-kit' ),
					'parallax'     => __( 'Parallax scroll', 'hooshyar-commerce-kit' ),
					'gradient'     => __( 'Animated gradient overlay', 'hooshyar-commerce-kit' ),
					'tilt'         => __( '3D tilt on hover', 'hooshyar-commerce-kit' ),
					'mask-reveal'  => __( 'Mask reveal on scroll', 'hooshyar-commerce-kit' ),
					'glow'         => __( 'Soft glow hover', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Background image', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'       => __( 'Badge', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. Special offer', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Your headline', 'hooshyar-commerce-kit' ),
				'default'     => __( 'Big summer sale', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => __( 'Subtitle', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Supporting text', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Shop now', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => __( 'Button link', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://',
				'default'     => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'button_text_2',
			array(
				'label'   => __( 'Second button text', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'button_link_2',
			array(
				'label' => __( 'Second button link', 'hooshyar-commerce-kit' ),
				'type'  => \Elementor\Controls_Manager::URL,
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Banner height', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 420 ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-banner' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* -----------------------------------------------------------------
		 * Style: overlay & content
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_overlay',
			array(
				'label' => __( 'Overlay', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(15, 12, 40, 0.55)',
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_gradient',
			array(
				'label'     => __( 'Gradient overlay', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__overlay--gradient' => 'background-image: linear-gradient(120deg, {{VALUE}}, transparent 70%);',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Badge color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_background',
			array(
				'label'     => __( 'Badge background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-banner__title',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Subtitle color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => __( 'Subtitle typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-banner__subtitle',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Description color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'label'    => __( 'Description typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-banner__desc',
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'   => __( 'Content alignment', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'right'  => array( 'title' => __( 'Right', 'hooshyar-commerce-kit' ), 'icon' => 'eicon-text-align-right' ),
					'center' => array( 'title' => __( 'Center', 'hooshyar-commerce-kit' ), 'icon' => 'eicon-text-align-center' ),
					'left'   => array( 'title' => __( 'Left', 'hooshyar-commerce-kit' ), 'icon' => 'eicon-text-align-left' ),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .hck-banner__content' => 'text-align: {{VALUE}}; align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Buttons', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_button_style_controls( $this, '{{WRAPPER}} .hck-banner__btn', 'banner_btn' );

		$this->end_controls_section();

		// Border & effects.
		$this->start_controls_section(
			'section_style_fx',
			array(
				'label' => __( 'Effects & Border', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'banner_radius',
			array(
				'label'      => __( 'Border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .hck-banner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'banner_shadow',
				'label'    => __( 'Shadow', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-banner',
			)
		);

		$this->add_animation_controls( $this, '{{WRAPPER}} .hck-banner' );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$image_url = '';
		if ( ! empty( $settings['image']['url'] ) ) {
			$image_url = $settings['image']['url'];
		}

		$link = ! empty( $settings['button_link']['url'] ) ? $settings['button_link']['url'] : '';
		$link_2 = ! empty( $settings['button_link_2']['url'] ) ? $settings['button_link_2']['url'] : '';

		$classes  = 'hck-banner';
		$classes .= ' hck-banner--' . sanitize_html_class( $settings['layout'] );
		$classes .= ' hck-banner--fx-' . sanitize_html_class( $settings['effect'] );

		echo '<div class="' . esc_attr( $classes ) . '" data-hck-banner>';

		// Media layer.
		echo '<div class="hck-banner__media">';
		if ( $image_url ) {
			echo '<div class="hck-banner__image" style="background-image:url(' . esc_url( $image_url ) . ')"></div>';
		}
		echo '<div class="hck-banner__overlay"></div>';
		if ( 'gradient' === $settings['effect'] ) {
			echo '<div class="hck-banner__overlay hck-banner__overlay--gradient"></div>';
		}
		echo '</div>';

		// Content layer.
		echo '<div class="hck-banner__content">';

		if ( ! empty( $settings['badge'] ) ) {
			echo '<span class="hck-banner__badge">' . esc_html( $settings['badge'] ) . '</span>';
		}

		if ( ! empty( $settings['title'] ) ) {
			echo '<h2 class="hck-banner__title">' . esc_html( $settings['title'] ) . '</h2>';
		}

		if ( ! empty( $settings['subtitle'] ) ) {
			echo '<p class="hck-banner__subtitle">' . esc_html( $settings['subtitle'] ) . '</p>';
		}

		if ( ! empty( $settings['description'] ) ) {
			echo '<p class="hck-banner__desc">' . esc_html( $settings['description'] ) . '</p>';
		}

		if ( ! empty( $settings['button_text'] ) || ! empty( $settings['button_text_2'] ) ) {
			echo '<div class="hck-banner__actions">';

			if ( ! empty( $settings['button_text'] ) ) {
				printf(
					'<a class="hck-btn hck-banner__btn hck-btn--primary" href="%s">%s</a>',
					esc_url( $link ),
					esc_html( $settings['button_text'] )
				);
			}

			if ( ! empty( $settings['button_text_2'] ) ) {
				printf(
					'<a class="hck-btn hck-banner__btn hck-btn--ghost" href="%s">%s</a>',
					esc_url( $link_2 ),
					esc_html( $settings['button_text_2'] )
				);
			}

			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Editor template.
	 */
	protected function content_template() {
		?>
		<div class="hck-products-placeholder"><?php esc_html_e( 'HCK Banner — preview is rendered live.', 'hooshyar-commerce-kit' ); ?></div>
		<?php
	}
}
