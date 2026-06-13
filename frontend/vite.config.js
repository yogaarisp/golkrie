import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss()
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    rollupOptions: {
      output: {
        // Pisahkan vendor chunks supaya cache browser lebih efektif
        manualChunks: {
          'vue-vendor': ['vue', 'vue-router'],
          'axios': ['axios'],
        }
      }
    },
    // Kompres lebih agresif
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,  // Hapus console.log di production
        drop_debugger: true,
      }
    },
    // Warn kalau chunk > 500KB
    chunkSizeWarningLimit: 500,
  },
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      }
    }
  }
})
