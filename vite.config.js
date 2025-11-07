import { defineConfig } from 'vite';
import { resolve } from 'path';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

const isProduction = process.env.NODE_ENV === 'production';

export default defineConfig({
  base: isProduction ? '/wp-content/themes/Ruined/dist/' : '/',
  publicDir: 'public',

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
