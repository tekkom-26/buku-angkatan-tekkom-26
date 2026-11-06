@props([
    'title' => 'Ascent — Teknik Komputer 2026',
    'activePage' => 'home',
    'description' => 'Website Resmi Angkatan Teknik Komputer 2026 — Universitas Diponegoro.',
    'withNavbar' => true,
    'withFooter' => true,
])

<x-layouts.app
    :title="$title"
    :activePage="$activePage"
    :description="$description"
    :withNavbar="$withNavbar"
    :withFooter="$withFooter"
    {{ $attributes }}
>
    {{ $slot }}
</x-layouts.app>
