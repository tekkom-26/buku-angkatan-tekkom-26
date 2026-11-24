<x-layouts.app activePage="directory" :title="'Direktori Mahasiswa — ' . $cohortName">
    <main
        id="main-content"
        class="relative grow w-full bg-ambient-hero py-8 sm:py-12 lg:py-16 overflow-hidden"
    >
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:student-directory />
        </div>
    </main>
</x-layouts.app>
