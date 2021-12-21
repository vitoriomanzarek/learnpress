<?php

/**
 * Course Info Widget.
 *
 * @author   ThimPress
 * @category Widgets
 * @package  Learnpress/Widgets
 * @version  4.0.0
 * @extends  LP_Widget
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Widget_Course_Info' ) ) {

	/**
	 * Class LP_Widget_Course_Info
	 */
	class LP_Widget_Course_Info extends LP_Widget {

		/**
		 * LP_Widget_Course_Info constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'learnpress widget_course_info';
			$this->widget_description = esc_html__( 'Display the Course Infomation', 'learnpress' );
			$this->widget_id          = 'learnpress_widget_course_info';
			$this->widget_name        = esc_html__( 'LearnPress - Course Info', 'learnpress' );
			$this->settings           = array(
				'title'        => array(
					'label' => esc_html__( 'Title', 'learnpress' ),
					'type'  => 'text',
					'std'   => esc_html__( 'Course Info', 'learnpress' ),
				),
				'display_type' => array(
					'label'   => esc_html__( 'Display Type ', 'learnpress' ),
					'type'    => 'select',
					'options' => array(
						'current_course' => esc_html__( 'Get info by current Course when view Single course', 'learnpress' ),
						'course_id'      => esc_html__( 'Get info by select Course ID', 'learnpress' ),
					),
					'std'     => 'course_id',
				),
				'course_id'    => array(
					'label'     => esc_html__( 'Select Course', 'learnpress' ),
					'desc'      => esc_html__( 'Use for Display Type by Course ID', 'learnpress' ),
					'type'      => 'autocomplete',
					'post_type' => LP_COURSE_CPT,
					'std'       => '',
				),
				'css_class'    => array(
					'label' => esc_html__( 'CSS Class', 'learnpress' ),
					'type'  => 'text',
					'std'   => '',
				),
			);

			add_action(
				'template_redirect',
				function () {
					global $post;

					if ( $post ) {
						$this->widget_data_attr = array(
							'post_id' => $post->ID,
						);
					}
				}
			);

			parent::__construct();
		}

		public function lp_rest_api_content( $instance, $params ) {
			$instance['css_class'] = $instance['css_class'] ?? '';
			$display_type          = $instance['display_type'];

			$course_id = '';

			if ( $instance['display_type'] === 'current_course' ) {
				if ( ! empty( $params['post_id'] ) ) {
					$course_id = $params['post_id'];
				} else {
					return new WP_Error( 'no_params', esc_html__( 'Error: Please view in Single Course.', 'learnpress' ) );
				}
			} else {
				if ( ! empty( $instance['course_id'] ) ) {
					$course_id = $instance['course_id'];
				}
			}

			if ( ! empty( $course_id ) ) {
				$course = learn_press_get_course( $course_id );

				if ( $course ) {
					return learn_press_get_template_content(
						'widgets/course-info.php',
						array(
							'course'   => $course,
							'instance' => $instance,
						)
					);
				}
			}

			return new WP_Error( 'no_params', esc_html__( 'Error: Please select a Course.', 'learnpress' ) );
		}
	}
}
