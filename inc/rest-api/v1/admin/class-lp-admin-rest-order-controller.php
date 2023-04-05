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
			'search-users' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'search_users' ),
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
		$response->status ='error';

		$term        = $params['term'] ?? '';
		$type        = $params['type'] ?? '';
		$context     = $params['context'] ?? '';
		$context_id  = $params['contextID'] ?? '';
		$paged       = $params['paged'] ?? '';
		$multiple    = $params['multiple'] === 'yes';
		$exclude     = $params['exclude'] ?? array();

		try {
			$search = new LP_Modal_Search_Users_API( compact( 'term', 'type', 'context', 'context_id', 'paged', 'multiple', 'exclude' ) );

			$response->status  = 'success';
			$response->html    = $search->get_html_items();
			$response->nav     = $search->get_pagination();
			$response->users   = $search->get_items();

		} catch ( Exception $e ) {
			$response->message = $e->getMessage();
		}

		return rest_ensure_response( $response );
	}
}
