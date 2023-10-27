<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\SkinBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class CourseGrid extends SkinBase {

	public function get_id() {
		return 'grid';
	}

	public function get_title() {
		return esc_html__( 'Grid', 'learnpress' );
	}

	public function get_container_class() {
		return 'learnpress-el-list-courses--skin-grid';
	}

	public function register_controls( Widget_Base $widget, $args ) {
		$this->parent = $widget;

		parent::register_controls( $widget, $args );

		$this->register_control_thumnail();
		$this->register_title_controls();
		$this->register_price_controls();
		$this->register_excerpt_controls();
		$this->register_meta_data_controls();
		$this->register_read_more();
	}

	public function register_style_sections( Widget_Base $widget, $args ) {
		parent::register_style_sections( $widget, $args );

		$this->register_style_item();
		$this->register_style_image();
		$this->register_style_content();
		$this->register_style_pagination();
	}
}
