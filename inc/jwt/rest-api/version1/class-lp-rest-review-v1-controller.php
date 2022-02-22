<?php
/**
 * REST API for the Course Review Add-on.
 *
 * @package LearnPress/JWT/RESTAPI
 * @author Nhamdv <daonham95@gmail.com>
 */
if ( class_exists( 'LP_REST_Jwt_Posts_Controller' ) ) {
	class LP_Jwt_Course_Review_V1_Controller extends LP_REST_Jwt_Controller {
		protected $namespace = 'learnpress/v1';

		protected $rest_base = 'review';

		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/course/(?P<id>[\d]+)',
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item_review' ),
						'permission_callback' => '__return_true',
						'args'                => array(
							'page'     => array(
								'description'       => esc_html__( 'Paged', 'learnpress' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
							),
							'per_page' => array(
								'description'       => esc_html__( 'Per page', 'learnpress' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
							),
						),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/submit',
				array(
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'submit_review' ),
						'permission_callback' => '__return_true',
						'args'                => array(
							'id'      => array(
								'description'       => esc_html__( 'Course ID', 'learnpress' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
							),
							'rate'    => array(
								'description'       => esc_html__( 'Rate', 'learnpress' ),
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
							),
							'title'   => array(
								'description'       => esc_html__( 'Title', 'learnpress' ),
								'type'              => 'string',
								'sanitize_callback' => 'sanitize_text_field',
							),
							'content' => array(
								'description'       => esc_html__( 'Content', 'learnpress' ),
								'type'              => 'string',
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
				)
			);
		}

		protected function check_can_review( $course_id ) {
			$user_id = get_current_user_id();
			$user    = learn_press_get_user( $user_id );

			$can_review = false;

			if ( $user->has_course_status( $course_id, array( 'enrolled', 'completed', 'finished' ) ) && ! $this->user_get_comment( $course_id ) ) {
				$can_review = true;
			}

			return $can_review;
		}

		protected function user_get_comment( $course_id ) {
			static $comments;

			if ( ! isset( $comments ) ) {
				$comments = LP_Course_Reviews_DB::getInstance()->learn_press_get_user_rate( $course_id, get_current_user_id(), true );
			}

			return $comments;
		}

		public function get_item_review( $request ) {
			$course_id = $request->get_param( 'id' );
			$paged     = ! empty( $request->get_param( 'page' ) ) ? absint( $request->get_param( 'page' ) ) : 1;
			$per_page  = ! empty( $request->get_param( 'per_page' ) ) ? absint( $request->get_param( 'per_page' ) ) : LP_COURSE_REVIEW_PER_PAGE;

			$response       = new LP_REST_Response();
			$response->data = new stdClass();

			try {
				if ( empty( $course_id ) ) {
					throw new Exception( esc_html__( 'No Course ID param.', 'learnpress' ) );
				}

				$course = learn_press_get_course( $course_id );

				if ( ! $course ) {
					throw new Exception( esc_html__( 'Course not found.', 'learnpress' ) );
				}

				$user = learn_press_get_current_user();

				$course_rate = learn_press_get_course_rate( $course_id, false );

				$course_review = LP_Course_Reviews_DB::getInstance()->learn_press_get_course_review( $course_id, $paged, $per_page, true );

				$response->data->rated   = ! empty( $course_rate['rated'] ) ? number_format( $course_rate['rated'], 1 ) : 0;
				$response->data->total   = ! empty( $course_rate['total'] ) ? absint( $course_rate['total'] ) : 0;
				$response->data->items   = ! empty( $course_rate['items'] ) ? $course_rate['items'] : array();
				$response->data->reviews = ! empty( $course_review ) ? $course_review : array();

				$can_review                 = $this->check_can_review( $course_id );
				$template                   = 'single-course/tabs/reviews/loop-review.php';
				$paged                      = ! empty( $course_review['paged'] ) ? absint( $course_review['paged'] ) : 1;
				$pages                      = ! empty( $course_review['pages'] ) ? absint( $course_review['pages'] ) : 1;
				$response->data->template   = learn_press_get_template_content(
					$template,
					array(
						'reviews'       => $course_review['reviews'],
						'course_review' => $course_review,
						'course_id'     => $course_id,
						'paged'         => $paged,
						'pages'         => $pages,
					)
				);
				$response->data->can_review = $can_review;

				if ( ! $can_review ) {
					$review = $this->user_get_comment( $course_id );

					if ( $review && ! $review->comment_approved ) {
						$response->data->comment_approved = 0;
						$response->message                = esc_html__( 'You have already reviewed this course. It will be visible after it has been approved', 'learnpress' );
					}
				}

				$response->status = 'success';
			} catch ( \Throwable $th ) {
				$response->message = $th->getMessage();
			}

			return rest_ensure_response( $response );
		}

		public function submit_review( $request ) {
			$course_id = $request->get_param( 'id' );
			$rate      = $request->get_param( 'rate' );
			$title     = $request->get_param( 'title' );
			$content   = $request->get_param( 'content' );

			$user_id        = get_current_user_id();
			$response       = new LP_REST_Response();
			$response->data = new stdClass();

			try {
				if ( empty( $course_id ) ) {
					throw new Exception( esc_html__( 'No Course ID param.', 'learnpress' ) );
				}

				if ( empty( $user_id ) ) {
					throw new Exception( esc_html__( 'No User.', 'learnpress' ) );
				}

				if ( ! $this->check_can_review( $course_id ) ) {
					throw new Exception( esc_html__( 'You can not submit review.', 'learnpress' ) );
				}

				$add_review = LP_Course_Reviews_DB::getInstance()->learn_press_add_course_review(
					array(
						'user_id'   => $user_id,
						'course_id' => $course_id,
						'rate'      => ! empty( $rate ) ? $rate : 0,
						'title'     => ! empty( $title ) ? $title : '',
						'content'   => ! empty( $content ) ? $content : '',
						'force'     => true, // Not use cache.
					)
				);

				if ( $add_review ) {
					$response->data->comment_id = $add_review;
					$response->message          = is_admin() ? esc_html__( 'Your review submitted successfully', 'learnpress' ) : esc_html__( 'Thank you for your review. Your review will be visible after it has been approved', 'learnpress' );
					$response->status           = 'success';
				} else {
					throw new Exception( esc_html__( 'Cannot submit your review.', 'learnpress' ) );
				}
			} catch ( \Throwable $th ) {
				$response->message = $th->getMessage();
			}

			return rest_ensure_response( $response );
		}
	}
}
