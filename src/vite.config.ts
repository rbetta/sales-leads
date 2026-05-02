import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import vueDevTools from 'vite-plugin-vue-devtools';

// https://vite.dev/config/
export default defineConfig({
  
  // The source directory from which static assets will be copied into the
  // root of the output directory.
  publicDir: 'resources/static',
  
  plugins: [
    vue(),
    vueJsx(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@':				fileURLToPath(new URL('./resources/src',			import.meta.url)),
	  '@components':	fileURLToPath(new URL('./resources/src/components',	import.meta.url)),
	  '@classes':		fileURLToPath(new URL('./resources/src/classes',	import.meta.url)),
	  '@typedefs':		fileURLToPath(new URL('./resources/src/typedefs',	import.meta.url))
    },
  },
  build: {
    
	// The output directory for transpiled JavaScript results.
	// This is relative to the project root.
    outDir: 'public/dist',
    
	// Whether to clear the output directory before every build.
    emptyOutDir: true,
	
    rollupOptions: {
      output: {
        entryFileNames: 'main.js',
      },
	},
	
  }
});
