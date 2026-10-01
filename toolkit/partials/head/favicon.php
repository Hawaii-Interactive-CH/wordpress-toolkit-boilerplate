<?php

namespace Toolkit\partials\head;

/** @var string|null $color Tile and theme color (optional). */
$color = $color ?? '#ffffff';

$dir = get_template_directory_uri() . "/static/images/favicon";
$dir_exists = file_exists(get_template_directory() . "/static/images/favicon");

?>

<?php if ($dir_exists) { ?>
    <link rel="icon" type="image/png" href="<?= esc_url($dir . "/favicon-96x96.png") ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= esc_url($dir . "/favicon.svg") ?>" />
    <link rel="shortcut icon" href="<?= esc_url($dir . "/favicon.ico") ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= esc_url($dir . "/apple-touch-icon.png") ?>" />
    <link rel="manifest" href="<?= esc_url($dir . "/site.webmanifest") ?>">
    <meta name="apple-mobile-web-app-title" content="<?= esc_attr(get_bloginfo("name")) ?>" />
    <meta name="msapplication-TileColor" content="<?= esc_attr($color) ?>">
    <meta name="msapplication-config" content="<?= esc_url($dir . "/browserconfig.xml") ?>">
    <meta name="theme-color" content="<?= esc_attr($color) ?>">
<?php } ?>
