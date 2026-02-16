export default class Autoplay {
  constructor(carousel) {
    this.carousel = carousel
    this.interval = null
    this.delay = carousel.config.autoplayDelay || 3000

    this.start()
    this.attachHoverPause()
  }

  start() {
    this.stop()

    this.interval = setInterval(() => {
      this.carousel.next()
    }, this.delay)
  }

  stop() {
    if (this.interval) {
      clearInterval(this.interval)
      this.interval = null
    }
  }

  attachHoverPause() {
    this.carousel.root.addEventListener('mouseenter', () => {
      this.stop()
    })

    this.carousel.root.addEventListener('mouseleave', () => {
      this.start()
    })
  }
}