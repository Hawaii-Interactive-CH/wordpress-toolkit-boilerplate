<?php

namespace Toolkit;

?>

<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Favicon -->
  <?= render_partial("head/favicon", [
      "color" => "#ffffff",
  ]) ?>

  <?php if (!seo_plugin_active()) { ?>
    <!-- SEO -->
    <?= render_partial("head/seo") ?>

    <!-- JSON-LD -->
    <?= render_partial("head/jsonld") ?>
  <?php } ?>

  <!-- JavaScript -->
  <script>
  window.baseUrl = <?= wp_json_encode(get_home_url(), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) ?>;
  window.appName = <?= wp_json_encode(sanitize_title(get_bloginfo("name")), JSON_HEX_TAG) ?>;
  window.apiUrl = <?= wp_json_encode(get_rest_url(), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) ?>;
  </script>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <header class="header header--horizontal">
    <div class="row">
      <div id="logo">
        <a href="<?= esc_url(home_url('/')) ?>"><?= esc_html(get_bloginfo("name")) ?></a>
      </div>
      <nav id="main-nav" class="main-nav" aria-label="<?= esc_attr__("Main menu", "toolkit") ?>">
        <?php wp_nav_menu([
            "theme_location" => "main_menu",
            "container" => "",
        ]); ?>
      </nav>
      <button type="button" class="hamburger" aria-controls="main-nav" aria-expanded="false" aria-label="<?= esc_attr__("Menu", "toolkit") ?>">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <main class="main">