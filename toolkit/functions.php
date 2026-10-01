<?php

namespace Toolkit;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

define( 'TOOLKIT_ACTIVE_THEME_PATH', get_template_directory() );
define( 'TOOLKIT_ACTIVE_THEME_URL', get_template_directory_uri() );

// Plugin versions before 3.0 only define WP_TOOLKIT_DIR.
if ( ! defined( 'HI_TOOLKIT_DIR' ) && defined( 'WP_TOOLKIT_DIR' ) ) {
    define( 'HI_TOOLKIT_DIR', WP_TOOLKIT_DIR );
}

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
        if (defined('HI_TOOLKIT_DIR')) {
            $possiblePaths[] = HI_TOOLKIT_DIR . $relativeFilePath;
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

 if (defined('HI_TOOLKIT_DIR')) {

    // custom post type
    $toRegister = [
        ["\\Toolkit\\models\\Config", 'register'],
    ];

    add_action('acf/init', function () use ($toRegister) {
        foreach ($toRegister as $register) {
            $register();
        }
    });
}

// Theme REST API routes
require_once TOOLKIT_ACTIVE_THEME_PATH . '/routes/api.php';

// Customizer: runtime CSS custom properties
\Toolkit\utils\CustomizerService::register();

// Performance: inline critical CSS, defer main stylesheet
\Toolkit\utils\CriticalCSSService::register();

// register menu
add_action("init", function () {
    load_theme_textdomain("toolkit", TOOLKIT_ACTIVE_THEME_PATH . "/languages");

    register_nav_menus([
        "main_menu" => __("Main menu", "toolkit"),
        "footer_menu" => __("Footer menu", "toolkit"),
    ]);
});

const FULL_SIZE = 99999;
$size = null;

if (defined('HI_TOOLKIT_DIR')) {
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

// Templates depend on the plugin (render_partial, base models): show a
// message instead of a fatal error on the front end when it is missing.
add_action("template_redirect", function () {
    if (!defined('HI_TOOLKIT_DIR')) {
        wp_die(
            esc_html__("This theme requires the WordPress Toolkit Plugin.", "toolkit"),
            esc_html__("Missing plugin", "toolkit"),
            ["response" => 503]
        );
    }
});

add_action("admin_init", function () {
    // Check if the WordPress Toolkit Plugin is active before continuing
    if (!defined('HI_TOOLKIT_DIR')) {
        // Alert to install the WordPress Toolkit Plugin
        add_action("admin_notices", function () {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php _e(
                        "Please install the WordPress Toolkit Plugin to use the Toolkit.",
                        "toolkit"
                    ); ?>
                </p>
            </div>
            <?php
        });
    }

    if (defined('HI_TOOLKIT_DIR')) {
    // add new size for wysiwyg
        add_filter("image_size_names_choose", function ($sizes) {
            // "size_name" => "Label"
            return $sizes;
        });
    }
});