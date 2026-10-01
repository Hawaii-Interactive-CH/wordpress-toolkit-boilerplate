<?php
/* Template Name: Redirection page enfant */

namespace Toolkit;

$children = get_pages([
    "child_of" => $post->ID,
    "sort_column" => "menu_order",
]);
if ($children) {
    wp_redirect(get_permalink($children[0]->ID));
    exit;
}

wp_redirect(home_url());
exit;
