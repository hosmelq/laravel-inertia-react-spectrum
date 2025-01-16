import './reset.css'
import '@react-spectrum/s2/page.css'
import {StrictMode} from 'react'
import {RouterProvider} from 'react-aria-components'
import ReactDOMServer from 'react-dom/server'
import {createInertiaApp, router} from '@inertiajs/react'
import createServer from '@inertiajs/react/server'
import * as Sentry from '@sentry/react'

Sentry.init({
  dsn: import.meta.env.VITE_SENTRY_DSN,
  integrations: [
    Sentry.browserProfilingIntegration(),
    Sentry.browserTracingIntegration(),
    Sentry.captureConsoleIntegration(),
    Sentry.replayIntegration(),
  ],
  replaysOnErrorSampleRate: 1.0,
  replaysSessionSampleRate: 0.25,
  tracesSampleRate: 0.25,
})

createServer((page) => {
  return createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    resolve: (name) => {
      const routes = import.meta.glob('./routes/**/*.tsx', {eager: true})

      return routes[`./routes/${name}.tsx`]
    },
    setup({App, props}) {
      return (
        <StrictMode>
          <RouterProvider
            navigate={(path, routerOptions) =>
              router.visit(path, routerOptions)
            }
          >
            <App {...props} />
          </RouterProvider>
        </StrictMode>
      )
    },
  })
})
