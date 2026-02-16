# Style Guide — How to Add New Carousel & Navigation Designs

This document explains how to create new carousel styles and navigation styles for the Performance Carousel plugin. It is intended for both human developers and AI coding agents.

---

## Architecture Overview

The plugin uses two independent style registries:

- **Carousel styles** control the visual appearance of the carousel container (shadows, borders, blur, outlines, etc.)
- **Navigation styles** control the prev/next arrow buttons (shape, color, size, animation)

Any navigation style can be combined with any carousel style. They are completely independent.

### Auto-Discovery

Styles are **not** registered in a central file. The system auto-discovers them by scanning folders:

```
styles/
  carousel/{style_key}/     <- carousel visual styles live here
  nav/{style_key}/          <- navigation arrow styles live here
```

The discovery engine is in `includes/styles.php` via `pf_discover_styles()`. It uses `glob()` to find all subfolders, then loads each `{style_key}.php` file.

### What Happens at Runtime

1. Admin selects a carousel style and nav style in the config page
2. Style options are saved to post meta alongside the carousel config
3. On the frontend, the shortcode renderer:
   - Adds CSS classes: `pf-style-{style_key}` and `pf-nav-{style_key}`
   - Sets inline CSS custom properties from saved option values
   - Calls `pf_enqueue_style_assets()` which loads **only** that style's CSS (and JS if it exists)

---

## File Structure Per Style

### Carousel Style (required files)

```
styles/carousel/{style_key}/
  {style_key}.php      <- REQUIRED: style definition (name, options, tier)
  {style_key}.css      <- REQUIRED: visual CSS rules
  {style_key}.js       <- OPTIONAL: JS enhancements (only if needed)
```

### Navigation Style (required files)

```
styles/nav/{style_key}/
  {style_key}.php      <- REQUIRED: style definition
  {style_key}.css      <- REQUIRED: visual CSS rules
  {style_key}.js       <- OPTIONAL: JS enhancements (rarely needed)
```

**Important:** The folder name, PHP filename, CSS filename, and JS filename must all match the `style_key`. For example, a carousel style called "neon" must be:

```
styles/carousel/neon/
  neon.php
  neon.css
  neon.js   (optional)
```

---

## Step-by-Step: Adding a Carousel Style (CSS Only)

This example creates a "Bordered" style with a configurable border.

### 1. Create the folder

```
styles/carousel/bordered/
```

### 2. Create `bordered.php`

This file **must return an array**. It is loaded via `require` by the auto-discovery engine.

```php
<?php
/**
 * Carousel Style: Bordered
 *
 * Adds a solid border around the carousel with configurable
 * color, width, and corner radius.
 *
 * @since 0.2
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Bordered',
    'description' => 'Solid border with rounded corners',
    'tier'        => 'free',
    'options'     => [
        'border_color'  => ['type' => 'color',  'label' => 'Border Color',      'default' => '#333333'],
        'border_width'  => ['type' => 'number', 'label' => 'Border Width (px)',  'default' => 2],
        'border_radius' => ['type' => 'number', 'label' => 'Border Radius (px)', 'default' => 8],
    ],
];
```

#### PHP definition rules

| Field         | Type   | Required | Description |
|---------------|--------|----------|-------------|
| `name`        | string | Yes      | Display name shown in admin dropdowns |
| `description` | string | Yes      | Short description shown next to the name |
| `tier`        | string | Yes      | `'free'` or `'premium'` |
| `content`     | array  | Nav only | Nav styles only. Defines `prev_html` and `next_html` — the markup rendered inside the prev/next buttons. Falls back to `'Prev'`/`'Next'` text if omitted. |
| `options`     | array  | Yes      | Configurable fields (can be empty `[]`) |

#### Option field definition

Each entry in `options` is keyed by a unique `option_key` and has:

| Field     | Type   | Required | Description |
|-----------|--------|----------|-------------|
| `type`    | string | Yes      | `'color'`, `'number'`, or `'text'` |
| `label`   | string | Yes      | Human-readable label for the admin form |
| `default` | mixed  | Yes      | Default value used when no override is saved |

**Option key naming:** Use `snake_case`. The key becomes a CSS custom property:
- Carousel style options: `--pf-{option_key}` (e.g., `border_color` becomes `--pf-border_color`)
- Navigation style options: `--pf-nav-{option_key}` (e.g., `arrow_color` becomes `--pf-nav-arrow_color`)

### 3. Create `bordered.css`

The CSS class is always `.pf-style-{style_key}` for carousel styles.

Use CSS custom properties with fallback defaults that match your PHP defaults:

```css
/**
 * Carousel Style: Bordered
 *
 * Uses CSS custom properties set inline by the shortcode renderer:
 *   --pf-border_color, --pf-border_width, --pf-border_radius
 */
.pf-style-bordered {
  border: var(--pf-border_width, 2px) solid var(--pf-border_color, #333);
  border-radius: var(--pf-border_radius, 8px);
  overflow: hidden;
}
```

#### CSS naming conventions

| Style type | CSS class pattern            | CSS variable prefix |
|------------|------------------------------|---------------------|
| Carousel   | `.pf-style-{style_key}`      | `--pf-`             |
| Navigation | `.pf-nav-{style_key}`        | `--pf-nav-`         |

#### CSS rules

- Always use `var(--pf-{key}, {fallback})` so styles work even without inline overrides
- The fallback value in CSS **must match** the `default` value in the PHP definition
- Carousel styles target `.pf-style-{key}` and can also target child elements like `.pf-style-{key} .pf-track`
- Nav styles target `.pf-nav-{key} .pf-prev` and `.pf-nav-{key} .pf-next`
- Nav buttons have base styles (position, cursor, z-index) from `assets/css/carousel.css` — your nav CSS overrides appearance only

### 4. Done

No other files need editing. The auto-discovery picks it up immediately. Go to any carousel's Config page in the admin and the new style appears in the dropdown.

---

## Step-by-Step: Adding a Carousel Style (With JS)

This example creates a "Parallax" style that uses JavaScript for a scroll-based effect.

### 1. Create the folder and PHP/CSS as above

```
styles/carousel/parallax/
  parallax.php
  parallax.css
  parallax.js      <- this is the addition
```

### 2. `parallax.php` — same pattern as before

```php
<?php
/**
 * Carousel Style: Parallax
 *
 * Applies a parallax scroll effect to carousel slides,
 * creating depth as the user scrolls the page.
 *
 * @since 0.2
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Parallax',
    'description' => 'Scroll-based parallax depth effect',
    'tier'        => 'premium',
    'options'     => [
        'parallax_intensity' => ['type' => 'number', 'label' => 'Parallax Intensity (%)', 'default' => 30],
    ],
];
```

### 3. `parallax.css` — visual base

```css
/**
 * Carousel Style: Parallax
 *
 * Uses CSS custom properties set inline by the shortcode renderer:
 *   --pf-parallax_intensity
 */
.pf-style-parallax {
  overflow: hidden;
  perspective: 1000px;
}

.pf-style-parallax .pf-slide img {
  transition: transform 0.1s ease-out;
  will-change: transform;
}
```

### 4. `parallax.js` — JavaScript enhancements

The JS file is automatically loaded in the footer only when a carousel on the page uses this style. Use an IIFE to avoid polluting the global scope.

```js
/**
 * Carousel Style JS: Parallax
 *
 * Adds scroll-based parallax movement to carousel slide images.
 * Only loaded when a carousel uses the parallax style.
 *
 * @param {NodeList} carousels  All .pf-style-parallax elements on the page.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var carousels = document.querySelectorAll('.pf-style-parallax');
        if (!carousels.length) return;

        /**
         * Calculates and applies parallax offset on scroll.
         *
         * @param {Element} carousel  The carousel root element.
         * @returns {void}
         */
        function applyParallax(carousel) {
            var images = carousel.querySelectorAll('.pf-slide img');
            var intensity = parseFloat(
                getComputedStyle(carousel).getPropertyValue('--pf-parallax_intensity')
            ) || 30;

            window.addEventListener('scroll', function () {
                var rect = carousel.getBoundingClientRect();
                var offset = (rect.top / window.innerHeight) * intensity;
                images.forEach(function (img) {
                    img.style.transform = 'translateY(' + offset + 'px)';
                });
            });
        }

        carousels.forEach(applyParallax);
    });
})();
```

#### JS conventions

- Wrap everything in an IIFE: `(function() { ... })();`
- Wait for `DOMContentLoaded`
- Select only your style's carousels: `document.querySelectorAll('.pf-style-{style_key}')`
- Exit early if none found: `if (!carousels.length) return;`
- Read CSS custom properties via `getComputedStyle(el).getPropertyValue('--pf-{key}')`
- Follow JSDoc standards from AGENTS.md: types in curly braces, `@param`, `@returns`
- No jQuery dependency — use vanilla JS for frontend style scripts

---

## Step-by-Step: Adding a Navigation Style

Nav styles follow the same pattern but target the arrow buttons.

### Example: "Circle" nav style

### 1. Create the folder

```
styles/nav/circle/
  circle.php
  circle.css
```

### 2. `circle.php`

```php
<?php
/**
 * Navigation Style: Circle
 *
 * Round circular arrow buttons with a solid background
 * and centered arrow icon.
 *
 * @since 0.2
 * @return array Style definition with name, description, tier, and options.
 */
if (!defined('ABSPATH')) exit;

return [
    'name'        => 'Circle',
    'description' => 'Round circular arrow buttons',
    'tier'        => 'free',
    'content'     => [
        'prev_html' => '&#8592;',
        'next_html' => '&#8594;',
    ],
    'options'     => [
        'bg_color'    => ['type' => 'color',  'label' => 'Background Color', 'default' => '#000000'],
        'arrow_color' => ['type' => 'color',  'label' => 'Arrow Color',      'default' => '#ffffff'],
        'size'        => ['type' => 'number', 'label' => 'Button Size (px)', 'default' => 40],
    ],
];
```

### 3. `circle.css`

Nav styles use the `.pf-nav-{style_key}` prefix and target `.pf-prev` / `.pf-next`:

```css
/**
 * Navigation Style: Circle
 *
 * Uses CSS custom properties set inline by the shortcode renderer:
 *   --pf-nav-bg_color, --pf-nav-arrow_color, --pf-nav-size
 */
.pf-nav-circle .pf-prev,
.pf-nav-circle .pf-next {
  background: var(--pf-nav-bg_color, #000);
  color: var(--pf-nav-arrow_color, #fff);
  border: none;
  border-radius: 50%;
  width: var(--pf-nav-size, 40px);
  height: var(--pf-nav-size, 40px);
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: calc(var(--pf-nav-size, 40px) * 0.4);
  transition: opacity 0.2s;
}

.pf-nav-circle .pf-prev { left: 12px; }
.pf-nav-circle .pf-next { right: 12px; }

.pf-nav-circle .pf-prev:hover,
.pf-nav-circle .pf-next:hover {
  opacity: 0.8;
}
```

---

## Checklist for Adding a New Style

Use this checklist every time you add a style:

- [ ] Folder created: `styles/{carousel|nav}/{style_key}/`
- [ ] PHP file returns an array with `name`, `description`, `tier`, `options` (and `content` for nav styles)
- [ ] PHP file has `if (!defined('ABSPATH')) exit;` guard
- [ ] PHP file has PHPDoc block with `@since` and `@return` tags
- [ ] CSS file uses `.pf-style-{key}` (carousel) or `.pf-nav-{key}` (nav) class prefix
- [ ] CSS file uses `var(--pf-{key}, fallback)` for every configurable option
- [ ] CSS fallback values match PHP `default` values
- [ ] CSS file has a JSDoc-style comment header listing the CSS variables it uses
- [ ] JS file (if needed) uses IIFE, waits for DOMContentLoaded, selects only its own carousels
- [ ] JS file has JSDoc with `@param` and `@returns` per AGENTS.md
- [ ] Folder name, PHP filename, CSS filename, and JS filename all match `style_key`
- [ ] No changes needed to any other file in the plugin

---

## How CSS Variables Flow (End to End)

```
1. PHP definition (bordered.php):
   'border_color' => ['type' => 'color', 'default' => '#333333']

2. Admin saves user's override to post meta:
   'carousel_style_options' => ['border_color' => '#ff0000']

3. Shortcode renderer builds inline style attribute:
   style="--pf-border_color:#ff0000"

4. CSS file reads it:
   border: var(--pf-border_width, 2px) solid var(--pf-border_color, #333);
                                                 ^^^^^^^^^^^^^^^^ uses #ff0000
```

For nav styles, the variable prefix is `--pf-nav-` instead of `--pf-`.

---

## Existing Styles Reference

### Carousel Styles

| Key       | Name             | Tier | Has JS | Options |
|-----------|------------------|------|--------|---------|
| `default` | Clean            | free | No     | None    |
| `elevated`| Elevated Shadow  | free | Yes    | shadow_color, shadow_blur, shadow_spread, border_radius |
| `frosted` | Frosted Glass    | free | Yes    | blur_strength, outline_color, outline_width, bg_opacity |

### Navigation Styles

| Key           | Name              | Tier | Has JS | Content          | Options |
|---------------|-------------------|------|--------|------------------|---------|
| `minimal`     | Minimal Chevron   | free | No     | CSS chevron spans | arrow_size, arrow_color |
| `capsule`     | Floating Capsule  | free | No     | ← → arrows       | bg_color, arrow_color, border_radius, padding |
| `custom-text` | Custom Text       | free | No     | User-defined text | prev_text, next_text, text_color, bg_color, border_color, font_size, padding |

---

## Supported Option Types

| Type     | HTML Input       | Example Default | Notes |
|----------|------------------|-----------------|-------|
| `color`  | `<input type="color">` | `'#333333'`, `'#00000050'` | Hex format. Alpha hex supported. |
| `number` | `<input type="number">` | `20`, `0`, `80` | Integers. Appended with `px` in CSS variables automatically for numeric values. |
| `text`   | `<input type="text">`   | `'solid'`, `'dashed'` | Freeform string. |

---

## Performance Notes

- Only the CSS/JS for active styles is loaded on the frontend (via `pf_enqueue_style_assets()`)
- A page with 3 carousels all using "elevated" loads `elevated.css` and `elevated.js` only once
- The "default" (Clean) style has no CSS or JS files — zero extra network requests
- Style definitions are cached in memory per request via `static $cache` in the registry functions
