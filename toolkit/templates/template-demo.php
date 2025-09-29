<?php

/* Template Name: Demo */

namespace Toolkit;

use Toolkit\models\Page;
use Toolkit\models\Media;

get_header();
?>

<?php Page::current(function (Page $model) {
    ?>
    <h1>template demo - custom</h1>
<?php
}); ?>

<?php get_footer(); ?>
