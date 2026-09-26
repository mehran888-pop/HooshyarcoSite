<?php
/**
 * Elementor widget: Product banner (single product presented as a
 * creative hero / poster with effects).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Product_Banner
 */
class HCK_Widget_Product_Banner extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-product-banner';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Product Banner', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-images';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'product', 'banner', 'hero', 'spotlight', 'promo', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product',
			array(
				'label' => __( 'Product', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'product_id',
			array(
				'label'       => __( 'Product ID', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Leave empty to use the current product on a single product page.', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'spotlight',
				'options' => array(
					'spotlight'   => __( 'Spotlight (image + info)', 'hooshyar-commerce-kit' ),
					'poster'      => __( 'Poster (full-bleed image)', 'hooshyar-commerce-kit' ),
					'split-card'  => __( 'Split card', 'hooshyar-commerce-kit' ),
					'hero-center' => __( 'Centered hero', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Creative effect', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'float',
				'options' => array(
					'none'        => __( 'None', 'hooshyar-commerce-kit' ),
					'float'       => __( 'Floating product', 'hooshyar-commerce-kit' ),
					'zoom-pulse'  => __( 'Zoom pulse', 'hooshyar-commerce-kit' ),
					'parallax'    => __( 'Parallax scroll', 'hooshyar-commerce-kit' ),
					'glow'        => __( 'Neon glow', 'hooshyar-commerce-kit' ),
					'tilt'        => __( '3D tilt on hover', 'hooshyar-commerce-kit' ),
					'mask-reveal' => __( 'Mask reveal on scroll', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'   => __( 'Show price', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'   => __( 'Show rating', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'   => __( 'Show short description', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'   => __( 'Show add-to-cart button', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'custom_title',
			array(
				'label'       => __( 'Custom title', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Overrides the product name.', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_control(
			'custom_text',
			array(
				'label' => __( 'Custom text', 'hooshyar-commerce-kit' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'   => __( 'Badge', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Featured', 'hooshyar-commerce-kit' ),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Banner height', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 260, 'max' => 900 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 460 ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-pbanner' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style: background.
		$this->start_controls_section(
			'section_style_bg',
			array(
				'label' => __( 'Background', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => __( 'Background color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-pbanner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_gradient',
			array(
				'label'     => __( 'Gradient from', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-pbanner' => 'background-image: linear-gradient(135deg, {{VALUE}}, var(--hck-primary-rgb));',
				),
			)
		);

		$this->add_responsive_control(
			'banner_radius',
			array(
				'label'      => __( 'Border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top' => 24, 'right' => 24, 'bottom' => 24, 'left' => 24, 'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .hck-pbanner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'banner_shadow',
				'label'    => __( 'Shadow', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-pbanner',
			)
		);

		$this->end_controls_section();

		// Style: content.
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Title color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-pbanner__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Title typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-pbanner__title',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-pbanner__text'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .hck-pbanner__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-pbanner .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => __( 'Price typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-pbanner .price',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_button_style_controls( $this, '{{WRAPPER}} .hck-pbanner__btn', 'pbanner_btn' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_fx',
			array(
				'label' => __( 'Effects', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'image_max_width',
			array(
				'label'      => __( 'Product image max width', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 700 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 80 ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-pbanner__image img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_animation_controls( $this, '{{WRAPPER}} .hck-pbanner' );

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_id = ! empty( $settings['product_id'] ) ? absint( $settings['product_id'] ) : get_the_ID();
		$product    = $product_id ? wc_get_product( $product_id ) : null;

		if ( ! $product ) {
			echo '<p class="hck-no-products">' . esc_html__( 'Product not found.', 'hooshyar-commerce-kit' ) . '</p>';
			return;
		}

		$permalink = get_permalink( $product->get_id() );
		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src();

		$title = $settings['custom_title'] ? $settings['custom_title'] : $product->get_name();
		$text  = $settings['custom_text'] ? $settings['custom_text'] : $product->get_short_description();

		$classes  = 'hck-pbanner';
		$classes .= ' hck-pbanner--' . sanitize_html_class( $settings['layout'] );
		$classes .= ' hck-pbanner--fx-' . sanitize_html_class( $settings['effect'] );

		echo '<div class="' . esc_attr( $classes ) . '" data-hck-pbanner>';

		// Decorative shapes.
		echo '<div class="hck-pbanner__shapes" aria-hidden="true"><span></span><span></span><span></span></div>';

		echo '<div class="hck-pbanner__media">';
		echo '<div class="hck-pbanner__image">';
		printf(
			'<img src="%s" alt="%s" loading="lazy" />',
			esc_url( $image_url ),
			esc_attr( $product->get_name() )
		);
		echo '</div>';
		echo '</div>';

		echo '<div class="hck-pbanner__content">';

		if ( ! empty( $settings['badge'] ) ) {
			echo '<span class="hck-pbanner__badge">' . esc_html( $settings['badge'] ) . '</span>';
		}

		echo '<h2 class="hck-pbanner__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h2>';

		if ( 'yes' === $settings['show_rating'] && $product->get_average_rating() > 0 ) {
			echo '<div class="hck-pbanner__rating">' . wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ) . '</div>';
		}

		if ( 'yes' === $settings['show_price'] ) {
			echo '<div class="hck-pbanner__price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
		}

		if ( 'yes' === $settings['show_excerpt'] && $text ) {
			echo '<div class="hck-pbanner__excerpt">' . esc_html( wp_trim_words( wp_strip_all_tags( $text ), 26 ) ) . '</div>';
		}

		if ( 'yes' === $settings['show_button'] ) {
			if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
				printf(
					'<a class="hck-btn hck-pbanner__btn hck-btn--primary hck-btn--lg" href="%s">%s</a>',
					esc_url( $permalink ),
					esc_html__( 'View product', 'hooshyar-commerce-kit' )
				);
			} elseif ( $product->is_in_stock() && $product->is_purchasable() ) {
				printf(
					'<a href="%s" class="hck-btn hck-pbanner__btn hck-btn--primary hck-btn--lg add_to_cart_button ajax_add_to_cart" data-product_id="%d" data-quantity="1" rel="nofollow">%s</a>',
					esc_url( HCK_Helpers::add_to_cart_url( $product ) ),
					absint( $product->get_id() ),
					esc_html( $product->add_to_cart_text() )
				);
			} else {
				echo '<span class="hck-btn hck-btn--disabled hck-btn--lg hck-pbanner__btn">' . esc_html__( 'Out of stock', 'hooshyar-commerce-kit' ) . '</span>';
			}
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Editor template.
	 */
	protected function content_template() {
		?>
		<div class="hck-products-placeholder"><?php esc_html_e( 'HCK Product Banner — preview is rendered live.', 'hooshyar-commerce-kit' ); ?></div>
		<?php
	}
}
