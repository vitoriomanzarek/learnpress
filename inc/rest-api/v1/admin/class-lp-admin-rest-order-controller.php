<?php

/**
 * Class LP_REST_Users_Controller
 *
 * @since 3.3.0
 */
class LP_REST_Admin_Order_Controller extends LP_Abstract_REST_Controller
{

    public function __construct()
    {
        $this->namespace = 'lp/v1/admin';
        $this->rest_base = 'order';

        parent::__construct();
    }

    /**
     * Register rest routes.
     */
    public function register_routes()
    {
        $this->routes = array(
            'list-order' => array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_list_orders' ),
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
	public function get_list_orders( WP_REST_Request $request )
    {
        $response = new LP_REST_Response();
        $params         = $request->get_params();

        $args = array(
            'post_type'   => LP_ORDER_CPT,
            'post_status' => $params['post_status'] ?? 'any',
            'posts_per_page' => $params['posts_per_page'] ?? 10,
            'order_by' => $params['order_by'] ?? 'date',
            'order' => $params['order_by'] ?? 'desc',
        );

        $data  = array();

        $query = new \WP_Query($args);

        try {
            ob_start();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    learn_press_get_template('admin/order-list/list-item.php');
                }
                wp_reset_postdata();
            }

            $response->status = 'success';
            $response->data   = ob_get_clean();
        } catch (Exception $e) {
            $response->message = $e->getMessage();
        }

        return rest_ensure_response($response);
    }
}
