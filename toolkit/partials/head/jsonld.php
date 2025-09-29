<?php

namespace Toolkit\partials\head;
?>

<?php
    global $post;
    setup_postdata($post);

    // Prepare data for JSON-LD
    $json_ld_data = [
        "@context" => "http://schema.org",
        "@type" => "WebSite",
        "headline" => get_the_title(),
        "image" => [get_the_post_thumbnail_url($post, 'full')],
        "url" => get_permalink($post),
        "name" => get_bloginfo('name'),
        "countryOfOrigin" => "Switzerland",
        "inLanguage" => str_replace('lang=', '', get_language_attributes()),
        "author" => [
            "@type" => "Person",
            "name" => get_bloginfo('name'),
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => get_bloginfo('name'),
            "logo" => [
                "@type" => "ImageObject",
                "url" => get_stylesheet_directory_uri() . "/static/images/logo.jpg"
            ]
        ],
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => get_home_url() . "/?s={search_term_string}",
            "query-input" => "required name=search_term_string"
        ],
        "datePublished" => get_the_date('c', $post),
        "description" => get_the_excerpt()
    ];

    // Convert array to JSON string
    $json_ld_string = json_encode($json_ld_data);
    ?>

    <script type="application/ld+json">
        <?php echo $json_ld_string; ?>
    </script>