<?php
/**
 * Plugin Name: Beehivehub Carousel
 * Description: Performance-first modular carousel.
 * Version: 0.1
 */

if (!defined('ABSPATH')) exit;

// Load style registries
require_once plugin_dir_path(__FILE__) . 'includes/styles.php';

// Enqueue frontend assets
function pf_enqueue_assets() {
    wp_enqueue_script(
        'pf-carousel',
        plugin_dir_url(__FILE__) . 'assets/js/carousel.js',
        [],
        '0.1',
        true
    );

    wp_enqueue_style(
        'pf-carousel-style',
        plugin_dir_url(__FILE__) . 'assets/css/carousel.css',
        [],
        '0.1'
    );
}
add_action('wp_enqueue_scripts', 'pf_enqueue_assets');

// Shortcode
function pf_carousel_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $carousel_id = sanitize_text_field($atts['id']);

    if (!$carousel_id) return 'Carousel ID missing';

    $meta_key = '_pf_carousel_config_' . $carousel_id;

    // Find the post that holds this carousel config
    $posts = get_posts([
        'post_type'  => ['post', 'page'],
        'meta_key'   => $meta_key,
        'numberposts' => 1,
    ]);

    if (empty($posts)) return '<!-- Carousel not found -->';

    $config = get_post_meta($posts[0]->ID, $meta_key, true);
    $config = is_array($config) ? $config : [];
    $images = $config['images'] ?? [];

    // Build CSS classes from style selections and enqueue only the needed assets
    $carousel_style = $config['carousel_style'] ?? 'default';
    $nav_style = $config['nav_style'] ?? 'minimal';
    $nav_placement = $config['nav_placement'] ?? 'overlay';
    $show_dots = ($config['show_dots'] ?? 'false') === 'true';

    // Nav class goes on wrapper so nav CSS applies to buttons regardless of placement
    $classes = 'pf-carousel pf-style-' . esc_attr($carousel_style);
    $wrap_classes = 'pf-carousel-wrap pf-placement-' . esc_attr($nav_placement) . ' pf-nav-' . esc_attr($nav_style);

    pf_enqueue_style_assets('carousel', $carousel_style);
    pf_enqueue_style_assets('nav', $nav_style);

    // Get nav button content from the style definition
    $nav_styles = pf_get_nav_styles();
    $nav_def = $nav_styles[$nav_style] ?? [];
    $prev_html = $nav_def['content']['prev_html'] ?? 'Prev';
    $next_html = $nav_def['content']['next_html'] ?? 'Next';

    // For custom-text style, override with user-saved text
    $nav_opts = $config['nav_style_options'] ?? [];
    if ($nav_style === 'custom-text') {
        if (!empty($nav_opts['prev_text'])) $prev_html = esc_html($nav_opts['prev_text']);
        if (!empty($nav_opts['next_text'])) $next_html = esc_html($nav_opts['next_text']);
    }

    // Carousel CSS vars on .pf-carousel, nav CSS vars on wrapper
    $carousel_css_vars = [];
    foreach ($config['carousel_style_options'] ?? [] as $key => $val) {
        $carousel_css_vars[] = '--pf-' . esc_attr($key) . ':' . esc_attr($val) . (is_numeric($val) ? 'px' : '');
    }
    $carousel_inline = !empty($carousel_css_vars) ? implode(';', $carousel_css_vars) : '';

    $nav_css_vars = [];
    foreach ($config['nav_style_options'] ?? [] as $key => $val) {
        $nav_css_vars[] = '--pf-nav-' . esc_attr($key) . ':' . esc_attr($val) . (is_numeric($val) ? 'px' : '');
    }
    $nav_inline = !empty($nav_css_vars) ? implode(';', $nav_css_vars) : '';

    $dots_html = '';
    if ($show_dots && count($images) > 1) {
        $dots_html = '<div class="pf-dots">';
        for ($i = 0; $i < count($images); $i++) {
            $active = $i === 0 ? ' active' : '';
            $dots_html .= '<button class="pf-dot' . $active . '" type="button" data-index="' . $i . '"></button>';
        }
        $dots_html .= '</div>';
    }

    ob_start(); ?>
    <div class="<?php echo $wrap_classes; ?>"
         <?php if ($nav_inline): ?>style="<?php echo $nav_inline; ?>"<?php endif; ?>>
        <?php if ($nav_placement === 'split-side'): ?>
        <button class="pf-prev" type="button"><?php echo $prev_html; ?></button>
        <?php endif; ?>
        <div class="<?php echo $classes; ?>"
             <?php if ($carousel_inline): ?>style="<?php echo $carousel_inline; ?>"<?php endif; ?>
             data-config='<?php echo json_encode([
                'slidesToShow'=>1,
                'autoplay'=>$config['autoplay'] ?? true,
                'autoplayDelay'=>$config['autoplayDelay'] ?? 2000,
                'showDots'=>$show_dots
             ]); ?>'>
            <div class="pf-track">
                <?php
                foreach ($images as $img) {
                    if (is_numeric($img)) {
                        $url = wp_get_attachment_url($img);
                    } else {
                        $url = esc_url($img);
                    }
                    echo '<div class="pf-slide"><img src="'. $url .'" /></div>';
                }
                ?>
            </div>
            <?php if ($nav_placement === 'overlay'): ?>
            <button class="pf-prev" type="button"><?php echo $prev_html; ?></button>
            <button class="pf-next" type="button"><?php echo $next_html; ?></button>
            <?php echo $dots_html; ?>
            <?php endif; ?>
        </div>
        <?php if ($nav_placement === 'split-side'): ?>
        <button class="pf-next" type="button"><?php echo $next_html; ?></button>
        <?php endif; ?>
        <?php if ($nav_placement === 'below' || $nav_placement === 'above'): ?>
        <div class="pf-nav-bar">
            <button class="pf-prev" type="button"><?php echo $prev_html; ?></button>
            <button class="pf-next" type="button"><?php echo $next_html; ?></button>
        </div>
        <?php endif; ?>
        <?php if ($nav_placement !== 'overlay') echo $dots_html; ?>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('pf_carousel', 'pf_carousel_shortcode');

// Admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Beehivehub Carousel',
        'Beehivehub Carousel',
        'manage_options',
        'pf-carousel-settings',
        'pf_render_admin_page',
        'dashicons-images-alt2',
        60
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('pf_carousel_options_group', 'pf_autoplay');
    register_setting('pf_carousel_options_group', 'pf_autoplay_delay');
});

// AJAX handler for updating carousel config (including images and styles)
add_action('wp_ajax_pf_update_carousel_config', function() {
    $post_id = intval($_POST['post_id']);
    $edit_id = sanitize_text_field($_POST['edit_id']);
    $autoplay = sanitize_text_field($_POST['autoplay']);
    $autoplayDelay = intval($_POST['autoplayDelay']);
    $images = isset($_POST['images']) ? array_map('sanitize_text_field', $_POST['images']) : [];

    // Style data
    $carousel_style = sanitize_text_field($_POST['carousel_style'] ?? 'default');
    $nav_style = sanitize_text_field($_POST['nav_style'] ?? 'minimal');
    $carousel_style_options = isset($_POST['carousel_style_options']) ? array_map('sanitize_text_field', $_POST['carousel_style_options']) : [];
    $nav_style_options = isset($_POST['nav_style_options']) ? array_map('sanitize_text_field', $_POST['nav_style_options']) : [];

    // Navigation config
    $nav_placement = sanitize_text_field($_POST['nav_placement'] ?? 'overlay');
    $show_dots = sanitize_text_field($_POST['show_dots'] ?? 'false');

    if (!$post_id || !$edit_id) wp_send_json_error();

    $meta_key = '_pf_carousel_config_' . $edit_id;
    $config = [
        'id' => $edit_id,
        'autoplay' => $autoplay,
        'autoplayDelay' => $autoplayDelay,
        'images' => $images,
        'carousel_style' => $carousel_style,
        'carousel_style_options' => $carousel_style_options,
        'nav_style' => $nav_style,
        'nav_style_options' => $nav_style_options,
        'nav_placement' => $nav_placement,
        'show_dots' => $show_dots,
    ];

    update_post_meta($post_id, $meta_key, $config);

    wp_send_json_success();
});

// AJAX handler for fetching style-specific option fields
add_action('wp_ajax_pf_get_style_options_html', function() {
    $style_key = sanitize_text_field($_POST['style_key'] ?? '');
    $registry = sanitize_text_field($_POST['registry'] ?? '');

    if ($registry === 'carousel') {
        $styles = pf_get_carousel_styles();
    } elseif ($registry === 'nav') {
        $styles = pf_get_nav_styles();
    } else {
        wp_send_json_error('Invalid registry');
    }

    if (!isset($styles[$style_key])) wp_send_json_error('Invalid style');

    $html = pf_render_style_options_html($styles[$style_key]['options']);
    wp_send_json_success($html);
});

// AJAX handler for creating carousel
add_action('wp_ajax_pf_create_carousel', function() {
    $id = sanitize_text_field($_POST['pf_new_id']);
    $autoplay = sanitize_text_field($_POST['pf_new_autoplay']);
    $autoplayDelay = intval($_POST['pf_new_autoplayDelay']);
    $carousel_style = sanitize_text_field($_POST['pf_new_carousel_style'] ?? 'default');
    $nav_style = sanitize_text_field($_POST['pf_new_nav_style'] ?? 'minimal');

    if (!$id) wp_send_json_error('Missing ID');

    $post_id = wp_insert_post([
        'post_title' => 'Carousel ' . $id,
        'post_status' => 'publish',
        'post_type' => 'post'
    ]);

    if (!$post_id) wp_send_json_error('Failed to create post');

    $meta_key = '_pf_carousel_config_' . $id;
    $config = [
        'id' => $id,
        'autoplay' => $autoplay,
        'autoplayDelay' => $autoplayDelay,
        'images' => [],
        'carousel_style' => $carousel_style,
        'carousel_style_options' => [],
        'nav_style' => $nav_style,
        'nav_style_options' => [],
        'nav_placement' => 'overlay',
        'show_dots' => 'false',
    ];

    update_post_meta($post_id, $meta_key, $config);

    wp_send_json_success(['post_id' => $post_id]);
});

// Enqueue admin assets
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_pf-carousel-settings') return;

    wp_enqueue_media();

    wp_enqueue_style(
        'pf-admin-style',
        plugin_dir_url(__FILE__) . 'admin/admin.css',
        [],
        '0.1'
    );

    wp_enqueue_script(
        'pf-admin-js',
        plugin_dir_url(__FILE__) . 'admin/admin.js',
        ['jquery'],
        '0.1',
        true
    );
});

// Include admin page rendering
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';