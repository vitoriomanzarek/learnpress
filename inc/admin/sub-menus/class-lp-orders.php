<?php

/**
 * Class LP_Orders
 *
 * @since 3.0.0
 */
class LP_Orders extends LP_Abstract_Submenu {
	/**
	 * LP_Orders constructor.
	 */
	public function __construct() {
		$this->id         = 'learn-press-orders1';
		$this->menu_title = __( 'Orders', 'learnpress' );
		$this->page_title = __( 'Orders', 'learnpress' );
		$this->priority   = 50;
		$this->callback   = [ $this, 'display' ];

		//add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		parent::__construct();
	}


	public function page_content() {

	}
}

return new LP_Orders();
