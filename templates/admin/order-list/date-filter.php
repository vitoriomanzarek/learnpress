<?php
if (!isset($data)) {
    return;
}

?>
<div class="alignleft actions">
    <select name="m" id="filter-by-date">
        <option selected="selected" value="0">All dates</option>
        <option value="202304">April 2023</option>
    </select>
    <input type="submit" name="filter_action" id="post-query-submit" class="button"
           value="<?php esc_attr_e('Filter', 'learnpress');?>">
</div>
