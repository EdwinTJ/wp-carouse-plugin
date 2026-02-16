/**
 * Carousel Style JS: Elevated Shadow
 *
 * Optional JS enhancements for the Elevated Shadow style.
 * This file is only loaded when a carousel uses this style.
 *
 * @param {NodeList} carousels  All .pf-style-elevated elements on the page.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var carousels = document.querySelectorAll('.pf-style-elevated');
        if (carousels.length) {
            console.log('JS loaded for style: Elevated Shadow (' + carousels.length + ' instance(s))');
        }
    });
})();
