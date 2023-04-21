<?php
if (! isset($data)) {
    return;
}
?>
<div class="alignleft actions bulkactions">
    <select name="action2" id="bulk-action-selector-bottom">
        <option value="-1"><?php esc_html_e('Bulk actions', 'learnpress'); ?></option>
        <option value="edit" class="hide-if-no-js"><?php esc_html_e('Edit', 'learnpress'); ?></option>
        <option value="trash"><?php esc_html_e('Move to Trash', 'learnpress'); ?></option>
    </select>
    <input type="submit" id="doaction2" class="button action"
           value="<?php esc_attr_e('Apply', 'learnpress'); ?>">
</div>
