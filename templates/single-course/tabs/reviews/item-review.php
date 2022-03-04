<?php
/**
 * Template for displaying item in loop course review.
 *
 * @author ThimPress
 * @package LearnPress/Course-Review/Templates
 * @version  1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( empty( $review ) ) {
	return;
}

?>

<li>
	<div class="review-author">
		<?php echo get_avatar( $review->user_email ); ?>
	</div>
	<div class="review-author-info">
		<h4 class="user-name">
			<?php do_action( 'learn_press_before_review_username' ); ?>
			<?php echo $review->display_name; ?>
			<?php do_action( 'learn_press_after_review_username' ); ?>
		</h4>
		<?php learn_press_get_template( 'single-course/tabs/reviews/rating-stars.php', array( 'rated' => $review->rate ) ); ?>
		<p class="review-title">
			<?php do_action( 'learn_press_before_review_title' ); ?>
			<?php echo $review->title; ?>
			<?php do_action( 'learn_press_after_review_title' ); ?>
		</p>
	</div>
	<div class="review-text">
		<div class="review-content">
			<?php do_action( 'learn_press_before_review_content' ); ?>
			<?php echo $review->content; ?>
			<?php do_action( 'learn_press_after_review_content' ); ?>
		</div>
	</div>
</li>
