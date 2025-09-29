<?php

namespace Toolkit;

use Toolkit\models\Page;
use Toolkit\models\Media;

get_header();
?>

<?php Page::current(function (Page $model) {
    ?>
    <section>
        <h1><?= $model->title() ?></h1>
        <?php if ($model->is_password_required()) { ?>
            <?= $model->password_form() ?>
        <?php } else { ?>
            <div><?= $model->content() ?></div>
        <?php } ?>
        <?php $model->thumbnail(function (Media $media) {
            ?>
            <img src="<?= $media->src(
                "original"
            ) ?>" alt="<?= $media->title() ?>">
        <?php
        }); ?>
    </section>
<?php
}); ?>

<?php get_footer(); ?>
