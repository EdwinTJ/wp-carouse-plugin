export class ModuleManager {
  constructor(carousel) {
    this.carousel = carousel
  }

  use(Module) {
    new Module(this.carousel)
  }
}