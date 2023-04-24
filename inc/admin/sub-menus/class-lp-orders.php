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
		$this->id         = 'learn-press-orders';
		$this->menu_title = __( 'Orders', 'learnpress' );
		$this->page_title = __( 'Orders', 'learnpress' );
		$this->priority   = 50;
		$this->callback   = [ $this, 'display' ];

		//add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function display() {
		$data = $this;
		learn_press_get_template( 'admin/order-list.php', compact( 'data' ) );
	}

	/**
	 * @param $number
	 *
	 * @return void
	 */
	public function select_all( $number ) {
		?>
		<td class="manage-column column-cb check-column">
			<input id="<?php echo esc_attr( 'cb-select-all-' . $number ); ?>" type="checkbox">
		</td>
		<?php
	}

	/**
	 * @return void
	 */
	public function get_thead() {
		?>
		<thead>
		<tr>
			<?php
//			$this->select_all( 1 );
			$this->get_columns_label();
			?>
		</tr>
		</thead>
		<?php
	}

	public function get_tbody() {
		?>
		<tbody id="the-list">
		<?php
			$this->get_place_holder();
		?>
		</tbody>
		<?php
	}

	/**
	 * @return void
	 */
	public function get_tfoot() {
		?>
		<tfoot>
		<tr>
			<?php
//			$this->select_all( 2 );
			$this->get_columns_label();
			?>
		</tr>
		</tfoot>
		<?php
	}

	public function get_columns_label() {
		$data = $this;
		learn_press_get_template( 'admin/order-list/columns-label.php', compact( 'data' ) );
	}

	/**
	 * @return void
	 */
	private function get_place_holder() {
		for ( $i = 0; $i < 10; $i ++ ) {
			?>
			<tr>
				<?php
				for ( $j = 0; $j < 7; $j ++ ) {
					if ( $j === 0 ) {
						?>
						<th>
							<div class="lp-placeholder"></div>
						</th>
						<?php
					} else {
						?>
						<td>
							<div class="lp-placeholder"></div>
						</td>
						<?php
					}
				}
				?>
			</tr>
			<?php
		}
	}
}

return new LP_Orders();
