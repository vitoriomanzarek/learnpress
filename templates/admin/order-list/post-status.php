<?php
$num = wp_count_posts( LP_ORDER_CPT, 'readable' );
?>
	<ul class="subsubsub">
		<li class="all"><a href="#" class="current">All <span
					class="count">(24)</span></a> |
		</li>
		<?php
		$status = learn_press_get_register_order_statuses();

		foreach ( $status as $name => $item ) {
			?>
			<li class="<?php echo esc_attr( $name ); ?>">
				<a href="#">
					<?php echo esc_attr( $item['label'] ); ?> <span class="count">(18)</span></a>
				<?php
				if ( $name !== 'lp-failed' ) {
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
