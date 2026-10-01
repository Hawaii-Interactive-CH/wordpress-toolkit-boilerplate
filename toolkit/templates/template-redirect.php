<?php
/* Template Name: Redirect to first child page */

namespace Toolkit;

$children = get_pages([
    "child_of" => get_queried_object_id(),
    "sort_column" => "menu_order",
]);
if ($children) {
    wp_redirect(get_permalink($children[0]->ID));
    exit;
}

wp_redirect(home_url());
exit;
