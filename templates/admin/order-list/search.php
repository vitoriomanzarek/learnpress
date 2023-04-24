<?php
if ( ! isset( $data ) ) {
	return;
}
?>

<p class="search-box">
	<input
		type="search" id="order-id" name="order-id" value=""
		placeholder="<?php esc_attr_e( 'Order ID', 'learnpress' ); ?>"
	>
	<input
		type="search" id="course-name" name="course-name" value=""
		placeholder="<?php esc_attr_e( 'Course Name', 'learnpress' ); ?>"
	>
	<input type="submit" id="search-submit" class="button"
		   value="<?php esc_html_e( 'Search Orders', 'learnpress' ); ?>">
</p>
