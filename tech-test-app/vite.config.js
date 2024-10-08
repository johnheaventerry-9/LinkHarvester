import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: 'public/build',
    manifest: true,
    rollupOptions: {
      input: '/resources/js/app.js',
    },
  },
});
