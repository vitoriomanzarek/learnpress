<?php
$num = wp_count_posts( LP_ORDER_CPT, 'readable' );
?>
	<ul class="subsubsub">
		<li class="all"><a href="edit.php?post_type=lp_order&amp;all_posts=1">All <span
					class="count">(24)</span></a> |
		</li>
		<li class="lp-pending"><a href="edit.php?post_status=lp-pending&amp;post_type=lp_order" class="current"
			>Pending Payment <span class="count">(18)</span></a> |
		</li>
		<li class="lp-processing"><a href="edit.php?post_status=lp-processing&amp;post_type=lp_order">Processing
				<span class="count">(1)</span></a> |
		</li>
		<li class="lp-completed"><a href="edit.php?post_status=lp-completed&amp;post_type=lp_order">Completed
				<span class="count">(3)</span></a> |
		</li>
		<li class="lp-cancelled"><a href="edit.php?post_status=lp-cancelled&amp;post_type=lp_order">Cancelled
				<span class="count">(1)</span></a> |
		</li>
		<li class="lp-failed"><a href="edit.php?post_status=lp-failed&amp;post_type=lp_order">Failed <span
					class="count">(1)</span></a></li>
	</ul>
<?php
