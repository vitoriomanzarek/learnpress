<?php
/**
 * Template for displaying button to toggle course wishlist on/off.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/buttons/wishlist.php.
 *
 * @author ThimPress
 * @package LearnPress/Wishlist/Templates
 * @version 3.0.1
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( empty( $user_id ) || empty( $course_id ) ) {
	return;
}

$state   = learn_press_user_wishlist_has_course( $course_id, $user_id ) ? 'on' : 'off';
$classes = array( 'course-wishlist' );
if ( $state == 'on' ) {
	$classes[] = 'on';
}

do_action( 'learn-press/before-wishlist-form' ); ?>

<form name="course-wishlist" class="course-wishlist" method="post" enctype="multipart/form-data">

	<?php do_action( 'learn-press/before-wishlist-button' ); ?>

	<input type="hidden" name="course-id" value="<?php echo esc_attr( $course_id ); ?>"/>

	<?php
	printf(
		'<button type="submit" class="lp4 learn-press-course-wishlist lp-button learn-press-course-wishlist-button wishlist-button %s" data-nonce="%s">%s</button>',
		join( ' ', $classes ),
		wp_create_nonce( 'course-toggle-wishlist' ),
		$state == 'on' ? __( 'Remove from Wishlist', 'learnpress' ) : __( 'Add to Wishlist', 'learnpress' )
	);

	do_action( 'learn-press/after-wishlist-button' );
	?>

</form>

<?php do_action( 'learn-press/after-wishlist-form' ); ?>
