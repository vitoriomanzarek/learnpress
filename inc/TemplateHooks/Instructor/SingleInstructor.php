<?php
/**
 * Template hooks Archive Package.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
namespace LearnPress\TemplateHooks;

use LearnPress\Helpers\Template;

class SingleInstructor {
	public static function instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	protected function __construct() {
		add_action( 'learn-press/single-instructor/layout', [ $this, 'sections' ] );
		//add_action( 'wp_head', [ $this, 'add_internal_style_to_head' ] );
	}

	/*public function add_internal_style_to_head() {
		echo '<style id="123123" type="text/css">body{background: red !important;}</style>';
	}*/

	/**
	 * Get display name html of instructor.
	 *
	 * @param \LP_User $instructor
	 *
	 * @return string
	 */
	public function html_display_name( \LP_User $instructor ): string {
		$html_wrapper = apply_filters(
			'learn-press/single-instructor/display-name/wrapper',
			[
				'<h2><span class="instructor-display-name">' => '</span></h2>',
			]
		);
		return Template::instance()->nest_elements( $html_wrapper, $instructor->get_display_name() );
	}

	/**
	 * Get html social of instructor.
	 *
	 * @param \LP_User $instructor
	 *
	 * @return string
	 */
	public function html_social( \LP_User $instructor ): string {
		$content = '';

		try {
			$html_wrapper = apply_filters(
				'learn-press/single-instructor/social/wrapper',
				[
					'<div class="instructor-social">' => '</div>',
				]
			);

			$content = '';
			$socials = $instructor->get_profile_social( $instructor->get_id() );
			ob_start();
			foreach ( $socials as $k => $social ) {
				echo $social;
			}
			$content = ob_get_clean();
			$content = Template::instance()->nest_elements( $html_wrapper, $content );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}

	/**
	 * Get html description of instructor.
	 *
	 * @param \LP_User $instructor
	 *
	 * @return string
	 */
	public function html_description( \LP_User $instructor ): string {
		$content = '';

		try {
			$html_wrapper = apply_filters(
				'learn-press/single-instructor/description/wrapper',
				[
					'<p class="instructor-description">' => '</p>',
				]
			);

			$content = Template::instance()->nest_elements( $html_wrapper, $instructor->get_description() );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}

	/**
	 * Get html avatar of instructor.
	 *
	 * @param \LP_User $instructor
	 *
	 * @return string
	 */
	public function html_avatar( \LP_User $instructor ): string {
		$content = '';

		try {
			$html_wrapper = apply_filters(
				'learn-press/single-instructor/avatar/wrapper',
				[
					'<div class="instructor-avatar">' => '</div>',
				]
			);

			$content = Template::instance()->nest_elements( $html_wrapper, $instructor->get_profile_picture() );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}

	public function sections( $data = [] ) {
		wp_enqueue_style( 'learnpress' );
		wp_enqueue_style( 'lp-instructor' );
		/**
		 * @var \WP_Query $wp_query
		 */
		global $wp_query;
		$instructor = false;

		try {
			if ( isset( $data['instructor_id'] ) ) {
				$instructor_id = $data['instructor_id'];
				$instructor    = learn_press_get_user( $instructor_id );
			} elseif ( $wp_query->get( 'is_single_instructor' )
					&& $wp_query->get( 'instructor_name' ) ) {
				$user = get_user_by( 'slug', $wp_query->get( 'instructor_name' ) );
				if ( $user ) {
					$instructor = learn_press_get_user( $user->ID );
				}
			}

			if ( ! $instructor || ! $instructor->can_create_course() ) {
				return;
			}

			$html_wrapper = apply_filters(
				'learn-press/single-instructor/sections/wrapper',
				[
					'<article class="lp-content-area">'  => '</article>',
					'<div class="lp-single-instructor">' => '</div>',
				]
			);

			$sections = apply_filters(
				'learn-press/single-instructor/sections/wrapper',
				[
					'header'  => [ 'text_html' => '<header><h1>' . __( 'Instructor', 'learnpress' ) . '</h1></header>' ],
					'info'    => [ 'text_html' => $this->info( $instructor ) ],
					'courses' => [ 'text_html' => $this->list_courses( $instructor ) ],
				]
			);

			ob_start();
			Template::instance()->print_sections( $sections, compact( 'instructor' ) );
			$content = ob_get_clean();
			echo Template::instance()->nest_elements( $html_wrapper, $content );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}
	}

	/**
	 * @param \LP_User $instructor
	 *
	 * @return false|string
	 */
	public function info( \LP_User $instructor ): string {
		$content = '';

		try {
			$html_wrapper = apply_filters(
				'learn-press/single-instructor/info/wrapper',
				[
					'<div class="lp-single-instructor__info">' => '</div>',
				]
			);

			$sections = apply_filters(
				'learn-press/single-instructor/info/sections',
				[
					'image'      => [ 'text_html' => $this->html_avatar( $instructor ) ],
					'info_right' => [ 'text_html' => $this->info_right( $instructor ) ],
				]
			);

			ob_start();
			Template::instance()->print_sections( $sections, compact( 'instructor' ) );
			$content = ob_get_clean();
			$content = Template::instance()->nest_elements( $html_wrapper, $content );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}

	public function info_right( \LP_User $instructor ): string {
		$content = '';

		try {
			$html_wrapper = apply_filters(
				'learn-press/single-instructor/info-right/wrapper',
				[
					'<div class="lp-single-instructor__info__right">' => '</div>',
				]
			);

			$sections = apply_filters(
				'learn-press/single-instructor/info-right/sections',
				[
					'title'       => [ 'text_html' => $this->html_display_name( $instructor ) ],
					'social'      => [ 'text_html' => $this->html_social( $instructor ) ],
					'description' => [ 'text_html' => $this->html_description( $instructor ) ],
				]
			);

			ob_start();
			Template::instance()->print_sections( $sections, compact( 'instructor' ) );
			$content = ob_get_clean();
			$content = Template::instance()->nest_elements( $html_wrapper, $content );
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}

	public function list_courses( \LP_User $instructor ): string {
		$content      = '';
		$html_wrapper = apply_filters(
			'learn-press/single-instructor/courses/wrapper',
			[
				'<div class="instructor-courses">'   => '</div>',
				'<ul class="ul-instructor-courses">' => '</ul>',
			]
		);

		try {
			// Get option load courses of Instructor via ajax
			$load_ajax = false;

			// Query courses of instructor
			if ( ! $load_ajax ) {
				$filter              = new \LP_Course_Filter();
				$filter->post_author = $instructor->get_id();
				$filter->limit       = 20;

				$courses = \LP_Course::get_courses( $filter );
				ob_start();
				foreach ( $courses as $course_obj ) {
					$course = \LP_Course::get_course( $course_obj->ID );
					echo $this->course_items( $course );
				}
				//Template::instance()->get_frontend_template( 'single-instructor/item-course.php', compact( 'courses' ) );
				$content = ob_get_clean();
			}
		} catch ( \Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return Template::instance()->nest_elements( $html_wrapper, $content );
	}

	/**
	 * Display list courses.
	 *
	 * @param \LP_Course $course
	 *
	 * @return void
	 */
	public function course_items( \LP_Course $course ): string {
		$content      = '';
		$html_wrapper = apply_filters(
			'learn-press/single-instructor/course_items/wrapper',
			[
				'<li class="item-course">' => '</li>',
			],
			$course
		);

		try {
			$singleCourseTemplate = SingleCourse::instance();
			ob_start();
			$html_img              = sprintf(
				'<a href="%s">%s</a>',
				$course->get_permalink(),
				$singleCourseTemplate->html_image( $course )
			);
			$html_title            = sprintf(
				'<h3><a href="%s">%s</a></h3>',
				$course->get_permalink(),
				$singleCourseTemplate->html_title( $course )
			);
			$html_price_categories = sprintf(
				'<div class="price-categories">%s %s</div>',
				$course->get_course_price_html(),
				$singleCourseTemplate->html_categories( $course )
			);

			$count_lesson  = $course->count_items( LP_LESSON_CPT );
			$count_student = $course->get_total_user_enrolled_or_purchased();
			$ico_lesson    = sprintf( '<span class="course-ico lesson">%s</span>', wp_remote_fopen( LP_PLUGIN_URL . 'assets/images/icons/ico-file.svg' ) );
			$ico_student   = sprintf( '<span class="course-ico student">%s</span>', wp_remote_fopen( LP_PLUGIN_URL . 'assets/images/icons/ico-students.svg' ) );
			$html_count    = sprintf(
				'<div class="course-count">%s %s</div>',
				sprintf( '<div class="course-count-lesson">%s %d %s</div>', $ico_lesson, $count_lesson, _n( 'Lesson', 'Lessons', $count_lesson ) ),
				sprintf( '<div class="course-count-student">%s %d %s</div>', $ico_student, $count_student, _n( 'Student', 'Students', $count_student ) )
			);

			$sections = apply_filters(
				'learn-press/single-instructor/course_items/sections',
				[
					'img'              => [ 'text_html' => $html_img ],
					'price-categories' => [ 'text_html' => $html_price_categories ],
					'title'            => [ 'text_html' => $html_title ],
					'count'            => [ 'text_html' => $html_count ],
				],
				$course,
				$singleCourseTemplate
			);
			Template::instance()->print_sections( $sections, compact( 'course' ) );
			$content = ob_get_clean();
			$content = Template::instance()->nest_elements( $html_wrapper, $content );
		} catch ( \Throwable $e ) {
			ob_end_clean();
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $content;
	}
}

SingleInstructor::instance();
