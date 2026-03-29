<?php

namespace Toolkit\models\custom;

use Toolkit\models\CustomPostType;
use Toolkit\models\Media;

// Prevent direct access.
defined('ABSPATH') or exit;

class Article extends CustomPostType implements \JsonSerializable
{
    const TYPE = 'article';
    const SLUG = 'articles';

    public static function type_settings(): array
    {
        return [
            'label'        => __('Articles', 'toolkit'),
            'public'       => true,
            'has_archive'  => true,
            'menu_icon'    => 'dashicons-text-page',
            'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite'      => ['slug' => self::SLUG, 'with_front' => false],
            'show_in_rest' => true,
        ];
    }

    public function category(?callable $callback = null): mixed
    {
        return $this->terms(ArticleCategory::class, $callback);
    }

    public function jsonSerialize(): mixed
    {
        $data = [
            'id'      => $this->id(),
            'title'   => $this->title(),
            'slug'    => $this->slug(),
            'link'    => $this->link(),
            'excerpt' => $this->excerpt(20),
            'date'    => $this->date('Y-m-d'),
        ];

        $this->thumbnail(function (Media $media) use (&$data) {
            $data['thumbnail'] = $media->src('image-m');
        });

        return $data;
    }
}
