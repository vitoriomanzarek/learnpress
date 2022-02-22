<?php
/**
 * Course Review Shortcode.
 *
 * @author  ThimPress
 * @category Shortcodes
 * @package  Learnpress/Shortcodes
 * @version  4.0.0
 * @extends  LP_Abstract_Shortcode
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Shortcode_Course_Review' ) ) {
	class LP_Shortcode_Course_Review extends LP_Abstract_Shortcode {

		/**
		 * LP_Shortcode_Course_Review constructor.
		 *
		 * @param mixed $atts
		 */
		public function __construct( $atts = '' ) {
			parent::__construct( $atts );

			$this->_atts = shortcode_atts(
				array(
					'course_id'      => 0,
					'show_rate'      => 'yes',
					'show_review'    => 'yes',
					'display_amount' => '1',
				),
				$this->_atts
			);
		}

		/**
		 * Output form.
		 *
		 * @return string
		 */
		public function output() {
			ob_start();

			$enqueued_script = wp_script_is( 'lp-single-course', 'enqueued' );
			$enqueued_style  = wp_script_is( 'learnpress', 'enqueued' );
			if ( ! $enqueued_script && ! $enqueued_style ) {
				wp_enqueue_script( 'lp-single-course' );
				wp_enqueue_style( 'learnpress' );
			}

			$message = '';
			if ( LP_Settings::get_option( 'course_review' ) == 'yes' ) {

				$atts      = $this->_atts;
				$course_id = $atts['course_id'];
				if ( ! $course_id ) {
					$course_id = get_the_ID();
				}

				if ( $atts['show_rate'] == 'yes' ) {
					$course_rate_res = learn_press_get_course_rate( $course_id, false );
					if ( $course_rate_res['total'] ) {
						$rated = $course_rate_res['rated'];
						$total = $course_rate_res['total'];
						learn_press_get_template(
							'single-course/tabs/reviews/count-rated.php',
							array(
								'rated'           => $rated,
								'course_rate_res' => $course_rate_res,
								'total'           => $total,
							)
						);
					} else {
						$message = esc_html__( 'No stars for course !', 'learnpress' );
					}
				}

				if ( $atts['show_review'] == 'yes' ) {
					$course_review = LP_Course_Reviews_DB::getInstance()->learn_press_get_course_review( $course_id, 1, $atts['display_amount'] );
					if ( $course_review['total'] ) {
						$reviews = $course_review['reviews'];
						$paged   = $course_review['paged'];
						$pages   = $course_review['pages'];
						learn_press_get_template(
							'single-course/tabs/reviews/loop-review.php',
							array(
								'reviews'       => $reviews,
								'course_id'     => $course_id,
								'course_review' => $course_review,
								'paged'         => $paged,
								'pages'         => $pages,
							)
						);
					} else {
						$message = esc_html__( 'No reviews for course !', 'learnpress' );
					}
				}
			} else {
				$message = esc_html__( 'Please enable option Review Course in Settings LearnPress !', 'learnpress' );
			}
			if ( $message ) {
				learn_press_display_message( $message, 'error' );
			}

			return ob_get_clean();
		}
	}
}
