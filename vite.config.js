import { defineConfig } from 'vite';
import { resolve } from 'path';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

const isProduction = process.env.NODE_ENV === 'production';

export default defineConfig({
  base: isProduction ? '/wp-content/themes/Ruined/dist/' : '/',
  publicDir: 'public',
  
  // Configure Vite to handle ES modules
  esbuild: {
    target: 'esnext',
    supported: { 
      'dynamic-import': true,
      'import-meta': true
    }
  },
  
  optimizeDeps: {
    include: [
      'gsap',
      'gsap/ScrollTrigger',
      'gsap/ScrollToPlugin',
      'alpinejs',
      'splitting'
    ],
    exclude: ['swiper'],
    esbuildOptions: {
      target: 'esnext',
      supported: {
        'dynamic-import': true,
        'import-meta': true
      }
    }
  },

  plugins: [
    {
      name: 'php-reload',
      handleHotUpdate({ file, server }) {
        if (file.endsWith('.php')) {
          server.ws.send({ type: 'full-reload', path: '*' });
        }
      }
    }
  ],

  css: {
    postcss: {
      plugins: [
        tailwindcss(),
        autoprefixer(),
      ],
    },
    preprocessorOptions: {
      scss: {
        includePaths: [resolve(__dirname, 'src/scss')],
      }
    }
  },

  build: {
    manifest: true,
    outDir: 'dist',
    emptyOutDir: true,
    sourcemap: !isProduction,
    minify: isProduction ? 'esbuild' : false,
    rollupOptions: {
      input: {
        'main': resolve(__dirname, 'src/js/main.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        manualChunks: {
          gsap: ['gsap'],
          swiper: ['swiper']
        },
        assetFileNames: (assetInfo) => {
          const ext = assetInfo.name.split('.').pop();
          if (ext === 'css') {
            return 'css/[name].css';
          }
          return 'assets/[name][extname]';
        },
      },
    },
  },

  server: {
    cors: true,
    strictPort: true,
    port: 5173,
  },
});
