<?php

namespace Toolkit;

use Toolkit\models\Media;
use Toolkit\models\PostType;

get_header();
?>

<section>
    <h1><?= is_home() ? single_post_title('', false) : get_the_archive_title() ?></h1>

    <?php while (have_posts()) {
        the_post();
        $model = PostType::new(get_post_type(), get_the_ID());
        ?>
        <article>
            <a href="<?= $model->link() ?>">
                <?php $model->thumbnail(function (Media $media) { ?>
                    <img src="<?= $media->src("image-m") ?>" alt="<?= esc_attr($media->alt()) ?>">
                <?php }); ?>
                <h2><?= $model->title() ?></h2>
                <p><?= $model->excerpt(20) ?></p>
            </a>
        </article>
    <?php } ?>

    <?php the_posts_pagination(); ?>
</section>

<?php get_footer(); ?>
