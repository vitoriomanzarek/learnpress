<?php
/**
 * Template for displaying content of single lesson.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

defined('ABSPATH') || exit;

/**
 * Header for page
 */
if (! wp_is_block_theme()) {
    get_header('course');
}

/**
 * @since 3.0.0
 */
do_action('learn-press/before-main-content');
do_action('learn-press/before-main-content-single-course');

global $wp, $wp_query, $lp_course_item;
$vars = $wp->query_vars;

if (!isset($vars['course-name'])) {
    return;
}


$args = array(
    'name'      => $vars['course-name'],
    'post_type' => LP_COURSE_CPT,
);

$query    = new \WP_Query($args);


if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        learn_press_get_template('content-single-course');
    }
    wp_reset_postdata();
}
/**
 * @since 3.0.0
 */
do_action('learn-press/after-main-content-single-course');
do_action('learn-press/after-main-content');

/**
 * LP sidebar
 */
do_action('learn-press/sidebar');

/**
 * Footer for page
 */
if (! wp_is_block_theme()) {
    get_footer('course');
}
