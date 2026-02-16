document.addEventListener('DOMContentLoaded', () => {
    // Listen for all config forms in the Shortcodes tab
    document.querySelectorAll('.pf-carousel-edit-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();

            const postId = form.querySelector('[name="pf_post_id"]').value;
            const editId = form.querySelector('[name="pf_edit_id"]').value;
            const autoplay = form.querySelector('[name="pf_autoplay"]').value;
            const autoplayDelay = form.querySelector('[name="pf_autoplayDelay"]').value;

            // AJAX request
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'pf_update_carousel_config',
                    post_id: postId,
                    edit_id: editId,
                    autoplay,
                    autoplayDelay
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update the table row values
                    const row = document.querySelector(`tr[data-id="${editId}"]`);
                    if (row) {
                        row.querySelector('.col-autoplay').textContent = autoplay;
                        row.querySelector('.col-autoplayDelay').textContent = autoplayDelay;
                    }
                    alert('Carousel config updated!');
                } else {
                    alert('Failed to update config');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error updating config');
            });
        });
    });
});