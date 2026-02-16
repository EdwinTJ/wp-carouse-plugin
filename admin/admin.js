jQuery(document).ready(($) => {

    // ── Style card selection (visual toggle) ──
    $('.pf-style-cards').on('change', 'input[type="radio"]', function() {
        const cards = $(this).closest('.pf-style-cards');
        cards.find('.pf-style-card').removeClass('pf-style-card-selected');
        $(this).closest('.pf-style-card').addClass('pf-style-card-selected');
    });

    // ── Inline Edit ──
    $('#pf-shortcodes-table').on('click', '.pf-edit-inline', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');

        row.addClass('editing');

        const autoplayVal = row.find('.col-autoplay').text().trim();
        const delayVal = row.find('.col-autoplayDelay').text().replace(' ms', '').trim();

        row.find('.col-autoplay').html(`<select><option value="true" ${autoplayVal==='true'?'selected':''}>Enabled</option><option value="false" ${autoplayVal==='false'?'selected':''}>Disabled</option></select>`);
        row.find('.col-autoplayDelay').html(`<input type="number" value="${delayVal}" min="500" step="100" />`);

        row.find('.pf-action-edit').hide();
        row.find('.pf-action-save, .pf-action-cancel').show();
    });

    // Cancel inline edit
    $('#pf-shortcodes-table').on('click', '.pf-cancel-inline', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');

        row.removeClass('editing');

        const autoplayVal = row.find('.col-autoplay select').val();
        const delayVal = row.find('.col-autoplayDelay input').val();

        row.find('.col-autoplay').text(autoplayVal);
        row.find('.col-autoplayDelay').text(delayVal + ' ms');

        row.find('.pf-action-edit').show();
        row.find('.pf-action-save, .pf-action-cancel').hide();
    });

    // Save inline edit
    $('#pf-shortcodes-table').on('click', '.pf-save-inline', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');
        const post_id = row.data('post');
        const edit_id = row.data('id');
        const autoplay = row.find('.col-autoplay select').val();
        const autoplayDelay = row.find('.col-autoplayDelay input').val();

        $.post(ajaxurl, {
            action: 'pf_update_carousel_config',
            post_id: post_id,
            edit_id: edit_id,
            autoplay: autoplay,
            autoplayDelay: autoplayDelay
        }, function(res){
            if(res.success){
                row.find('.col-autoplay').text(autoplay);
                row.find('.col-autoplayDelay').text(autoplayDelay + ' ms');
                row.removeClass('editing');
                row.find('.pf-action-edit').show();
                row.find('.pf-action-save, .pf-action-cancel').hide();
            } else {
                alert('Error updating carousel.');
            }
        });
    });

    // ── Create New Carousel (redirect to shortcodes tab with success notice) ──
    $('#pf-new-carousel-form').on('submit', function(e){
        e.preventDefault();
        const formData = $(this).serializeArray();
        const data = { action: 'pf_create_carousel' };
        formData.forEach(f => data[f.name] = f.value);

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: data,
            success: function(res){
                if(res.success){
                    const newId = data.pf_new_id;
                    const postId = res.data && res.data.post_id ? res.data.post_id : '';
                    window.location.href = `?page=pf-carousel-settings&tab=shortcodes&created=${encodeURIComponent(newId)}&post=${postId}`;
                } else {
                    alert('Error creating carousel: ' + (res.data || 'Unknown error'));
                }
            }
        });
    });

    // ── Copy shortcode to clipboard ──
    $('#pf-shortcodes-table').on('click', '.pf-copy-shortcode', function(e){
        e.preventDefault();
        const input = $(this).siblings('.pf-shortcode-hidden');
        const btn = $(this);

        if (navigator.clipboard) {
            navigator.clipboard.writeText(input.val()).then(function() {
                btn.text('Copied!');
                setTimeout(() => btn.text('Copy'), 1500);
            });
        } else {
            input.show().select();
            input[0].setSelectionRange(0, 99999);
            document.execCommand('copy');
            input.hide();
            btn.text('Copied!');
            setTimeout(() => btn.text('Copy'), 1500);
        }
    });

    // ── Delete carousel (placeholder — no backend behavior yet) ──
    $('#pf-shortcodes-table').on('click', '.pf-delete-carousel', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');
        const carouselId = row.data('id');
        alert('Delete functionality for "' + carouselId + '" is coming soon.');
    });

    // ── Style selector change — fetch dynamic fields via AJAX (config page) ──
    function loadStyleOptions(cardsContainer, targetId, registry) {
        $(cardsContainer).on('change', 'input[type="radio"]', function(){
            const style = $(this).val();
            $.post(ajaxurl, {
                action: 'pf_get_style_options_html',
                style_key: style,
                registry: registry
            }, function(res){
                if(res.success){
                    $(targetId).html(res.data);
                }
            });
        });
    }
    loadStyleOptions('#pf-carousel-style-cards', '#pf-carousel-style-options', 'carousel');
    loadStyleOptions('#pf-nav-style-cards', '#pf-nav-style-options', 'nav');

    // ── Image source toggle (radio buttons) ──
    $('input[name="pf_image_source"]').on('change', function(){
        const val = $(this).val();
        if(val === 'media'){
            $('#pf-media-library-section').show();
            $('#pf-url-section').hide();
        } else {
            $('#pf-media-library-section').hide();
            $('#pf-url-section').show();
        }
    });

    // ── Remove individual image from grid ──
    $('#pf-selected-images').on('click', '.pf-remove-image', function(){
        $(this).closest('.pf-image-card').remove();
        if ($('#pf-selected-images .pf-image-card').length === 0) {
            $('#pf-no-images-msg').show();
        }
    });

    // ── WP Media Library ──
    let pf_media_frame;
    $('#pf-add-images').on('click', function(e){
        e.preventDefault();
        if(pf_media_frame){
            pf_media_frame.open();
            return;
        }

        pf_media_frame = wp.media({
            title: 'Select Carousel Images',
            button: { text: 'Add to Carousel' },
            multiple: true
        });

        pf_media_frame.on('select', function(){
            const selection = pf_media_frame.state().get('selection');
            const grid = $('#pf-selected-images');
            grid.empty();
            $('#pf-no-images-msg').hide();
            selection.map(function(attachment){
                attachment = attachment.toJSON();
                const thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                grid.append(
                    `<div class="pf-image-card" data-id="${attachment.id}">` +
                    `<img src="${thumb}" />` +
                    `<button type="button" class="pf-remove-image" title="Remove image">&times;</button>` +
                    `</div>`
                );
            });
        });

        pf_media_frame.open();
    });

    // ── Save full config form (redirect to shortcodes with success notice) ──
    $('#pf-full-config-form').on('submit', function(e){
        e.preventDefault();
        const post_id = new URLSearchParams(window.location.search).get('post');
        const edit_id = new URLSearchParams(window.location.search).get('id');
        const autoplay = $(this).find('select[name="pf_autoplay"]').val();
        const autoplayDelay = $(this).find('input[name="pf_autoplayDelay"]').val();

        let images = [];
        if($('input[name="pf_image_source"]:checked').val() === 'media'){
            $('#pf-selected-images .pf-image-card').each(function(){
                images.push($(this).data('id'));
            });
        } else {
            $('#pf-image-urls').val().split("\n").forEach(url => {
                if(url.trim()) images.push(url.trim());
            });
        }

        // Collect style selections and their options
        const carousel_style = $('input[name="pf_carousel_style"]:checked').val() || 'default';
        const nav_style = $('input[name="pf_nav_style"]:checked').val() || 'minimal';

        let carousel_style_options = {};
        $('#pf-carousel-style-options input[data-key]').each(function(){
            carousel_style_options[$(this).data('key')] = $(this).val();
        });

        let nav_style_options = {};
        $('#pf-nav-style-options input[data-key]').each(function(){
            nav_style_options[$(this).data('key')] = $(this).val();
        });

        $.post(ajaxurl, {
            action: 'pf_update_carousel_config',
            post_id: post_id,
            edit_id: edit_id,
            autoplay: autoplay,
            autoplayDelay: autoplayDelay,
            images: images,
            carousel_style: carousel_style,
            nav_style: nav_style,
            carousel_style_options: carousel_style_options,
            nav_style_options: nav_style_options
        }, function(res){
            if(res.success){
                window.location.href = `?page=pf-carousel-settings&tab=shortcodes&saved=${encodeURIComponent(edit_id)}`;
            } else {
                alert('Error saving carousel.');
            }
        });
    });

});
