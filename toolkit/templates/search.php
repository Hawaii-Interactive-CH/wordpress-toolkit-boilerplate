<?php

namespace Toolkit;

use Toolkit\models\Search;

get_header();
?>

<div class="search-query">
    <form action="<?= esc_url(home_url('/')) ?>" role="search">
        <div class="input-wrapper">
            <label for="search"><?= esc_html__('Your search', 'toolkit') ?></label>
            <h1>
                <input name="s" id="search" type="search" value="<?php the_search_query(); ?>">
                <input id="submit" type="submit" value="" aria-label="<?= esc_attr__('Search', 'toolkit') ?>">
            </h1>
        </div>
    </form>

    <div class="search-wrapper">
        <?php Search::all(function ($model) {
            ?>
            <article class="search-item">
                <a href="<?= $model->link() ?>">
                    <h2><?= $model->title() ?></h2>
                    <p class="excerpt"><?= $model->excerpt(16) ?></p>
                </a>
            </article>
        <?php
        }); ?>
    </div>

</div>

<?php get_footer(); ?>
