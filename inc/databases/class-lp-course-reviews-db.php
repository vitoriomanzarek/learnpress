<?php
/**
 * Class LP_Course_Reviews_DB
 *
 * @author tungnx
 * @since 4.1.6
 */

defined( 'ABSPATH' ) || exit();

class LP_Course_Reviews_DB extends LP_Database {
	private static $_instance;
	public $tb_comments;
	public $tb_commentmeta;

	protected function __construct() {
		global $wpdb;
		$prefix               = $wpdb->prefix;
		$this->tb_comments    = $prefix . 'comments';
		$this->tb_commentmeta = $prefix . 'commentmeta';
		parent::__construct();
	}

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get the rating user has posted for a course.
	 *
	 * @param int $course_id
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function learn_press_get_user_rate( $course_id = null, $user_id = null, $force = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		if ( ! $course_id ) {
			$course_id = get_the_ID();
		}

		// Get in cache if it is already get
		if ( ! ( $comment = wp_cache_get( 'user-' . $user_id . '/' . $course_id, 'lp-user-rate' ) ) || $force ) {

			$query   = $this->wpdb->prepare(
				"
	        SELECT *
	        FROM {$this->tb_posts} p
	        INNER JOIN {$this->tb_comments} c ON c.comment_post_ID = p.ID
	        WHERE c.comment_post_ID = %d
	        AND c.user_id = %d
	        AND c.comment_type = %s
	    ",
				$course_id,
				$user_id,
				'review'
			);
			$comment = $this->wpdb->get_row( $query );

			if ( $comment ) {
				$comment->comment_title = get_comment_meta( $comment->comment_ID, '_lpr_review_title', true );
				$comment->rating        = get_comment_meta( $comment->comment_ID, '_lpr_rating', true );
			}

			wp_cache_set( 'user-' . $user_id . '/' . $course_id, $comment, 'lp-user-rate' );
		}

		return $comment;
	}

	/**
	 * Get rating for a course
	 *
	 * @param int  $course_id
	 * @param bool $get_items
	 *
	 * @return array
	 */
	public function leanr_press_get_ratings_result( $course_id = 0, $get_items = false ) {
		// return learn_press_get_ratings_result_bak($course_id);
		if ( false === ( $result = wp_cache_get( 'course-' . $course_id, 'lp-course-ratings' ) ) ) {

			$query = $this->wpdb->prepare(
				"
				SELECT
					cm.meta_value `rate`, COUNT(1) `count`
				FROM
					{$this->tb_comments} c
						INNER JOIN
					{$this->tb_commentmeta} cm ON c.comment_ID = cm.comment_id AND meta_key = %s
				WHERE
					c.comment_approved = 1
						AND c.comment_type = %s
						AND c.user_id > 0
						AND c.comment_post_ID = %d
				GROUP BY `cm`.`meta_value`
			",
				'_lpr_rating',
				'review',
				$course_id
			);
			$rows  = $this->wpdb->get_results( $query/*, OBJECT_K */ );

			$count = 0;
			$rate  = 0;
			$avg   = 0;
			$items = array();

			for ( $i = 5; $i > 0; $i -- ) {
				$items[ $i ] = array(
					'rated'   => $i,
					'total'   => 0,
					'percent' => 0,
				);
			}

			if ( $rows ) {

				$count       = wp_list_pluck( $rows, 'count' );
				$count       = array_sum( $count );
				$round       = array();
				$one_hundred = 0;

				foreach ( $rows as $row ) {
					$rate               += $row->rate * $row->count;
					$percent             = $row->count / $count * 100;
					$items[ $row->rate ] = array(
						'rated'         => $row->rate,
						'total'         => $row->count,
						'percent'       => floor( $percent ),
						'percent_float' => $percent,
					);
					$one_hundred        += $items[ $row->rate ]['percent'];
					$round[ $row->rate ] = $percent - floor( $percent );
				}

				if ( $one_hundred < 100 ) {
					arsort( $round );
					foreach ( $round as $key => $value ) {
						$percent                  = $items[ $key ]['percent'];
						$items[ $key ]['percent'] = ceil( $items[ $key ]['percent_float'] );

						if ( $percent < $items[ $key ]['percent'] ) {
							$one_hundred ++;
							if ( $one_hundred == 100 ) {
								break;
							}
						}
					}
				}

				$avg = $rate / $count;
			}

			$result = array(
				'course_id' => $course_id,
				'total'     => $count,
				'rated'     => $avg,
				'items'     => $items,
			);

			wp_cache_set( 'course-' . $course_id, $result, 'lp-course-ratings' );
		}

		return $result;
	}
	/**
	 * @param int     $course_id
	 * @param int     $paged
	 * @param int     $per_page
	 * @param boolean $force
	 *
	 * @return mixed
	 */
	public function learn_press_get_course_review( $course_id, $paged = 1, $per_page = LP_COURSE_REVIEW_PER_PAGE, $force = false ) {

		$key = 'course-' . md5( serialize( array( $course_id, $paged, $per_page ) ) );

		if ( false === ( $results = wp_cache_get( $key, 'lp-course-review' ) ) || $force ) {

			$per_page = absint( apply_filters( 'learn_press_course_reviews_per_page', $per_page ) );
			$paged    = absint( $paged );

			if ( $per_page == 0 ) {
				$per_page = 9999999;
			}

			if ( $paged == 0 ) {
				$paged = 1;
			}

			$start    = ( $paged - 1 ) * $per_page;
			$start    = max( $start, 0 );
			$per_page = max( $per_page, 1 );
			$results  = array(
				'reviews'  => array(),
				'paged'    => $paged,
				'total'    => 0,
				'per_page' => $per_page,
			);

			$query = $this->wpdb->prepare(
				"
	        SELECT SQL_CALC_FOUND_ROWS u.user_email, u.display_name, c.comment_ID as comment_id, cm1.meta_value as title, c.comment_content as content, cm2.meta_value as rate
	        FROM {$this->tb_posts} p
	        INNER JOIN {$this->tb_comments} c ON p.ID = c.comment_post_ID
	        INNER JOIN {$this->tb_users} u ON u.ID = c.user_id
	        INNER JOIN {$this->tb_commentmeta} cm1 ON cm1.comment_id = c.comment_ID AND cm1.meta_key = %s
	        INNER JOIN {$this->tb_commentmeta} cm2 ON cm2.comment_id = c.comment_ID AND cm2.meta_key = %s
	        WHERE p.ID = %d AND c.comment_type = %s AND c.comment_approved = %d
	        ORDER BY c.comment_date DESC
	        LIMIT %d, %d
	    ",
				'_lpr_review_title',
				'_lpr_rating',
				$course_id,
				'review',
				1,
				$start,
				$per_page
			);

			$course_review = $this->wpdb->get_results( $query );

			if ( $course_review ) {
				$ratings            = _learn_press_get_ratings( $course_id );
				$results['reviews'] = $course_review;
				$results['total']   = $ratings[ $course_id ]['total'];
				$results['pages']   = ceil( $results['total'] / $per_page );
				if ( $results['total'] <= $start + $per_page ) {
					$results['finish'] = true;
				}
			}

			wp_cache_set( $key, $results, 'lp-course-review' );
		}

		return $results;
	}

	/**
	 * Add new review for a course
	 *
	 * @param array
	 *
	 * @return int
	 */
	public function learn_press_add_course_review( $args = array() ) {
		$args        = wp_parse_args(
			$args,
			array(
				'title'     => '',
				'content'   => '',
				'rate'      => '',
				'user_id'   => 0,
				'course_id' => 0,
				'force'     => 0,
			)
		);
		$user_id     = $args['user_id'];
		$course_id   = $args['course_id'];
		$user_review = $this->learn_press_get_user_rate( $course_id, $user_id, $args['force'] );
		$comment_id  = 0;

		if ( ! $user_review ) {
			$user       = get_user_by( 'id', $user_id );
			$comment_id = wp_new_comment(
				array(
					'comment_post_ID'      => $course_id,
					'comment_author'       => $user->display_name,
					'comment_author_email' => $user->user_email,
					'comment_author_url'   => '',
					'comment_content'      => $args['content'],
					'comment_parent'       => 0,
					'user_id'              => $user->ID,
					'comment_approved'     => 1,
					'comment_type'         => 'review', // let filter to not display it as comments
				)
			);
		}
		if ( $comment_id ) {
			add_comment_meta( $comment_id, '_lpr_rating', $args['rate'] );
			add_comment_meta( $comment_id, '_lpr_review_title', $args['title'] );
		}

		return $comment_id;
	}
}

LP_Course_Reviews_DB::getInstance();

