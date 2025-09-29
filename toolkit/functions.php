<?php

namespace Toolkit;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

define( 'TOOLKIT_ACTIVE_THEME_PATH', get_template_directory() );
define( 'TOOLKIT_ACTIVE_THEME_URL', get_template_directory_uri() );

function toolkit_autoloader($class) {
    // Base namespace for the Toolkit.
    $baseNamespace = 'Toolkit';

    // Check if the class uses the Toolkit namespace.
    if (strpos($class, $baseNamespace . '\\') === 0) {
        // Construct the relative class path.
        $relativeClassPath = str_replace($baseNamespace . '\\', '', $class);
        $relativeFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClassPath) . '.php';

        // Define the paths to check for the class file.
        $possiblePaths = [];

        // Always check the theme directory.
        $possiblePaths[] = TOOLKIT_ACTIVE_THEME_PATH . DIRECTORY_SEPARATOR . $relativeFilePath;

        // Check the plugin directory if the class is not found in the theme.
        if (defined('WP_TOOLKIT_DIR')) {
            $possiblePaths[] = WP_TOOLKIT_DIR . $relativeFilePath;
        }

        // Attempt to require the class file from the first matching path.
        foreach ($possiblePaths as $filePath) {
            if (file_exists($filePath)) {
                require_once $filePath;
                return;
            }
        }
    }
}

// Register the custom autoloader.
spl_autoload_register('Toolkit\\toolkit_autoloader');

/**
 * custom post type 
 * Could also be done from the plugin 
 */

 if (defined('WP_TOOLKIT_DIR')) {

    // custom post type
    $toRegister = [
        ["\\Toolkit\\models\\Config", 'register'],
    ];

    foreach ($toRegister as $register) {
        $register();
    }
}

// register menu
register_nav_menus([
    "main_menu" => "Menu principal",
    "footer_menu" => "Menu pied de page"
]);

const FULL_SIZE = 99999;
$size = null;

if (defined('WP_TOOLKIT_DIR')) {
    $size = "\\Toolkit\\utils\\Size"::get_instance();

    $size->init();
    add_filter(
        "image_resize_dimensions",
        ["\\Toolkit\\utils\\Upscale", "resize"],
        10,
        6
    );
    add_action("init", function () use ($size) {
        //Size::add(string $name, int $width, int $height, bool|array $crop = false);

        // Exemple full-width
        $size::add("image-xl-2x", 3840, FULL_SIZE, false);
        $size::add("image-xl", 1920, FULL_SIZE, false);
        $size::add("image-l-2x", 2560, FULL_SIZE, false);
        $size::add("image-l", 1280, FULL_SIZE, false);
        $size::add("image-m-2x", 1720, FULL_SIZE, false);
        $size::add("image-m", 860, FULL_SIZE, false);
        $size::add("image-s-2x", 800, FULL_SIZE, false);
        $size::add("image-s", 400, FULL_SIZE, false);
    });
}

add_action("admin_init", function () {
    // Check if the WordPress Toolkit Plugin is active before continuing
    if (!defined('WP_TOOLKIT_DIR')) {
        // Alert to install the WordPress Toolkit Plugin
        add_action("admin_notices", function () {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php _e(
                        "Please install the WordPress Toolkit Plugin to use the Toolkit.",
                        "wordpress-toolkit-boilerplate"
                    ); ?>
                </p>
            </div>
            <?php
        });
    }

    if (defined('WP_TOOLKIT_DIR')) {
    // add new size for wysiwyg
        add_filter("image_size_names_choose", function ($sizes) {
            // "size_name" => "Label"
            return $sizes;
        });
    }
});