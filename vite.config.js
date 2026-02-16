import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    outDir: 'assets/js',
    emptyOutDir: false,
    rollupOptions: {
      input: 'src/index.js',
      output: {
        entryFileNames: 'carousel.js',
        format: 'iife'
      }
    }
  }
})