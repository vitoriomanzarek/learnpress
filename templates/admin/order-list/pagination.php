<?php
if (! isset($data) && ! isset($has_current_page)) {
    return;
}

$num   = wp_count_posts(LP_ORDER_CPT, 'readable');
$total = array_sum((array) $num);

foreach (get_post_stati(array( 'show_in_admin_all_list' => false )) as $state) {
    $total -= $num->$state;
}
?>
    <div class="tablenav-pages">
            <span class="displaying-num">
                <?php
                printf(esc_html(_n('%s item', '%s items', $total, 'learnpress')), $total);
                ?>
            </span>
        <span class="pagination-links"><span class="tablenav-pages-navspan button disabled">«</span>
                <?php
                if (! empty($has_current_page)) {
                    ?>
                    <span class="paging-input">
                        <input class="current-page" id="current-page-selector" type="text" name="paged" value="1"
                               size="1">
                        <span class="tablenav-paging-text"> of <span class="total-pages">2</span></span></span>
                    <?php
                }
                ?>
            <a class="next-page button"><span>›</span></a>
            <a class="last-page button"><span>»</span></a></span>
    </div>
<?php


