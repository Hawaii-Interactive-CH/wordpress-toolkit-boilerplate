<?php
/**
 * FontManager
 *
 * Handles the registration of local fonts in the theme.
 *
 * Features:
 * - Registers custom fonts for use in the theme
 * - Supports local font files (woff, woff2)
 * - Handles multiple font weights and styles
 */

namespace Toolkit\utils;

// Prevent direct access.
defined("ABSPATH") or exit();

class FontManager
{
    /**
     * Initialize the FontManager.
     */
    public static function init()
    {
        // Add initialization if needed
    }

    /**
     * Register theme fonts from url.
     *
     * @param array $fonts Array of font configurations
     * [
     *   [
     *       'family' => 'Roboto',
     *       'variants' => '300,400,500,700'
     *   ],
     *   [
     *       'family' => 'Open+Sans',
     *       'variants' => '400,600,700'
     *   ],
     *   [
     *       'family' => 'Montserrat',
     *       'variants' => '300,400italic,700'
     *   ]
     * ]
     */
    public static function registerFonts($fonts)
    {
        $font_families = [];
        $font_variants = [];

        foreach ($fonts as $font) {
            $font_families[] = $font["family"];
            $font_variants[] = $font["variants"];
        }

        $font_families = implode("|", $font_families);
        $font_variants = implode(",", $font_variants);

        add_action("wp_enqueue_scripts", function () use (
            $font_families,
            $font_variants
        ) {
            wp_enqueue_style(
                "google-fonts",
                "https://fonts.googleapis.com/css?family={$font_families}:{$font_variants}"
            );
        });
    }

    /**
     * Register theme fonts from local files.
     *
     * @param array $fonts Array of font configurations
     * Example structure:
     * [
     *     [
     *         'family' => 'UncutSans-Variable',
     *         'variations' => [
     *             [
     *                 'weight' => 300,
     *                 'style' => 'normal',
     *                 'sources' => [
     *                     'woff' => 'UncutSans-Variable.woff',
     *                     'woff2' => 'UncutSans-Variable.woff2'
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     */
    public static function registerLocalFont($fonts)
    {
        if (empty($fonts)) {
            return;
        }

        $fontsDir = "/static/assets/fonts/";
        $fontPath = get_template_directory_uri() . $fontsDir;

        add_action("wp_enqueue_scripts", function () use ($fonts, $fontPath) {
            // Generate a unique handle for the font stylesheet
            $handle = "local-fonts-" . wp_unique_id();

            // Start building the CSS
            $css = "";

            foreach ($fonts as $font) {
                if (empty($font["family"]) || empty($font["variations"])) {
                    continue;
                }

                foreach ($font["variations"] as $variation) {
                    if (empty($variation["sources"])) {
                        continue;
                    }

                    $css .= "@font-face {\n";
                    $css .= "    font-family: \"{$font["family"]}\";\n";

                    // Build the src attribute with multiple formats
                    $sources = [];
                    foreach ($variation["sources"] as $format => $path) {
                        // Convert @fonts placeholder to theme directory URL
                        $url = $fontPath . $path;
                        $sources[] = "        url(\"{$url}\") format(\"{$format}\")";
                    }
                    $css .= "    src:\n" . implode(",\n", $sources) . ";\n";

                    // Add font-weight if specified
                    if (!empty($variation["weight"])) {
                        $css .= "    font-weight: {$variation["weight"]};\n";
                    }

                    // Add font-style if specified
                    if (!empty($variation["style"])) {
                        $css .= "    font-style: {$variation["style"]};\n";
                    }

                    $css .= "}\n\n";
                }
            }

            // Add the generated CSS as an inline stylesheet
            wp_register_style($handle, false);
            wp_enqueue_style($handle);
            wp_add_inline_style($handle, $css);
        });
    }
}
