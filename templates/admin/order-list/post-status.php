<?php
$count_orders   = wp_count_posts( LP_ORDER_CPT, 'readable' );
$pending_num    = intval( $count_orders->{LP_ORDER_PENDING_DB} );
$processing_num = intval( $count_orders->{LP_ORDER_PROCESSING_DB} );
$completed_num  = intval( $count_orders->{LP_ORDER_COMPLETED_DB} );
$cancelled_num  = intval( $count_orders->{LP_ORDER_CANCELLED_DB} );
$failed_num     = intval( $count_orders->{LP_ORDER_FAILED_DB} );
$total          = $pending_num + $processing_num + $completed_num + $cancelled_num + $failed_num;
?>
	<ul class="lp-order-status-list subsubsub">
		<?php
		if ( $total ) {
			?>
			<li class="all"><a href="#" class="current"><?php esc_html_e( 'All', 'learnpress' ); ?> <span
						class="count">(<?php echo esc_html( $total ); ?>)</span></a> |
			</li>
			<?php
		}
		$status = learn_press_get_register_order_statuses();

		foreach ( $status as $name => $item ) {
			?>
			<li class="<?php echo esc_attr( $name ); ?>">
				<a href="#">
					<?php echo esc_attr( $item['label'] ); ?> <span class="count">(
						<?php
						if ( $name === LP_ORDER_PENDING_DB ) {
							echo esc_html( $pending_num );
						} elseif ( $name === LP_ORDER_PROCESSING_DB ) {
							echo esc_html( $processing_num );
						} elseif ( $name === LP_ORDER_COMPLETED_DB ) {
							echo esc_html( $completed_num );
						} elseif ( $name === LP_ORDER_CANCELLED_DB ) {
							echo esc_html( $cancelled_num );
						} else {
							echo esc_html( $failed_num );
						}
						?>
						)</span></a>
				<?php
				if ( $name !== LP_ORDER_FAILED_DB ) {
					?>
					|
					<?php
				}
				?>
			</li>
			<?php
		}
		?>
	</ul>
<?php
