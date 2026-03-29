<?php

namespace Toolkit\models\custom;

use Toolkit\models\OptionPage;

// Prevent direct access.
defined('ABSPATH') or exit;

class SiteOptions extends OptionPage
{
    const ID = 'site-options';

    const PARAMS = [
        'page_title'  => 'Site Options',
        'menu_title'  => 'Options',
        'parent_slug' => 'options-general.php',
        'capability'  => 'manage_options',
        'redirect'    => false,
    ];

    public static function phone(): string
    {
        return (string) self::acf('phone');
    }

    public static function email(): string
    {
        return (string) self::acf('email');
    }

    public static function address(): string
    {
        return (string) self::acf('address');
    }

    public static function facebook(): string
    {
        return (string) self::acf('facebook_url');
    }

    public static function instagram(): string
    {
        return (string) self::acf('instagram_url');
    }
}
