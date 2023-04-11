<?php

/**
 * Class LP_REST_Admin_Order_Controller
 *
 * @since 3.3.0
 */
class LP_REST_Admin_Order_Controller extends LP_Abstract_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'lp/v1/admin';
        $this->rest_base = 'orders';

        parent::__construct();
    }

    /**
     * Register rest routes.
     */
    public function register_routes() {
        $this->routes = array(
            'list-order' => array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_orders' ),
//                  'permission_callback' => function () {
//                      return current_user_can( 'edit_posts' );
//                  }
                ),
            ),
        );

        parent::register_routes();
    }

    /**
     * Get order list in admin settings.
     *
     * @return void
     */
    public function get_orders(WP_REST_Request $request) {
        $params = $request->get_params();

        $args = array(
            'post_type'      => LP_ORDER_CPT,
            'posts_per_page' => $params['posts_per_page'] ?? 20,
            'paged' => $params['paged'] ?? 1,
        );

        $response = new LP_REST_Response();

        try {
            if (empty($params)) {
                throw new Exception(esc_html__('No params!', 'learnpress'));
            }
        } catch (Exception $e) {
            $response->message = $e->getMessage();
        }

        return rest_ensure_response($response);
    }
}
