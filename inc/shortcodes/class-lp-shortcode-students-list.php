<?php
/**
 * Students list shortcode class.
 *
 * @author   ThimPress
 * @package  LearnPress/Students-List/Classes
 * @version  4.1.7
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Abstract_Shortcode' ) ) {
	return;
}

if ( ! class_exists( 'LP_Shortcode_Students_List' ) ) {
	/**
	 * Class LP_Shortcode_Students_List.
	 */
	class LP_Shortcode_Students_List extends LP_Abstract_Shortcode {

		/**
		 * LP_Shortcode_Students_List constructor.
		 *
		 * @param mixed $atts
		 */
		public function __construct( $atts = '' ) {
			parent::__construct( $atts );
			$this->_atts = shortcode_atts(
				array(
					'title' => '',
					'id'    => '',
				),
				$this->_atts
			);
		}

		/**
		 * Shortcode output.
		 *
		 * @return mixed|string
		 */
		public function output() {
			wp_enqueue_style( 'learnpress' );
			wp_enqueue_script( 'lp-single-course' );

			$atts = $this->_atts;
			try {

				if ( $atts['title'] ) { ?>
					<h3 class="students-list-title"><?php echo esc_html( $atts['title'] ); ?></h3>
					<?php
				}

				if ( ! $atts['id'] ) {
					throw new Exception( __( 'Error: Please enter Course ID.', 'learnpress' ) );
				}

				$course_id = esc_html( $atts['id'] );

				$course_ids = array();
				if ( strpos( $course_id, ',' ) ) {
					$course_ids = explode( ',', $course_id );
				} else {
					$course_ids[] = $course_id;
				}

				ob_start();

				echo '<div id="learn-press__short-code">';
				foreach ( $course_ids as $course_id ) {

					$course = learn_press_get_course( $course_id );

					if ( ! $course ) {
						throw new Exception( __( 'Error: Course ID invalid, please check it again.', 'learnpress' ) );
					} else {

						echo esc_html__( 'Course: ', 'learnpress' ) . '<a href="' . get_permalink( $course_id ) . '">' . get_the_title( $course_id ) . '</a>';

						learn_press_get_template(
							'/single-course/tabs/student-list.php',
							array(
								'course' => $course,
							),
						);
					}
				}
				echo '</div>';
				return ob_get_clean();

			} catch ( Exception $ex ) {
				return learn_press_get_message( $ex->getMessage(), 'error' );
			}

		}
	}
}

new LP_Shortcode_Students_List();
