<?php
/**
 * Template for displaying widget course review.
 *
 * @author ThimPress
 * @package LearnPress/Course-Review/Templates/Widget
 * @version 1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
if ( ! $course_review || ! $course_rate || ! $course_id ) {
	return;
}

if ( $course_review['total'] ) {
	$reviews = $course_review['reviews'];
	$pages   = $course_review['pages'];
	learn_press_get_template(
		'single-course/tabs/reviews/loop-review.php',
		array(
			'reviews'       => $reviews,
			'course_review' => $course_review,
			'course_id'     => $course_id,
			'paged'         => 1,
			'pages'         => $pages,
		)
	);
} else {
	_e( 'No review to load', 'learnpress' );
}
