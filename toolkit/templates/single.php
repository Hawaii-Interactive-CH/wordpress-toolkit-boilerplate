<?php

namespace Toolkit;

use Toolkit\models\Media;
use Toolkit\models\Post;

get_header();
?>

<?php Post::current(function (Post $model) {
    ?>

    <section>
        <h1><?= $model->title() ?></h1>
        <div><?= $model->content() ?></div>
        <?php $model->thumbnail(function (Media $media) {
            ?>
           <!-- exemple d'une image fullwidth et son responsive -->
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
            <!-- Contrôler seulement la taille de l'image, le browser décide la variante plus adapté contrairement à celle d'avant où on ordonne au browser comment faire -->
            <!-- <img src="cat.jpg" alt="cat"
                srcset="<?= $media->src(
                    "image-s"
                ) ?> 400w, <?= $media->src("image-m") ?> 860w, <?= $media->src("image-l") ?> 1280w, <?= $media->src("image-xl") ?> 1920w"
                sizes="100vw"> -->
        <?php
        }); ?>
    </section>

<?php
}); ?>

<?php get_footer(); ?>
