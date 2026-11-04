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

        <section id="data" class="relative py-20 md:py-28 bg-brand-mist/60">
            {{-- Subtle top separator line --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-px bg-gradient-to-r from-transparent via-brand-teal/30 to-transparent"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Section header — Rule 3: custom badge + readable subtitle --}}
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-mint/40 text-brand-deep border border-brand-teal/20 mb-4">Data Angkatan</span>
                    <flux:heading class="text-brand-deep" size="2xl">Statistik & Distribusi</flux:heading>
                    <p class="mt-3 text-sm text-brand-deep/70 leading-relaxed">
                        Gambaran umum komposisi angkatan Teknik Komputer 2026 berdasarkan data terkini.
                    </p>
                </div>

                {{-- Main Stat Cards — Rule 2: white bg + border + shadow --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-10">

                    {{-- Total Mahasiswa --}}
                    <div class="group relative overflow-hidden rounded-xl border border-brand-teal/15 bg-white shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-teal/4 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="p-7 md:p-8 flex items-center gap-5 relative">
                            <div class="shrink-0 w-16 h-16 rounded-2xl bg-brand-teal/10 flex items-center justify-center ring-1 ring-brand-teal/10">
                                <flux:icon icon="users" class="size-7 text-brand-teal" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-widest mb-1">Total Mahasiswa</p>
                                <p class="text-4xl md:text-5xl font-extrabold text-brand-deep tracking-tight">{{ number_format($total_students) }}</p>
                                <p class="text-xs text-brand-teal font-medium mt-1">Aktif terdaftar</p>
                            </div>
                        </div>
                    </div>

                    {{-- Angkatan --}}
                    <div class="group relative overflow-hidden rounded-xl border border-brand-cyan/15 bg-white shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-cyan/4 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="p-7 md:p-8 flex items-center gap-5 relative">
                            <div class="shrink-0 w-16 h-16 rounded-2xl bg-brand-cyan/10 flex items-center justify-center ring-1 ring-brand-cyan/10">
                                <flux:icon icon="academic-cap" class="size-7 text-brand-cyan" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-widest mb-1">Angkatan</p>
                                <p class="text-4xl md:text-5xl font-extrabold text-brand-deep tracking-tight">2026</p>
                                <p class="text-xs text-brand-cyan font-medium mt-1">Teknik Komputer</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Class Distribution Header --}}
                <div class="flex items-center justify-between mb-5">
                    <flux:heading size="lg" class="!text-brand-deep !font-semibold">Distribusi Per Kelas</flux:heading>
                    <span class="text-xs text-brand-deep/50 font-medium">{{ count($per_class) }} Kelas &middot; {{ $total_students }} Mahasiswa</span>
                </div>

                {{-- Class Cards Grid — Rule 2: white cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                    @foreach ($per_class as $index => $class)
                        <div class="group text-center rounded-xl border border-brand-teal/12 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-default">
                            <div class="p-6 md:p-7">
                                {{-- Class letter badge --}}
                                <div class="w-12 h-12 mx-auto mb-4 rounded-xl flex items-center justify-center text-xl font-black {{ $index % 2 === 0 ? 'bg-brand-teal/10 text-brand-teal' : 'bg-brand-cyan/10 text-brand-cyan' }}">
                                    {{ $class['class_name'] }}
                                </div>
                                <p class="text-3xl md:text-4xl font-extrabold text-brand-deep tracking-tight">{{ $class['total'] }}</p>
                                <p class="text-xs text-brand-deep/50 mt-1.5 font-medium uppercase tracking-wider">Mahasiswa</p>
                                {{-- Mini bar visualization --}}
                                <div class="mt-4 h-1.5 rounded-full bg-brand-deep/6 overflow-hidden">
                                    <div
                                        class="h-full rounded-full {{ $index % 2 === 0 ? 'bg-brand-teal' : 'bg-brand-cyan' }}"
                                        style="width: {{ round($class['total'] / $total_students * 100) }}%"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-20 md:py-28 bg-white relative overflow-hidden">
            {{-- Decorative dots pattern --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-mint/40 text-brand-deep border border-brand-teal/20 mb-4">Filosofi Kita</span>
                    <flux:heading class="text-brand-deep" size="2xl">Lima Pilar Ascent</flux:heading>
                    <p class="mt-3 text-sm text-brand-deep/70 leading-relaxed">
                        Nilai-nilai yang menjadi fondasi karakter dan kebersamaan angkatan Teknik Komputer 2026.
                    </p>
                </div>

                {{-- Values Grid — Rule 2: white cards with border + shadow --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
                    @foreach ($values as $value)
                        <div class="group relative overflow-hidden rounded-xl border border-brand-teal/12 bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            {{-- Hover accent bar at top --}}
                            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r {{ $value['color'] === 'teal' ? 'from-brand-teal to-brand-cyan' : 'from-brand-cyan to-brand-mint' }} scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>

                            <div class="p-6 text-center pt-7">
                                {{-- Icon circle --}}
                                <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center mb-5 {{ $value['color'] === 'teal' ? 'bg-brand-teal/8' : 'bg-brand-cyan/8' }} group-hover:scale-110 transition-transform duration-300">
                                    <flux:icon icon="{{ $value['icon'] }}" class="size-6 {{ $value['color'] === 'teal' ? 'text-brand-teal' : 'text-brand-cyan' }}" />
                                </div>

                                <h3 class="font-semibold text-brand-deep text-[15px] mb-2 leading-snug">{{ $value['title'] }}</h3>
                                {{-- Rule 3: more readable description --}}
                                <p class="text-[13px] text-brand-deep/60 leading-relaxed">{{ $value['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <section id="kontak" class="py-20 md:py-28 bg-brand-mist/80 relative overflow-hidden">
            {{-- Background decoration --}}
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-teal/30 to-transparent"></div>
            <div class="absolute -right-40 -top-40 w-[500px] h-[500px] rounded-full bg-brand-teal/6 blur-3xl"></div>
            <div class="absolute -left-40 -bottom-40 w-[400px] h-[400px] rounded-full bg-brand-cyan/6 blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">

                {{-- Section header — Rule 3 --}}
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-teal/15 text-brand-deep border border-brand-teal/25 mb-4">Hubungi Kami</span>
                    <flux:heading class="text-brand-deep" size="2xl">Ada Pertanyaan atau Saran?</flux:heading>
                    <p class="mt-3 text-sm text-brand-deep/70 leading-relaxed">
                        Jangan ragu untuk menghubungi kami. Kami senang mendengar dari Anda.
                    </p>
                </div>

                {{-- Two-column layout --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                    {{-- Left: Contact Info Card — Rule 2: white card --}}
                    <div class="lg:col-span-2">
                        <div class="h-full rounded-xl border border-brand-teal/15 bg-white shadow-sm p-7 md:p-8 space-y-7">
                            <div>
                                <flux:heading size="lg" class="!text-brand-deep !font-semibold mb-1">Informasi Kontak</flux:heading>
                                <p class="text-xs text-brand-deep/50">Temukan kami di berbagai platform berikut.</p>
                            </div>

                            {{-- Address --}}
                            <div class="flex items-start gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-teal/8 flex items-center justify-center group-hover:bg-brand-teal/15 transition-colors">
                                    <flux:icon icon="map-pin" class="size-4 text-brand-teal" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-wider mb-0.5">Alamat</p>
                                    <p class="text-sm text-brand-deep/80 leading-relaxed">{{ $contact['address'] }}</p>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-start gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-teal/8 flex items-center justify-center group-hover:bg-brand-teal/15 transition-colors">
                                    <flux:icon icon="envelope" class="size-4 text-brand-teal" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-wider mb-0.5">Email</p>
                                    <a href="mailto:{{ $contact['email'] }}" class="text-sm text-brand-teal hover:text-brand-deep font-medium transition-colors">{{ $contact['email'] }}</a>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-teal/8 flex items-center justify-center group-hover:bg-brand-teal/15 transition-colors">
                                    <flux:icon icon="phone" class="size-4 text-brand-teal" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-wider mb-0.5">Telepon</p>
                                    <a href="tel:{{ $contact['phone'] }}" class="text-sm text-brand-teal hover:text-brand-deep font-medium transition-colors">{{ $contact['phone'] }}</a>
                                </div>
                            </div>

                            {{-- Instagram --}}
                            <div class="flex items-start gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-brand-teal/8 flex items-center justify-center group-hover:bg-brand-teal/15 transition-colors">
                                    <flux:icon icon="photo" class="size-4 text-brand-teal" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-brand-deep/50 uppercase tracking-wider mb-0.5">Instagram</p>
                                    <a href="https://instagram.com/{{ str_replace('@', '', $contact['instagram']) }}" target="_blank" rel="noopener" class="text-sm text-brand-teal hover:text-brand-deep font-medium transition-colors">{{ $contact['instagram'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Message Form — Rule 2: white card | Rule 4: teal CTA --}}
                    <div class="lg:col-span-3">
                        <div class="h-full rounded-xl border border-brand-teal/15 bg-white shadow-sm p-7 md:p-8">
                            <form action="#" method="POST" class="space-y-5">
                                <div>
                                    <flux:heading size="lg" class="!text-brand-deep !font-semibold mb-1">Kirim Pesan</flux:heading>
                                    <p class="text-xs text-brand-deep/50">Isi formulir di bawah untuk mengirim pesan langsung kepada kami.</p>
                                </div>

                                {{-- Name field --}}
                                <flux:field>
                                    <flux:label class="!text-brand-deep/70 !text-xs font-medium">Nama Lengkap</flux:label>
                                    <flux:input
                                        name="name"
                                        type="text"
                                        placeholder="Masukkan nama kamu..."
                                        class="!rounded-xl !bg-white !border-gray-200 !shadow-sm focus:!shadow-md focus:!border-brand-teal/40 transition-all duration-200"
                                    />
                                </flux:field>

                                {{-- Email field --}}
                                <flux:field>
                                    <flux:label class="!text-brand-deep/70 !text-xs font-medium">Alamat Email</flux:label>
                                    <flux:input
                                        name="email"
                                        type="email"
                                        placeholder="nama@email.com"
                                        class="!rounded-xl !bg-white !border-gray-200 !shadow-sm focus:!shadow-md focus:!border-brand-teal/40 transition-all duration-200"
                                    />
                                </flux:field>

                                {{-- Message textarea --}}
                                <flux:field>
                                    <flux:label class="!text-brand-deep/70 !text-xs font-medium">Pesan</flux:label>
                                    <flux:textarea
                                        name="message"
                                        placeholder="Tulis pesan kamu di sini..."
                                        rows="4"
                                        class="!bg-white !border-gray-200 !shadow-sm focus:!shadow-md focus:!border-brand-teal/40 transition-all duration-200"
                                    ></flux:textarea>
                                </flux:field>

                                {{-- Submit button — Rule 4: brand-teal bg + white text --}}
                                <div class="flex items-center justify-between pt-2">
                                    <p class="text-[11px] text-brand-deep/40">Biasanya membalas dalam 1×24 jam.</p>
                                    <flux:button type="submit" variant="primary" icon-trailing="paper-airplane" class="!text-brand-mist">
                                        Kirim Pesan
                                    </flux:button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <x-footer activePage="home" />

    </body>
</html>
