<?php
/**
 * Template for displaying courses wishlist in wishlist tab of user profile page.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.10
 */

defined( 'ABSPATH' ) || exit();

if ( empty( $user_id ) || empty( $user_wishlist || empty( $args_query_user_courses_wishlist ) ) ) {
	learn_press_display_message( esc_html__( 'No courses in your wishlist !', 'learnpress' ) );
	return;
}
?>
<div class="profile-wishlist">
	<?php echo lp_skeleton_animation_html( 10, '100%', 'height:20px', 'width:100%' ); ?>
	<input type="hidden" name="args_query_user_courses_wishlist"
			value="<?php echo htmlentities( wp_json_encode( $args_query_user_courses_wishlist ) ); ?>">
</div>

