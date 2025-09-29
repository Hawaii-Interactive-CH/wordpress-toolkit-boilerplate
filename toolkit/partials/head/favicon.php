<?php

namespace Toolkit\partials\head;

$dir = get_template_directory_uri() . "/static/images/favicon";
$dir_exists = file_exists(get_template_directory() . "/static/images/favicon");

?>

<?php if ($dir_exists) { ?>
    <link rel="icon" type="image/png" href="<?= $dir ?>/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= $dir ?>/favicon.svg" />
    <link rel="shortcut icon" href="<?= $dir ?>/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $dir ?>/apple-touch-icon.png" />
    <link rel="manifest" href="<?= $dir ?>/site.webmanifest">
    <meta name="apple-mobile-web-app-title" content="Transparency and Truth" />
    <meta name="msapplication-TileColor" content="<?= $color ?>">
    <meta name="msapplication-config" content="<?= $dir ?>/browserconfig.xml">
    <meta name="theme-color" content="<?= $color ?>">
<?php } ?>
