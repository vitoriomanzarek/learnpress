<?php

/**
 * Class LP_REST_Users_Controller
 *
 * @since 3.3.0
 */
class LP_REST_Admin_Order_Controller extends LP_Abstract_REST_Controller {
	public function __construct() {
		$this->namespace = 'lp/v1/admin';
		$this->rest_base = 'order';

		add_filter( 'posts_join_paged', array( $this, 'posts_join_paged' ), 10, 2 );
		add_filter( 'posts_where_paged', array( $this, 'posts_where_paged' ), 10, 2 );
		parent::__construct();
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$this->routes = array(
			'order-list' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_orders' ),
					'permission_callback' => '__return_true',
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_orders( WP_REST_Request $request ) {
		$response = new LP_REST_Response();
		$params   = $request->get_params();

		$args = array(
			'post_type'      => LP_ORDER_CPT,
			'post_status'    => $params['post_status'] ?? [
				'lp-pending',
				'lp-processing',
				'lp-completed',
				'lp-cancelled',
				'lp-failed',
			],
			'posts_per_page' => $params['posts_per_page'] ?? 10,
			'paged'          => $params['paged'] ?? 1,
			'order_by'       => $params['order_by'] ?? 'date',
			'order'          => $params['order_by'] ?? 'desc',
		);

		if ( isset( $params['order-id'] ) ) {
			$args['order-id'] = $params['order-id'];
		}

		if ( isset( $params['course-name'] ) ) {
			$args['course-name'] = $params['course-name'];
		}

		if ( isset( $params['student'] ) ) {
			$args['student'] = $params['student'];
		}

		if ( isset( $params['author'] ) ) {
			$args['author'] = $params['author'];
		}

		//        $data  = array();
		$query = new \WP_Query( $args );

		try {
			ob_start();
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					learn_press_get_template( 'admin/order-list/list-item.php' );
				}
				wp_reset_postdata();
			} else {
				?>
				<tr class="no-items">
					<td class="colspanchange" colspan="6">
						<?php esc_html_e( 'No order found', 'learnpress' ); ?>
					</td>
				</tr>
				<?php
			}

			$response->status = 'success';
			$response->data   = ob_get_clean();
			ob_start();
			$data = array(
				'paged'         => intval( $args['paged'] ),
				'max_pages'     => $query->max_num_pages,
				'total'         => $query->found_posts,
				'item_per_page' => $args['posts_per_page'],
			);
			learn_press_get_template( 'admin/order-list/pagination.php', compact( 'data' ) );
			$response->pagination = ob_get_clean();
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * @param $where
	 * @param $query
	 *
	 * @return mixed|string
	 */
	public function posts_where_paged( $where, $query ) {
		global $wpdb;
		if ( isset( $query->query_vars['order-id'] ) && ! empty( $query->query_vars['order-id'] ) ) {
			$order_id = str_replace( '#', '', $_REQUEST['order-id'] );
			$where   .= $wpdb->prepare(
				"
                    AND {$wpdb->posts}.ID = %s
                	",
				$order_id
			);
		}

		if ( isset( $query->query_vars['course-name'] ) && ! empty( $query->query_vars['course-name'] ) ) {
			$where .= $wpdb->prepare(
				'
                    AND loi.order_item_name LIKE %s
                	',
				'%' . $wpdb->esc_like( trim( $query->query_vars['course-name'] ) ) . '%'
			);
		}

		if ( isset( $query->query_vars['student'] ) && ! empty( $query->query_vars['student'] ) ) {
			$where .= $wpdb->prepare(
				"
                    AND {$wpdb->postmeta}.meta_key = %s AND {$wpdb->postmeta}.meta_value = %s
                	",
				'_user_id',
				$query->query_vars['student']
			);
		}

		return $where;
	}

	/**
	 * @param $join
	 * @param $query
	 *
	 * @return mixed|string
	 */
	public function posts_join_paged( $join, $query ) {
		global $wpdb;

		if ( isset( $query->query_vars['course-name'] ) && ! empty( $query->query_vars['course-name'] ) ) {
			$join .= " INNER JOIN {$wpdb->learnpress_order_items} AS loi ON {$wpdb->posts}.ID = loi.order_id";
		}

		if ( isset( $query->query_vars['student'] ) && ! empty( $query->query_vars['student'] ) ) {
			$join .= " INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id";
		}

		return $join;
	}
}
