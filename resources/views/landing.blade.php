<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ascent — Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-surface-canvas text-primary-900 antialiased">

        <x-navbar activePage="home" />

        {{-- Section 1: Hero Asymmetric Bento Grid (Issue #18) --}}
        <x-landing.hero />


        <x-footer activePage="home" />

    </body>
</html>
