<?php
/**
 * Full-width responsive image: the browser picks the best size from the
 * width descriptors (sizes="100vw"), including 2x variants for retina screens.
 *
 * Usage: <?= render_partial('media/full-width', ['media' => $media, 'lazy' => false]) ?>
 * Pass 'lazy' => false for images above the fold (e.g. the main image of a page).
 */

namespace Toolkit\partials\media;

/** @var \Toolkit\models\Media $media */
/** @var bool|null $lazy */
$lazy = $lazy ?? true;
?>
<figure>
    <?= $media->picture([
        ['srcset' => [
            'image-s'     => '400w',
            'image-s-2x'  => '800w',
            'image-m'     => '860w',
            'image-l'     => '1280w',
            'image-m-2x'  => '1720w',
            'image-xl'    => '1920w',
            'image-l-2x'  => '2560w',
            'image-xl-2x' => '3840w',
        ]],
    ], '', $lazy, true, 'image-xl') ?>
    <?php if ($media->caption()) { ?>
        <figcaption><?= esc_html($media->caption()) ?></figcaption>
    <?php } ?>
</figure>
