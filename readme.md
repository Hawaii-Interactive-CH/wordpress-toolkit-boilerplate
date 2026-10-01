# WordPress Toolkit

## Documentation technique

### Prérequis

- PHP 8.1
- Dernière version de Wordpress
- asdf, asdf-nodejs : https://atoz.hawaii.do/development/asdf/

### Installation

Lancer un serveur php contenant wordpress via Local by Flywheel ou MAMP.

Il faut installer le plugin [wordpress-toolkit-plugin](https://github.com/Hawaii-Interactive-CH/wordpress-toolkit-plugin) dans le dossier `./plugins` ou l'upload via l'admin Wordpress et l'activer dans l'administration de Wordpress. Ce plugin permet de charger les fonctionnalités de base du thème.

#### Nouveau projet

1. Télécharger Wordpress sur https://wordpress.org/download/
2. Décompresser et copier les fichiers dans votre dossier web.
3. Vider le dossier des thèmes `./wp-content/themes`
4. Cloner ce dépot git dans votre dossier de thème.
5. Supprimer le dossier `.git` et crer un nouveau dépot git avec `git init`
6. Créer un nouveau projet sur https://git.hawai.li/ et suivre les instructions pour lier a ce dépôt
7. Installer les dépendances `npm install`
8. Copier `.env.example` to `.env` et configurer les variables d'environnement si besoin

#### Projet existant

1. Télécharger Wordpress sur https://wordpress.org/download/
2. Décompresser et copier les fichiers dans votre dossier web.
3. Vider le dossier des thèmes `./wp-content/themes`
4. Cloner le dépot git du projet dans votre dossier de thème.
5. Installer les dépendances `npm install`
6. Copier `.env.example` to `.env` et configurer les variables d'environnement si besoin

### Commandes

- `npm run watch ou dev` : Compile les assets et recharge le browser quand les fichiers changent
- `npm run production ou build` : Compile les assets en mode production (Exectuer cette commande avant de publier un site)

A noter que le projet utilise vitejs pour compiler les assets. Il est possible de modifier le fichier `vite.config.js` pour ajouter des fonctionnalités supplémentaires.

### Tests

La CI GitHub Actions (`.github/workflows/ci.yml`) lance les mêmes vérifications à chaque push et pull request.

- `composer install` puis `composer lint` : vérifie la syntaxe de tous les fichiers PHP du thème
- `composer phpstan` : analyse statique (PHPStan niveau 5, avec les stubs WordPress, ACF Pro et le plugin Toolkit)
- `npm run test:smoke` : parcourt les pages principales (accueil, article, page, archives, recherche, 404, CPT et taxonomies) et échoue en cas de code HTTP inattendu, d'erreur PHP, de page vide ou de JSON-LD invalide

Le test de fumée tourne par défaut sur `wp-env` (Docker requis) :

```bash
npm run build
npx wp-env start
npm run wp-env:setup
npm run test:smoke
```

Il peut aussi tourner sur un site Local existant : `SMOKE_BASE_URL=http://localhost:10220 npm run test:smoke`

### Developpement

Local WP compile automatiquement vite en mode dev. Pour les autres environements, il faut ajouter dans le `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

#### Custom Post Type

Pour créer un nouveau CPT, il faut créer un fichier dans le dossier `./toolkit/models/custom` ou via le générateur de CPT intégré au plugin.

---

### Design Tokens & CSS Custom Properties

Le thème expose ses variables de design sous forme de **CSS custom properties** définies dans `src/scss/partials/base/_root.scss`. Elles sont utilisables directement en CSS/SCSS sur tout le site.

#### Propriétés disponibles

| Custom Property     | Valeur par défaut                          | Description                  |
|---------------------|--------------------------------------------|------------------------------|
| `--color-main`      | `#0077ac`                                  | Couleur principale           |
| `--color-second`    | `#42c0ec`                                  | Couleur secondaire           |
| `--color-tertiary`  | `#005580`                                  | Couleur tertiaire            |
| `--color-neutral`   | `#6b7280`                                  | Couleur neutre               |
| `--color-white`     | `#fff`                                     | Couleur claire               |
| `--color-black`     | `#383838`                                  | Couleur foncée               |
| `--font-size`       | `20px` (→ `18px` sous `$medium`)           | Taille de police de base     |
| `--line-height`     | `1.26`                                     | Hauteur de ligne de base     |
| `--global-grid`     | `1280px`                                   | Largeur max du conteneur     |
| `--global-margin`   | `20px`                                     | Espacement de base           |
| `--ease`            | `ease-in-out`                              | Easing standard              |
| `--bezier`          | `cubic-bezier(0.175, 0.885, 0.32, 1.275)` | Easing expressif             |
| `--header-height`   | `50px`                                     | Hauteur du header            |

#### Utilisation en SCSS

Les variables SCSS dans `_variables.scss` sont des alias vers ces custom properties :

```scss
@use "../base" as base;

.element {
    color: base.$color-main;       // compiles to: color: var(--color-main)
    padding: base.margin(2);       // compiles to: padding: 40px  (valeur statique)
    transition: color 0.3s base.$ease; // compiles to: transition: color 0.3s var(--ease)
}
```

> **Note :** `$global-margin` et les breakpoints (`$tablet`, `$desktop`, etc.) restent des valeurs SCSS pures car ils sont utilisés dans des calculs ou des `@media` queries — les CSS custom properties ne sont pas supportées dans ce contexte.

#### Utilisation en CSS

```css
.element {
    color: var(--color-main);
    max-width: var(--global-grid);
    margin: 0 auto;
}
```

---

### Customizer WordPress

Les design tokens sont modifiables en temps réel via **Apparence → Personnaliser → Theme Design** dans l'administration WordPress.

#### Sections disponibles

- **Colors** — couleurs principale, secondaire, tertiaire, neutre, claire, foncée
- **Typography** — taille de police de base, hauteur de ligne
- **Layout** — largeur max du conteneur, espacement de base

Les valeurs sont sauvegardées en base de données (`theme_mods`). Seules les valeurs modifiées sont injectées comme `<style>:root { ... }</style>` dans le `<head>` à la priorité 99, ce qui prend le dessus sur les valeurs compilées dans le CSS. Les valeurs par défaut restent celles du SCSS, y compris celles qui changent selon le breakpoint (`--font-size`).

Les couleurs alimentent aussi la palette de l'éditeur de blocs (`theme.json`, filtre `wp_theme_json_data_theme`), et toutes les variables sont injectées dans l'éditeur.

La prévisualisation est instantanée (via `postMessage`) — aucun rechargement de la page n'est nécessaire.

**Fichier :** `toolkit/utils/CustomizerService.php`

---

### CSS Critique & Performance

Le thème implémente une stratégie de **CSS critique** pour éliminer le CSS bloquant le rendu.

#### Fonctionnement

1. **Build** — `src/scss/critical.scss` est compilé séparément par Vite en `toolkit/public/css/critical.[hash].css`. Il contient uniquement les styles nécessaires au premier rendu : custom properties, reset, typographie de base, conteneurs, header et navigation.
2. **Inline** — `CriticalCSSService` lit le fichier via le manifeste Vite et l'injecte en `<style id="critical-css">` dans le `<head>` à la priorité 1.
3. **Déféré** — Le CSS principal (`app.[hash].css`) est converti du chargement bloquant (`<link rel="stylesheet">`) vers le pattern asynchrone preload/onload :

```html
<link rel="preload" href="/css/app.[hash].css" as="style"
      onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="/css/app.[hash].css"></noscript>
```

#### En mode développement

Le service est inactif quand le manifeste Vite n'existe pas (i.e. `npm run dev`). Vite injecte ses propres assets via le dev server — aucune modification nécessaire.

#### Modifier le CSS critique

Ajouter ou retirer des imports dans `src/scss/critical.scss`. Règle générale : n'inclure que ce qui est visible **avant le premier scroll**.

**Fichier :** `toolkit/utils/CriticalCSSService.php`

---

### SEO — Open Graph, Twitter Card & Canonical

Le partiel `toolkit/partials/head/seo.php` injecte automatiquement tous les méta-tags SEO dans le `<head>`.

#### Tags générés

| Tag | Valeur |
|-----|--------|
| `<meta name="description">` | Extrait (30 mots) → tagline du site |
| `<link rel="canonical">` | `$model->link()` ou `get_permalink()` |
| `og:type` | `article` sur les posts, `website` ailleurs |
| `og:title` | Titre de la page |
| `og:description` | Extrait |
| `og:url` | URL canonique |
| `og:site_name` | Nom du site WordPress |
| `og:locale` | Langue courante (`fr_FR`, `en_US`, …) |
| `og:image` + dimensions + type | Miniature en taille `image-l` (1280 px) |
| `article:published_time` | Date ISO 8601 (posts uniquement) |
| `article:modified_time` | Date de modification ISO 8601 (posts uniquement) |
| `twitter:card` | `summary_large_image` si miniature, sinon `summary` |
| `twitter:title` / `description` / `image` | Identiques aux valeurs OG |
| `<meta name="robots" content="noindex, nofollow">` | Uniquement sur les posts protégés par mot de passe |

#### Utilisation

Le partiel est appelé automatiquement dans `header.php` sans modèle — il se rabat sur les fonctions WordPress core :

```php
<?= render_partial('head/seo') ?>
```

Pour passer un modèle typé depuis un template (excerpt plus précis, données ACF) :

```php
<?php Page::current(function (Page $model) { ?>
    <?= render_partial('head/seo', ['model' => $model]) ?>
<?php }); ?>
```

Quand `$model` est fourni, le partiel utilise `$model->excerpt(30)`, `$model->link()`, et `$model->thumbnail()`. Sans modèle, il résout les données via `get_queried_object_id()` et les fonctions WP standard — ce qui couvre les archives, la page d'accueil et les pages de recherche.

**Fichier :** `toolkit/partials/head/seo.php`