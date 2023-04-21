<?php
?>
<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
    <a href="http://realpress.local:10006/wp-admin/edit.php?post_status=lp-pending&amp;post_type=lp_order&amp;orderby=title&amp;order=asc">
        <span><?php esc_html_e('Order', 'learnpress');?></span>
        <span class="sorting-indicator"></span>
    </a>
</th>
<th scope="col" id="order_student" class="manage-column column-order_student sortable desc">
    <a href="http://realpress.local:10006/wp-admin/edit.php?post_status=lp-pending&amp;post_type=lp_order&amp;orderby=student&amp;order=asc">
        <span><?php esc_html_e('Student', 'learnpress');?></span>
        <span class="sorting-indicator"></span>
    </a>
</th>
<th scope="col" id="order_items" class="manage-column column-order_items">Purchased</th>
<th scope="col" id="order_date" class="manage-column column-order_date sortable desc">
    <a href="http://realpress.local:10006/wp-admin/edit.php?post_status=lp-pending&amp;post_type=lp_order&amp;orderby=date&amp;order=asc">
        <span><?php esc_html_e('Date', 'learnpress');?></span>
        <span class="sorting-indicator"></span>
    </a>
</th>
<th scope="col" id="order_total" class="manage-column column-order_total sortable desc">
    <a href="http://realpress.local:10006/wp-admin/edit.php?post_status=lp-pending&amp;post_type=lp_order&amp;orderby=order_total&amp;order=asc">
        <span><?php esc_html_e('Total', 'learnpress');?></span>
        <span class="sorting-indicator"></span>
    </a>
</th>
<th scope="col" id="order_status" class="manage-column column-order_status">
    <span class="status_head tips" data-tip="Status"><?php esc_html_e('Total', 'learnpress');?></span>
</th>
