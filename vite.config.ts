import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
   plugins: [
      laravel({
         input: ['resources/css/app.css', 'resources/js/app.tsx'],
         ssr: 'resources/js/ssr.tsx',
         refresh: true,
      }),
      react(),
      tailwindcss(),
   ],
   esbuild: {
      jsx: 'automatic',
   },
   resolve: {
      alias: {
         'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
      },
   },
   build: {
      rollupOptions: {
         external: [
            '@tavus/cvi-ui/dist/style.css',
         ],
         output: {
            manualChunks(id) {
               if (!id.includes('node_modules')) {
                  // App pages stay as individual lazy chunks (import.meta.glob).
                  return;
               }

               // Peel the heaviest libraries into their own cacheable chunks so
               // they are only fetched on the routes that actually use them and
               // don't bloat the initial payload.
               if (id.includes('@tavus')) return 'tavus';
               if (id.includes('@zoom')) return 'zoom';
               if (id.includes('codemirror')) return 'codemirror';
               if (id.includes('recharts') || id.includes('d3-')) return 'charts';
               if (id.includes('plyr')) return 'player';
               if (id.includes('jspdf') || id.includes('puppeteer')) return 'pdf';
               if (id.includes('@radix-ui') || id.includes('lucide-react')) return 'ui';

               // Core framework — shared by every page, loaded up front.
               if (id.includes('react') || id.includes('@inertiajs') || id.includes('scheduler')) {
                  return 'vendor';
               }

               return 'libs';
            },
         },
      },
      commonjsOptions: {
         include: [/node_modules/],
         transformMixedEsModules: true,
      },
      chunkSizeWarningLimit: 3000,
   },
   optimizeDeps: {
      include: ['@tavus/cvi-ui'],
      exclude: [],
   },
});
