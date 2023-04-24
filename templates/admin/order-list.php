<?php
if (! isset($data)) {
    return;
}

?>
<div class="lp-order-list wrap">
    <h1 class="wp-heading-inline">
        <?php echo wp_kses_post($data->get_menu_title()); ?>
    </h1>
    <a href="<?php echo add_query_arg(array( 'post_type' => LP_ORDER_CPT ), admin_url('post-new.php')); ?>"
       class="page-title-action">
        <?php esc_html_e('Add New', 'learnpress'); ?>
    </a>
    <hr class="wp-header-end">
    <?php
    learn_press_get_template('admin/order-list/post-status.php', compact('data'));
    ?>
    <form id="posts-filter" method="get">
        <?php
        learn_press_get_template('admin/order-list/search.php', compact('data'));
        ?>
        <div class="tablenav top">
            <?php
            $table_top_items = apply_filters('learn-press/admin/order-list/top-table/items', array(
                'admin/order-list/bulk-action-top.php',
                'admin/order-list/date-filter.php',
                'admin/order-list/pagination.php',
            ));

            foreach ($table_top_items as $item) {
                learn_press_get_template($item, compact('data'));
            }
            ?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <?php
            $data->get_thead();
            $data->get_tbody();
            $data->get_tfoot();
            ?>
        </table>
        <div class="tablenav bottom">
            <?php
            $table_bottom_items = apply_filters('learn-press/admin/order-list/bottom-table/items', array(
                'admin/order-list/bulk-action-bottom.php',
                'admin/order-list/pagination.php',
            ));

            foreach ($table_bottom_items as $item) {
                learn_press_get_template($item, compact('data'));
            }
            ?>
            <br class="clear">
        </div>
    </form>
</div>
