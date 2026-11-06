<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Direktori Mahasiswa — Ascent Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="bg-brand-mist text-brand-deep antialiased">

        {{-- Navbar --}}
        <x-navbar activePage="unlock" />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">


            <div class="mb-8">
                {{-- Terminal-style subtitle --}}
                <p class="text-xs font-mono text-brand-teal/60 tracking-wider mb-2">
                    &gt; SELECT * FROM tekkom26
                </p>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-brand-deep tracking-tight">
                            Biodata Mahasiswa
                        </h1>
                        <p class="text-sm text-brand-deep/55 mt-1">
                            Direktori lengkap anggota Teknik Komputer 2026 — Ascent
                        </p>
                    </div>

                    {{-- Badge counter --}}
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-semibold bg-brand-mint/40 text-brand-deep border border-brand-teal/20 shrink-0">
                        <flux:icon icon="users" class="size-4 me-1.5 text-brand-teal" />
                        {{ $totalStudents }} Anggota Terdaftar
                    </span>
                </div>
            </div>


            <div class="mb-8">
                <flux:card class="!bg-white !border-brand-teal/15 !p-0 overflow-hidden">
                    <form method="GET" action="{{ route('directory.index') }}">
                        <div class="flex flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-brand-deep/10">

                            {{-- Search input --}}
                            <div class="flex-1 min-w-0 flex items-center px-4 py-3">
                                <flux:icon icon="magnifying-glass" class="size-4 text-brand-deep/40 shrink-0 me-2" />
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Cari nama atau NIM..."
                                    class="w-full border-0 p-0 text-sm text-brand-deep placeholder:text-brand-deep/40 focus:outline-none focus:ring-0 bg-transparent"
                                />
                            </div>

                            {{-- Class dropdown --}}
                            <div class="sm:w-48 flex items-center px-4 py-3">
                                <select
                                    name="class"
                                    class="w-full border-0 p-0 text-sm text-brand-deep bg-transparent focus:outline-none focus:ring-0 cursor-pointer"
                                >
                                    <option value="">Semua Kelas</option>
                                    @foreach ($filters as $filter)
                                        <option value="{{ $filter }}" {{ request('class') === $filter ? 'selected' : '' }}>
                                            Kelas {{ $filter }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Submit button --}}
                            <div class="shrink-0 flex">
                                <flux:button
                                    type="submit"
                                    variant="primary"
                                    icon="funnel"
                                    class="!rounded-none !h-full !px-6 !text-brand-mist"
                                >
                                    Filter
                                </flux:button>
                            </div>
                        </div>
                    </form>

                    {{-- Active filters / reset --}}
                    @if(request('search') || request('class'))
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-brand-teal/5 border-t border-brand-deep/10">
                            <span class="text-xs text-brand-deep/50">Filter aktif:</span>
                            @if(request('search'))
                                <span class="inline-flex items-center gap-1 text-xs bg-white border border-brand-deep/10 rounded-full px-2.5 py-1 text-brand-deep">
                                    "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('class'))
                                <span class="inline-flex items-center gap-1 text-xs bg-white border border-brand-deep/10 rounded-full px-2.5 py-1 text-brand-deep">
                                    Kelas {{ request('class') }}
                                </span>
                            @endif
                            <a href="{{ route('directory.index') }}" class="ms-auto text-xs text-brand-deep/50 hover:text-brand-deep underline underline-offset-2">
                                Reset semua
                            </a>
                        </div>
                    @endif
                </flux:card>
            </div>

            <div class="flex items-center justify-between mb-5">
                <p class="text-xs text-brand-deep/45">
                    Menampilkan {{ $students->firstItem() }}–{{ $students->lastItem() }} dari {{ $students->total() }} mahasiswa
                </p>
                @if(request('search') || request('class'))
                    <p class="text-xs text-brand-teal/70 font-medium">
                        Filter aktif:
                        @if(request('search')) pencarian "<em>{{ request('search') }}</em>" @endif
                        @if(request('class')) · Kelas {{ request('class') }} @endif
                    </p>
                @endif
            </div>


            @if($students->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-5">

                    @foreach ($students as $student)
                        <a href="/students/{{ $student['nim'] }}"
                           class="group block relative rounded-xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-brand-teal/40"
                        >

                            {{-- Avatar image --}}
                            <div class="aspect-[3/4] w-full overflow-hidden bg-brand-deep/5">
                                <img
                                    src="{{ $student['avatar_url'] }}"
                                    alt="Foto {{ $student['nickname'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student['nickname']) }}&background=176B70&color=fff&size=400&font-size=0.35&bold=true'"
                                />
                            </div>

                            {{-- Gradient overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-deep/90 via-brand-deep/40 to-transparent"></div>

                            {{-- Card content (overlaid) --}}
                            <div class="absolute inset-0 flex flex-col justify-end p-3.5 sm:p-4">

                                {{-- Top row: NIM + Class badge --}}
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="text-[10px] sm:text-[11px] font-mono text-white/50 tracking-wider">
                                        {{ $student['nim'] }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-brand-cyan/25 text-white/90 border border-white/15 backdrop-blur-sm">
                                        Kelas {{ $student['class_name'] }}
                                    </span>
                                </div>

                                {{-- Name --}}
                                <h3 class="text-base sm:text-lg font-bold text-white leading-tight truncate">
                                    {{ $student['nickname'] }}
                                </h3>

                                {{-- Origin city --}}
                                <p class="text-xs text-white/65 mt-0.5 flex items-center gap-1">
                                    <flux:icon icon="map-pin" class="size-3" />
                                    {{ $student['origin_city'] }}
                                </p>
                            </div>
                        </a>
                    @endforeach

                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-20">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-deep/5 flex items-center justify-center">
                        <flux:icon icon="face-smile" class="size-7 text-brand-deep/25" />
                    </div>
                    <h3 class="text-lg font-semibold text-brand-deep/60">Tidak ada hasil ditemukan</h3>
                    <p class="text-sm text-brand-deep/40 mt-1">Coba ubah kata kunci atau filter yang digunakan.</p>
                    <a href="{{ route('directory.index') }}" class="inline-flex items-center mt-4 text-sm font-medium text-brand-teal hover:text-brand-deep transition-colors">
                        <flux:icon icon="arrow-path" class="size-4 me-1.5" />
                        Reset semua filter
                    </a>
                </div>
            @endif


            @if($students->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $students->withQueryString()->links('pagination::tailwind') }}
                </div>
            @endif


        </main>


        {{-- Footer --}}
        <x-footer activePage="unlock" />

        @livewireScripts
    </body>
</html>
