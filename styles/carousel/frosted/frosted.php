<?php
/**
 * Carousel Style: Frosted Glass
 *
 * Applies a backdrop blur overlay with a subtle outline border,
 * creating a frosted glass/glassmorphism effect.
 *
 * @since 0.1
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Frosted Glass',
    'description' => 'Blur overlay with subtle outline',
    'tier'        => 'free',
    'options'     => [
        'blur_strength' => ['type' => 'number', 'label' => 'Blur Strength (px)',       'default' => 8],
        'outline_color' => ['type' => 'color',  'label' => 'Outline Color',            'default' => '#ffffff50'],
        'outline_width' => ['type' => 'number', 'label' => 'Outline Width (px)',        'default' => 1],
        'bg_opacity'    => ['type' => 'number', 'label' => 'Background Opacity (%)',    'default' => 80],
    ],
];
