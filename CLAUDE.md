# Bintintan Theme — Claude Context

This is a WordPress theme built on top of the **WordPress Toolkit Plugin** (`wordpress-toolkit-plugin-main`). The plugin provides the model system, query builder, image optimization, and service infrastructure. The theme provides templates, partials, assets, and project-specific models.

**Plugin path:** `/wp-content/plugins/wordpress-toolkit-plugin-main`
**Theme path:** `/wp-content/themes/bintintan`
**Shared PHP namespace:** `Toolkit\`

---

## Autoloading

The theme registers a PSR-4 autoloader in `toolkit/functions.php`. For any `Toolkit\` class, it checks the **theme directory first**, then falls back to the **plugin directory**. This means theme classes override plugin classes when they share the same namespace path.

```
Toolkit\models\Page         → toolkit/models/Page.php         (theme)
Toolkit\models\custom\Event → toolkit/models/custom/Event.php (theme)
Toolkit\utils\Size          → plugin/utils/Size.php           (plugin fallback)
```

---

## Model Patterns

### Retrieving the current post/page

Use the static `current()` method with a callback. The callback receives a typed model instance:

```php
<?php Page::current(function (Page $model) { ?>
    <h1><?= $model->title() ?></h1>
    <div><?= $model->content() ?></div>
<?php }); ?>
```

The callback pattern is used throughout — `current()`, `thumbnail()`, `next()`, `previous()`, `parent()`, `acf_media()`, `acf_file()` all accept callables. If no callback is passed, the model instance is returned.

### Querying posts

Use `PostType::query()` which returns a fluent `QueryBuilder`:

```php
// All published posts, paginated
$posts = Post::query()
    ->order('date', QueryBuilder::ORDER_DESC)
    ->paginate(10, $page)
    ->find_all();

// Filter by meta
$items = Event::query()
    ->add_meta_query('event_date', date('Y-m-d'), '>=')
    ->order('date', 'ASC')
    ->find_all();

// Filter by taxonomy
$items = Article::query()
    ->add_tax_query(ArticleCategory::class, 'slug', 'news')
    ->find_all();
```

Available terminal methods: `find_all()`, `find_one()`, `find_by_id($id)`, `count_all()`, `page_number()`, `pagination()`.

### Responsive images

Use `$model->thumbnail()` with a callback that receives a `Media` instance. Use `$media->picture()` to render the `<picture>` element:

```php
<?php $model->thumbnail(function (Media $media) {
    echo '<figure>' . $media->picture([
        ['size' => 'image-xl', 'size2x' => 'image-xl-2x', 'media' => '(min-width: 1281px)', 'sizes' => false],
        ['size' => 'image-l',  'size2x' => 'image-l-2x',  'media' => '(max-width: 1280px)', 'sizes' => false],
        ['size' => 'image-m',  'size2x' => 'image-m-2x',  'media' => '(max-width: 860px)',  'sizes' => false],
        ['size' => 'image-s',  'size2x' => 'image-s-2x',  'media' => '(max-width: 400px)',  'sizes' => false],
    ], '', true, false, 'image-xl') . '</figure>';
}); ?>
```

**Registered image sizes** (defined in `toolkit/functions.php`):

| Name          | Width (px) |
|---------------|-----------|
| `image-s`     | 400       |
| `image-s-2x`  | 800       |
| `image-m`     | 860       |
| `image-m-2x`  | 1720      |
| `image-l`     | 1280      |
| `image-l-2x`  | 2560      |
| `image-xl`    | 1920      |
| `image-xl-2x` | 3840      |

All sizes use unconstrained height (`FULL_SIZE = 99999`). WebP variants are generated automatically by `Toolkit\utils\Size`.

### ACF fields on models

```php
// Scalar field
$value = $model->acf('field_key');

// ACF media field
$model->acf_media('hero_image', function (Media $media) {
    echo $media->src('image-xl');
});

// ACF relation field (array of post IDs)
$related = $model->relation('related_posts', Article::class, function (Article $item) {
    return ['title' => $item->title(), 'link' => $item->link()];
});
```

### Media method reference

`Media` instances are provided by `thumbnail()`, `acf_media()`, and gallery helpers.

| Method | Description |
|---|---|
| `src($size)` | URL for a registered size (falls back to `full` if not yet generated). `$size` defaults to `"thumbnail"`. |
| `srcset($sizes)` | Builds a `srcset` attribute string from `['size-name' => 'Xw']` map; skips unresolved sizes. |
| `picture($sources, $class, $lazy, $decode, $fallback_size)` | Renders a full `<picture>` element. See modes below. |
| `inline_svg()` | Returns sanitized inline `<svg>` markup for SVG attachments (strips `<?xml>` and DOCTYPE). |
| `alt()` | Alt text from the WordPress media library. |
| `caption()` | Caption from the WordPress media library. |

#### `picture()` source modes

```php
// Width-descriptor mode (default) — sizes="100vw" added automatically
echo $media->picture([
    ['size' => 'image-xl', 'size2x' => 'image-xl-2x', 'media' => '(min-width: 1280px)'],
    ['size' => 'image-s',  'size2x' => 'image-s-2x'],
], 'my-class', true, true, 'image-xl');

// Density-descriptor mode (1x/2x) — opt in with 'sizes' => false
echo $media->picture([
    ['size' => 'image-s',  'size2x' => 'image-s-2x',  'media' => '(max-width: 640px)', 'sizes' => false],
    ['size' => 'image-xl', 'size2x' => 'image-xl-2x', 'sizes' => false],
]);

// Explicit srcset array mode
echo $media->picture([
    ['srcset' => ['image-l' => '1280w', 'image-xl' => '1920w']],
]);
```

**Signature:** `picture(array $sources, string $class = '', bool $lazy = true, bool $decode = false, string $fallback_size = ''): string`

When all sources have a `media` query, pass `$fallback_size` explicitly so the `<img>` src resolves correctly.

### File method reference

`File` instances are provided by `acf_file()`.

| Method | Description |
|---|---|
| `url()` | Direct URL to the attached file. |

---

### PostType method reference

| Method | Description |
|---|---|
| `title()` | Post title |
| `content()` | Filtered post content |
| `excerpt($words, $more)` | Excerpt, falls back to trimmed content |
| `link()` | Permalink |
| `slug()` | Post slug |
| `status()` | Post status string |
| `date($format)` | Publication date |
| `has_thumbnail()` | Boolean |
| `thumbnail($cb, $default)` | Callback with Media model |
| `acf($key)` | ACF field value |
| `has_acf($key)` | Boolean for ACF field |
| `acf_media($name, $cb)` | ACF image field as Media model |
| `acf_file($name, $cb)` | ACF file field as File model |
| `parent($cb)` | Parent post |
| `children($cb)` | Child posts array |
| `next($cb)` | Next post |
| `previous($cb)` | Previous post |
| `terms($taxonomy, $cb)` | Terms by taxonomy class |
| `relation($field, $class, $cb)` | ACF relation field as model array |
| `is_sticky()` | Boolean |
| `is_password_required()` | Boolean |

---

## Custom Post Types

Custom post types live in `toolkit/models/custom/` and extend `CustomPostType`:

```php
<?php
namespace Toolkit\models\custom;

use Toolkit\models\CustomPostType;

class Event extends CustomPostType implements \JsonSerializable
{
    const TYPE = 'event';
    const SLUG = 'events';

    public static function type_settings()
    {
        return [
            'label'       => __('Events', 'toolkit'),
            'public'      => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-calendar-alt',
            'supports'    => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite'     => ['slug' => self::SLUG, 'with_front' => false],
            'show_in_rest' => true,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id'    => $this->id(),
            'title' => $this->title(),
            'slug'  => $this->slug(),
            'link'  => $this->link(),
        ];
    }
}
```

Register in `toolkit/functions.php` inside the `$toRegister` array:

```php
$toRegister = [
    ["\\Toolkit\\models\\Config", 'register'],
    ["\\Toolkit\\models\\custom\\Event", 'register'],
];
```

Or use a dedicated `add_action('init', ...)` call for CPTs with a companion taxonomy.

### Admin list columns

```php
Event::add_columns([
    'event_date' => [
        'label'    => __('Date', 'toolkit'),
        'render'   => fn($post) => esc_html($post->acf('event_date')),
        'sortable' => ['key' => 'event_date', 'numeric' => false],
    ],
]);
```

---

## Taxonomies

Custom taxonomies extend `Taxonomy` and live in `toolkit/models/custom/`:

```php
<?php
namespace Toolkit\models\custom;

use Toolkit\models\Taxonomy;
use Toolkit\models\custom\Event;

class EventCategory extends Taxonomy
{
    const TYPE = 'event_category';

    public static function register()
    {
        register_taxonomy(self::TYPE, Event::TYPE, [
            'hierarchical'  => true,
            'show_in_rest'  => true,
            'show_admin_column' => true,
            'labels' => ['name' => __('Categories', 'toolkit')],
        ]);
    }
}
```

Register: `add_action('init', fn() => EventCategory::register());`

---

## ACF Blocks

Blocks extend `Toolkit\models\Block`. The class defines the block type and settings; the template is a separate PHP file.

### Class — `toolkit/models/custom/BlockKeyNumbers.php`

```php
<?php
namespace Toolkit\models\custom;

use Toolkit\models\Block;

class BlockKeyNumbers extends Block
{
    const TYPE = 'block-key-numbers';

    public static function settings()
    {
        return [
            'title'       => __('Key Numbers', 'toolkit'),
            'description' => __('Displays key statistics.', 'toolkit'),
            'mode'        => 'auto',
            'icon'        => 'chart-bar',
            'keywords'    => ['numbers', 'stats'],
        ];
    }
}
```

### Template — `toolkit/partials/blocks/block-key-numbers.php`

The `$block` variable is automatically injected as an instance of the class:

```php
<?php
/** @var \Toolkit\models\custom\BlockKeyNumbers $block */
?>
<div class="block-key-numbers">
    <?php $title = $block->acf('title'); ?>
    <?php if ($title): ?>
        <h2><?= esc_html($title) ?></h2>
    <?php endif; ?>
</div>
```

### Registration

Register blocks on `acf/init`, not `init`:

```php
add_action('acf/init', function () {
    \Toolkit\models\custom\BlockKeyNumbers::register();
});
```

The `register()` method in `Block` throws an `Exception` if the template file does not exist — create the template before registering.

---

## ACF Option Pages

Option pages extend `Toolkit\models\OptionPage`:

```php
<?php
namespace Toolkit\models\custom;

use Toolkit\models\OptionPage;

class SiteOptions extends OptionPage
{
    const ID = 'site-options';

    const PARAMS = [
        'page_title'  => 'Site Options',
        'menu_title'  => 'Options',
        'parent_slug' => 'options-general.php',
        'capability'  => 'manage_options',
        'redirect'    => false,
    ];

    public static function phone() { return self::acf('phone'); }
    public static function email() { return self::acf('email'); }
}
```

Register on `acf/init`:

```php
add_action('acf/init', fn() => \Toolkit\models\custom\SiteOptions::register());
```

Read fields anywhere:

```php
$phone = \Toolkit\models\custom\SiteOptions::phone();
// or
$email = \Toolkit\models\custom\SiteOptions::acf('email');
```

WPML language suffix is appended to the `post_id` automatically.

---

## Services

Services are static classes with a `register()` method that hooks into WordPress. They live in `toolkit/utils/`:

```php
<?php
namespace Toolkit\utils;

class SeoService
{
    public static function register()
    {
        add_action('wp_head', [self::class, 'inject_meta'], 5);
        add_filter('the_title', [self::class, 'filter_title'], 10);
    }

    public static function inject_meta()
    {
        // TODO: implement
    }

    public static function filter_title($value)
    {
        // TODO: implement
        return $value;
    }
}
```

Bootstrap in `toolkit/functions.php`:

```php
\Toolkit\utils\SeoService::register();
```

---

## Partials

Render partials with the plugin's `render_partial()` helper. The path is relative to `toolkit/partials/` and omits the `.php` extension:

```php
echo render_partial('head/seo', ['model' => $model]);
// loads: toolkit/partials/head/seo.php
// $model is available as a local variable inside the partial
```

---

## Build System (Vite)

### Dev / production detection

`AssetService` checks `WP_DEBUG` to decide between the Vite dev server (port `5173`) and the hashed production manifest at `toolkit/public/.vite/manifest.json`.

```bash
npm run dev    # starts Vite dev server with live PHP reload
npm run build  # outputs hashed files to toolkit/public/
```

### Entry points

| Entry | Source |
|---|---|
| `app` | `src/javascript/app.js` |
| `blocks` | `src/scss/blocks.scss` |

Add new entry points in `vite.config.js` under `rollupOptions.input`.

### JS path aliases

| Alias | Resolves to |
|---|---|
| `@` | `src/` |
| `@js` | `src/javascript/` |
| `@hooks` | `src/javascript/hooks/` |
| `@utils` | `src/javascript/utils/` |
| `@blocks` | `src/javascript/blocks/` |
| `@components` | `src/components/` |
| `@scss` | `src/scss/` |
| `@fonts` | `src/scss/assets/fonts/` |

Use them in JS/SCSS:

```js
import '@hooks/mobileNav';
import '@utils/HttpRequest';
```

```scss
@use '@scss/partials/base' as base;
```

### React / Vue

Both plugins are configured in `vite.config.js`. React is active by default via `src/javascript/react/main.jsx`. Vue is available but commented out in `app.js`. Mount components on `data-component` attributes or dedicated container elements (e.g. `<div id="demo-react-toolkit">`).

---

## SCSS Structure

```
src/scss/
├── app.scss          ← main entry, @use all partials
├── blocks.scss       ← block-specific styles entry
└── partials/
    ├── base/
    │   ├── _variables.scss   ← all design tokens (colors, breakpoints, spacing)
    │   └── index.scss
    ├── layout/       ← header, footer, nav, grid, containers
    ├── ui/           ← buttons, hamburger, scroll, filter
    ├── pages/        ← page-specific overrides
    ├── blocks/       ← one file per ACF block
    └── vendor/       ← third-party overrides
```

### Key variables (`_variables.scss`)

```scss
$color-main:   #0077ac;
$color-second: #42c0ec;
$white:        #fff;
$black:        #383838;

$large:   1400px;
$desktop: 1280px;
$medium:  1124px;
$tablet:  860px;
$small:   600px;
$phone:   400px;

$global-grid:   1280px;
$global-margin: 20px;

@function margin($n) { @return $global-margin * $n; }
```

Cross-folder imports require explicit `@use`:

```scss
@use '../base' as base;
// usage: base.$color-main, base.margin(2)
```

---

## Available Skills (Claude Code slash commands)

These skills scaffold boilerplate in the **current working directory** (the theme, not the plugin):

| Command | What it generates |
|---|---|
| `/toolkit:create-cpt` | `models/custom/{Name}.php` — CustomPostType class |
| `/toolkit:create-block` | `models/custom/Block{Name}.php` + `partials/blocks/{slug}.php` |
| `/toolkit:create-taxonomy` | `models/custom/{Name}.php` — Taxonomy class |
| `/toolkit:create-service` | `toolkit/utils/{Name}Service.php` — Service class |
| `/toolkit:create-option-page` | `models/custom/{Name}.php` — OptionPage class |

Each skill prompts for the required parameters if not passed as arguments.

---

## Required Plugins

| Plugin | Usage |
|---|---|
| **WordPress Toolkit Plugin** | Core dependency — models, services, image optimization |
| **ACF Pro** | Required for `Block`, `OptionPage`, and `acf()` methods |
| **WPML** (optional) | Language suffix on `OptionPage` post IDs |
| **Gravity Forms** (optional) | `Toolkit\utils\GravityForm` integration |

The theme displays an admin notice if the Toolkit plugin is not active.

---

## Key File Map

| Purpose | Path |
|---|---|
| Theme bootstrap & autoloader | `toolkit/functions.php` |
| Image size registration | `toolkit/functions.php` (Size::add calls) |
| Nav menu registration | `toolkit/functions.php` |
| Page template | `toolkit/templates/page.php` |
| Front page template | `toolkit/templates/front-page.php` |
| Single post template | `toolkit/templates/single.php` |
| Archive template | `toolkit/templates/archive.php` |
| Block partials | `toolkit/partials/blocks/` |
| ACF JSON exports | `toolkit/acf-json/` |
| JS entry point | `src/javascript/app.js` |
| SCSS entry point | `src/scss/app.scss` |
| Design tokens | `src/scss/partials/base/_variables.scss` |
| Vite config | `vite.config.js` |
| Font generation | `generate_fonts.js` / `convert_fonts.sh` |
