<?php
/**
 * Carousel Style: Curved
 *
 * Recreates the Dribbble-style 3D curved gallery effect using
 * CSS 3D transforms and perspective.
 *
 * @since 0.1
 * @return array Style definition.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Curved 3D Gallery',
    'description' => 'A cylindrical wrap effect that curves images toward the viewer.',
    'tier'        => 'premium',
    'options'     => [
        'perspective' => ['type' => 'number', 'label' => 'Depth (Perspective)', 'default' => 1200],
        'curvature'   => ['type' => 'number', 'label' => 'Curvature Intensity', 'default' => 15],
        'spacing'     => ['type' => 'number', 'label' => 'Slide Spacing (px)', 'default' => 20],
    ],
];