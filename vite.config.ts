import laravel from 'laravel-vite-plugin'
import macros from 'unplugin-parcel-macros'
import {defineConfig} from 'vite'
import checker from 'vite-plugin-checker'
import manifestSRI from 'vite-plugin-manifest-sri'
import tsconfigPaths from 'vite-tsconfig-paths'
import {sentryVitePlugin} from '@sentry/vite-plugin'
import react from '@vitejs/plugin-react'

export default defineConfig(() => {
  return {
    build: {
      cssMinify: 'lightningcss',
      rollupOptions: {
        output: {
          manualChunks(id) {
            if (
              /@react-spectrum\/s2\/.*\.css$/.test(id) ||
              /macro-(.*)\.css$/.test(id)
            ) {
              return 's2-styles'
            }
          },
        },
      },
      sourcemap: 'hidden',
    },
    plugins: [
      checker({
        typescript: true,
      }),
      laravel({
        input: ['resources/js/app.tsx'],
        ssr: 'resources/js/ssr.tsx',
      }),
      macros.vite(),
      manifestSRI(),
      react(),
      tsconfigPaths(),
      sentryVitePlugin({
        authToken: process.env.SENTRY_AUTH_TOKEN,
        org: '',
        project: '',
        release: {
          name: process.env.SOURCE_COMMIT,
        },
      }),
    ],
  }
})
