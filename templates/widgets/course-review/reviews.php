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

if ( ! $course_review || ! $course_rate || ! $course_id ) {
	return;
}

learn_press_get_template(
	'widgets/course-review/course-rate.php',
	array(
		'course_id'   => $course_id,
		'course_rate' => $course_rate,
	)
);
learn_press_get_template(
	'widgets/course-review/course-review.php',
	array(
		'course_id'     => $course_id,
		'course_review' => $course_review,
		'course_rate'   => $course_rate,
	)
);

