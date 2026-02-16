import { PerformanceCarousel } from './core/Carousel'
import Autoplay from './modules/autoplay'


document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.pf-carousel').forEach(root => {

    // Read config from data attribute
    let config = {}

    const rawConfig = root.getAttribute('data-config')
    if (rawConfig) {
      try {
        config = JSON.parse(rawConfig)
      } catch (e) {
        console.warn('Invalid JSON in data-config')
      }
    }

    config.modules = config.modules || []
    if (config.autoplay) {
      config.modules.push(Autoplay)
    }
    new PerformanceCarousel(root, config)
  })
})