<?php
/**
 * JSON-LD structured data: Organization and WebSite on every page, plus the
 * current WebPage or Article on singular views.
 *
 * The organization logo uses the Site Icon (Settings → General) when set.
 */

namespace Toolkit\partials\head;

$site_name = get_bloginfo('name');
$home_url  = home_url('/');
$language  = get_bloginfo('language');

$organization = [
    '@type' => 'Organization',
    '@id'   => $home_url . '#organization',
    'name'  => $site_name,
    'url'   => $home_url,
];

$logo = get_site_icon_url(512);
if ($logo) {
    $organization['logo'] = $logo;
}

$graph = [
    $organization,
    [
        '@type'           => 'WebSite',
        '@id'             => $home_url . '#website',
        'name'            => $site_name,
        'url'             => $home_url,
        'inLanguage'      => $language,
        'publisher'       => ['@id' => $home_url . '#organization'],
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ],
];

if (is_singular()) {
    $post_id   = get_queried_object_id();
    $is_entry  = in_array(get_post_type($post_id), ['post', 'article'], true);
    $permalink = get_permalink($post_id);

    $page = [
        '@type'         => $is_entry ? 'Article' : 'WebPage',
        '@id'           => $permalink . '#' . ($is_entry ? 'article' : 'webpage'),
        'url'           => $permalink,
        'headline'      => wp_strip_all_tags(get_the_title($post_id)),
        'inLanguage'    => $language,
        'isPartOf'      => ['@id' => $home_url . '#website'],
        'datePublished' => get_the_date('c', $post_id),
        'dateModified'  => get_the_modified_date('c', $post_id),
    ];

    $description = trim(wp_strip_all_tags(get_the_excerpt($post_id)));
    if ($description) {
        $page['description'] = $description;
    }

    $image = get_the_post_thumbnail_url($post_id, 'image-l');
    if ($image) {
        $page['image'] = $image;
    }

    if ($is_entry) {
        $author = get_the_author_meta('display_name', (int) get_post_field('post_author', $post_id));
        if ($author) {
            $page['author'] = ['@type' => 'Person', 'name' => $author];
        }
        $page['publisher'] = ['@id' => $home_url . '#organization'];
    }

    $graph[] = $page;
}
?>
<script type="application/ld+json">
<?= wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>

</script>
