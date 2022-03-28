<?php
/**
 * Template for displaying list course wishlist in wishlist tab of user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/wishlist/list.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();
if ( empty( $user_id ) || empty( $courses ) || empty( $current_page ) || empty( $num_pages ) || empty( $per_page ) ) {
	return;
}

?>
<?php if ( $current_page === 1 ) : ?>
<div class="lp-archive-courses">
	<ul <?php lp_item_course_class( array( 'profile-courses-list' ) ); ?> data-layout="grid" data-size="3">
	<?php endif; ?>
		<?php
		global $post;

		foreach ( $courses as $course_item ) {

			$course = learn_press_get_course( $course_item['id'] );
			$post   = get_post( $course_item['id'] );
			setup_postdata( $post );

			learn_press_get_template( 'content-course.php' );
		}

		wp_reset_postdata();
		?>
		<?php if ( $current_page === 1 ) : ?>
	</ul>
</div>

<?php endif; ?>

<?php if ( $num_pages > 1 && $current_page < $num_pages && $current_page === 1 ) : ?>
	<div class="lp_profile_course_progress__nav">
		<button class="lp-button view-more-wishlist" data-paged-wishlist="<?php echo absint( $current_page + 1 ); ?>"
				data-number="<?php echo absint( $num_pages ); ?>" data-per-page ="<?php echo absint( $per_page ); ?>">
			<?php esc_html_e( 'View more', 'learnpress' ); ?>
		</button>
	</div>
<?php endif; ?>
