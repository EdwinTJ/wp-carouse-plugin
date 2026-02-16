<?php
/**
 * Plugin Name: Beehivehub Carousel
 * Description: Performance-first modular carousel.
 * Version: 0.1
 */

if (!defined('ABSPATH')) exit;

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

function pf_carousel_shortcode() {
    ob_start();
    ?>

    <div class="pf-carousel" 
     data-config='{"slidesToShow":1,"autoplay":true,"autoplayDelay":2000}'>        <div class="pf-track">
            <div class="pf-slide"><img src="https://picsum.photos/800/400?1" /></div>
            <div class="pf-slide"><img src="https://picsum.photos/800/400?2" /></div>
            <div class="pf-slide"><img src="https://picsum.photos/800/400?3" /></div>
        </div>

        <button class="pf-prev" type="button">
            Prev
        </button>
        <button class="pf-next" type="button">
            Next
        </button>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('pf_carousel', 'pf_carousel_shortcode');

// Register admin menu
add_action('admin_menu', function() {
    add_menu_page(
        'Performance Carousel',         // Page title
        'Performance Carousel',         // Menu title
        'manage_options',               // Capability
        'pf-carousel-settings',         // Menu slug
        'pf_render_admin_page',         // Callback
        'dashicons-images-alt2',        // Icon
        60                              // Position
    );
});

// Register settings
add_action('admin_init', function() {
    register_setting('pf_carousel_options_group', 'pf_autoplay');
    register_setting('pf_carousel_options_group', 'pf_autoplay_delay');
});

// Enqueue admin assets
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_pf-carousel-settings') return;

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

// Include admin-page rendering file
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';