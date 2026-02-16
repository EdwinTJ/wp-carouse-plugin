<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1>Beehivehub Carousel Settings</h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=pf-carousel-settings&tab=shortcodes" class="nav-tab <?php echo $active_tab=='shortcodes'?'nav-tab-active':''; ?>">Shortcodes</a>
        <a href="?page=pf-carousel-settings&tab=create" class="nav-tab <?php echo $active_tab=='create'?'nav-tab-active':''; ?>">Create New Carousel</a>
        <a href="?page=pf-carousel-settings&tab=global" class="nav-tab <?php echo $active_tab=='global'?'nav-tab-active':''; ?>">Global Defaults</a>
    </h2>

    <?php if ($active_tab == 'shortcodes'): ?>
        <h2>Carousel Shortcodes</h2>

        <?php if (isset($_GET['created'])): ?>
            <div class="notice notice-success is-dismissible">
                <p>Carousel <strong><?php echo esc_html(sanitize_text_field($_GET['created'])); ?></strong> created successfully. <a href="?page=pf-carousel-settings&tab=config&id=<?php echo esc_attr(sanitize_text_field($_GET['created'])); ?>&post=<?php echo intval($_GET['post'] ?? 0); ?>">Configure it now &rarr;</a></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['saved'])): ?>
            <div class="notice notice-success is-dismissible">
                <p>Carousel <strong><?php echo esc_html(sanitize_text_field($_GET['saved'])); ?></strong> configuration saved.</p>
            </div>
        <?php endif; ?>

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
                $has_carousels = false;
                foreach ($all_posts as $post):
                    $meta = get_post_meta($post->ID);
                    foreach ($meta as $key => $value):
                        if (strpos($key, '_pf_carousel_config_') === 0):
                            $has_carousels = true;
                            $config = maybe_unserialize($value[0]);
                            $shortcode = '[pf_carousel id="' . esc_attr($config['id']) . '"]';
                            ?>
                            <tr data-id="<?php echo esc_attr($config['id']); ?>" data-post="<?php echo $post->ID; ?>">
                                <td><?php echo esc_html($post->post_title); ?></td>
                                <td><strong><?php echo esc_html($config['id']); ?></strong></td>
                                <td class="col-shortcode">
                                    <code class="pf-shortcode-text"><?php echo esc_html($shortcode); ?></code>
                                    <button class="pf-copy-shortcode button button-small" title="Copy shortcode to clipboard">Copy</button>
                                    <input type="text" readonly value="<?php echo esc_attr($shortcode); ?>" class="pf-shortcode-hidden" />
                                </td>
                                <td class="col-autoplay"><?php echo esc_html($config['autoplay']); ?></td>
                                <td class="col-autoplayDelay"><?php echo esc_html($config['autoplayDelay']); ?> ms</td>
                                <td class="pf-actions-cell">
                                    <div class="pf-row-actions">
                                        <span class="pf-action-edit">
                                            <a href="#" class="pf-edit-inline" title="Quick edit autoplay settings inline">Quick Edit</a>
                                        </span>
                                        <span class="pf-action-save" style="display:none;">
                                            <a href="#" class="pf-save-inline">Save</a>
                                        </span>
                                        <span class="pf-action-cancel" style="display:none;">
                                            <a href="#" class="pf-cancel-inline">Cancel</a>
                                        </span>
                                        <span class="pf-action-sep pf-action-edit-sep"> | </span>
                                        <span class="pf-action-config">
                                            <a href="?page=pf-carousel-settings&tab=config&id=<?php echo esc_attr($config['id']); ?>&post=<?php echo $post->ID; ?>" title="Full configuration: images, styles, and all settings">Configure</a>
                                        </span>
                                        <span class="pf-action-sep"> | </span>
                                        <span class="pf-action-delete">
                                            <a href="#" class="pf-delete-carousel" title="Delete this carousel">Delete</a>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endif;
                    endforeach;
                endforeach;

                if (!$has_carousels): ?>
                    <tr><td colspan="6">No carousels found. <a href="?page=pf-carousel-settings&tab=create">Create your first carousel &rarr;</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php elseif ($active_tab == 'create'): ?>
        <h2>Create New Carousel</h2>
        <p class="description">Set up a new carousel with basic settings. After creating, you'll be taken to the full configuration page to add images and fine-tune styles.</p>

        <form id="pf-new-carousel-form">
            <table class="form-table">
                <tr>
                    <th>Carousel ID <span class="pf-required">*</span></th>
                    <td>
                        <input type="text" name="pf_new_id" required placeholder="e.g. homepage-hero" />
                        <p class="description">A unique identifier used in the shortcode. Use letters, numbers, and hyphens.</p>
                    </td>
                </tr>
                <tr>
                    <th>Autoplay</th>
                    <td>
                        <select name="pf_new_autoplay">
                            <option value="true">Enabled</option>
                            <option value="false">Disabled</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Autoplay Delay (ms)</th>
                    <td>
                        <input type="number" name="pf_new_autoplayDelay" value="3000" min="500" step="100" />
                        <p class="description">Time between slides in milliseconds (e.g. 3000 = 3 seconds).</p>
                    </td>
                </tr>
            </table>

            <h3>Carousel Style</h3>
            <p class="description">Choose how the carousel container looks. You can change this later in the full configuration.</p>
            <div class="pf-style-cards" id="pf-carousel-style-cards">
                <?php foreach (pf_get_carousel_styles() as $key => $style): ?>
                    <label class="pf-style-card <?php echo $key === 'default' ? 'pf-style-card-selected' : ''; ?>">
                        <input type="radio" name="pf_new_carousel_style" value="<?php echo esc_attr($key); ?>" <?php checked($key, 'default'); ?> />
                        <span class="pf-style-card-name"><?php echo esc_html($style['name']); ?></span>
                        <span class="pf-style-card-desc"><?php echo esc_html($style['description']); ?></span>
                        <?php if ($style['tier'] !== 'free'): ?>
                            <span class="pf-style-card-badge"><?php echo esc_html(ucfirst($style['tier'])); ?></span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <h3>Navigation Style</h3>
            <p class="description">Choose the arrow/button style for navigating between slides.</p>
            <div class="pf-style-cards" id="pf-nav-style-cards">
                <?php foreach (pf_get_nav_styles() as $key => $style): ?>
                    <label class="pf-style-card <?php echo $key === 'minimal' ? 'pf-style-card-selected' : ''; ?>">
                        <input type="radio" name="pf_new_nav_style" value="<?php echo esc_attr($key); ?>" <?php checked($key, 'minimal'); ?> />
                        <span class="pf-style-card-name"><?php echo esc_html($style['name']); ?></span>
                        <span class="pf-style-card-desc"><?php echo esc_html($style['description']); ?></span>
                        <?php if ($style['tier'] !== 'free'): ?>
                            <span class="pf-style-card-badge"><?php echo esc_html(ucfirst($style['tier'])); ?></span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <?php submit_button('Create Carousel'); ?>
        </form>

    <?php else: // global defaults ?>
        <h2>Global Defaults</h2>
        <div class="pf-info-box">
            <p><strong>What are Global Defaults?</strong></p>
            <p>These settings are used as the starting values whenever you create a new carousel. Changing these defaults will <strong>not</strong> affect existing carousels &mdash; only new ones you create going forward.</p>
        </div>
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
                            <option value="true" <?php selected($autoplay,'true'); ?>>Enabled</option>
                            <option value="false" <?php selected($autoplay,'false'); ?>>Disabled</option>
                        </select>
                        <p class="description">Whether new carousels auto-advance between slides by default.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Autoplay Delay (ms)</th>
                    <td>
                        <?php $autoplay_delay = get_option('pf_autoplay_delay',3000); ?>
                        <input type="number" name="pf_autoplay_delay" value="<?php echo esc_attr($autoplay_delay); ?>" min="500" step="100" />
                        <p class="description">Default time between slides in milliseconds (e.g. 3000 = 3 seconds).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Defaults'); ?>
        </form>
    <?php endif; ?>
</div>
