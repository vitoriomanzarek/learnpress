<?php

/**
 * Students list Course Widget.
 *
 * @author   ThimPress
 * @category Widgets
 * @package  Learnpress/Widgets
 * @version  4.0.0
 * @extends  LP_Widget
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Widget_Students_List' ) ) {

	/**
	 * Class LP_Widget_Students_List
	 */
	class LP_Widget_Students_List extends LP_Widget {

		/**
		 * LP_Widget_Students_List constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'learnpress widget_student_list';
			$this->widget_description = esc_html__( 'Display the Students List', 'learnpress' );
			$this->widget_id          = 'learnpress_widget_student_list';
			$this->widget_name        = esc_html__( 'LearnPress - Students List', 'learnpress' );
			$this->settings           = array(
				'title'     => array(
					'label' => esc_html__( 'Title', 'learnpress' ),
					'type'  => 'text',
					'std'   => esc_html__( 'Students List', 'learnpress' ),
				),
				'course_id' => array(
					'label'     => esc_html__( 'Select Course', 'learnpress' ),
					'type'      => 'autocomplete',
					'post_type' => LP_COURSE_CPT,
					'std'       => '',
				),
				'limit'     => array(
					'label' => esc_html__( 'Limit', 'learnpress' ),
					'type'  => 'number',
					'min'   => 1,
					'max'   => 5,
					'std'   => 1,
				),
				'filter'    => array(
					'label'   => esc_html__( 'Filter', 'learnpress' ),
					'type'    => 'select',
					'options' => array(
						''         => esc_html__( 'All', 'learnpress' ),
						'enrolled' => esc_html__( 'In Progress', 'learnpress' ),
						'finished' => esc_html__( 'Finished', 'learnpress' ),
					),
					'std'     => '',
				),
			);

			parent::__construct();
		}

		public function lp_rest_api_content( $instance, $params ) {

			if ( ! empty( $instance['course_id'] ) ) {

				$course_id = $instance['course_id'];
				$course    = learn_press_get_course( $instance['course_id'] );

				$students = LP_User_Items_DB::getInstance()->get_user_ids_attend_courses(
					$course_id,
					array(
						'limit'  => absint( $instance['limit'] ),
						'status' => $instance['filter'],
					)
				);

				if ( $students ) {
					return learn_press_get_template_content(
						'widgets/student-list.php',
						array(
							'course'   => $course,
							'students' => $students,
						),
					);
				} else {
					return new WP_Error( 'no_params', esc_html__( 'Error: No user enroll Course.', 'learnpress' ) );
				}
			}

			return new WP_Error( 'no_params', esc_html__( 'Error: Please select a Course.', 'learnpress' ) );
		}
	}
}
