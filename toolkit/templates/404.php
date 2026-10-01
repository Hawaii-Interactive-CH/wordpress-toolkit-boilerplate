<?php

namespace Toolkit;

get_header();

?>

<section class="page404-wrapper">
    <h1><?= esc_html__('Oops!', 'toolkit') ?></h1>
    <p><?= esc_html__('The page you are looking for does not exist.', 'toolkit') ?></p>
    <a href="<?= esc_url(home_url('/')) ?>" class="btn"><?= esc_html__('Back to the home page', 'toolkit') ?></a>
</section>

<?php get_footer(); ?>
