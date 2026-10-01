<?php

namespace Toolkit;

use Toolkit\models\Page;
use Toolkit\models\Media;

get_header();
?>

<?php Page::current(function (Page $model) { ?>
    <section>
        <h1><?= esc_html($model->title()) ?></h1>

        <?php $model->thumbnail(function (Media $media) {
            echo render_partial('media/full-width', ['media' => $media, 'lazy' => false]);
        }); ?>

        <?php if ($model->is_password_required()) { ?>
            <?= $model->password_form() ?>
        <?php } else { ?>
            <div><?= $model->content() ?></div>
        <?php } ?>
    </section>
<?php }); ?>

<?php get_footer(); ?>
