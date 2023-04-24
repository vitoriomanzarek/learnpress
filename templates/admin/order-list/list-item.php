<?php
$id              = get_the_ID();
$edit_action_url = add_query_arg(
	[
		'post'   => $id,
		'action' => 'your_action_name',
	],
	admin_url( 'post.php' )
);
?>
<tr id="<?php echo esc_attr( 'post-' . $id ); ?>"
	class="<?php echo esc_attr( 'iedit author-other level-0 post-' . $id . ' type-lp_order status-lp-pending hentry entry' ); ?>">
	<th scope="row" class="check-column">
		<input id="<?php echo esc_attr( 'cb-select-' . $id ); ?>" type="checkbox" name="post[]"
			   value="<?php echo esc_attr( $id ); ?>">
	</th>
	<td class="title column-title has-row-actions column-primary page-title" data-colname="Order">
		<div class="locked-info"><span class="locked-avatar"></span> <span
				class="locked-text"></span></div>
		<strong><a class="row-title" href="<?php echo esc_url_raw( $edit_action_url ); ?>"><?php printf( esc_html( '#%08d' ), $id ); ?></a></strong>
	</td>
	<td class="order_student column-order_student" data-colname="Student"><a
			href="user-edit.php?user_id=2">test (test test)</a><span></span></td>
	<td class="order_items column-order_items" data-colname="Purchased"><a
			href="http://realpress.local/courses/sample-course/">course123nnn (#10)</a></td>
	<td class="order_date column-order_date" data-colname="Date"><abbr
			title="2023/04/12 4:12:53 am">2023/04/12</abbr></td>
	<td class="order_total column-order_total" data-colname="Total">$0.00</td>
	<td class="order_status column-order_status" data-colname="Status"><span
			class="lp-order-status pending"><i class="dashicons dashicons-flag"></i>Pending</span>
	</td>
</tr>

