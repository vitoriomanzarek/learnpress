<?php

/**
 * Class LP_REST_Admin_Order_Controller
 *
 * @since 3.3.0
 */
class LP_REST_Admin_Order_Controller extends LP_Abstract_REST_Controller {

	public function __construct() {
		$this->namespace = 'lp/v1/admin';
		$this->rest_base = 'order';

		parent::__construct();
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$this->routes = array(
			'search-users'   => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'search_users' ),
					'permission_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				),
			),
			'search-courses' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'search_courses' ),
					'permission_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				),
			),
			'add-courses'    => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_courses' ),
					'permission_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				),
			),
			'remove-courses' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'remove_courses' ),
					'permission_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * Get Users.
	 *
	 * @return void
	 */
	public function search_users( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new LP_REST_Response();
		$response->status = 'error';

		$term       = $params['term'] ?? '';
		$type       = $params['type'] ?? '';
		$context    = $params['context'] ?? '';
		$context_id = $params['contextID'] ?? '';
		$paged      = $params['paged'] ?? '';
		$multiple   = $params['multiple'] === 'yes';
		$exclude    = $params['exclude'] ?? array();

		try {
			$search = new LP_Modal_Search_Users_API( compact( 'term', 'type', 'context', 'context_id', 'paged', 'multiple', 'exclude' ) );

			$response->status = 'success';
			$response->html   = $search->get_html_items();
			$response->nav    = $search->get_pagination();
			$response->users  = $search->get_items();

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Search courses by requesting params.
	 */
	public function search_courses( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new LP_REST_Response();
		$response->status = 'error';

		$term       = $params['term'] ?? '';
		$type       = $params['type'] ?? '';
		$context    = $params['context'] ?? '';
		$context_id = $params['contextID'] ?? '';
		$paged      = $params['paged'] ?? '';
		$exclude    = $params['exclude'] ?? array();

		try {
			$search = new LP_Modal_Search_Courses_API( compact( 'term', 'type', 'context', 'context_id', 'paged', 'exclude' ) );

			$response->status = 'success';
			$response->html   = $search->get_html_items();
			$response->nav    = $search->get_pagination();
			$response->users  = $search->get_items();

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );

	}

	public function add_courses( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new LP_REST_Response();
		$response->status = 'error';

		$item_ids = $params['items'] ?? array();
		$order_id = $params['orderID'] ?? 0;

		try {

			if ( ! current_user_can( 'edit_lp_orders' ) ) {
				throw new Exception( __( 'You do not have permission to do this action.', 'learnpress' ) );
			}

			// validate order
			if ( ! is_numeric( $order_id ) || learn_press_get_post_type( $order_id ) != 'lp_order' ) {
				throw new Exception( __( 'Invalid order ID', 'learnpress' ) );
			}

			// validate item
			$order      = learn_press_get_order( $order_id );
			$order_item = $order->add_items( $item_ids );
			if ( $order_item ) {
				$html                        = '';
				$order_items                 = $order->get_items();
				$order_data                  = learn_press_update_order_items( $order_id );
				$currency_symbol             = learn_press_get_currency_symbol( $order_data['currency'] );
				$order_data['subtotal_html'] = learn_press_format_price( $order_data['subtotal'], $currency_symbol );
				$order_data['total_html']    = learn_press_format_price( $order_data['total'], $currency_symbol );

				if ( $order_items ) {
					foreach ( $order_items as $item ) {
						if ( ! in_array( $item['id'], $order_item ) ) {
							continue;
						}

						ob_start();
						learn_press_admin_view( 'meta-boxes/order/order-item.php', compact( 'item', 'order' ) );
						$html .= ob_get_clean();
					}
				}

				$response->status     = 'success';
				$response->item_html  = $html;
				$response->order_data = $order_data;
			}
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}

	public function remove_courses( WP_REST_Request $request ) {
		$params           = $request->get_params();
		$response         = new LP_REST_Response();
		$response->status = 'error';

		$item_ids = $params['itemID'] ?? array();
		$order_id = $params['orderID'] ?? 0;

		try {

			if ( ! current_user_can( 'edit_lp_orders' ) ) {
				throw new Exception( __( 'You do not have permission to do this action.', 'learnpress' ) );
			}

			// validate order
			if ( ! is_numeric( $order_id ) || learn_press_get_post_type( $order_id ) != 'lp_order' ) {
				throw new Exception( __( 'Invalid order ID', 'learnpress' ) );
			}

			// validate item
			$order = learn_press_get_order( $order_id );

			global $wpdb;

			foreach ( $item_ids as $item_id ) {
				$order->remove_item( $item_id );
			}

			$order_data                  = learn_press_update_order_items( $order_id );
			$currency_symbol             = learn_press_get_currency_symbol( $order_data['currency'] );
			$order_data['subtotal_html'] = learn_press_format_price( $order_data['subtotal'], $currency_symbol );
			$order_data['total_html']    = learn_press_format_price( $order_data['total'], $currency_symbol );
			$order_items                 = $order->get_items();

			if ( $order_items ) {
				$html = '';
				foreach ( $order_items as $item ) {
					ob_start();
					include learn_press_get_admin_view( 'meta-boxes/order/order-item.php' );
					$html .= ob_get_clean();
				}

				$response->status     = 'success';
				$response->item_html  = $html;
				$response->order_data = $order_data;
			}
		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}
}
