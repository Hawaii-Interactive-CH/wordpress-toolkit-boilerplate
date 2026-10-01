<?php

namespace Toolkit\routes;

// Prevent direct access.
defined('ABSPATH') or exit;

/**
 * Register theme REST API routes
 *
 * @example
 * register_rest_route(sanitize_title(get_bloginfo('name')) . '/v1', '/articles', [
 *     'methods'             => 'GET',
 *     'callback'            => fn() => \Toolkit\models\custom\Article::query()->find_all(),
 *     'permission_callback' => '__return_true',
 * ]);
 */
add_action('rest_api_init', function () {
});
