<?php
/**
 * Template for displaying content of Student list Course widget.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/widgets/student-list.php
 *
 * @author   ThimPress
 * @category Widgets
 * @package  Learnpress/Templates
 * @version  4.1.7
 */

defined( 'ABSPATH' ) || exit();

if ( empty( $course ) || empty( $students ) ) {
	return;
}

?>
<div id="learn-press__widget-student-list">
	<div class="wrap-students-list">
		<div class="learnpress-course-student-list">
			<div class="course-students-list">
				<div class="content-student-list">
					<ul class="students" data-id ="<?php echo $course->get_id(); ?>">
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
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
