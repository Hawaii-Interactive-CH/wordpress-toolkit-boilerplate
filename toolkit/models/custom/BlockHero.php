<?php

namespace Toolkit\models\custom;

use Toolkit\models\Block;

// Prevent direct access.
defined('ABSPATH') or exit;

class BlockHero extends Block
{
    const TYPE = 'block-hero';

    public static function settings(): array
    {
        return [
            'title'       => __('Hero', 'toolkit'),
            'description' => __('Full-width hero banner with title, subtitle, and a call-to-action link.', 'toolkit'),
            'mode'        => 'edit',
            'icon'        => 'cover-image',
            'keywords'    => ['hero', 'banner', 'cta'],
        ];
    }
}
