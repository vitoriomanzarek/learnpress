<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use LearnPress\TemplateHooks\Instructor\SingleInstructorTemplate;

abstract class SkinBase extends Elementor_Skin_Base {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/learnpress_list_courses_by_page/section_options/before_section_end', array( $this, 'register_controls' ), 10, 2 );
	}

	public function get_container_class() {
		return '';
	}

	public function register_controls( Widget_Base $widget, $args ) {
		$this->parent = $widget;

		$this->register_control_collumn();
	}

	protected function register_control_collumn() {
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'learnpress' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}}' => '--lp-el-list-courses-grid-columns: {{VALUE}}',
				),
			)
		);
	}

	protected function register_control_thumnail() {
		$this->add_responsive_control(
			'item_ratio',
			array(
				'label'          => esc_html__( 'Image Ratio', 'learnpress' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 0.66,
				),
				'tablet_default' => array(
					'size' => '',
				),
				'mobile_default' => array(
					'size' => 0.5,
				),
				'range'          => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					),
				),
				'separator'      => 'before',
				'selectors'      => array(
					'{{WRAPPER}} .learnpress-el-list-course__thumbnail > a' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'          => esc_html__( 'Image Width', 'learnpress' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', '%', 'em', 'rem', 'vw', 'custom' ),
				'range'          => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 10,
						'max' => 600,
					),
				),
				'default'        => array(
					'size' => 100,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => '',
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'      => array(
					'{{WRAPPER}} .learnpress-el-list-course__thumbnail' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			array(
				'label'     => esc_html__( 'Title', 'learnpress' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => esc_html__( 'Title HTML Tag', 'learnpress' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h3',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
	}

	protected function register_price_controls() {
		$this->add_control(
			'show_price',
			array(
				'label'     => esc_html__( 'Price', 'learnpress' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default'   => 'yes',
			)
		);
	}

	protected function register_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			array(
				'label'     => esc_html__( 'Excerpt', 'learnpress' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => esc_html__( 'Excerpt Length', 'learnpress' ),
				'type'      => Controls_Manager::NUMBER,
				/** This filter is documented in wp-includes/formatting.php */
				'default'   => apply_filters( 'excerpt_length', 25 ),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'apply_to_custom_excerpt',
			array(
				'label'     => esc_html__( 'Apply to custom Excerpt', 'learnpress' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'learnpress' ),
				'label_off' => esc_html__( 'No', 'learnpress' ),
				'default'   => 'no',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);
	}

	protected function register_read_more() {
		// Show Read More
		$this->add_control(
			'show_read_more',
			array(
				'label'     => esc_html__( 'Read More', 'learnpress' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Show', 'learnpress' ),
				'label_off' => esc_html__( 'Hide', 'learnpress' ),
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		// Read More Text
		$this->add_control(
			'read_more_text',
			array(
				'label'     => esc_html__( 'Read More Text', 'learnpress' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read More', 'learnpress' ),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);
	}

	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data',
			array(
				'label'       => esc_html__( 'Meta Data', 'learnpress' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'duration', 'level' ),
				'multiple'    => true,
				'options'     => array(
					'duration'      => esc_html__( 'Duration', 'learnpress' ),
					'level'         => esc_html__( 'Level', 'learnpress' ),
					'count_lesson'  => esc_html__( 'Count Lesson', 'learnpress' ),
					'count_quiz'    => esc_html__( 'Count Quiz', 'learnpress' ),
					'count_student' => esc_html__( 'Count Student', 'learnpress' ),
				),
				'separator'   => 'before',
			)
		);
	}

	public function render() {
		global $post;

		$query = $this->parent->query_courses();

		if ( empty( $query['courses'] ) ) {
			return;
		}

		$this->render_loop_header( $query );

		foreach ( $query['courses'] as $course ) {
			$post = get_post( absint( $course->ID ) );
			setup_postdata( $post );

			$this->render_course();
		}

		wp_reset_postdata();

		$this->render_loop_footer( $query );
	}

	protected function render_course() {
		$this->render_header_course();
			$this->render_thumbnail();

			$this->render_before_content();
				$this->render_title();
				$this->render_price();
				$this->render_meta();
				$this->render_excerpt();
				$this->render_read_more();
			$this->render_after_content();
		$this->render_footer_course();
	}

	public function render_top_bar( $query ) {
		?>
		<div class="learnpress-el-list-courses__top-bar">
			<div class="learnpress-el-list-courses__top-bar__inner">
				<?php
				$settings = $this->parent->get_settings_for_display();

				if ( $settings['show_result_count'] ) {
					$this->render_result_count( $query );
				}

				if ( $settings['show_sorting'] ) {
					$this->render_ordering( $query );
				}
				?>
			</div>
		</div>
		<?php
	}

	public function render_result_count( $query ) {
		$total_rows = absint( $query['total_rows'] );
		$filter     = $query['filter'];

		if ( $total_rows <= 0 ) {
			return;
		}
		?>
		<p class="learnpress-el-list-course__top-bar__result-count">
			<?php
			if ( $total_rows === 1 ) {
				printf( esc_html__( 'Showing the single result', 'learnpress' ) );
			} elseif ( $filter->limit === $total_rows ) {
				printf( esc_html__( 'Showing all %d results', 'learnpress' ), $total_rows );
			} else {
				$first = ( $filter->limit * $filter->page ) - $filter->limit + 1;
				$last  = min( $total_rows, $filter->limit * $filter->page );

				printf( esc_html__( 'Showing %1$d&ndash;%2$d of %3$d results', 'learnpress' ), $first, $last, $total_rows );
			}
			?>
		</p>
		<?php
	}

	public function render_ordering( $query ) {
		$filter  = $query['filter'];
		$orderby = isset( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'post_date';

		$options = array(
			'post_date'       => esc_html__( 'Default', 'learnpress' ),
			'post_title'      => esc_html__( 'Oldest', 'learnpress' ),
			'post_title_desc' => esc_html__( 'Newest', 'learnpress' ),
			'price_low'       => esc_html__( 'Price: Low to High', 'learnpress' ),
			'price'           => esc_html__( 'Price: High to Low', 'learnpress' ),
		);
		?>
		<form method="get" class="learnpress-el-list-courses__top-bar__ordering">
			<label for="learnpress-el-list-courses__top-bar__ordering__select" class="learnpress-el-list-courses__top-bar__ordering__label">
				<?php esc_html_e( 'Sort by', 'learnpress' ); ?>
			</label>
			<select id="learnpress-el-list-courses__top-bar__ordering__select" class="learnpress-el-list-courses__top-bar__ordering__select" name="order_by">
				<?php foreach ( $options as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $orderby, $key ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="paged" value="1" />
		</form>
		<?php
	}

	protected function render_meta() {
		$settings = $this->get_instance_value( 'meta_data' );

		if ( empty( $settings ) ) {
			return;
		}
		?>
		<div class="learnpress-el-list-course__meta">
			<?php
			if ( in_array( 'duration', $settings ) ) {
				$this->render_duration();
			}
			if ( in_array( 'level', $settings ) ) {
				$this->render_level();
			}
			if ( in_array( 'count_lesson', $settings ) ) {
				$this->render_count_lesson();
			}
			if ( in_array( 'count_quiz', $settings ) ) {
				$this->render_count_quiz();
			}
			if ( in_array( 'count_student', $settings ) ) {
				$this->render_count_student();
			}
			?>
		</div>
		<?php
	}

	protected function render_duration() {
		?>
		<span class="learnpress-el-list-course__meta__duration">
			<?php
			echo wp_kses_post( learn_press_get_post_translated_duration( get_the_ID(), esc_html__( 'Lifetime access', 'learnpress' ) ) );
			?>
		</span>
		<?php
	}

	protected function render_level() {
		$level = learn_press_get_post_level( get_the_ID() );

		if ( ! $level ) {
			return;
		}
		?>
		<span class="learnpress-el-list-course__meta__level">
			<?php echo esc_html( $level ); ?>
		</span>
		<?php
	}

	protected function render_count_lesson() {
		$course = learn_press_get_course( get_the_ID() );

		if ( ! $course ) {
			return;
		}

		$lessons = $course->count_items( LP_LESSON_CPT );
		?>
		<span class="learnpress-el-list-course__meta__count-lesson">
			<?php printf( _n( '%d lesson', '%d lessons', absint( $lessons ), 'learnpress' ), absint( $lessons ) ); ?>
		</span>
		<?php
	}

	protected function render_count_quiz() {
		$course = learn_press_get_course( get_the_ID() );

		if ( ! $course ) {
			return;
		}
		$quizzes = $course->count_items( LP_QUIZ_CPT );
		?>
		<span class="learnpress-el-list-course__meta__count-quiz">
			<?php printf( _n( '%d quiz', '%d quizzes', absint( $quizzes ), 'learnpress' ), absint( $quizzes ) ); ?>
		<?php
	}

	protected function render_count_student() {
		$course = learn_press_get_course( get_the_ID() );

		if ( ! $course ) {
			return;
		}
		$students = $course->count_students();
		?>
		<span class="learnpress-el-list-course__meta__count-student">
			<?php printf( _n( '%d student', '%d students', absint( $students ), 'learnpress' ), absint( $students ) ); ?>
		</span>
		<?php
	}

	protected function render_price() {
		if ( ! $this->get_instance_value( 'show_price' ) ) {
			return;
		}

		$course = learn_press_get_course( get_the_ID() );

		if ( ! $course ) {
			return;
		}

		$price_html  = $course->get_course_price_html();
		$class_price = 'learnpress-el-list-course__price__inner';
		if ( $course->is_free() ) {
			$class_price .= ' learnpress-el-list-course__price__free';
		} elseif ( $course->has_sale_price() ) {
			$class_price .= ' learnpress-el-list-course__price__has_sale';
		}
		?>
		<?php if ( $price_html ) : ?>
			<div class="learnpress-el-list-course__price">
				<?php echo wp_kses_post( '<span class="' . esc_attr( $class_price ) . '">' . wp_kses_post( $price_html ) . '</span>' ); ?>
			</div>
		<?php endif; ?>
		<?php
	}

	protected function render_excerpt() {
		if ( ! $this->get_instance_value( 'show_excerpt' ) ) {
			return;
		}

		$excerpt_length = $this->get_instance_value( 'excerpt_length' );
		$apply_to       = $this->get_instance_value( 'apply_to_custom_excerpt' );

		if ( $apply_to ) {
			$excerpt = get_the_excerpt();
		} else {
			$excerpt = get_the_content();
		}

		$excerpt = wp_trim_words( $excerpt, $excerpt_length, '' );

		if ( empty( $excerpt ) ) {
			return;
		}
		?>
		<div class="learnpress-el-list-course__excerpt">
			<?php echo wp_kses_post( $excerpt ); ?>
		</div>
		<?php
	}

	protected function render_read_more() {
		if ( ! $this->get_instance_value( 'show_read_more' ) ) {
			return;
		}
		?>
		<div class="learnpress-el-list-course__read-more">
			<a href="<?php the_permalink(); ?>">
				<?php echo esc_html( $this->get_instance_value( 'read_more_text' ) ); ?>
			</a>
		</div>
		<?php
	}

	protected function render_title() {
		if ( ! $this->get_instance_value( 'show_title' ) ) {
			return;
		}

		$tag = $this->get_instance_value( 'title_tag' );
		?>
		<<?php Utils::print_validated_html_tag( $tag ); ?> class="learnpress-el-list-course__title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</<?php Utils::print_validated_html_tag( $tag ); ?>>
		<?php
	}

	protected function render_before_content() {
		?>
		<div class="learnpress-el-list-course__content">
		<?php
	}

	protected function render_after_content() {
		?>
		</div>
		<?php
	}

	protected function render_thumbnail() {
		// check has thumbnail.
		if ( ! has_post_thumbnail() ) {
			return;
		}
		?>
		<div class="learnpress-el-list-course__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'full' ); ?>
			</a>
		</div>
		<?php
	}

	protected function render_header_course() {
		?>
		<div <?php post_class( array( 'learnpress-el-list-course' ) ); ?>>
		<?php
	}

	protected function render_footer_course() {
		?>
		</div>
		<?php
	}

	protected function render_loop_header( $query ) {
		?>
		<div class="learnpress-el-list-courses <?php echo esc_attr( $this->get_container_class() ); ?>">
			<?php $this->render_top_bar( $query ); ?>
			<div class="learnpress-el-list-courses__inner">
		<?php
	}

	protected function render_loop_footer( $query ) {
		?>
		</div> <!-- /.learnpress-el-list-courses__inner -->
		<?php
		$settings = $this->parent->get_settings_for_display();

		if ( ! isset( $settings['pagination_type'] ) || $settings['query_type'] === '' ) {
			return;
		}

		$page_limit = \LP_Database::get_total_pages( $query['filter']->limit, $query['total_rows'] );

		if ( 2 > $page_limit ) {
			return;
		}

		$ajax_pagination = in_array( $settings['pagination_type'], array( 'load_more_on_click', 'infinite_scroll' ), true );
		$has_numbers     = in_array( $settings['pagination_type'], array( 'numbers', 'numbers_and_prev_next' ) );
		$has_prev_next   = in_array( $settings['pagination_type'], array( 'prev_next', 'numbers_and_prev_next' ) );

		if ( $settings['pagination_type'] === '' ) {
			return;
		}

		$paged = $query['filter']->page ?? 1;

		$current_page = $this->parent->get_current_page();

		$next_page = intval( $current_page ) + 1;

		if ( $ajax_pagination ) {
			?>
			<div class="learnpress-el-list-courses__load-more" data-page="<?php echo absint( $paged ); ?>" data-max-page="<?php echo absint( $page_limit ); ?>" data-next-page="<?php echo esc_url( $this->parent->get_wp_link_page( $next_page ) ); ?>" data-infinity-scroll="<?php echo absint( $settings['pagination_type'] === 'infinite_scroll' ); ?>">
				<div class="learnpress-el-list-courses__load-more__inner">
					<div class="learnpress-el-list-courses__load-more__spinner">
						<i class="fas fa-spinner"></i>
					</div>
					<?php if ( $settings['pagination_type'] === 'load_more_on_click' ) : ?>
						<div class="learnpress-el-list-courses__load-more__button">
							<a href="#">
								<?php echo esc_html( $settings['load_more_text'] ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		} else {
			$links = array();

			if ( $has_numbers ) {
				$paginate_args = array(
					'type'               => 'array',
					'current'            => $this->parent->get_current_page(),
					'total'              => $page_limit,
					'prev_next'          => false,
					'show_all'           => true,
					'before_page_number' => '<span class="elementor-screen-only">' . esc_html__( 'Page', 'learnpress' ) . '</span>',
				);

				if ( is_singular() && ! is_front_page() ) {
					global $wp_rewrite;

					if ( $wp_rewrite->using_permalinks() ) {
						$paginate_args['base']   = trailingslashit( get_permalink() ) . '%_%';
						$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
					} else {
						$paginate_args['format'] = '?page=%#%';
					}
				}

				// Is single instructor page.
				$instructor = SingleInstructorTemplate::instance()->detect_instructor_by_page();

				if ( $instructor && $settings['query_type'] === 'instructor' ) {
					$paginate_args['base']   = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
					$paginate_args['format'] = '';
				}

				$links = paginate_links( $paginate_args );
			}

			if ( $has_prev_next ) {
				$prev_next = $this->parent->get_posts_nav_link( $page_limit );
				array_unshift( $links, $prev_next['prev'] );
				$links[] = $prev_next['next'];
			}
			?>
			<nav class="learnpress-el-list-courses__pagination" aria-label="<?php esc_attr_e( 'Pagination', 'learnpress' ); ?>">
				<?php echo implode( PHP_EOL, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
		<?php } ?>

		</div> <!-- /.learnpress-el-list-courses -->
		<?php
	}
}
