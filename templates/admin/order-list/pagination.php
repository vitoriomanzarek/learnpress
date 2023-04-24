<?php
if ( ! isset( $data ) ) {
	return;
}


$max_page = $data['max_pages'];

$current_page = $data['paged'];
$next_page    = $current_page + 1;
$prev_page    = $current_page - 1;
$total        = $data['total'];

?>
	<div class="tablenav-pages">
		<span class="displaying-num">
			<?php
			printf( esc_html( _n( '%s item', '%s items', $total, 'learnpress' ) ), $total );
			?>
		</span>
		<span class="pagination-links">
			<?php
			if ( $current_page === 1 ) {
				?>
				<span class="tablenav-pages-navspan button disabled">«</span>
				<span class="tablenav-pages-navspan button disabled">‹</span>
				<?php
			} else {
				?>
				<a class="first-page button"><span>«</span></a>
				<a class="prev-page button"><span>‹</span></a>
				<?php
			}
			?>
			<span class="paging-input">
					<input class="current-page" id="current-page-selector" type="text" name="paged"
						   value="<?php echo esc_attr( $current_page ); ?>"
						   size="1">
					<span class="tablenav-paging-text"> of <span
							class="total-pages"><?php echo esc_html( $max_page ); ?>
					</span></span>
			</span>
			<?php
			if ( $current_page === intval( $max_page ) ) {
				?>
				<span class="tablenav-pages-navspan button disabled">›</span>
				<span class="tablenav-pages-navspan button disabled">»</span>
				<?php
			} else {
				?>
				<a class="next-page button"><span>›</span></a>
				<a class="last-page button"><span>»</span></a>
				<?php
			}
			?>

	</div>
<?php


