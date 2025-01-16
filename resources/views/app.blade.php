<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', Config::string('app.locale')) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ url('favicon.ico') }}" sizes="any" />

        <meta name="theme-color" content="#ffffff" />

        <link rel="preconnect" href="https://rsms.me/">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        @viteReactRefresh
        @vite('resources/js/app.tsx')

        @routes
        @inertiaHead
    </head>

    <body>
        @inertia
    </body>
</html>
