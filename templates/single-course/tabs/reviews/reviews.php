<?php
/**
 * Template for displaying reivews tab of single course.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit();

if ( empty( $course_id ) || empty( $user ) || empty( $course_rate_res ) || empty( $course_review ) ) {
	return;
}
$rated = $course_rate_res['rated'];
$total = $course_rate_res['total'];
?>


<?php
// Load template show count review
learn_press_get_template(
	'single-course/tabs/reviews/count-rated.php',
	array(
		'rated'           => $rated,
		'course_rate_res' => $course_rate_res,
		'total'           => $total,
	)
);
?>
<?php
if ( $course_review['total'] ) {
	$reviews = $course_review['reviews'];
	$pages   = $course_review['pages'];
	?>
	<?php
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
	?>
	<?php
}

if ( $user->has_course_status( $course_id, array( 'enrolled', 'completed', 'finished' ) ) ) {
	if ( ! LP_Course_Reviews_DB::getInstance()->learn_press_get_user_rate( $course_id ) ) {
		?>
			<div class="course-review-wrapper" id="course-review">
				<div class="review-form" id="review-form">
					<form>
						<h3>
							<?php _e( 'Write a review', 'learnpress' ); ?>
						</h3>
						<ul class="review-fields">
							<?php do_action( 'learn_press_before_review_fields' ); ?>
							<li>
								<label><?php _e( 'Title', 'learnpress' ); ?> <span class="required">*</span></label>
								<input type="text" name="review_title"/>
							</li>
							<li>
								<label><?php _e( 'Content', 'learnpress' ); ?><span class="required">*</span></label>
								<textarea name="review_content"></textarea>
							</li>
							<li>
								<label><?php _e( 'Rating', 'learnpress' ); ?><span class="required">*</span></label>
								<ul class="review-stars">
									<?php for ( $i = 1; $i <= 5; $i ++ ) { ?>
										<li class="review-title" title="<?php echo $i; ?>">
											<span class="dashicons dashicons-star-empty"></span></li>
									<?php } ?>
								</ul>
							</li>
							<?php do_action( 'learn_press_after_review_fields' ); ?>
							<li class="review-actions">
								<button type="button" class="lp-button submit-review"
										data-id="<?php echo $course_id; ?>"><?php _e( 'Add review', 'learnpress' ); ?></button>
								<span class="ajaxload"></span>
								<span class="error"></span>
								<?php wp_nonce_field( 'learn_press_course_review_' . get_the_ID(), 'review-nonce' ); ?>
								<input type="hidden" name="rating" value="0">
								<input type="hidden" name="lp-ajax" value="add_review">
								<input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>">
								<input type="hidden" name="empty_title" value="<?php echo __( 'Please enter the review title', 'learnpress' ); ?>">
								<input type="hidden" name="empty_content" value="<?php echo __( 'Please enter the review content', 'learnpress' ); ?>">
								<input type="hidden" name="empty_rating" value="<?php echo __( 'Please select your rating', 'learnpress' ); ?>">
							</li>
						</ul>
					</form>
				</div>
			</div>
		<?php
	}
}

$args           = array(
	'user_id' => learn_press_get_current_user_id(),
	'post_id' => $course_id,
);
$comments_count = get_comments( $args );

if ( ! empty( $comments_count ) && ! $comments_count[0]->comment_approved ) {
	echo '<div class="learn-press-message success">Your review has been submitted and is awaiting approve. </div>';
}
