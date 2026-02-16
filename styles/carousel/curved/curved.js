/**
 * Carousel Style JS: Curved
 * Dynamically applies 3D rotation based on horizontal position.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var carousels = document.querySelectorAll('.pf-style-curved');
        if (!carousels.length) return;

        function updateCurvature(carousel) {
            var track = carousel.querySelector('.pf-track');
            var slides = carousel.querySelectorAll('.pf-slide');
            var carouselRect = carousel.getBoundingClientRect();
            var centerX = carouselRect.left + (carouselRect.width / 2);
            
            var intensity = parseFloat(getComputedStyle(carousel).getPropertyValue('--pf-curvature')) || 15;

            slides.forEach(function (slide) {
                var slideRect = slide.getBoundingClientRect();
                var slideCenter = slideRect.left + (slideRect.width / 2);
                
                // Calculate distance from center (-1 to 1)
                var distanceFromCenter = (slideCenter - centerX) / (carouselRect.width / 2);
                
                // Apply rotation and slight Z translation for depth
                var rotateY = distanceFromCenter * intensity;
                var translateZ = Math.abs(distanceFromCenter) * -50; // Pulls edges back
                
                slide.style.transform = 'rotateY(' + rotateY + 'deg) translateZ(' + translateZ + 'px)';
            });
        }

        carousels.forEach(function (carousel) {
            // Update on scroll/resize and initial load
            carousel.addEventListener('scroll', function() { updateCurvature(carousel); });
            window.addEventListener('resize', function() { updateCurvature(carousel); });
            updateCurvature(carousel);
        });
    });
})();