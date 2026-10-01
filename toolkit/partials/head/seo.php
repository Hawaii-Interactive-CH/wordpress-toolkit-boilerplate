<?php
/**
 * SEO meta partial — Open Graph, Twitter Card, canonical, and description.
 *
 * @var \Toolkit\models\PostType|null $model  Current page model (optional).
 *
 * Works standalone (no model required). When $model is passed, richer data is
 * used: typed excerpt, model link, and thumbnail resolved via the Media model.
 * Falls back to WordPress core functions for archives, home, and search pages.
 *
 * Usage — baseline (call once in header.php):
 *   <?= render_partial('head/seo') ?>
 *
 * Usage — with model from a template file (richer data):
 *   <?= render_partial('head/seo', ['model' => $model]) ?>
 */

namespace Toolkit\partials\head;

/** @var \Toolkit\models\PostType|null $model */
$model = $model ?? null;

// ---------------------------------------------------------------------------
// Site-wide values
// ---------------------------------------------------------------------------

$site_name   = get_bloginfo('name');
$site_locale = str_replace('-', '_', get_locale());

// ---------------------------------------------------------------------------
// Title  (page title only — no "| Site Name" suffix for social tags)
// ---------------------------------------------------------------------------

$post_id = $model ? $model->id() : (int) get_queried_object_id();

$title = $post_id
    ? esc_attr(get_the_title($post_id))
    : esc_attr($site_name);

// ---------------------------------------------------------------------------
// Description  (excerpt → site tagline)
// ---------------------------------------------------------------------------

if ($model) {
    $raw_desc = wp_strip_all_tags($model->excerpt(30, ''));
} elseif ($post_id) {
    $raw_desc = wp_strip_all_tags(get_the_excerpt($post_id));
} else {
    $raw_desc = '';
}

$description = esc_attr(trim($raw_desc)) ?: esc_attr(get_bloginfo('description'));

// ---------------------------------------------------------------------------
// Canonical URL
// ---------------------------------------------------------------------------

$canonical = $model
    ? esc_url($model->link())
    : esc_url(get_permalink($post_id) ?: home_url('/'));

// ---------------------------------------------------------------------------
// Open Graph type
// ---------------------------------------------------------------------------

$og_type = is_singular() ? 'article' : 'website';

// ---------------------------------------------------------------------------
// OG image  (image-l = 1280 px, closest to Facebook's recommended 1200 px)
// ---------------------------------------------------------------------------

$og_image        = '';
$og_image_width  = 0;
$og_image_height = 0;
$og_image_type   = '';

$thumbnail_id = $post_id ? (int) get_post_thumbnail_id($post_id) : 0;

if ($thumbnail_id) {
    $img_data = wp_get_attachment_image_src($thumbnail_id, 'image-l');

    if ($img_data) {
        $og_image        = esc_url($img_data[0]);
        $og_image_width  = (int) $img_data[1];
        $og_image_height = (int) $img_data[2];
        $og_image_type   = wp_check_filetype($img_data[0])['type'] ?: '';
    }
}

// ---------------------------------------------------------------------------
// Article dates  (only for og:type = article)
// ---------------------------------------------------------------------------

$published_time = '';
$modified_time  = '';

if ($og_type === 'article' && $post_id) {
    $published_time = get_the_date('c', $post_id);
    $modified_time  = get_the_modified_date('c', $post_id);
}

// ---------------------------------------------------------------------------
// Robots  (noindex for password-protected posts)
// ---------------------------------------------------------------------------

$noindex = $model
    ? $model->is_password_required()
    : ($post_id && post_password_required($post_id));
?>

<?php if ($noindex) : ?>
<meta name="robots" content="noindex, nofollow">
<?php endif ?>

<!-- SEO -->
<meta name="description" content="<?= $description ?>">
<link rel="canonical" href="<?= $canonical ?>">

<!-- Open Graph -->
<meta property="og:type"        content="<?= esc_attr($og_type) ?>">
<meta property="og:title"       content="<?= $title ?>">
<meta property="og:description" content="<?= $description ?>">
<meta property="og:url"         content="<?= $canonical ?>">
<meta property="og:site_name"   content="<?= esc_attr($site_name) ?>">
<meta property="og:locale"      content="<?= esc_attr($site_locale) ?>">
<?php if ($og_image) : ?>
<meta property="og:image"        content="<?= $og_image ?>">
<?php if ($og_image_width)  : ?><meta property="og:image:width"  content="<?= $og_image_width ?>">
<?php endif ?>
<?php if ($og_image_height) : ?><meta property="og:image:height" content="<?= $og_image_height ?>">
<?php endif ?>
<?php if ($og_image_type)   : ?><meta property="og:image:type"   content="<?= esc_attr($og_image_type) ?>">
<?php endif ?>
<?php endif ?>
<?php if ($og_type === 'article' && $published_time) : ?>
<meta property="article:published_time" content="<?= esc_attr($published_time) ?>">
<?php endif ?>
<?php if ($og_type === 'article' && $modified_time) : ?>
<meta property="article:modified_time"  content="<?= esc_attr($modified_time) ?>">
<?php endif ?>

<!-- Twitter Card -->
<meta name="twitter:card"        content="<?= $og_image ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title"       content="<?= $title ?>">
<meta name="twitter:description" content="<?= $description ?>">
<?php if ($og_image) : ?>
<meta name="twitter:image" content="<?= $og_image ?>">
<?php endif ?>
