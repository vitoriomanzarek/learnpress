<?php
$id              = get_the_ID();
$edit_action_url = add_query_arg(
	[
		'post'   => $id,
		'action' => 'edit',
	],
	admin_url( 'post.php' )
);

$lp_order = learn_press_get_order( $id );
?>
<tr id="<?php echo esc_attr( 'post-' . $id ); ?>"
	class="<?php echo esc_attr( 'iedit author-other level-0 post-' . $id . ' type-lp_order status-lp-pending hentry entry' ); ?>">
<!--	<th scope="row" class="check-column">-->
<!--		<input id="--><?php //echo esc_attr( 'cb-select-' . $id ); ?><!--" type="checkbox" name="post[]"-->
<!--			   value="--><?php //echo esc_attr( $id ); ?><!--">-->
<!--	</th>-->
	<td class="title column-title has-row-actions column-primary page-title" data-colname="Order">
		<div class="locked-info"><span class="locked-avatar"></span> <span
				class="locked-text"></span></div>
		<strong><a class="row-title"
				   href="<?php echo esc_url_raw( $edit_action_url ); ?>"><?php printf( esc_html( '#%08d' ), $id ); ?></a></strong>
	</td>
	<td class="order_student column-order_student" data-colname="Student">
		<?php
		$user_ids = $lp_order->get_users();
		if ( $user_ids ) {
			$outputs = array();
			foreach ( $user_ids as $user_id ) {
				if ( get_user_by( 'id', $user_id ) ) {
					$user      = learn_press_get_user( $user_id );
					$outputs[] = sprintf(
						'<a href="user-edit.php?user_id=%d">%s (%s)</a><span>%s</span>',
						$user_id,
						$user->get_data( 'user_login' ),
						$user->get_data( 'display_name' ),
						$user->get_data( 'user_email' )
					);
				} else {
					if ( sizeof( $user_ids ) == 1 ) {
						$outputs[] = $lp_order->get_customer_name();
					}
				}
			}
			echo join( ', ', $outputs );
		} else {
			echo esc_html__( '(Guest)', 'learnpress' );
		}
		?>
	</td>
	<td class="order_items column-order_items" data-colname="Purchased">
		<?php
		$links = array();
		$items = $lp_order->get_items();
		$count = sizeof( $items );

		foreach ( $items as $item ) {
			if ( empty( $item['course_id'] ) || get_post_type( $item['course_id'] ) !== LP_COURSE_CPT ) {
				$links[] = apply_filters( 'learn-press/order-item-not-course-id', esc_html__( 'The course does not exist', 'learnpress' ), $item );
			} elseif ( get_post_status( $item['course_id'] ) !== 'publish' ) {
				$links[] = get_the_title( $item['course_id'] ) . sprintf( ' (#%d - %s)', $item['course_id'], esc_html__( 'Deleted', 'learnpress' ) );
			} else {
				$link = '<a href="' . get_the_permalink( $item['course_id'] ) . '">' . get_the_title( $item['course_id'] ) . ' (#' . $item['course_id'] . ')' . '</a>';
				if ( $count > 1 ) {
					$link = sprintf( '<li>%s</li>', $link );
				}
				$links[] = apply_filters( 'learn-press/order-item-link', $link, $item );

			}
		}

		if ( $count > 1 ) {
			echo sprintf( '<ol>%s</ol>', join( '', $links ) );
		} elseif ( 1 == $count ) {
			echo join( '', $links );
		} else {
			echo esc_html__( '(No item)', 'learnpress' );
		}
		?>
	</td>
	<td class="order_date column-order_date" data-colname="Date">
		<?php
		$t_time    = get_the_time( 'Y/m/d g:i:s a' );
		$m_time    = $t_time = get_the_time( 'Y/m/d g:i:s a' );
		$m_time    = get_the_date( '', $id );
		$time      = get_post_time( 'G', true, $id );
		$time_diff = time() - $time;

		if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
			$h_time = sprintf( __( '%s ago', 'learnpress' ), human_time_diff( $time ) );
		} else {
			$h_time = mysql2date( 'Y/m/d', $m_time );
		}

		echo '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'learn_press_order_column_time', $h_time, $lp_order ) ) . '</abbr>';
		?>
	</td>
	<td class="order_total column-order_total" data-colname="Total">
		<?php
		echo wp_kses_post( $lp_order->get_formatted_order_total() );
		$method_title = $lp_order->get_payment_method_title();

		if ( $method_title ) {
			?>
			<div class="payment-method-title">
				<?php echo wp_kses_post( $lp_order->get_total() == 0 ? $method_title : sprintf( __( 'Pay via <strong>%s</strong>', 'learnpress' ), apply_filters( 'learn-press/order-payment-method-title', $method_title, $lp_order ), $lp_order ) ); ?>
			</div>
			<?php
		}
		?>
	</td>
	<td class="order_status column-order_status" data-colname="Status">
		<?php
		$lp_order_icons = LP_Order::get_icons_status();
		$icon           = $lp_order_icons[ $lp_order->get_status() ] ?? '';
		echo sprintf(
			'<span class="lp-order-status %s">%s%s</span>',
			$lp_order->get_status(),
			$icon,
			LP_Order::get_status_label( $lp_order->get_status() )
		);
		?>
	</td>
</tr>

