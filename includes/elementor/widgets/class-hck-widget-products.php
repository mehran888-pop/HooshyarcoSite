<?php
/**
 * Elementor widget: Products (grid / carousel / slider / list / masonry).
 *
 * @package HooshyarCommerceKit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class HCK_Widget_Products
 */
class HCK_Widget_Products extends HCK_Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hck-products';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'HCK Products', 'hooshyar-commerce-kit' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * Widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'products', 'grid', 'carousel', 'slider', 'list', 'shop', 'hooshyar' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		/* -----------------------------------------------------------------
		 * Content
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Products', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => array(
					'latest'      => __( 'Latest products', 'hooshyar-commerce-kit' ),
					'best_selling' => __( 'Best selling', 'hooshyar-commerce-kit' ),
					'on_sale'     => __( 'On sale', 'hooshyar-commerce-kit' ),
					'featured'    => __( 'Featured', 'hooshyar-commerce-kit' ),
					'category'    => __( 'Category', 'hooshyar-commerce-kit' ),
					'ids'         => __( 'Specific IDs', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'       => __( 'Category slugs (comma separated)', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'condition'   => array( 'source' => 'category' ),
				'placeholder' => 'hoodies, tshirts',
			)
		);

		$this->add_control(
			'ids',
			array(
				'label'       => __( 'Product IDs (comma separated)', 'hooshyar-commerce-kit' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'condition'   => array( 'source' => 'ids' ),
				'placeholder' => '12, 45, 78',
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Limit', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => __( 'Order by', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => __( 'Date', 'hooshyar-commerce-kit' ),
					'title'         => __( 'Title', 'hooshyar-commerce-kit' ),
					'menu_order'    => __( 'Menu order', 'hooshyar-commerce-kit' ),
					'popularity'    => __( 'Popularity', 'hooshyar-commerce-kit' ),
					'rating'        => __( 'Rating', 'hooshyar-commerce-kit' ),
					'rand'          => __( 'Random', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => __( 'Descending', 'hooshyar-commerce-kit' ),
					'ASC'  => __( 'Ascending', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Display mode', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'     => __( 'Grid', 'hooshyar-commerce-kit' ),
					'carousel' => __( 'Carousel', 'hooshyar-commerce-kit' ),
					'slider'   => __( 'Slider (one at a time)', 'hooshyar-commerce-kit' ),
					'list'     => __( 'List', 'hooshyar-commerce-kit' ),
					'masonry'  => __( 'Masonry', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'template',
			array(
				'label'   => __( 'Card template', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'boxed',
				'options' => HCK_Settings::card_options(),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'hooshyar-commerce-kit' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '4',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'tablet_default' => '3',
				'mobile_default' => '2',
			)
		);

		$this->add_control(
			'show_image',
			array(
				'label'   => __( 'Show image', 'hooshyar-commerce-kit' ),
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
			'show_price',
			array(
				'label'   => __( 'Show price', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'   => __( 'Show short description', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_badges',
			array(
				'label'   => __( 'Show badges', 'hooshyar-commerce-kit' ),
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
			'hover_effect',
			array(
				'label'   => __( 'Hover effect', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => array(
					'zoom'  => __( 'Image zoom', 'hooshyar-commerce-kit' ),
					'slide' => __( 'Image slide', 'hooshyar-commerce-kit' ),
					'lift'  => __( 'Card lift', 'hooshyar-commerce-kit' ),
					'none'  => __( 'None', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->end_controls_section();

		// Carousel settings.
		$this->start_controls_section(
			'section_carousel',
			array(
				'label'     => __( 'Carousel Settings', 'hooshyar-commerce-kit' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => array( 'layout' => array( 'carousel', 'slider' ) ),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay speed (ms)', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 4500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Infinite loop', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'   => __( 'Navigation', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'arrows-dots',
				'options' => array(
					'arrows-dots' => __( 'Arrows & dots', 'hooshyar-commerce-kit' ),
					'arrows'      => __( 'Arrows only', 'hooshyar-commerce-kit' ),
					'dots'        => __( 'Dots only', 'hooshyar-commerce-kit' ),
					'progress'    => __( 'Progress bar', 'hooshyar-commerce-kit' ),
					'none'        => __( 'None', 'hooshyar-commerce-kit' ),
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition speed (ms)', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 600,
			)
		);

		$this->end_controls_section();

		/* -----------------------------------------------------------------
		 * Style: cards
		 * ---------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => __( 'Card Style', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => __( 'Background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .hck-card__inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-card__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => __( 'Border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-card__inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'label'    => __( 'Border', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-card__inner',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => __( 'Shadow', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-card__inner',
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => __( 'Gap between cards', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-products' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style: image.
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __( 'Image', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_ratio',
			array(
				'label'   => __( 'Image aspect ratio', 'hooshyar-commerce-kit' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '1/1',
				'options' => array(
					'1/1'  => '1:1',
					'4/3'  => '4:3',
					'3/4'  => '3:4',
					'16/9' => '16:9',
				),
				'selectors' => array(
					'{{WRAPPER}} .hck-card__image' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => __( 'Image border radius', 'hooshyar-commerce-kit' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hck-card__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style: title.
		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => __( 'Title', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-card__title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => __( 'Hover color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-card__title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-card__title',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => __( 'Price color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-card .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => __( 'Price typography', 'hooshyar-commerce-kit' ),
				'selector' => '{{WRAPPER}} .hck-card .price',
			)
		);

		$this->end_controls_section();

		// Style: button.
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_button_style_controls( $this, '{{WRAPPER}} .hck-card__btn', 'card_btn' );

		$this->end_controls_section();

		// Style: carousel navigation.
		$this->start_controls_section(
			'section_style_nav',
			array(
				'label'     => __( 'Carousel Navigation', 'hooshyar-commerce-kit' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout' => array( 'carousel', 'slider' ) ),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Arrow color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-carousel__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Arrow background', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-carousel__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Dot color', 'hooshyar-commerce-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hck-carousel__dot' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .hck-carousel__dot.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Entrance animation.
		$this->start_controls_section(
			'section_style_fx',
			array(
				'label' => __( 'Effects', 'hooshyar-commerce-kit' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_animation_controls( $this, '{{WRAPPER}} .hck-products' );

		$this->end_controls_section();
	}

	/**
	 * Render the widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$products = HCK_Helpers::get_products(
			array(
				'limit'    => $settings['limit'],
				'offset'   => $settings['offset'],
				'orderby'  => $settings['orderby'],
				'order'    => $settings['order'],
				'source'   => $settings['source'],
				'category' => isset( $settings['category'] ) ? $settings['category'] : '',
				'ids'      => isset( $settings['ids'] ) ? $settings['ids'] : '',
			)
		);

		if ( empty( $products ) ) {
			echo '<p class="hck-no-products">' . esc_html__( 'No products found.', 'hooshyar-commerce-kit' ) . '</p>';
			return;
		}

		$is_slider   = in_array( $settings['layout'], array( 'carousel', 'slider' ), true );
		$columns     = 'slider' === $settings['layout'] ? 1 : (int) $settings['columns'];
		$columns_tab = 'slider' === $settings['layout'] ? 1 : max( 1, (int) $settings['columns'] - 1 );
		$columns_mob = 1;

		$wrapper_class = 'hck-products';
		$wrapper_class .= ' hck-products--layout-' . sanitize_html_class( $settings['layout'] );
		$wrapper_class .= ' hck-products--card-' . sanitize_html_class( $settings['template'] );
		$wrapper_class .= ' hck-products--hover-' . sanitize_html_class( $settings['hover_effect'] );

		$wrapper_attrs = '';
		if ( $is_slider ) {
			$wrapper_attrs = sprintf(
				' data-hck-carousel data-autoplay="%s" data-speed="%s" data-loop="%s" data-nav="%s" data-columns="%d" data-columns-tablet="%d" data-columns-mobile="%d"',
				esc_attr( $settings['autoplay'] ),
				esc_attr( $settings['autoplay_speed'] ),
				esc_attr( $settings['loop'] ),
				esc_attr( $settings['navigation'] ),
				$columns,
				$columns_tab,
				$columns_mob
			);
		}

		echo '<div class="hck-carousel" ' . $wrapper_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		foreach ( $products as $product ) {
			echo HCK_Product::render_card( // phpcs:ignore WordPress.Security.EscapeOutput
				$product,
				array(
					'card_style'   => $settings['template'],
					'hover'        => $settings['hover_effect'],
					'show_image'   => 'yes' === $settings['show_image'],
					'show_rating'  => 'yes' === $settings['show_rating'],
					'show_price'   => 'yes' === $settings['show_price'],
					'show_excerpt' => 'yes' === $settings['show_excerpt'],
					'show_badges'  => 'yes' === $settings['show_badges'],
					'show_button'  => 'yes' === $settings['show_button'],
				)
			);
		}

		echo '</div>';

		if ( $is_slider ) {
			echo '<button type="button" class="hck-carousel__arrow hck-carousel__arrow--prev" aria-label="' . esc_attr__( 'Previous', 'hooshyar-commerce-kit' ) . '">' . HCK_Helpers::icon( 'arrow-r', array( 'size' => 18 ) ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<button type="button" class="hck-carousel__arrow hck-carousel__arrow--next" aria-label="' . esc_attr__( 'Next', 'hooshyar-commerce-kit' ) . '">' . HCK_Helpers::icon( 'arrow-l', array( 'size' => 18 ) ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
			echo '<div class="hck-carousel__dots"></div>';
		}

		echo '</div>';
	}

	/**
	 * Plain content template for the editor preview.
	 */
	protected function content_template() {
		?>
		<div class="hck-products-placeholder"><?php esc_html_e( 'HCK Products — preview is rendered live.', 'hooshyar-commerce-kit' ); ?></div>
		<?php
	}
}
