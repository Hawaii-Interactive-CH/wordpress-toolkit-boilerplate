<?php
/** @var \Toolkit\models\custom\BlockHero $block */

$title    = $block->acf('title');
$subtitle = $block->acf('subtitle');
$cta_text = $block->acf('cta_text');
$cta_url  = $block->acf('cta_url');
?>
<section class="block-hero">
    <?php $block->acf_media('background_image', function (\Toolkit\models\Media $media) { ?>
        <div class="block-hero__bg" aria-hidden="true">
            <?= $media->picture([
                ['size' => 'image-xl', 'size2x' => 'image-xl-2x', 'sizes' => false],
            ], 'block-hero__bg-img') ?>
        </div>
    <?php }); ?>

    <div class="block-hero__inner">
        <?php if ($title): ?>
            <h1 class="block-hero__title"><?= esc_html($title) ?></h1>
        <?php endif; ?>

        <?php if ($subtitle): ?>
            <p class="block-hero__subtitle"><?= esc_html($subtitle) ?></p>
        <?php endif; ?>

        <?php if ($cta_text && $cta_url): ?>
            <a class="block-hero__cta" href="<?= esc_url($cta_url) ?>">
                <?= esc_html($cta_text) ?>
            </a>
        <?php endif; ?>
    </div>
</section>
