/**
 * Carousel Style JS: Frosted Glass
 *
 * Optional JS enhancements for the Frosted Glass style.
 * This file is only loaded when a carousel uses this style.
 *
 * @param {NodeList} carousels  All .pf-style-frosted elements on the page.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var carousels = document.querySelectorAll('.pf-style-frosted');
        if (carousels.length) {
            console.log('JS loaded for style: Frosted Glass (' + carousels.length + ' instance(s))');
        }
    });
})();
