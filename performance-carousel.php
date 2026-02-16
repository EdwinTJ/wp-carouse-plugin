<?php
/**
 * Plugin Name: Performance Carousel
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