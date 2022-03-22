<?php
/**
 * Course review widget class.
 *
 * @author   ThimPress
 * @package  Learnpress/Widgets
 * @version  1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Creating the widget
class LP_Widget_Course_Review extends LP_Widget {

	/**
	 * LP_Widget_Course_Review.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'learnpress widget_course_review';
		$this->widget_description = esc_html__( 'Display the Course Review', 'learnpress' );
		$this->widget_id          = 'learnpress_widget_course_review';
		$this->widget_name        = esc_html__( 'LearnPress - Course Review', 'learnpress' );
		$this->settings           = array(
			'title'       => array(
				'label' => esc_html__( 'Title', 'learnpress' ),
				'type'  => 'text',
				'std'   => esc_html__( 'Course Review', 'learnpress' ),
			),
			'course_id'   => array(
				'label'     => esc_html__( 'Select Course', 'learnpress' ),
				'type'      => 'autocomplete',
				'post_type' => LP_COURSE_CPT,
				'std'       => '',
			),
			'option_show' => array(
				'label'   => esc_html__( 'Option show', 'learnpress' ),
				'type'    => 'select',
				'options' => array(
					'show_all'    => esc_html__( 'Show all', 'learnpress' ),
					'show_rate'   => esc_html__( 'Show ratings', 'learnpress' ),
					'show_review' => esc_html__( 'Show reviews', 'learnpress' ),
				),
				'std'     => 'show_all',
			),
			'amount'      => array(
				'label' => esc_html__( 'Amount Display', 'learnpress' ),
				'type'  => 'number',
				'min'   => 1,
				'max'   => LP_COURSE_REVIEW_PER_PAGE,
				'std'   => 1,
			),
		);

		parent::__construct();
	}

	public function lp_rest_api_content( $instance, $params ) {

		if ( LP_Settings::get_option( 'course_review' ) == 'no' && ! class_exists( 'LP_Addon_Course_Review' ) ) {
			return new WP_Error( 'no_option', esc_html__( 'Error: Please enable option Review Course in Settings LearnPress.', 'learnpress' ) );
		}

		if ( ! empty( $instance['course_id'] ) ) {
			$course_id = absint( $instance['course_id'] );
			// show rate course widget
			$course_rate = learn_press_get_course_rate( $course_id, false );

			// show review course widget
			$amount        = ! empty( $instance['amount'] ) ? $instance['amount'] : 1;
			$course_review = LP_Course_Reviews_DB::getInstance()->learn_press_get_course_review( $course_id, 1, $amount );

			if ( $course_rate['total'] ) {
				if ( ! empty( $instance['option_show'] ) ) {
					switch ( $instance['option_show'] ) {
						case 'show_rate':
							return learn_press_get_template_content(
								'widgets/course-review/widget-course-rate.php',
								array(
									'course_rate' => $course_rate,
									'course_id'   => $course_id,
								)
							);
							break;
						case 'show_review':
							return learn_press_get_template_content(
								'widgets/course-review/widget-course-review.php',
								array(
									'course_review' => $course_review,
									'course_id'     => $course_id,
								)
							);
							break;
						default:
							return learn_press_get_template_content(
								'widgets/course-review/widget-reviews.php',
								array(
									'course_id'     => $course_id,
									'course_review' => $course_review,
									'course_rate'   => $course_rate,
								)
							);
							break;
					}
				}
			} else {
				return new WP_Error( 'no_data', esc_html__( 'Error: Course is not review.', 'learnpress' ) );
			}
		}

		return new WP_Error( 'no_params', esc_html__( 'Error: Please select a Course.', 'learnpress' ) );
	}
}
