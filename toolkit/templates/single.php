<?php

namespace Toolkit;

use Toolkit\models\Media;
use Toolkit\models\PostType;

get_header();
?>

<?php (function (PostType $model) { ?>
    <article>
        <h1><?= esc_html($model->title()) ?></h1>

        <?php $model->thumbnail(function (Media $media) {
            echo render_partial('media/full-width', ['media' => $media, 'lazy' => false]);
        }); ?>

        <?php if ($model->is_password_required()) { ?>
            <?= $model->password_form() ?>
        <?php } else { ?>
            <div><?= $model->content() ?></div>
        <?php } ?>
    </article>
<?php })(PostType::new(get_post_type(), get_queried_object_id())); ?>

<?php get_footer(); ?>
