import { EventBus } from './EventBus'
import { ModuleManager } from './ModuleManager'

export class PerformanceCarousel {
  constructor(root, config = {}) {
    this.root = root
    this.track = root.querySelector('.pf-track')
    this.slides = Array.from(root.querySelectorAll('.pf-slide'))

    this.config = {
      slidesToShow: 1,
      loop: false,
      speed: 400,
      breakpoints: {},
      modules: [],
      ...config
    }

    this.index = 0
    this.events = new EventBus()
    this.modules = new ModuleManager(this)

    // Prev/Next btn
    this.prevButton = root.querySelector('.pf-prev')
    this.nextButton = root.querySelector('.pf-next')

    this.setup()
    this.attachModules()
  }

  setup() {
    this.updateSlidesToShow()
    this.updateDimensions()
    this.attachControls()
    window.addEventListener('resize', () => {
      this.updateSlidesToShow()
      this.updateDimensions()
    })
  }

  updateSlidesToShow() {
    const width = window.innerWidth
    const breakpoints = this.config.breakpoints

    let slidesToShow = this.config.slidesToShow

    Object.keys(breakpoints).forEach(bp => {
      if (width <= bp) {
        slidesToShow = breakpoints[bp].slidesToShow
      }
    })

    this.currentSlidesToShow = slidesToShow
  }

  updateDimensions() {
    const slideWidth = this.root.clientWidth / this.currentSlidesToShow
    this.slides.forEach(slide => {
      slide.style.width = `${slideWidth}px`
    })
    this.moveTo(this.index, false)
  }

  moveTo(index, animate = true) {
    if (!this.config.loop) {
      index = Math.max(0, Math.min(index, this.slides.length - this.currentSlidesToShow))
    }

    this.index = index

    const offset = -(index * this.slides[0].clientWidth)

    this.track.style.transition = animate
      ? `transform ${this.config.speed}ms ease`
      : 'none'

    this.track.style.transform = `translate3d(${offset}px, 0, 0)`

    this.events.emit('slideChange', { index })
  }

  next() {
    this.moveTo(this.index + 1)
  }

  prev() {
    this.moveTo(this.index - 1)
  }

  attachModules() {
    this.config.modules.forEach(Module => {
      this.modules.use(Module)
    })
  }

  attachControls() {
    if (this.nextButton) {
      this.nextButton.addEventListener('click', () => {
        this.next()
      })
    }

    if (this.prevButton) {
      this.prevButton.addEventListener('click', () => {
        this.prev()
      })
    }
  }
}