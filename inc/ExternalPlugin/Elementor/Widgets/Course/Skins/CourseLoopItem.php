<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\SkinBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Thim_EL_Kit\Utilities\Elementor as Thim_EL_Utilities;
use Thim_EL_Kit\Functions as Thim_EL_Functions;

class CourseLoopItem extends SkinBase {

	public function get_id() {
		return 'loop-item';
	}

	public function get_title() {
		return esc_html__( 'Loop Item', 'learnpress' );
	}

	public function get_container_class() {
		return 'learnpress-el-list-courses--skin-loop';
	}

	public function register_controls( Widget_Base $widget, $args ) {
		$this->parent = $widget;

		parent::register_controls( $widget, $args );

		$this->register_loop_item();
	}

	public function register_style_sections( Widget_Base $widget, $args ) {
		parent::register_style_sections( $widget, $args );

		$this->register_style_pagination();
	}

	public function register_loop_item() {
		$this->add_control(
			'template_id',
			array(
				'label'         => esc_html__( 'Choose a template', 'thim-elementor-kit' ),
				'type'          => Controls_Manager::SELECT2,
				'default'       => '0',
				'options'       => array( '0' => esc_html__( 'None', 'thim-elementor-kit' ) ) + Thim_EL_Functions::instance()->get_pages_loop_item( 'lp_course' ),
				'prevent_empty' => false,
			)
		);
	}

	protected function render_course() {
		$id = $this->get_instance_value( 'template_id' );

		Thim_EL_Utilities::instance()->render_loop_item_content( $id );
	}
}
