<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\SkinBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class CourseLoopItem extends SkinBase {

	public function get_id() {
		return 'loop-item';
	}

	public function get_title() {
		return esc_html__( 'Loop Item', 'learnpress' );
	}

	public function register_controls( Widget_Base $widget, $args ) {
		$this->parent = $widget;

		parent::register_controls( $widget, $args );
	}
}
