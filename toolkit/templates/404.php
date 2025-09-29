<?php

namespace Toolkit;

get_header(); 

?>

<main>
    <div class="page404-wrapper">
        <h1><?= __('Oups !', 'toolkit') ?></h1>
        <p><?= __('La page que vous recherchez n\'existe pas', 'toolkit') ?></p>
        <a href="<?= home_url() ?>" class="btn"><?= __('Revenir sur la page d\'accueil', 'toolkit') ?></a>
    </div>
</main>

<?php get_footer(); ?>
