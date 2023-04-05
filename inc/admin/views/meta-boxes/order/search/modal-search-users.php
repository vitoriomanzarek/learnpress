<div id="modal-search-users" class="learn-press-modal">
	<div class="modal-overlay"></div>
	<div class="modal-wrapper">
		<div class="modal-container">
			<header><?php echo __( 'Search users', 'learnpress' ); ?></header>
			<article>
				<div class="wrap-search">
					<input type="text" name="search" placeholder="<?php esc_html_e( 'Search users', 'learnpress' ); ?>" autocomplete="off"/>
					<span class="icon-loading"></span>
				</div>
				<ul class="search-results"></ul>
			</article>
			<footer>
				<div class="search-nav">
				</div>
				<button class="button close-modal"><?php echo __( 'Close', 'learnpress' ); ?></button>
				<button class="button button-primary add-user"><?php echo __( 'Add', 'learnpress' ); ?></button>
			</footer>
		</div>
	</div>
</div>
