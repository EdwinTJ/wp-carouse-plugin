<?php
if (!defined('ABSPATH')) exit;

function pf_render_admin_page() {
    // Tabs
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'shortcodes';

    ?>
    <div class="wrap">
        <h1>Performance Carousel Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=pf-carousel-settings&tab=shortcodes" class="nav-tab <?php echo $active_tab=='shortcodes'?'nav-tab-active':''; ?>">Shortcodes</a>
            <a href="?page=pf-carousel-settings&tab=global" class="nav-tab <?php echo $active_tab=='global'?'nav-tab-active':''; ?>">Global Defaults</a>
        </h2>

        <?php if ($active_tab == 'shortcodes'): ?>
            <h2>Carousel Shortcodes</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Post/Page</th>
                        <th>Carousel ID</th>
                        <th>Autoplay</th>
                        <th>Autoplay Delay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query all posts/pages to find carousel shortcodes
                    $all_posts = get_posts(['post_type' => ['post','page'], 'numberposts' => -1]);
                    foreach ($all_posts as $post):
                        // Find all pf_carousel meta keys
                        $meta = get_post_meta($post->ID);
                        foreach ($meta as $key => $value):
                            if (strpos($key, '_pf_carousel_config_') === 0):
                                $config = maybe_unserialize($value[0]);
                                ?>
                                    <tr data-id="<?php echo esc_attr($config['id']); ?>">
                                        <td><?php echo esc_html($post->post_title); ?></td>
                                        <td><?php echo esc_html($config['id']); ?></td>
                                        <td class="col-autoplay"><?php echo esc_html($config['autoplay']); ?></td>
                                        <td class="col-autoplayDelay"><?php echo esc_html($config['autoplayDelay']); ?></td>
                                        <td><a href="#" class="pf-edit-link" data-id="<?php echo esc_attr($config['id']); ?>">Edit</a></td>
                                    </tr>
                            <?php
                            endif;
                        endforeach;
                    endforeach;
                    ?>
                </tbody>
            </table>

            <?php
            // Edit form
            if (isset($_GET['edit'])):
                $edit_id = sanitize_text_field($_GET['edit']);
                foreach ($all_posts as $post) {
                    $meta_key = '_pf_carousel_config_' . $edit_id;
                    $config = get_post_meta($post->ID, $meta_key, true);
                    if ($config) {
                        ?>
                        <h3>Edit Carousel <?php echo esc_html($edit_id); ?> (Post: <?php echo esc_html($post->post_title); ?>)</h3>
                        <form method="post">
                            <input type="hidden" name="pf_edit_id" value="<?php echo esc_attr($edit_id); ?>">
                            <input type="hidden" name="pf_post_id" value="<?php echo esc_attr($post->ID); ?>">
                            <table class="form-table">
                                <tr>
                                    <th>Autoplay</th>
                                    <td>
                                        <select name="pf_autoplay">
                                            <option value="true" <?php selected($config['autoplay'], 'true'); ?>>True</option>
                                            <option value="false" <?php selected($config['autoplay'], 'false'); ?>>False</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Autoplay Delay</th>
                                    <td>
                                        <input type="number" name="pf_autoplayDelay" value="<?php echo esc_attr($config['autoplayDelay']); ?>">
                                    </td>
                                </tr>
                            </table>
                            <?php submit_button('Save Carousel'); ?>
                        </form>
                        <?php
                        break;
                    }
                }
            endif;
            ?>

        <?php else: // Global Defaults ?>
            <h2>Global Defaults</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('pf_carousel_options_group');
                do_settings_sections('pf_carousel_options_group');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Autoplay</th>
                        <td>
                            <?php $autoplay = get_option('pf_autoplay', 'true'); ?>
                            <select name="pf_autoplay">
                                <option value="true" <?php selected($autoplay,'true'); ?>>True</option>
                                <option value="false" <?php selected($autoplay,'false'); ?>>False</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Autoplay Delay (ms)</th>
                        <td>
                            <?php $autoplay_delay = get_option('pf_autoplay_delay',3000); ?>
                            <input type="number" name="pf_autoplay_delay" value="<?php echo esc_attr($autoplay_delay); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

// Save edits for shortcode-specific settings
add_action('admin_init', function() {
    if (isset($_POST['pf_edit_id'], $_POST['pf_post_id'])) {
        $edit_id = sanitize_text_field($_POST['pf_edit_id']);
        $post_id = intval($_POST['pf_post_id']);
        $meta_key = '_pf_carousel_config_' . $edit_id;

        $config = [
            'id' => $edit_id,
            'autoplay' => sanitize_text_field($_POST['pf_autoplay']),
            'autoplayDelay' => intval($_POST['pf_autoplayDelay']),
        ];
        update_post_meta($post_id, $meta_key, $config);

        // Redirect to avoid resubmission
        wp_redirect(admin_url('admin.php?page=pf-carousel-settings&tab=shortcodes'));
        exit;
    }
});