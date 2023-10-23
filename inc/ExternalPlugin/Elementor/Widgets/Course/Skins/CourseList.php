<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\SkinBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class CourseList extends SkinBase {

	public function get_id() {
		return 'list';
	}

	public function get_title() {
		return esc_html__( 'List', 'learnpress' );
	}

	public function register_controls( Widget_Base $widget, $args ) {
		$this->parent = $widget;

		parent::register_controls( $widget, $args );

		$this->register_control_thumnail();
		$this->register_title_controls();
		$this->register_excerpt_controls();
		$this->register_read_more();
	}

	protected function register_control_thumnail() {
		$this->add_responsive_control(
			'item_ratio',
			[
				'label' => esc_html__( 'Image Ratio', 'learnpress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
				],
				'tablet_default' => [
					'size' => '',
				],
				'mobile_default' => [
					'size' => 0.5,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => esc_html__( 'Image Width', 'learnpress' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
			]
		);
	}

	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Title', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'learnpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);
	}

	protected function register_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__( 'Excerpt', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label' => esc_html__( 'Excerpt Length', 'learnpress' ),
				'type' => Controls_Manager::NUMBER,
				/** This filter is documented in wp-includes/formatting.php */
				'default' => apply_filters( 'excerpt_length', 25 ),
				'condition' => [
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'apply_to_custom_excerpt',
			[
				'label' => esc_html__( 'Apply to custom Excerpt', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'learnpress' ),
				'label_off' => esc_html__( 'No', 'learnpress' ),
				'default' => 'no',
				'condition' => [
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				],
			]
		);
	}

	protected function register_read_more() {
		// Show Read More
		$this->add_control(
			'show_read_more',
			[
				'label' => esc_html__( 'Read More', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		// Read More Text
		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Read More Text', 'learnpress' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'learnpress' ),
				'condition' => [
					$this->get_control_id( 'show_read_more' ) => 'yes',
				],
			]
		);
	}
}
