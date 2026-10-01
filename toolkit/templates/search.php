<?php

namespace Toolkit;

use Toolkit\models\PostType;

get_header();
?>

<section class="search-query">
    <?php /* translators: %s: search query */ ?>
    <h1><?= esc_html(sprintf(__('Search results for “%s”', 'toolkit'), get_search_query(false))) ?></h1>

    <form action="<?= esc_url(home_url('/')) ?>" role="search">
        <label for="search"><?= esc_html__('Your search', 'toolkit') ?></label>
        <input name="s" id="search" type="search" value="<?php the_search_query(); ?>">
        <button type="submit"><?= esc_html__('Search', 'toolkit') ?></button>
    </form>

    <?php if (have_posts()) { ?>
        <?php while (have_posts()) {
            the_post();
            echo render_partial('content/card', ['model' => PostType::new(get_post_type(), get_the_ID())]);
        } ?>

        <?php the_posts_pagination(); ?>
    <?php } else { ?>
        <p class="empty-state"><?= esc_html__('No results. Try other keywords.', 'toolkit') ?></p>
    <?php } ?>
</section>

<?php get_footer(); ?>
