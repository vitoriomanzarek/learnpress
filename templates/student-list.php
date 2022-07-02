<?php
/**
 * Template for displaying student list tab of single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/tabs/student-list.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  1.0.0
 */

/**
 * @var LP_Course $course
 */
if ( empty( $course ) ) {
	$course = LP_Global::course();
}

defined( 'ABSPATH' ) || exit();
?>

<?php do_action( 'learn_press_before_students_list' ); ?>
	<div class="wrap-students-list">
		<?php
		$filters = apply_filters(
			'learn_press_get_students_list_filter',
			array(
				''         => esc_html__( 'All', 'learnpress' ),
				'enrolled' => esc_html__( 'In Progress', 'learnpress' ),
				'finished' => esc_html__( 'Finished', 'learnpress' ),
			)
		);
		?>

		<div class="filter-students">
			<label for="students-list-filter"><?php esc_html_e( 'Student filter', 'learnpress' ); ?></label>
			<select class="students-list-filter">
				<?php
				foreach ( $filters as $key => $_filter ) {
					echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $_filter ) . '</option>';
				}
				?>
			</select>
		</div>

		<div class="learnpress-course-student-list">
			<?php lp_skeleton_animation_html( 4, '100%', 'height: 30px;border-radius:4px;' ); ?>
			<div class="content-student-list" data-id ="<?php echo $course->get_id(); ?>"></div>
		</div>
	</div>
<?php
do_action( 'learn_press_after_students_list' );



