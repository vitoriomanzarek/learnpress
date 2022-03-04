<?php
/**
 * Template for displaying widget reviews by course
 *
 * @author ThimPress
 * @package LearnPress/Course-Review/Templates/Widget
 * @version  1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( empty( $course_review ) || empty( $course_rate ) || empty( $course_id ) ) {
	return;
}

learn_press_get_template(
	'widgets/course-review/widget-course-rate.php',
	array(
		'course_id'   => $course_id,
		'course_rate' => $course_rate,
	)
);
learn_press_get_template(
	'widgets/course-review/widget-course-review.php',
	array(
		'course_id'     => $course_id,
		'course_review' => $course_review,
		'course_rate'   => $course_rate,
	)
);

