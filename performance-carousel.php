<?php
/**
 * Plugin Name: Beehivehub Carousel
 * Description: Performance-first modular carousel.
 * Version: 0.1
 */

if (!defined('ABSPATH')) exit;

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

    ob_start(); ?>
    <div class="pf-carousel" data-config='<?php echo json_encode([
        'slidesToShow'=>1,
        'autoplay'=>$config['autoplay'] ?? true,
        'autoplayDelay'=>$config['autoplayDelay'] ?? 2000
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
        <button class="pf-prev" type="button">Prev</button>
        <button class="pf-next" type="button">Next</button>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('pf_carousel', 'pf_carousel_shortcode');

// Admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Performance Carousel',
        'Performance Carousel',
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

// AJAX handler for updating carousel config (including images)
add_action('wp_ajax_pf_update_carousel_config', function() {
    $post_id = intval($_POST['post_id']);
    $edit_id = sanitize_text_field($_POST['edit_id']);
    $autoplay = sanitize_text_field($_POST['autoplay']);
    $autoplayDelay = intval($_POST['autoplayDelay']);
    $images = isset($_POST['images']) ? array_map('sanitize_text_field', $_POST['images']) : [];

    if (!$post_id || !$edit_id) wp_send_json_error();

    $meta_key = '_pf_carousel_config_' . $edit_id;
    $config = [
        'id' => $edit_id,
        'autoplay' => $autoplay,
        'autoplayDelay' => $autoplayDelay,
        'images' => $images
    ];

    update_post_meta($post_id, $meta_key, $config);

    wp_send_json_success();
});

// AJAX handler for creating carousel
add_action('wp_ajax_pf_create_carousel', function() {
    $id = sanitize_text_field($_POST['pf_new_id']);
    $autoplay = sanitize_text_field($_POST['pf_new_autoplay']);
    $autoplayDelay = intval($_POST['pf_new_autoplayDelay']);

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
        'images' => []
    ];

    update_post_meta($post_id, $meta_key, $config);

    wp_send_json_success();
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