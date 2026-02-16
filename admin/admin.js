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

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: JSON.stringify({
                action: 'pf_update_carousel_config',
                post_id,
                edit_id,
                autoplay,
                autoplayDelay
            }),
            contentType: 'application/json',
            success: function(res){
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

});