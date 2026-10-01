#!/usr/bin/env bash
# Prepares the wp-env site for the smoke test: activates the theme and plugin,
# enables the example models that don't need ACF Pro, and creates content.
set -euo pipefail

wp() {
    npx wp-env run cli wp "$@"
}

wp theme activate toolkit
wp rewrite structure '/%postname%/' --hard
wp option update toolkit_enabled_models '{"Article":1,"ArticleCategory":1}' --format=json

# Models are registered on init: flush rewrites in a new request so the CPT slugs exist
wp rewrite flush --hard

if [ "$(wp post list --post_type=article --format=count)" = "0" ]; then
    term_id=$(wp term create article_category "Smoke" --porcelain)
    article_id=$(wp post create --post_type=article --post_status=publish --post_title="Smoke article" --post_content="Smoke test" --post_author=1 --porcelain)
    wp post term set "$article_id" article_category "$term_id" --by=id
fi
