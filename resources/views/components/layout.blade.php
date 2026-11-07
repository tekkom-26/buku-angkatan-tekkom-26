@props([
    'title' => null,
    'activePage' => 'home',
    'description' => null,
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
