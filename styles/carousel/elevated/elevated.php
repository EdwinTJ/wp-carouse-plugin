<?php
/**
 * Carousel Style: Elevated Shadow
 *
 * Adds a box shadow and rounded corners to the carousel container,
 * creating a lifted/floating card appearance.
 *
 * @since 0.1
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Elevated Shadow',
    'description' => 'Box shadow with rounded corners',
    'tier'        => 'free',
    'options'     => [
        'shadow_color'  => ['type' => 'color',  'label' => 'Shadow Color',      'default' => '#00000050'],
        'shadow_blur'   => ['type' => 'number', 'label' => 'Shadow Blur (px)',   'default' => 20],
        'shadow_spread' => ['type' => 'number', 'label' => 'Shadow Spread (px)', 'default' => 0],
        'border_radius' => ['type' => 'number', 'label' => 'Border Radius (px)', 'default' => 12],
    ],
];
