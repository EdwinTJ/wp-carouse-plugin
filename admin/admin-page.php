<?php
if (!defined('ABSPATH')) exit;

function pf_render_admin_page() {
    // Get saved options (or defaults)
    $autoplay = get_option('pf_autoplay', 'true');
    $autoplay_delay = get_option('pf_autoplay_delay', 3000);
    ?>
    <div class="wrap">
        <h1>Performance Carousel Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('pf_carousel_options_group');
                do_settings_sections('pf_carousel_options_group');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Autoplay</th>
                    <td>
                        <select name="pf_autoplay">
                            <option value="true" <?php selected($autoplay, 'true'); ?>>True</option>
                            <option value="false" <?php selected($autoplay, 'false'); ?>>False</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Autoplay Delay (ms)</th>
                    <td>
                        <input type="number" name="pf_autoplay_delay" value="<?php echo esc_attr($autoplay_delay); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}