jQuery(document).ready(($) => {

    // Inline Edit
    $('#pf-shortcodes-table').on('click', '.pf-edit-inline', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');

        // Add editing class for highlight
        row.addClass('editing');

        // Replace cells with inputs/selects
        const autoplayVal = row.find('.col-autoplay').text();
        const delayVal = row.find('.col-autoplayDelay').text();

        row.find('.col-autoplay').html(`<select><option value="true" ${autoplayVal==='true'?'selected':''}>True</option><option value="false" ${autoplayVal==='false'?'selected':''}>False</option></select>`);
        row.find('.col-autoplayDelay').html(`<input type="number" value="${delayVal}" />`);

        // Toggle action buttons
        row.find('.pf-edit-inline').hide();
        row.find('.pf-save-inline, .pf-cancel-inline').show();
    });

    // Cancel inline edit
    $('#pf-shortcodes-table').on('click', '.pf-cancel-inline', function(e){
        e.preventDefault();
        const row = $(this).closest('tr');

        // Remove editing class
        row.removeClass('editing');

        // Reset cells to original values
        const autoplayVal = row.find('.col-autoplay select').val();
        const delayVal = row.find('.col-autoplayDelay input').val();

        row.find('.col-autoplay').text(autoplayVal);
        row.find('.col-autoplayDelay').text(delayVal);

        // Toggle action buttons
        row.find('.pf-edit-inline').show();
        row.find('.pf-save-inline, .pf-cancel-inline').hide();
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
                // Update table cells with new values
                row.find('.col-autoplay').text(autoplay);
                row.find('.col-autoplayDelay').text(autoplayDelay);

                // Remove editing highlight
                row.removeClass('editing');

                // Toggle action buttons
                row.find('.pf-edit-inline').show();
                row.find('.pf-save-inline, .pf-cancel-inline').hide();
            } else {
                alert('Error updating carousel.');
            }
        });
    });

    // Create New Carousel
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
                    alert('Carousel created!');
                    location.reload(); // reload to show new row in Shortcodes tab
                } else {
                    alert('Error creating carousel: ' + res.data);
                }
            }
        });
    });

    // Copy shortcode to clipboard
    $('#pf-shortcodes-table').on('click', '.pf-copy-shortcode', function(e){
        e.preventDefault();
        const input = $(this).siblings('input');
        input.select();
        input[0].setSelectionRange(0, 99999); // for mobile

        try {
            document.execCommand('copy');
            alert('Shortcode copied: ' + input.val());
        } catch (err) {
            alert('Failed to copy shortcode');
        }
    });
    
    // Style selector change — fetch dynamic fields via AJAX
    function loadStyleOptions(selectId, targetId, registry) {
        $(selectId).on('change', function(){
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
    loadStyleOptions('#pf-carousel-style', '#pf-carousel-style-options', 'carousel');
    loadStyleOptions('#pf-nav-style', '#pf-nav-style-options', 'nav');

    // Media Library toggle
    $('#pf-image-source').on('change', function(){
        const val = $(this).val();
        if(val === 'media'){
            $('#pf-media-library-section').show();
            $('#pf-url-section').hide();
        } else {
            $('#pf-media-library-section').hide();
            $('#pf-url-section').show();
        }
    });

    // WP Media Library
    let pf_media_frame;
    $('#pf-add-images').on('click', function(e){
        e.preventDefault();
        if(pf_media_frame){
            pf_media_frame.open();
            return;
        }

        pf_media_frame = wp.media({
            title: 'Select Images',
            button: { text: 'Add to Carousel' },
            multiple: true
        });

        pf_media_frame.on('select', function(){
            const selection = pf_media_frame.state().get('selection');
            const ul = $('#pf-selected-images');
            ul.empty();
            selection.map(function(attachment){
                attachment = attachment.toJSON();
                ul.append(`<li data-id="${attachment.id}"><img src="${attachment.url}" width="100" /> ${attachment.url}</li>`);
            });
        });

        pf_media_frame.open();
    });

    // Save full config form
    $('#pf-full-config-form').on('submit', function(e){
        e.preventDefault();
        const post_id = new URLSearchParams(window.location.search).get('post');
        const edit_id = new URLSearchParams(window.location.search).get('id');
        const autoplay = $(this).find('select[name="pf_autoplay"]').val();
        const autoplayDelay = $(this).find('input[name="pf_autoplayDelay"]').val();

        let images = [];
        if($('#pf-image-source').val() === 'media'){
            $('#pf-selected-images li').each(function(){
                images.push($(this).data('id'));
            });
        } else {
            $('#pf-image-urls').val().split("\n").forEach(url => {
                if(url.trim()) images.push(url.trim());
            });
        }

        // Collect style selections and their options
        const carousel_style = $('#pf-carousel-style').val() || 'default';
        const nav_style = $('#pf-nav-style').val() || 'minimal';

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
                alert('Carousel saved!');
                location.reload();
            } else {
                alert('Error saving carousel.');
            }
        });
    });

});