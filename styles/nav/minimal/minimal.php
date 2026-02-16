<?php
/**
 * Navigation Style: Minimal Chevron
 *
 * Simple CSS-border chevron arrows with no background.
 * Lightweight and unobtrusive navigation controls.
 *
 * @since 0.1
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Minimal Chevron',
    'description' => 'Simple thin arrows',
    'tier'        => 'free',
    'content'     => [
        'prev_html' => '',
        'next_html' => '',
    ],
    'options'     => [
        'arrow_size'  => ['type' => 'number', 'label' => 'Arrow Size (px)',  'default' => 24],
        'arrow_color' => ['type' => 'color',  'label' => 'Arrow Color',      'default' => '#333333'],
    ],
];
