<?php
/**
 * Navigation Style: Custom Text
 *
 * Simple text-based navigation buttons with user-configurable labels.
 * Users can change the "Prev" and "Next" text to anything they want.
 *
 * @since 0.1
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Custom Text',
    'description' => 'Text buttons with custom labels',
    'tier'        => 'free',
    'content'     => [
        'prev_html' => 'Prev',
        'next_html' => 'Next',
    ],
    'options'     => [
        'prev_text'     => ['type' => 'text',   'label' => 'Previous Button Text', 'default' => 'Prev'],
        'next_text'     => ['type' => 'text',   'label' => 'Next Button Text',     'default' => 'Next'],
        'text_color'    => ['type' => 'color',  'label' => 'Text Color',           'default' => '#333333'],
        'bg_color'      => ['type' => 'color',  'label' => 'Background Color',     'default' => '#ffffff'],
        'border_color'  => ['type' => 'color',  'label' => 'Border Color',         'default' => '#cccccc'],
        'font_size'     => ['type' => 'number', 'label' => 'Font Size (px)',       'default' => 14],
        'padding'       => ['type' => 'number', 'label' => 'Padding (px)',         'default' => 10],
    ],
];
