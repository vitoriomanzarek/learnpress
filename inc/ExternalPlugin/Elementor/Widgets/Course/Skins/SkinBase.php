<?php
namespace LearnPress\ExternalPlugin\Elementor\Widgets\Course\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use LearnPress\TemplateHooks\Instructor\SingleInstructorTemplate;

abstract class SkinBase extends Elementor_Skin_Base {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/learnpress_list_courses_by_page/section_options/before_section_end', [ $this, 'register_controls' ], 10, 2 );
	}

	public function register_controls( Widget_Base $widget, $args) {
		$this->parent = $widget;

		$this->register_control_collumn();
	}

	protected function register_control_collumn() {
		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'learnpress' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				]
			]
		);
	}

	public function render() {
		global $post;

		$query = $this->parent->query_courses();

		if ( empty( $query['courses'] ) ) {
			return;
		}

		$this->render_loop_header();

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
				$this->render_meta();
				$this->render_excerpt();
			$this->render_after_content();
		$this->render_footer_course();
	}

	protected function render_meta() {
		?>
		<div class="learnpress-el-list-course__meta">
		</div>
		<?php
	}

	protected function render_excerpt() {
		?>
		<div class="learnpress-el-list-course__excerpt">
		<?php echo wp_kses_post( wp_trim_words( get_the_excerpt( get_the_ID() ), 20, '' ) ); ?>
		</div>
		<?php
	}

	protected function render_title() {
		?>
		<h3 class="learnpress-el-list-course__title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
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
		?>
		<div class="learnpress-el-list-course__thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'thumbnail' ); ?>
			</a>
		</div>
		<?php
	}

	protected function render_header_course() {
		?>
		<div <?php post_class( [ 'learnpress-el-list-course' ] ); ?>>
		<?php
	}

	protected function render_footer_course() {
		?>
		</div>
		<?php
	}

	protected function render_loop_header() {
		?>
		<div class="learnpress-el-list-courses">
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
		$has_numbers = in_array( $settings['pagination_type'], [ 'numbers', 'numbers_and_prev_next' ] );
		$has_prev_next = in_array( $settings['pagination_type'], [ 'prev_next', 'numbers_and_prev_next' ] );

		if ( $settings['pagination_type'] === '' ) {
			return;
		}

		$paged = $query['filter']->page ?? 1;

		$current_page = $this->parent->get_current_page();

		$next_page = intval( $current_page ) + 1;

		if ( $ajax_pagination ) {
			?>
			<div class="learnpress-el-list-course__load-more" data-page="<?php echo absint( $paged ); ?>" data-max-page="<?php echo absint( $page_limit ) ?>" data-next-page="<?php echo esc_url( $this->parent->get_wp_link_page( $next_page ) ); ?>" data-infinity-scroll="<?php echo absint( $settings['pagination_type'] === 'infinite_scroll' ); ?>">
				<div class="learnpress-el-list-course__load-more__inner">
					<div class="learnpress-el-list-course__load-more__spinner">
						<i class="fas fa-spinner"></i>
					</div>
					<?php if ( $settings['pagination_type'] === 'load_more_on_click' ) : ?>
						<div class="learnpress-el-list-course__load-more__button">
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
				$paginate_args = [
					'type' => 'array',
					'current' => $this->parent->get_current_page(),
					'total' => $page_limit,
					'prev_next' => false,
					'show_all' => true,
					'before_page_number' => '<span class="elementor-screen-only">' . esc_html__( 'Page', 'learnpress' ) . '</span>',
				];

				if ( is_singular() && ! is_front_page() ) {
					global $wp_rewrite;

					if ( $wp_rewrite->using_permalinks() ) {
						$paginate_args['base'] = trailingslashit( get_permalink() ) . '%_%';
						$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
					} else {
						$paginate_args['format'] = '?page=%#%';
					}
				}

				// Is single instructor page.
				$instructor = SingleInstructorTemplate::instance()->detect_instructor_by_page();

				if ( $instructor && $settings['query_type'] === 'instructor' ) {
					$paginate_args['base'] = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
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
