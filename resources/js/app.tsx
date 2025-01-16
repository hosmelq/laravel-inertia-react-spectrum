import './reset.css'
import '@react-spectrum/s2/page.css'
import {StrictMode} from 'react'
import {RouterProvider} from 'react-aria-components'
import {createRoot, hydrateRoot} from 'react-dom/client'
import type {VisitOptions} from '@inertiajs/core'
import {createInertiaApp, router} from '@inertiajs/react'
import * as Sentry from '@sentry/react'

declare module 'react-aria-components' {
  interface RouterConfig {
    routerOptions: VisitOptions
  }
}

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

void createInertiaApp({
  progress: {
    color: '#000',
  },
  resolve: (name) => {
    const routes = import.meta.glob('./routes/**/*.tsx', {eager: true})

    return routes[`./routes/${name}.tsx`]
  },
  setup({App, el, props}) {
    const app = (
      <StrictMode>
        <RouterProvider
          navigate={(path, routerOptions) => router.visit(path, routerOptions)}
        >
          <App {...props} />
        </RouterProvider>
      </StrictMode>
    )

    if (import.meta.env.SSR) {
      hydrateRoot(el, app)
    } else {
      createRoot(el).render(app)
    }
  },
})
