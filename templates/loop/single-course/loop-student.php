<?php
/**
 * Template for displaying loop student list tab of single course.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.2
 */

defined( 'ABSPATH' ) || exit();

if ( empty( $students ) || empty( $course ) || empty( $current_page ) || empty( $num_pages ) ) {
	return;
}

?>
<?php if ( $current_page === 1 ) : ?>
	<ul class="students">
		<?php endif; ?>
			<?php
			$students_list_avatar_size = apply_filters( 'learn_press_students_list_avatar_size', 60 );

			foreach ( $students as $student_id ) {
				$result  = $process = '';
				$student = learn_press_get_user( $student_id );

				$course_data       = $student->get_course_data( $course->get_id() );
				$course_results    = $course_data->get_results( false );
				$passing_condition = $course->get_passing_condition();

				$result = $course_results['result'];
				?>

				<li class="students-enrolled">
					<div class="user-image">
						<?php echo get_avatar( $student->get_id(), $students_list_avatar_size, '', $student->get_data( 'display_name' ), array( 'class' => 'students_list_avatar' ) ); ?>
					</div>
					<div class="user-info">
						<a class="name" href="<?php echo learn_press_user_profile_link( $student->get_id() ); ?>"
							title="<?php echo $student->get_data( 'display_name' ) . ' profile'; ?>">
							<?php echo $student->get_data( 'display_name' ); ?>
						</a>
						<div class="lp-course-status">
								<span class="number"><?php echo round( $course_results['result'], 2 ); ?>
									<span class="percentage-sign">%</span>
								</span>
							<?php $graduation = $course_data->get_graduation(); ?>
							<?php if ( $graduation ) : ?>
								<span class="lp-graduation <?php echo esc_attr( $graduation ); ?>"
										style="color: #222; font-weight: 600;">
									<?php learn_press_course_grade_html( $graduation ); ?>
								</span>
							<?php endif; ?>
						</div>

						<div class="learn-press-progress lp-course-progress <?php echo $course_data->is_passed() ? ' passed' : ''; ?>"
								data-value="<?php echo $course_results['result']; ?>"
								data-passing-condition="<?php echo $passing_condition; ?>">
							<div class="progress-bg lp-progress-bar">
								<div class="progress-active lp-progress-value"
										style="left: <?php echo $course_results['result']; ?>%;">
								</div>
							</div>
							<div class="lp-passing-conditional"
									data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'learnpress' ), $passing_condition ); ?>"
									style="left: <?php echo $passing_condition; ?>%;">
							</div>
						</div>
					</div>
				</li>
		<?php } ?>
		<?php if ( $current_page === 1 ) : ?>
	</ul>
<?php endif; ?>
<?php if ( $num_pages > 1 && $current_page < $num_pages && $current_page === 1 ) : ?>
	<div class="lp_student_list_button">
		<button class="lp-button" data-paged="<?php echo absint( $current_page + 1 ); ?>"
				data-number="<?php echo absint( $num_pages ); ?>">
			<?php esc_html_e( 'View more', 'learnpress' ); ?>
		</button>
	</div>
<?php endif; ?>
