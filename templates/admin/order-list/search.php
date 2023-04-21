<?php
if (! isset($data)) {
    return;
}
?>

<p class="search-box">
    <input
        type="search" id="post-search-input" name="s" value=""
        placeholder="<?php esc_attr_e('Order number, course name, etc.', 'learnpress'); ?>"
        style="width: 300px;"
    >
    <input type="submit" id="search-submit" class="button" value="<?php esc_html_e('Search Orders', 'learnpress');?>">
</p>
