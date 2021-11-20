<?php

namespace MartfuryAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Banner Small widget
 */
class Banner_Large_2 extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'martfury-banner-large-2';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Banner Large 2', 'martfury' );
	}


	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-banner';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'martfury' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->_register_banner_controls();
		$this->_register_discount_box_controls();
		$this->_register_content_box_controls();
		$this->_register_price_box_controls();
	}

	protected function _register_banner_controls() {
		$this->start_controls_section(
			'section_banner',
			[ 'label' => esc_html__( 'Banner', 'martfury' ) ]
		);
		$this->add_responsive_control(
			'height',
			[
				'label'     => esc_html__( 'Height', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 191,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2' => 'height: {{SIZE}}{{UNIT}};',
				],

			]
		);
		$this->add_control(
			'banner_url', [
				'label'         => esc_html__( 'URL', 'martfury' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'martfury' ),
				'show_external' => true,
				'default'       => [
					'url'         => '',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'banners_background',
				'label'          => __( 'Background', 'martfury' ),
				'types'          => [ 'classic', 'gradient' ],
				'selector'       => '{{WRAPPER}} .mf-elementor-banner-large-2 .feature-image',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'image'      => [
						'default' => [
							'url' => 'https://via.placeholder.com/1650x191/f8f8f8?text=1650x191+Image',
						],
					],
				],
				'separator'      => 'before',
			]
		);
		$this->end_controls_section();
	}

	protected function _register_discount_box_controls() {
		$this->start_controls_section(
			'section_discount_box',
			[ 'label' => esc_html__( 'Discount Box', 'martfury' ) ]
		);
		$this->add_control(
			'discount_text',
			[
				'label'       => esc_html__( 'Discount Text', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '20%',
				'label_block' => true,
			]
		);
		$this->add_control(
			'discount_subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Discount', 'martfury' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'discount_title',
			[
				'label'       => esc_html__( 'Title', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Limited Stock Only', 'martfury' ),
				'label_block' => true,
			]
		);
		$this->end_controls_section(); // End Content

		// Style
		$this->start_controls_section(
			'section_discount_box_style',
			[
				'label' => __( 'Discount Box', 'martfury' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'show_discount_box',
			[
				'label'     => esc_html__( 'Show', 'martfury' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'block' => esc_html__( 'Show', 'martfury' ),
					'none'  => esc_html__( 'Hide', 'martfury' ),
				],
				'default'   => 'block',
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box' => 'display: {{VALUE}}',

				],
			]
		);

		$discount = [
			'element' => 'discount_box',
			'selector' => '.mf-elementor-banner-large-2 .discount-box',
		];

		$this->element_position_handler( $discount );

		$this->add_control(
			'divider_2',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'discount_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'martfury' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('discount_box_style_tabs');

		// Discount
		$this->start_controls_tab(
			'style_discount_tab',
			[
				'label' => __( 'Discount', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'discount_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount',
			]
		);
		$this->add_control(
			'discount_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'discount_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		// Subtitle
		$this->start_controls_tab(
			'style_discount_subtitle_tab',
			[
				'label' => __( 'Subtitle', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'discount_subtitle_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount-subtitle',
			]
		);
		$this->add_control(
			'discount_subtitle_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount-subtitle' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'discount_subtitle_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		// Title
		$this->start_controls_tab(
			'style_discount_title_tab',
			[
				'label' => __( 'Title', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'discount_title_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount-title',
			]
		);
		$this->add_control(
			'discount_title_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .discount-box .discount-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function _register_content_box_controls() {
		$this->start_controls_section(
			'section_content_box',
			[ 'label' => esc_html__( 'Content Box', 'martfury' ) ]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'martfury' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'This is the title', 'martfury' ),
				'placeholder' => esc_html__( 'Enter your title', 'martfury' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'martfury' ),
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => __( 'This is the description', 'martfury' ),
				'placeholder' => esc_html__( 'Enter your description', 'martfury' ),
				'label_block' => true,
			]
		);
		$this->end_controls_section(); // End Content

		// Style
		$this->start_controls_section(
			'section_content_box_style',
			[
				'label' => __( 'Content Box', 'martfury' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'show_content_box',
			[
				'label'     => esc_html__( 'Show', 'martfury' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'block' => esc_html__( 'Show', 'martfury' ),
					'none'  => esc_html__( 'Hide', 'martfury' ),
				],
				'default'   => 'block',
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .content-box' => 'display: {{VALUE}}',

				],
			]
		);

		$content = [
			'element' => 'content_box',
			'selector' => '.mf-elementor-banner-large-2 .content-box',
		];

		$this->element_position_handler( $content );

		$this->add_control(
			'divider_3',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'content_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'martfury' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .content-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('content_box_style_tabs');

		// Title
		$this->start_controls_tab(
			'style_title_tab',
			[
				'label' => __( 'Title', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .content-box .title',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .content-box .title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .content-box .title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		// Description
		$this->start_controls_tab(
			'style_desc_tab',
			[
				'label' => __( 'Description', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .content-box .description',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .content-box .description' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function _register_price_box_controls() {
		$this->start_controls_section(
			'section_price_box',
			[ 'label' => esc_html__( 'Price Box', 'martfury' ) ]
		);

		$this->add_control(
			'regular_price',
			[
				'label'       => esc_html__( 'Regular Price', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '$160.50',
				'label_block' => true,
			]
		);
		$this->add_control(
			'sale_price',
			[
				'label'       => esc_html__( 'Regular Price', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '$89.05',
				'label_block' => true,
			]
		);
		$this->add_control(
			'divider_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'link_text', [
				'label'       => esc_html__( 'Button Text', 'martfury' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Shop Now', 'martfury' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'link_url', [
				'label'         => esc_html__( 'Button URL', 'martfury' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'martfury' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);
		$this->end_controls_section(); // End Content

		// Style
		$this->start_controls_section(
			'section_price_box_style',
			[
				'label' => __( 'Price Box', 'martfury' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'show_price_box',
			[
				'label'     => esc_html__( 'Show', 'martfury' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'block' => esc_html__( 'Show', 'martfury' ),
					'none'  => esc_html__( 'Hide', 'martfury' ),
				],
				'default'   => 'block',
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box' => 'display: {{VALUE}}',

				],
			]
		);

		$price = [
			'element' => 'price_box',
			'selector' => '.mf-elementor-banner-large-2 .price-box',
		];

		$this->element_position_handler( $price );

		$this->add_control(
			'divider_4',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'price_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'martfury' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('price_box_style_tabs');

		// Regular Price
		$this->start_controls_tab(
			'style_regular_price_tab',
			[
				'label' => __( 'Regular Price', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'regular_price_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .regular-price',
			]
		);
		$this->add_control(
			'regular_price_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .regular-price' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'regular_price_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .regular-price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		// Sale Price
		$this->start_controls_tab(
			'style_sale_price_tab',
			[
				'label' => __( 'Sale Price', 'martfury' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .sale-price',
			]
		);
		$this->add_control(
			'sale_price_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .sale-price' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'sale_price_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'martfury' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ ],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .sale-price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_style',
			[
				'label'     => esc_html__( 'Button', 'martfury' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'button_border',
				'label'     => __( 'Border', 'martfury' ),
				'selector'  => '{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button',
			]
		);
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'martfury' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'divider_5',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button',
			]
		);
		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'martfury' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('button_style_tabs');

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __( 'Normal', 'martfury' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'button_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Hover', 'martfury' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'button_hover_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'martfury' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mf-elementor-banner-large-2 .price-box .banner-button:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function element_position_handler($args) {
		$selector = $args['selector'];

		$this->add_responsive_control(
			$args['element'] . '_horizontal_content',
			[
				'label'   => __( 'Horizontal Content', 'martfury' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'martfury' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'martfury' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'martfury' ),
						'icon'  => 'eicon-h-align-right',
					],
					'custom' => [
						'title' => __( 'Custom', 'martfury' ),
						'icon'  => 'eicon-custom',
					],
				],
				'desktop_default'      => 'custom',
				'tablet_default'       => 'custom',
				'mobile_default'       => 'custom',
				'toggle'               => true,
				'selectors_dictionary' => [
					'left'   => 'left: 0; right: auto;',
					'center' => 'left: 50%; right: auto;',
					'right'  => 'left: auto; right: 0;',
				],
				'selectors'  => [
					"{{WRAPPER}} $selector" => '{{VALUE}}',
				]
			]
		);
		$this->add_responsive_control(
			$args['element'] . '_vertical_content',
			[
				'label'   => __( 'Vertical Content', 'martfury' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => __( 'Top', 'martfury' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'martfury' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'martfury' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'custom' => [
						'title' => __( 'Custom', 'martfury' ),
						'icon'  => 'eicon-custom',
					],
				],
				'desktop_default'      => 'middle',
				'tablet_default'       => 'middle',
				'mobile_default'       => 'middle',
				'toggle'               => true,
				'selectors_dictionary' => [
					'top'    => 'top: 0; bottom: auto;',
					'middle' => 'top: 50%; bottom: auto;',
					'bottom' => 'top: auto; bottom: 0;',
				],
				'selectors'  => [
					"{{WRAPPER}} $selector" => '{{VALUE}}',
				]
			]
		);
			// Custom Position
		$this->add_responsive_control(
			$args['element'] . '_position_x',
			[
				'label'      => __( 'Horizontal Offset', 'martfury' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_horizontal_content' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'left: {{SIZE}}{{UNIT}}',
						],
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_horizontal_content_tablet' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'left: {{SIZE}}{{UNIT}}',
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_horizontal_content_mobile' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'left: {{SIZE}}{{UNIT}}',
						],
					],
				]
			]
		);

		$this->add_responsive_control(
			$args['element'] . '_position_y',
			[
				'label'      => __( 'Vertical Offset', 'martfury' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_vertical_content' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'top: {{SIZE}}{{UNIT}}',
						],
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_vertical_content_tablet' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'top: {{SIZE}}{{UNIT}}',
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_vertical_content_mobile' => 'custom',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => 'top: {{SIZE}}{{UNIT}}',
						],
					],
				]
			]
		);
		$this->add_responsive_control(
			$args['element'] . '_css_1',
			[
				'label'                => __( 'Banner Content CSS', 'martfury' ),
				'type'                 => Controls_Manager::HIDDEN,
				'desktop_default'      => 'center',
				'tablet_default'       => 'center',
				'mobile_default'       => 'center',
				'selectors_dictionary' => [
					'center' => 'transform: translate(-50%,-50%)',
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_horizontal_content' => 'center',
							$args['element'] . '_vertical_content'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_horizontal_content_tablet' => 'center',
							$args['element'] . '_vertical_content_tablet'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_horizontal_content_mobile' => 'center',
							$args['element'] . '_vertical_content_mobile'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
				]
			]
		);
		$this->add_responsive_control(
			$args['element'] . '_css_2',
			[
				'label'                => __( 'Banner Content CSS', 'martfury' ),
				'type'                 => Controls_Manager::HIDDEN,
				'desktop_default'      => 'center_y',
				'tablet_default'       => 'center_y',
				'mobile_default'       => 'center_y',
				'selectors_dictionary' => [
					'center_y' => 'transform: translate(0,-50%)',
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_horizontal_content!' => 'center',
							$args['element'] . '_vertical_content'    => 'middle',
						],
						'selectors'  => [
							'{{WRAPPER}} .banner-layer-wrapper {{CURRENT_ITEM}}' => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_horizontal_content_tablet!' => 'center',
							$args['element'] . '_vertical_content_tablet'    => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_horizontal_content_mobile!' => 'center',
							$args['element'] . '_vertical_content_mobile'    => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
				]
			]
		);
		$this->add_responsive_control(
			$args['element'] . '_css_3',
			[
				'label'                => __( 'Banner Content CSS', 'martfury' ),
				'type'                 => Controls_Manager::HIDDEN,
				'desktop_default'      => 'center_x',
				'tablet_default'       => 'center_x',
				'mobile_default'       => 'center_x',
				'selectors_dictionary' => [
					'center_x' => 'transform: translate(-50%,0)',
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_horizontal_content' => 'center',
							$args['element'] . '_vertical_content!'  => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_horizontal_content_tablet' => 'center',
							$args['element'] . '_vertical_content_tablet!'  => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_horizontal_content_mobile' => 'center',
							$args['element'] . '_vertical_content_mobile!'  => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
				]
			]
		);
		$this->add_responsive_control(
			$args['element'] . '_css_4',
			[
				'label'                => __( 'Banner Content CSS', 'martfury' ),
				'type'                 => Controls_Manager::HIDDEN,
				'desktop_default'      => 'no_center',
				'tablet_default'       => 'no_center',
				'mobile_default'       => 'no_center',
				'selectors_dictionary' => [
					'no_center' => 'transform: unset',
				],
				'device_args'          => [
					Controls_Stack::RESPONSIVE_DESKTOP => [
						'condition' => [
							$args['element'] . '_horizontal_content!' => 'center',
							$args['element'] . '_vertical_content!'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_TABLET  => [
						'condition' => [
							$args['element'] . '_horizontal_content_tablet!' => 'center',
							$args['element'] . '_vertical_content_tablet!'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
					Controls_Stack::RESPONSIVE_MOBILE  => [
						'condition' => [
							$args['element'] . '_horizontal_content_mobile!' => 'center',
							$args['element'] . '_vertical_content_mobile!'   => 'middle',
						],
						'selectors'  => [
							"{{WRAPPER}} $selector" => '{{VALUE}}',
						]
					],
				]
			]
		);
	}

	/**
	 * Render icon box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', [
			'mf-elementor-banner-large-2'
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php

			echo $this->get_link_control( 'banner_link', $settings['banner_url'], '', [ 'class' => 'feature-image' ] );

			?>
			<div class="banner-wrapper">
				<div class="discount-box">
					<div class="discount"><?php echo $settings['discount_text'] ?></div>
					<div class="discount-subtitle"><?php echo $settings['discount_subtitle'] ?></div>
					<div class="discount-title"><?php echo $settings['discount_title'] ?></div>
				</div>
				<div class="content-box">
					<h4 class="title"><?php echo $settings['title'] ?></h4>
					<div class="description"><?php echo $settings['description'] ?></div>
				</div>
				<div class="price-box">
					<div class="regular-price"><?php echo $settings['regular_price'] ?></div>
					<div class="sale-price"><?php echo $settings['sale_price'] ?></div>
					<?php echo $this->get_link_control( 'banner_button', $settings['link_url'], $settings['link_text'], [ 'class' => 'banner-button' ] ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render icon box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected
	function _content_template() {

	}

	/**
	 * Get the link control
	 *
	 * @return string.
	 */
	protected function get_link_control( $link_key, $url, $content, $attr = [ ] ) {
		$attr_default = [ ];
		if ( isset( $url['url'] ) && $url['url'] ) {
			$attr_default['href'] = $url['url'];
		}

		if ( isset( $url['is_external'] ) && $url['is_external'] ) {
			$attr_default['target'] = '_blank';
		}

		if ( isset( $url['nofollow'] ) && $url['nofollow'] ) {
			$attr_default['rel'] = 'nofollow';
		}

		$tag = 'a';

		if ( empty( $attr_default['href'] ) ) {
			$tag = 'span';
		}

		$attr = wp_parse_args( $attr, $attr_default );

		$this->add_render_attribute( $link_key, $attr );

		return sprintf( '<%1$s %2$s>%3$s</%1$s>', $tag, $this->get_render_attribute_string( $link_key ), $content );
	}
}