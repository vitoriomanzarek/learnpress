<?php
/**
 * Template for displaying widget course rate.
 *
 * @author ThimPress
 * @package LearnPress/Course-Review/Templates/Widget
 * @version  1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( empty( $course_rate ) || empty( $course_id ) ) {
	return;
}

$rated = $course_rate['rated'];
$total = $course_rate['total'];
$text  = sprintf( _n( '%d rating : ', '%d ratings : ', $total, 'learnpress' ), $total );
?>

<div class="course-rate">
	<p class="review-number">
		<?php do_action( 'learn_press_before_total_review_number' ); ?>
		<?php echo $text; ?>
		<?php do_action( 'learn_press_after_total_review_number' ); ?>
	</p>
	<?php
	learn_press_get_template( 'single-course/tabs/reviews/rating-stars.php', array( 'rated' => $rated ) );
	?>
	<div class="lists-rate">
		<?php
		if ( $total > 0 && ! empty( $course_rate['items'] ) ) :
			foreach ( $course_rate['items'] as $item ) :
				?>
				<div class="detail-rate">
					<span><?php esc_html_e( $item['rated'] ); ?><?php _e( ' Star ', 'learnpress' ); ?></span>
					<span><?php esc_html_e( $item['total'] ); ?><?php _e( ' Rate ', 'learnpress' ); ?></span>
					<span><?php esc_html_e( $item['percent'] ); ?>%</span>
				</div>
				<?php
			endforeach;
		endif;
		?>
	</div>
</div>
