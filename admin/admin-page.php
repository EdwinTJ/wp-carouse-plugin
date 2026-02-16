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

        ?>
        <div class="wrap">
            <h1>Carousel Config: <?php echo esc_html($carousel_id); ?></h1>

            <h2>Carousel Images</h2>
            <p>Choose image source:</p>
            <select id="pf-image-source">
                <option value="media">Media Library</option>
                <option value="url">External URLs</option>
            </select>

            <div id="pf-media-library-section">
                <button type="button" id="pf-add-images" class="button">Select Images</button>
                <ul id="pf-selected-images" style="margin-top:10px;">
                    <?php
                    foreach ($config['images'] ?? [] as $img) {
                        if (is_numeric($img)) {
                            $url = wp_get_attachment_url($img);
                        } else {
                            $url = esc_url($img);
                        }
                        echo '<li data-id="'.esc_attr($img).'"><img src="'.esc_url($url).'" width="100" /> '.esc_html($url).'</li>';
                    }
                    ?>
                </ul>
            </div>

            <div id="pf-url-section" style="display:none;">
                <textarea id="pf-image-urls" rows="5" style="width:100%;" placeholder="Paste one URL per line"><?php
                    foreach ($config['images'] ?? [] as $img) {
                        if (!is_numeric($img)) echo esc_textarea($img) . "\n";
                    }
                ?></textarea>
            </div>

            <h2>Settings</h2>
            <form id="pf-full-config-form">
                <table class="form-table">
                    <tr>
                        <th>Autoplay</th>
                        <td>
                            <select name="pf_autoplay">
                                <option value="true" <?php selected($config['autoplay'] ?? '', 'true'); ?>>True</option>
                                <option value="false" <?php selected($config['autoplay'] ?? '', 'false'); ?>>False</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Autoplay Delay (ms)</th>
                        <td>
                            <input type="number" name="pf_autoplayDelay" value="<?php echo esc_attr($config['autoplayDelay'] ?? 2000); ?>">
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Carousel'); ?>
            </form>
        </div>
        <?php
        return;
    }

    // Main tabs: Shortcodes / Create / Global
    require plugin_dir_path(__FILE__) . 'tabs.php';
}
