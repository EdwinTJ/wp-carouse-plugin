<?php
if (!defined('ABSPATH')) exit;

/**
 * Carousel visual style registry.
 * Each style defines a name, description, and configurable options.
 * Options are rendered as admin fields and applied as CSS variables on the frontend.
 */
function pf_get_carousel_styles() {
    return [
        'default' => [
            'name' => 'Clean',
            'description' => 'Minimal, no effects',
            'options' => []
        ],
        'elevated' => [
            'name' => 'Elevated Shadow',
            'description' => 'Box shadow with rounded corners',
            'options' => [
                'shadow_color'  => ['type' => 'color', 'label' => 'Shadow Color', 'default' => '#00000050'],
                'shadow_blur'   => ['type' => 'number', 'label' => 'Shadow Blur (px)', 'default' => 20],
                'shadow_spread' => ['type' => 'number', 'label' => 'Shadow Spread (px)', 'default' => 0],
                'border_radius' => ['type' => 'number', 'label' => 'Border Radius (px)', 'default' => 12],
            ]
        ],
        'frosted' => [
            'name' => 'Frosted Glass',
            'description' => 'Blur overlay with subtle outline',
            'options' => [
                'blur_strength' => ['type' => 'number', 'label' => 'Blur Strength (px)', 'default' => 8],
                'outline_color' => ['type' => 'color', 'label' => 'Outline Color', 'default' => '#ffffff50'],
                'outline_width' => ['type' => 'number', 'label' => 'Outline Width (px)', 'default' => 1],
                'bg_opacity'    => ['type' => 'number', 'label' => 'Background Opacity (%)', 'default' => 80],
            ]
        ],
    ];
}

/**
 * Navigation (arrows) style registry.
 * Independent from carousel styles — any nav style works with any carousel style.
 */
function pf_get_nav_styles() {
    return [
        'minimal' => [
            'name' => 'Minimal Chevron',
            'description' => 'Simple thin arrows',
            'options' => [
                'arrow_size'  => ['type' => 'number', 'label' => 'Arrow Size (px)', 'default' => 24],
                'arrow_color' => ['type' => 'color', 'label' => 'Arrow Color', 'default' => '#333333'],
            ]
        ],
        'capsule' => [
            'name' => 'Floating Capsule',
            'description' => 'Rounded pill-shaped buttons',
            'options' => [
                'bg_color'      => ['type' => 'color', 'label' => 'Background Color', 'default' => '#000000aa'],
                'arrow_color'   => ['type' => 'color', 'label' => 'Arrow Color', 'default' => '#ffffff'],
                'border_radius' => ['type' => 'number', 'label' => 'Border Radius (px)', 'default' => 24],
                'padding'       => ['type' => 'number', 'label' => 'Padding (px)', 'default' => 12],
            ]
        ],
    ];
}

/**
 * Render HTML inputs for a style's options.
 * Used both for initial PHP render and AJAX field swapping.
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
