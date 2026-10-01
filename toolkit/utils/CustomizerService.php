<?php

namespace Toolkit\utils;

// Prevent direct access.
defined('ABSPATH') or exit();

class CustomizerService
{
    /**
     * Maps theme_mod keys to their CSS custom property, default value, and control config.
     */
    private static function settings(): array
    {
        return [
            // Colors
            'color_main'     => ['prop' => '--color-main',     'label' => 'Primary Color',   'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#0077ac'],
            'color_second'   => ['prop' => '--color-second',   'label' => 'Secondary Color', 'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#42c0ec'],
            'color_tertiary' => ['prop' => '--color-tertiary', 'label' => 'Tertiary Color',  'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#005580'],
            'color_neutral'  => ['prop' => '--color-neutral',  'label' => 'Neutral Color',   'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#6b7280'],
            'color_white'    => ['prop' => '--color-white',    'label' => 'Light Color',     'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#ffffff'],
            'color_black'    => ['prop' => '--color-black',    'label' => 'Dark Color',      'section' => 'toolkit_colors',     'type' => 'color',  'default' => '#383838'],
            // Typography
            'font_size'      => ['prop' => '--font-size',      'label' => 'Base Font Size (px)',  'section' => 'toolkit_typography', 'type' => 'number', 'default' => '20',   'unit' => 'px', 'min' => 14,  'max' => 28,   'step' => 1],
            'line_height'    => ['prop' => '--line-height',    'label' => 'Line Height',          'section' => 'toolkit_typography', 'type' => 'number', 'default' => '1.26', 'unit' => '',   'min' => 1.0, 'max' => 2.0,  'step' => 0.01],
            // Layout
            'global_grid'    => ['prop' => '--global-grid',    'label' => 'Container Max Width (px)', 'section' => 'toolkit_layout', 'type' => 'number', 'default' => '1280', 'unit' => 'px', 'min' => 960, 'max' => 1920, 'step' => 10],
            'global_margin'  => ['prop' => '--global-margin',  'label' => 'Base Spacing (px)',        'section' => 'toolkit_layout', 'type' => 'number', 'default' => '20',   'unit' => 'px', 'min' => 10,  'max' => 60,   'step' => 1],
        ];
    }

    public static function register(): void
    {
        add_action('customize_register',     [self::class, 'add_settings']);
        add_action('wp_head',                [self::class, 'output_css'], 99);
        add_action('customize_preview_init', [self::class, 'enqueue_preview_js']);
        add_filter('block_editor_settings_all', [self::class, 'editor_css']);
        add_filter('wp_theme_json_data_theme',  [self::class, 'theme_json_palette']);
    }

    public static function add_settings(\WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_panel('toolkit_design', [
            'title'    => __('Theme Design', 'toolkit'),
            'priority' => 30,
        ]);

        $wp_customize->add_section('toolkit_colors', [
            'title' => __('Colors', 'toolkit'),
            'panel' => 'toolkit_design',
        ]);

        $wp_customize->add_section('toolkit_typography', [
            'title' => __('Typography', 'toolkit'),
            'panel' => 'toolkit_design',
        ]);

        $wp_customize->add_section('toolkit_layout', [
            'title' => __('Layout', 'toolkit'),
            'panel' => 'toolkit_design',
        ]);

        foreach (self::settings() as $key => $setting) {
            $sanitize = $setting['type'] === 'color'
                ? 'sanitize_hex_color'
                : fn($val) => self::sanitize_number($val, $setting);

            $wp_customize->add_setting("toolkit_{$key}", [
                'default'           => $setting['default'],
                'sanitize_callback' => $sanitize,
                'transport'         => 'postMessage',
            ]);

            if ($setting['type'] === 'color') {
                $wp_customize->add_control(
                    new \WP_Customize_Color_Control($wp_customize, "toolkit_{$key}", [
                        'label'   => __($setting['label'], 'toolkit'),
                        'section' => $setting['section'],
                    ])
                );
            } else {
                $wp_customize->add_control("toolkit_{$key}", [
                    'label'       => __($setting['label'], 'toolkit'),
                    'section'     => $setting['section'],
                    'type'        => 'number',
                    'input_attrs' => [
                        'min'  => $setting['min'],
                        'max'  => $setting['max'],
                        'step' => $setting['step'] ?? 1,
                    ],
                ]);
            }
        }
    }

    /**
     * Returns the CSS custom property declarations for the saved theme mods.
     *
     * @param bool $modified_only Skip values equal to their default, so the compiled
     *                            SCSS (e.g. the smaller --font-size under $medium) still applies.
     * @return string[]
     */
    private static function declarations(bool $modified_only): array
    {
        $lines = [];

        foreach (self::settings() as $key => $setting) {
            $value = self::value($key, $setting);

            if ($value === '' || $value === null) {
                continue;
            }

            if ($modified_only && (string) $value === (string) $setting['default']) {
                continue;
            }

            $unit    = $setting['unit'] ?? '';
            $lines[] = "{$setting['prop']}: {$value}{$unit};";
        }

        return $lines;
    }

    /**
     * Outputs a <style> block at wp_head priority 99 with the customized CSS custom properties.
     * This overrides the compiled SCSS defaults at runtime.
     */
    public static function output_css(): void
    {
        $lines = self::declarations(true);

        if (empty($lines)) {
            return;
        }

        echo "\n<style id=\"toolkit-theme-vars\">\n:root {\n    "
            . implode("\n    ", $lines)
            . "\n}\n</style>\n";
    }

    /**
     * Adds every CSS custom property to the block editor styles. The editor does not
     * load the compiled SCSS, so it needs all values, not only the customized ones.
     */
    public static function editor_css(array $settings): array
    {
        $settings['styles'][] = [
            'css' => ':root { ' . implode(' ', self::declarations(false)) . ' }',
        ];

        return $settings;
    }

    /**
     * Builds the block editor color palette from the Customizer colors, so the
     * presets (has-main-color, --wp--preset--color--main…) follow the theme mods.
     */
    public static function theme_json_palette(\WP_Theme_JSON_Data $theme_json): \WP_Theme_JSON_Data
    {
        $palette = [];

        foreach (self::settings() as $key => $setting) {
            if ($setting['type'] !== 'color') {
                continue;
            }

            $palette[] = [
                'slug'  => str_replace('color_', '', $key),
                'name'  => __($setting['label'], 'toolkit'),
                'color' => self::value($key, $setting) ?: $setting['default'],
            ];
        }

        return $theme_json->update_with([
            'version'  => 3,
            'settings' => ['color' => ['palette' => $palette]],
        ]);
    }

    /**
     * Registers live-preview JS bindings via postMessage.
     * Each binding updates the matching CSS custom property instantly without a reload.
     */
    public static function enqueue_preview_js(): void
    {
        $js = '';

        foreach (self::settings() as $key => $setting) {
            $prop = esc_js($setting['prop']);
            $unit = esc_js($setting['unit'] ?? '');
            $js  .= "wp.customize('toolkit_{$key}',function(v){"
                  . "v.bind(function(val){"
                  . "document.documentElement.style.setProperty('{$prop}',val+'{$unit}');"
                  . "});"
                  . "});\n";
        }

        wp_add_inline_script('customize-preview', $js);
    }

    /**
     * Returns the saved theme mod, sanitized again: values printed in <style> tags
     * may have been saved outside the Customizer (WP-CLI, imports, other code).
     */
    private static function value(string $key, array $setting): ?string
    {
        $value = get_theme_mod("toolkit_{$key}", $setting['default']);

        if ($value === '' || $value === null) {
            return null;
        }

        return $setting['type'] === 'color'
            ? sanitize_hex_color((string) $value)
            : self::sanitize_number($value, $setting);
    }

    private static function sanitize_number(mixed $value, array $setting): string
    {
        $num = (float) $value;
        $min = (float) ($setting['min'] ?? PHP_FLOAT_MIN);
        $max = (float) ($setting['max'] ?? PHP_FLOAT_MAX);

        return (string) max($min, min($max, $num));
    }
}
