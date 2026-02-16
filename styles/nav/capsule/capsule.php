<?php
/**
 * Navigation Style: Floating Capsule
 *
 * Rounded pill-shaped navigation buttons with a semi-transparent
 * background, shadow, and hover scale effect.
 *
 * @since 0.1
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Floating Capsule',
    'description' => 'Rounded pill-shaped buttons',
    'tier'        => 'free',
    'content'     => [
        'prev_html' => '&#8592;',
        'next_html' => '&#8594;',
    ],
    'options'     => [
        'bg_color'      => ['type' => 'color',  'label' => 'Background Color',    'default' => '#000000aa'],
        'arrow_color'   => ['type' => 'color',  'label' => 'Arrow Color',          'default' => '#ffffff'],
        'border_radius' => ['type' => 'number', 'label' => 'Border Radius (px)',   'default' => 24],
        'padding'       => ['type' => 'number', 'label' => 'Padding (px)',          'default' => 12],
    ],
];
