<?php

namespace Toolkit;

use Toolkit\models\PostType;

get_header();
?>

<section>
    <h1><?= is_home() ? single_post_title('', false) : get_the_archive_title() ?></h1>

    <?php if (have_posts()) { ?>
        <?php while (have_posts()) {
            the_post();
            echo render_partial('content/card', ['model' => PostType::new(get_post_type(), get_the_ID())]);
        } ?>

        <?php the_posts_pagination(); ?>
    <?php } else { ?>
        <p class="empty-state"><?= esc_html__('No content found.', 'toolkit') ?></p>
    <?php } ?>
</section>

<?php get_footer(); ?>
