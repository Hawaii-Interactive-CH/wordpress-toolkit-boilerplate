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
        <?php $model->thumbnail(function (Media $media) { ?>
            <figure>
                <picture>
                    <source
                        media="(min-width: 1281px)"
                        srcset="<?= $media->src(
                            "image-xl"
                        ) ?> 1x, <?= $media->src("image-xl-2x") ?> 2x">
                    <source
                        media="(max-width: 1280px)"
                        srcset="<?= $media->src(
                            "image-l"
                        ) ?> 1x, <?= $media->src("image-l-2x") ?> 2x">
                    <source
                        media="(max-width: 860px)"
                        srcset="<?= $media->src(
                            "image-m"
                        ) ?> 1x, <?= $media->src("image-m-2x") ?> 2x">
                    <source
                        media="(max-width: 400px)"
                        srcset="<?= $media->src(
                            "image-s"
                        ) ?> 1x, <?= $media->src("image-s-2x") ?> 2x">
                    <img
                        srcset="<?= $media->src("image-l") ?> 1280w,
                        <?= $media->src("image-xl") ?> 1920w"
                        src="<?= $media->src("image-xl") ?>"
                        alt="<?= $media->alt() ?>">
                </picture>
            </figure>
        <?php }); ?>
    </section>
<?php }); ?>

<?php get_footer(); ?>
