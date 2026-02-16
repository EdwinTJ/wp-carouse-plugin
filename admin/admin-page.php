<?php
if (!defined('ABSPATH')) exit;

function pf_render_admin_page() {
    $active_tab = $_GET['tab'] ?? 'shortcodes';

    // Full carousel config page
    if ($active_tab === 'config' && isset($_GET['id'], $_GET['post'])) {
        $carousel_id = sanitize_text_field($_GET['id']);
        $post_id = intval($_GET['post']);
        $meta_key = '_pf_carousel_config_' . $carousel_id;
        $config = get_post_meta($post_id, $meta_key, true);
        $config = is_array($config) ? $config : [];

        $carousel_styles = pf_get_carousel_styles();
        $nav_styles = pf_get_nav_styles();
        $current_carousel_style = $config['carousel_style'] ?? 'default';
        $current_carousel_style_options = $config['carousel_style_options'] ?? [];
        $current_nav_style = $config['nav_style'] ?? 'minimal';
        $current_nav_style_options = $config['nav_style_options'] ?? [];

        ?>
        <div class="wrap">
            <p class="pf-back-link">
                <a href="?page=pf-carousel-settings&tab=shortcodes">&larr; Back to All Carousels</a>
            </p>
            <h1>Configure Carousel: <em><?php echo esc_html($carousel_id); ?></em></h1>
            <p class="description">Manage images, visual styles, and playback settings for this carousel. The shortcode is <code>[pf_carousel id="<?php echo esc_attr($carousel_id); ?>"]</code>.</p>

            <!-- Images Section -->
            <div class="pf-config-section">
                <h2>Carousel Images</h2>
                <p class="description">Choose where your images come from, then select or enter them below.</p>

                <div class="pf-image-source-toggle">
                    <label class="pf-toggle-option">
                        <input type="radio" name="pf_image_source" value="media" checked /> Media Library
                    </label>
                    <label class="pf-toggle-option">
                        <input type="radio" name="pf_image_source" value="url" /> External URLs
                    </label>
                </div>

                <div id="pf-media-library-section">
                    <button type="button" id="pf-add-images" class="button button-secondary">Select Images from Media Library</button>
                    <div id="pf-selected-images" class="pf-image-grid">
                        <?php
                        foreach ($config['images'] ?? [] as $img) {
                            if (is_numeric($img)) {
                                $url = wp_get_attachment_url($img);
                            } else {
                                $url = esc_url($img);
                            }
                            echo '<div class="pf-image-card" data-id="' . esc_attr($img) . '">';
                            echo '<img src="' . esc_url($url) . '" />';
                            echo '<button type="button" class="pf-remove-image" title="Remove image">&times;</button>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <?php if (empty($config['images'])): ?>
                        <p class="pf-empty-state" id="pf-no-images-msg">No images selected yet. Click the button above to add images.</p>
                    <?php endif; ?>
                </div>

                <div id="pf-url-section" style="display:none;">
                    <textarea id="pf-image-urls" rows="5" style="width:100%;" placeholder="Paste one image URL per line"><?php
                        foreach ($config['images'] ?? [] as $img) {
                            if (!is_numeric($img)) echo esc_textarea($img) . "\n";
                        }
                    ?></textarea>
                    <p class="description">Enter one full image URL per line (e.g. https://example.com/photo.jpg).</p>
                </div>
            </div>

            <!-- Carousel Style Section -->
            <div class="pf-config-section">
                <h2>Carousel Style</h2>
                <p class="description">Controls the visual appearance of the carousel container (shadows, borders, effects).</p>
                <div class="pf-style-cards" id="pf-carousel-style-cards">
                    <?php foreach ($carousel_styles as $key => $style): ?>
                        <label class="pf-style-card <?php echo $key === $current_carousel_style ? 'pf-style-card-selected' : ''; ?>">
                            <input type="radio" name="pf_carousel_style" value="<?php echo esc_attr($key); ?>" <?php checked($current_carousel_style, $key); ?> />
                            <span class="pf-style-card-name"><?php echo esc_html($style['name']); ?></span>
                            <span class="pf-style-card-desc"><?php echo esc_html($style['description']); ?></span>
                            <?php if ($style['tier'] !== 'free'): ?>
                                <span class="pf-style-card-badge"><?php echo esc_html(ucfirst($style['tier'])); ?></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div id="pf-carousel-style-options" class="pf-style-options-panel">
                    <?php echo pf_render_style_options_html($carousel_styles[$current_carousel_style]['options'] ?? [], $current_carousel_style_options); ?>
                </div>
            </div>

            <!-- Navigation Style Section -->
            <div class="pf-config-section">
                <h2>Navigation Style</h2>
                <p class="description">Controls the look of the previous/next navigation arrows.</p>
                <div class="pf-style-cards" id="pf-nav-style-cards">
                    <?php foreach ($nav_styles as $key => $style): ?>
                        <label class="pf-style-card <?php echo $key === $current_nav_style ? 'pf-style-card-selected' : ''; ?>">
                            <input type="radio" name="pf_nav_style" value="<?php echo esc_attr($key); ?>" <?php checked($current_nav_style, $key); ?> />
                            <span class="pf-style-card-name"><?php echo esc_html($style['name']); ?></span>
                            <span class="pf-style-card-desc"><?php echo esc_html($style['description']); ?></span>
                            <?php if ($style['tier'] !== 'free'): ?>
                                <span class="pf-style-card-badge"><?php echo esc_html(ucfirst($style['tier'])); ?></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div id="pf-nav-style-options" class="pf-style-options-panel">
                    <?php echo pf_render_style_options_html($nav_styles[$current_nav_style]['options'] ?? [], $current_nav_style_options); ?>
                </div>
            </div>

            <!-- Playback Settings -->
            <div class="pf-config-section">
                <h2>Playback Settings</h2>
                <form id="pf-full-config-form">
                    <table class="form-table">
                        <tr>
                            <th>Autoplay</th>
                            <td>
                                <select name="pf_autoplay">
                                    <option value="true" <?php selected($config['autoplay'] ?? '', 'true'); ?>>Enabled</option>
                                    <option value="false" <?php selected($config['autoplay'] ?? '', 'false'); ?>>Disabled</option>
                                </select>
                                <p class="description">Automatically advance slides without user interaction.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Autoplay Delay (ms)</th>
                            <td>
                                <input type="number" name="pf_autoplayDelay" value="<?php echo esc_attr($config['autoplayDelay'] ?? 2000); ?>" min="500" step="100">
                                <p class="description">Time between auto-advancing slides (e.g. 2000 = 2 seconds).</p>
                            </td>
                        </tr>
                    </table>
                    <div class="pf-save-bar">
                        <?php submit_button('Save Configuration', 'primary', 'submit', false); ?>
                        <a href="?page=pf-carousel-settings&tab=shortcodes" class="button button-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        return;
    }

    // Main tabs: Shortcodes / Create / Global
    require plugin_dir_path(__FILE__) . 'tabs.php';
}
