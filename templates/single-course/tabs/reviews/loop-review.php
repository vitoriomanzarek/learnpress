<?php
/**
 * Template for displaying loop course review.
 *
 * @author ThimPress
 * @package LearnPress/Course-Review/Templates
 * @version  1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! $reviews || ! $course_review || ! $paged || ! $course_id || ! $pages ) {
	return;
}

?>
<?php if ( $paged === 1 ) : ?>
	<div id="course-reviews">
		<h3 class="course-review-head"><?php _e( 'Reviews', 'learnpress' ); ?></h3>
			<ul class="course-reviews-list">
			<?php endif; ?>
				<?php foreach ( $reviews as $review ) { ?>
					<?php learn_press_get_template( 'single-course/tabs/reviews/item-review.php', array( 'review' => $review ) ); ?>
				<?php } ?>
			<?php if ( $paged === 1 ) : ?>
			</ul>
		</h3>
	<?php endif; ?>
	<?php if ( empty( $course_review['finish'] ) ) { ?>
		<button class="button course-review-load-more" id="course-review-load-more"
			data-paged="<?php echo absint( $course_review['paged'] + 1 ); ?>"
			data-id ="<?php echo $course_id; ?>"
			data-number="<?php echo absint( $pages ); ?>">
			<?php esc_html_e( ' View more ', 'learnpress' ); ?>
		</button>
	<?php } ?>
	<?php if ( $paged === 1 ) : ?>
	</div>
	<?php endif; ?>
