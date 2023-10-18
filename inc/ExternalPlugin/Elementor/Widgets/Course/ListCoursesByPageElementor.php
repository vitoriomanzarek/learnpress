<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course;

use LearnPress\ExternalPlugin\Elementor\LPElementorWidgetBase;
use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\CourseGrid;
use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\CourseList;
use LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins\CourseLoopItem;
use LearnPress\TemplateHooks\Instructor\SingleInstructorTemplate;
use Elementor\Controls_Manager;

class ListCoursesByPageElementor extends LPElementorWidgetBase {

	public function __construct( $data = [], $args = null ) {
		$this->title    = esc_html__( 'List Courses by Page', 'learnpress' );
		$this->name     = 'list_courses_by_page';
		$this->keywords = [ 'list courses', 'by page' ];
		$this->icon     = 'eicon-post-list';

		wp_register_script(
			'lp-courses-by-page',
			LP_PLUGIN_URL . 'assets/js/dist/elementor/courses.js',
			array(),
			uniqid(),
			true
		);
		$this->add_script_depends( 'lp-courses-by-page' );

		parent::__construct( $data, $args );
	}

	protected $_has_template_content = false;

	protected function register_skins() {
		$this->add_skin( new CourseGrid( $this ) );
		$this->add_skin( new CourseList( $this ) );

		if ( class_exists( '\Thim_EL_Kit' ) ) {
			$this->add_skin( new CourseLoopItem( $this ) );
		}
	}

	protected function register_controls() {
		$this->register_options_section();
		$this->register_query_section();
		$this->register_pagination_section();
	}

	protected function register_options_section() {
		$this->start_controls_section(
			'section_options',
			array(
				'label' => esc_html__( 'Options', 'learnpress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->end_controls_section();
	}

	protected function register_query_section() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Query', 'learnpress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		// Auto detect page: example archive course, instructor, etc...
		$this->add_control(
			'query_type',
			array(
				'label' => esc_html__( 'Query Type', 'learnpress' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => esc_html__( 'Default', 'learnpress' ),
					'archive' => esc_html__( 'Archive Course', 'learnpress' ),
					'instructor' => esc_html__( 'Single Instructor Page', 'learnpress' ),
					'related_course' => esc_html__( 'Related Course', 'learnpress' ),
				),
			)
		);

		// Enable Ajax Skeleton.
		$this->add_control(
			'enable_ajax_skeleton',
			array(
				'label' => esc_html__( 'Enable Ajax', 'learnpress' ),
				'description' => esc_html__( 'Enable Ajax Skeleton', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'learnpress' ),
				'label_off' => esc_html__( 'No', 'learnpress' ),
				'return_value' => 'yes',
			)
		);

		// Select category.
		$categories = get_terms(
			array(
				'taxonomy' => 'course_category',
				'hide_empty' => false,
			)
		);

		$categories_options = array();

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$categories_options[ $category->term_id ] = $category->name;
			}
		}

		$this->add_control(
			'category',
			array(
				'label' => esc_html__( 'Category', 'learnpress' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $categories_options,
				'multiple' => true,
				'label_block' => true,
				'condition' => array(
					'query_type' => '',
				),
			)
		);

		// Limit.
		$this->add_control(
			'limit',
			array(
				'label' => esc_html__( 'Course Limit', 'learnpress' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 10,
			)
		);

		// Course Per page.
		$this->add_control(
			'course_per_page',
			array(
				'label' => esc_html__( 'Course Per Page', 'learnpress' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 10,
			)
		);

		// Order by.
		$this->add_control(
			'orderby',
			array(
				'label' => esc_html__( 'Order by', 'learnpress' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'learnpress' ),
					'post_title' => esc_html__( 'Title asc', 'learnpress' ),
					'post_title_desc' => esc_html__( 'Title Desc', 'learnpress' ),
					'post_title' => esc_html__( 'Title', 'learnpress' ),
					'price_low' => esc_html__( 'Price Low to High', 'learnpress' ),
					'price' => esc_html__( 'Price High to Low', 'learnpress' ),
					'popular' => esc_html__( 'Popular', 'learnpress' ),
				),
				'default' => 'post_title',
				'condition' => array(
					'query_type' => '',
				),
			)
		);

		// Show result count.
		$this->add_control(
			'show_result_count',
			array(
				'label' => esc_html__( 'Show result count', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'learnpress' ),
				'label_off' => esc_html__( 'No', 'learnpress' ),
				'return_value' => 'yes',
				'separator' => 'before',
				'condition' => array(
					'query_type!' => '',
				),
			)
		);

		// Show sorting.
		$this->add_control(
			'show_sorting',
			array(
				'label' => esc_html__( 'Show sorting', 'learnpress' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( 'Yes', 'learnpress' ),
				'label_off' => esc_html__( 'No', 'learnpress' ),
				'return_value' => 'yes',
				'condition' => array(
					'query_type!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_pagination_section() {
		$this->start_controls_section(
			'section_pagination',
			array(
				'label' => esc_html__( 'Pagination', 'learnpress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => esc_html__( 'Pagination', 'learnpress' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'learnpress' ),
					'numbers' => esc_html__( 'Numbers', 'learnpress' ),
					'prev_next' => esc_html__( 'Previous/Next', 'learnpress' ),
					'numbers_and_prev_next' => esc_html__( 'Numbers', 'learnpress' ) . ' + ' . esc_html__( 'Previous/Next', 'learnpress' ),
					'load_more_on_click' => esc_html__( 'Load on Click', 'learnpress' ),
					'infinite_scroll' => esc_html__( 'Infinite Scroll', 'elementlearnpress' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pagination_prev_label',
			[
				'label' => esc_html__( 'Previous Label', 'learnpress' ),
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( '&laquo; Previous', 'learnpress' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'pagination_next_label',
			[
				'label' => esc_html__( 'Next Label', 'learnpress' ),
				'default' => esc_html__( 'Next &raquo;', 'learnpress' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label' => esc_html__( 'Load more text', 'learnpress' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Load more', 'learnpress' ),
				'condition' => [
					'pagination_type' => 'load_more_on_click',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {}

	public function query_courses() {
		if ( ! empty( $this->query_type() ) ) {
			$results = $this->query_courses_auto_detect_page();
		} else {
			$results = $this->query_courses_manual();
		}

		return $results;
	}

	public function query_courses_auto_detect_page() {
		$param = $_GET ?? [];

		$param['paged'] = 1;

		if ( ! empty( get_query_var( 'paged' ) ) ) {
			$param['paged'] = get_query_var( 'paged' );
		}

		$filter = new \LP_Course_Filter();

		$total_rows = 0;

		if ( ! empty( $this->get_settings_for_display( 'course_per_page' ) ) ) {
			$param['limit'] = $this->get_settings_for_display( 'course_per_page' ) ?? 10;
		}

		if ( $this->query_type() === 'archive' ) {
			if ( learn_press_is_course_category() || learn_press_is_course_tag() ) {
				$queried_object = get_queried_object();

				$param['term_id']  = $cat->term_id;
				$param['taxonomy'] = $cat->taxonomy;
			}
		}

		$instructor = SingleInstructorTemplate::instance()->detect_instructor_by_page();
		if ( $instructor && $this->query_type() === 'instructor' ) {
			$param['c_author'] = $instructor->get_id();
			$param['order'] = 'DESC';
		}

		if ( method_exists( 'LP_Course', 'handle_params_for_query_courses' ) ) {
			\LP_Course::handle_params_for_query_courses( $filter, $param );
		}

		$courses = \LP_Course::get_courses( $filter, $total_rows );

		return array(
			'filter' => $filter,
			'courses' => $courses,
			'total_rows' => $total_rows,
		);
	}

	public function query_courses_manual() {
		$filter = new \LP_Course_Filter();

		$total_rows = 0;

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['course_per_page' ] ) ) {
			$filter->limit = $settings['course_per_page' ] ?? 10;
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$filter->order_by =  $settings['orderby'];
		}

		$courses = \LP_Course::get_courses( $filter, $total_rows );

		return array(
			'filter' => $filter,
			'courses' => $courses,
			'total_rows' => $total_rows,
		);
	}

	protected function query_type() {
		return $this->get_settings_for_display( 'query_type' );
	}

	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = [];

		$paged = $this->get_current_page();

		$link_template = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings_for_display( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings_for_display( 'pagination_next_label' ) );
		}

		return $return;
	}

	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	public function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post       = get_post();
		$query_args = array();
		$url        = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ) ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( $i, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id']    = absint( wp_unslash( $_GET['preview_id'] ) );
				$query_args['preview_nonce'] = sanitize_text_field( wp_unslash( $_GET['preview_nonce'] ) );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}
}
