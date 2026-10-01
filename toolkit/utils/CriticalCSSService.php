<?php

namespace Toolkit\utils;

// Prevent direct access.
defined('ABSPATH') or exit();

/**
 * Inlines the built critical.scss output in <head> and defers the main
 * stylesheet so it no longer blocks rendering.
 *
 * In dev mode (no manifest) both hooks are no-ops, so Vite HMR is unaffected.
 */
class CriticalCSSService
{
    private static bool $buffering = false;

    public static function register(): void
    {
        add_action('wp_head', [self::class, 'inline_critical_css'], 1);
        add_action('wp_head', [self::class, 'start_defer_buffer'],  7);
        add_action('wp_head', [self::class, 'flush_defer_buffer'],  8);
    }

    /**
     * Outputs the critical CSS as an inline <style> block at priority 1,
     * before any stylesheet link tags are emitted.
     */
    public static function inline_critical_css(): void
    {
        $css = self::read_critical_css();

        if (!$css) {
            return;
        }

        echo "<style id=\"critical-css\">\n" . $css . "\n</style>\n";
    }

    /**
     * Opens an output buffer just before WordPress prints the enqueued
     * <link> tags (wp_print_styles runs on wp_head at priority 8). The
     * buffer is flushed by a callback registered later at the same priority.
     */
    public static function start_defer_buffer(): void
    {
        if (!self::manifest_path()) {
            return; // dev mode — nothing to defer
        }

        ob_start();
        self::$buffering = true;
    }

    /**
     * Closes the buffer and replaces blocking <link rel="stylesheet"> tags
     * with the preload/onload async pattern.
     */
    public static function flush_defer_buffer(): void
    {
        if (!self::$buffering) {
            return;
        }

        self::$buffering = false;
        $buffer = ob_get_clean();

        if ($buffer === false || $buffer === '') {
            return;
        }

        echo preg_replace_callback(
            '/<link\s[^>]*rel=["\']stylesheet["\'][^>]*>/i',
            function (array $matches): string {
                if (!preg_match('/href=["\']([^"\']+)["\']/', $matches[0], $href)) {
                    return $matches[0]; // unrecognised format — leave untouched
                }

                $url = esc_url($href[1]);

                return '<link rel="preload" href="' . $url . '" as="style" '
                     . 'onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
                     . '<noscript><link rel="stylesheet" href="' . $url . '"></noscript>';
            },
            $buffer,
        ) ?? $buffer;
    }

    // -------------------------------------------------------------------------

    private static function read_critical_css(): ?string
    {
        $manifest_path = self::manifest_path();

        if (!$manifest_path) {
            return null; // dev mode
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);
        $entry    = $manifest['src/scss/critical.scss']['file'] ?? null;

        if (!$entry) {
            return null;
        }

        $file_path = get_template_directory() . '/public/' . $entry;

        if (!file_exists($file_path)) {
            return null;
        }

        return file_get_contents($file_path) ?: null;
    }

    /**
     * Returns the absolute path to the Vite manifest, or null in development
     * mode where Vite serves files directly (a stale build may still exist).
     */
    private static function manifest_path(): ?string
    {
        if (self::vite_dev_server_active()) {
            return null;
        }

        $path = get_template_directory() . '/public/.vite/manifest.json';

        return file_exists($path) ? $path : null;
    }

    /**
     * Whether the plugin serves assets from the Vite dev server.
     * AssetService::is_dev_mode() is private in older plugin versions.
     */
    private static function vite_dev_server_active(): bool
    {
        if (!defined('HI_TOOLKIT_DIR')) {
            return false;
        }

        $class = new \ReflectionClass(AssetService::class);

        return $class->hasMethod('is_dev_mode')
            && $class->getMethod('is_dev_mode')->isPublic()
            && AssetService::is_dev_mode();
    }
}
