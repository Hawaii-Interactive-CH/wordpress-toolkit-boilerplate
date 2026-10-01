<?php
/**
 * Post card used by listings (archives, search).
 *
 * Usage: <?= render_partial('content/card', ['model' => $model]) ?>
 */

namespace Toolkit\partials\content;

use Toolkit\models\Media;

/** @var \Toolkit\models\PostType $model */
?>
<article class="card">
    <a href="<?= esc_url($model->link()) ?>">
        <?php $model->thumbnail(function (Media $media) { ?>
            <?= $media->picture([
                ['srcset' => ['image-s' => '400w', 'image-s-2x' => '800w', 'image-m' => '860w', 'image-m-2x' => '1720w'], 'sizes' => '(max-width: 860px) 100vw, 860px'],
            ], 'card__image', true, true, 'image-m') ?>
        <?php }); ?>
        <h2><?= esc_html($model->title()) ?></h2>
        <p><?= esc_html($model->excerpt(20)) ?></p>
    </a>
</article>
