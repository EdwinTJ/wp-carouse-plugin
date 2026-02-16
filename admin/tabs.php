<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1>Performance Carousel Settings</h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=pf-carousel-settings&tab=shortcodes" class="nav-tab <?php echo $active_tab=='shortcodes'?'nav-tab-active':''; ?>">Shortcodes</a>
        <a href="?page=pf-carousel-settings&tab=create" class="nav-tab <?php echo $active_tab=='create'?'nav-tab-active':''; ?>">Create New Carousel</a>
        <a href="?page=pf-carousel-settings&tab=global" class="nav-tab <?php echo $active_tab=='global'?'nav-tab-active':''; ?>">Global Defaults</a>
    </h2>

    <?php if ($active_tab == 'shortcodes'): ?>
        <h2>Carousel Shortcodes</h2>
        <table class="wp-list-table widefat fixed striped" id="pf-shortcodes-table">
            <thead>
                <tr>
                    <th>Post/Page</th>
                    <th>Carousel ID</th>
                    <th>Shortcode</th>
                    <th>Autoplay</th>
                    <th>Autoplay Delay</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_posts = get_posts(['post_type'=>['post','page'], 'numberposts'=>-1]);
                foreach ($all_posts as $post):
                    $meta = get_post_meta($post->ID);
                    foreach ($meta as $key => $value):
                        if (strpos($key, '_pf_carousel_config_') === 0):
                            $config = maybe_unserialize($value[0]);
                            $shortcode = '[pf_carousel id="' . esc_attr($config['id']) . '"]';
                            ?>
                            <tr data-id="<?php echo esc_attr($config['id']); ?>" data-post="<?php echo $post->ID; ?>">
                                <td><?php echo esc_html($post->post_title); ?></td>
                                <td><?php echo esc_html($config['id']); ?></td>
                                <td class="col-shortcode">
                                    <input type="text" readonly value="<?php echo esc_attr($shortcode); ?>" />
                                    <button class="pf-copy-shortcode button">Copy</button>
                                </td>
                                <td class="col-autoplay"><?php echo esc_html($config['autoplay']); ?></td>
                                <td class="col-autoplayDelay"><?php echo esc_html($config['autoplayDelay']); ?></td>
                                <td>
                                    <a href="#" class="pf-edit-inline">Edit</a> |
                                    <a href="#" class="pf-save-inline" style="display:none;">Save</a> |
                                    <a href="#" class="pf-cancel-inline" style="display:none;">Cancel</a> |
                                    <a href="?page=pf-carousel-settings&tab=config&id=<?php echo esc_attr($config['id']); ?>&post=<?php echo $post->ID; ?>">Config</a>
                                </td>
                            </tr>
                        <?php
                        endif;
                    endforeach;
                endforeach;
                ?>
            </tbody>
        </table>

    <?php elseif ($active_tab == 'create'): ?>
        <h2>Create New Carousel</h2>
        <form id="pf-new-carousel-form">
            <table class="form-table">
                <tr>
                    <th>Carousel ID</th>
                    <td><input type="text" name="pf_new_id" required /></td>
                </tr>
                <tr>
                    <th>Autoplay</th>
                    <td>
                        <select name="pf_new_autoplay">
                            <option value="true">True</option>
                            <option value="false">False</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Autoplay Delay</th>
                    <td><input type="number" name="pf_new_autoplayDelay" value="3000" /></td>
                </tr>
            </table>
            <?php submit_button('Create Carousel'); ?>
        </form>

    <?php else: // global defaults ?>
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
