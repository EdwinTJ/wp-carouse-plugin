<?php
/**
 * Style Registry — Auto-discovery for carousel and navigation styles.
 *
 * Scans the styles/ directory for self-contained style folders.
 * Each folder contains a PHP definition file, a CSS file, and an optional JS file.
 *
 * Directory structure:
 *   styles/carousel/{style_key}/{style_key}.php  — required, returns style definition array
 *   styles/carousel/{style_key}/{style_key}.css  — required, visual styles
 *   styles/carousel/{style_key}/{style_key}.js   — optional, JS enhancements
 *   styles/nav/{style_key}/{style_key}.php        — required
 *   styles/nav/{style_key}/{style_key}.css        — required
 *
 * To add a new style: create a folder, drop in the files, done.
 *
 * @since 0.1
 */
if (!defined('ABSPATH')) exit;

/**
 * Scans a directory for style folders and loads their PHP definitions.
 *
 * Each subfolder must contain a {folder_name}.php file that returns
 * an associative array with keys: name, description, tier, options.
 *
 * @param  string $directory Absolute path to the styles directory (carousel or nav).
 * @return array  Associative array of style_key => style_definition.
 */
function pf_discover_styles($directory) {
    $styles = [];

    if (!is_dir($directory)) {
        return $styles;
    }

    $folders = glob($directory . '/*', GLOB_ONLYDIR);

    foreach ($folders as $folder) {
        $key = basename($folder);
        $php_file = $folder . '/' . $key . '.php';

        if (file_exists($php_file)) {
            $definition = require $php_file;
            if (is_array($definition)) {
                $styles[$key] = $definition;
            }
        }
    }

    return $styles;
}

/**
 * Returns all registered carousel visual styles.
 *
 * Auto-discovers styles from styles/carousel/ directory.
 * Each style has: name, description, tier (free|premium), and options.
 *
 * @return array Associative array of style_key => style_definition.
 */
function pf_get_carousel_styles() {
    static $cache = null;
    if ($cache !== null) return $cache;

    $dir = plugin_dir_path(dirname(__FILE__)) . 'styles/carousel';
    $cache = pf_discover_styles($dir);

    return $cache;
}

/**
 * Returns all registered navigation (arrow) styles.
 *
 * Auto-discovers styles from styles/nav/ directory.
 * Independent from carousel styles — any nav style pairs with any carousel style.
 *
 * @return array Associative array of style_key => style_definition.
 */
function pf_get_nav_styles() {
    static $cache = null;
    if ($cache !== null) return $cache;

    $dir = plugin_dir_path(dirname(__FILE__)) . 'styles/nav';
    $cache = pf_discover_styles($dir);

    return $cache;
}

/**
 * Returns the absolute filesystem path to a style's asset directory.
 *
 * @param  string $registry  Either 'carousel' or 'nav'.
 * @param  string $style_key The style folder name (e.g. 'elevated').
 * @return string Absolute path to the style folder.
 */
function pf_get_style_path($registry, $style_key) {
    return plugin_dir_path(dirname(__FILE__)) . 'styles/' . $registry . '/' . $style_key . '/';
}

/**
 * Returns the URL to a style's asset directory.
 *
 * @param  string $registry  Either 'carousel' or 'nav'.
 * @param  string $style_key The style folder name (e.g. 'elevated').
 * @return string URL to the style folder.
 */
function pf_get_style_url($registry, $style_key) {
    return plugin_dir_url(dirname(__FILE__)) . 'styles/' . $registry . '/' . $style_key . '/';
}

/**
 * Enqueues the CSS and optional JS for a specific style.
 *
 * Only loads assets for styles actually used on the current page.
 * Called from the shortcode renderer so each carousel only loads what it needs.
 *
 * @param  string $registry  Either 'carousel' or 'nav'.
 * @param  string $style_key The style folder name (e.g. 'elevated').
 * @return void
 */
function pf_enqueue_style_assets($registry, $style_key) {
    $path = pf_get_style_path($registry, $style_key);
    $url  = pf_get_style_url($registry, $style_key);

    $css_file = $path . $style_key . '.css';
    $js_file  = $path . $style_key . '.js';

    $handle_prefix = 'pf-' . $registry . '-' . $style_key;

    if (file_exists($css_file)) {
        wp_enqueue_style(
            $handle_prefix . '-css',
            $url . $style_key . '.css',
            [],
            filemtime($css_file)
        );
    }

    if (file_exists($js_file)) {
        wp_enqueue_script(
            $handle_prefix . '-js',
            $url . $style_key . '.js',
            [],
            filemtime($js_file),
            true
        );
    }
}

/**
 * Renders HTML form inputs for a style's configurable options.
 *
 * Generates a WordPress-styled form table with inputs matching each option's type.
 * Used for both the initial PHP render and AJAX-driven field swapping.
 *
 * @param  array $options      Associative array of option_key => option_definition.
 * @param  array $saved_values Previously saved values to pre-fill inputs.
 * @return string HTML markup for the options form fields.
 */
function pf_render_style_options_html($options, $saved_values = []) {
    if (empty($options)) {
        return '<p><em>No configurable options for this style.</em></p>';
    }

    $html = '<table class="form-table">';
    foreach ($options as $key => $opt) {
        $value = $saved_values[$key] ?? $opt['default'];
        $input_name = 'pf_style_opt_' . esc_attr($key);

        $html .= '<tr>';
        $html .= '<th>' . esc_html($opt['label']) . '</th>';
        $html .= '<td>';

        if ($opt['type'] === 'color') {
            $html .= '<input type="color" name="' . $input_name . '" value="' . esc_attr($value) . '" data-key="' . esc_attr($key) . '">';
        } elseif ($opt['type'] === 'number') {
            $html .= '<input type="number" name="' . $input_name . '" value="' . esc_attr($value) . '" data-key="' . esc_attr($key) . '">';
        } else {
            $html .= '<input type="text" name="' . $input_name . '" value="' . esc_attr($value) . '" data-key="' . esc_attr($key) . '">';
        }

        $html .= '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';

    return $html;
}
