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

  <!-- SEO -->
  <?= render_partial("head/seo") ?>

  <!-- JSON-LD -->
  <?= render_partial("head/jsonld") ?>

  <!-- JavaScript -->
  <script>
  window.baseUrl = "<?= get_home_url() ?>";
  window.appName = "<?= sanitize_title(get_bloginfo("name")) ?>";
  window.apiUrl = "<?= get_rest_url() ?>";
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