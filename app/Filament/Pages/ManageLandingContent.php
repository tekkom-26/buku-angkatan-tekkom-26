<?php

namespace App\Filament\Pages;

use App\Services\WebContentService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Halaman admin Filament untuk mengelola konten dinamis Halaman Beranda (Landing Page).
 */
class ManageLandingContent extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Kelola Konten Web';

    protected static ?string $navigationLabel = 'Konten Beranda';

    protected static ?string $title = 'Pengelolaan Konten Beranda';

    protected string $view = 'filament.pages.manage-landing-content';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(WebContentService $contentService): void
    {
        $this->form->fill($contentService->getLandingContent());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('LandingContentTabs')
                    ->tabs([
                        // ==========================================
                        // TAB 1: HERO SECTION
                        // ==========================================
                        Tab::make('Hero Section')
                            ->icon(Heroicon::OutlinedSparkles)
                            ->schema([
                                Section::make('Teks & Narasi Utama')
                                    ->description('Konten kartu hero asimetris bagian kiri')
                                    ->schema([
                                        TextInput::make('hero.title')
                                            ->label('Judul Utama (Headline)')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('hero.motto')
                                            ->label('Motto / Slogan Angkatan')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('hero.narrative')
                                            ->label('Narasi Pengantar')
                                            ->rows(4)
                                            ->required(),
                                    ]),

                                Section::make('Aksi & Media')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('hero.directory_url')
                                            ->label('URL Tombol Direktori')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('hero.identity_url')
                                            ->label('URL Tombol Filosofi')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('hero.mascot_placeholder')
                                            ->label('Path Asset Maskot Chibi')
                                            ->helperText('Contoh: /assets/Placeholder.png')
                                            ->columnSpanFull()
                                            ->maxLength(255),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: DECODING SECTION
                        // ==========================================
                        Tab::make('Decoding Section')
                            ->icon(Heroicon::OutlinedCube)
                            ->schema([
                                Section::make('Identitas Typografi & Logo')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('decoding.wordmark')
                                            ->label('Display Wordmark')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('decoding.sub_label')
                                            ->label('Sub-label Monospace')
                                            ->maxLength(255),

                                        TextInput::make('decoding.logo_placeholder')
                                            ->label('Path Asset Logo')
                                            ->helperText('Contoh: /assets/Placeholder.png')
                                            ->columnSpanFull()
                                            ->maxLength(255),
                                    ]),

                                Section::make('Movement 01')
                                    ->schema([
                                        TextInput::make('decoding.movement_one_title')
                                            ->label('Tag Judul (Monospace)')
                                            ->maxLength(255),

                                        TextInput::make('decoding.movement_one_heading')
                                            ->label('Heading Movement 01')
                                            ->maxLength(255),

                                        Textarea::make('decoding.movement_one_body')
                                            ->label('Teks Narasi Movement 01')
                                            ->rows(3),
                                    ]),

                                Section::make('Movement 02')
                                    ->schema([
                                        TextInput::make('decoding.movement_two_title')
                                            ->label('Tag Judul (Monospace)')
                                            ->maxLength(255),

                                        TextInput::make('decoding.movement_two_heading')
                                            ->label('Heading Movement 02')
                                            ->maxLength(255),

                                        Textarea::make('decoding.movement_two_body')
                                            ->label('Teks Narasi Movement 02')
                                            ->rows(3),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: DIRECTORY BANNER
                        // ==========================================
                        Tab::make('Directory Banner')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                Section::make('Banner Penemuan Direktori')
                                    ->schema([
                                        TextInput::make('directory_banner.headline')
                                            ->label('Headline Banner')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('directory_banner.description')
                                            ->label('Deskripsi Singkat')
                                            ->rows(3)
                                            ->required(),

                                        TextInput::make('directory_banner.cta_label')
                                            ->label('Label Tombol CTA')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('directory_banner.directory_url')
                                            ->label('Target URL Direktori')
                                            ->maxLength(255),

                                        TextInput::make('directory_banner.total_students')
                                            ->label('Badge Telemetri Mahasiswa (e.g. 100+)')
                                            ->maxLength(50),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * Simpan pembaruan konten beranda ke database dan bersihkan cache.
     */
    public function save(WebContentService $contentService): void
    {
        $state = $this->form->getState();

        $contentService->updateLandingContent($state);

        Notification::make()
            ->title('Konten Beranda Berhasil Diperbarui')
            ->success()
            ->send();
    }
}
