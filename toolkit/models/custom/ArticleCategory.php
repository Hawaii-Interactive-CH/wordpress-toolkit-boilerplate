<?php

namespace Toolkit\models\custom;

use Toolkit\models\Taxonomy;

// Prevent direct access.
defined('ABSPATH') or exit;

class ArticleCategory extends Taxonomy
{
    const TYPE = 'article_category';

    public static function register(): void
    {
        register_taxonomy(self::TYPE, Article::TYPE, [
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'article-category', 'with_front' => false],
            'labels'            => [
                'name'          => __('Categories', 'toolkit'),
                'singular_name' => __('Category', 'toolkit'),
                'add_new_item'  => __('Add New Category', 'toolkit'),
            ],
        ]);
    }
}
