<?php

namespace App\Filament\Pages;

use App\Services\ContentImageService;
use App\Services\WebContentService;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use InvalidArgumentException;
use RuntimeException;
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

                                        Group::make([
                                            ImageEntry::make('hero_mascot_preview')
                                                ->label('Pratinjau Gambar Maskot Saat Ini')
                                                ->imageHeight(140)
                                                ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('hero.mascot_placeholder')))
                                                ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('hero.mascot_placeholder')))),

                                            self::imageUpload('hero.mascot_upload', 'Unggah Gambar Maskot'),

                                            TextInput::make('hero.mascot_placeholder')
                                                ->label('Path Asset / URL R2 Maskot')
                                                ->helperText('Terisi otomatis setelah unggah gambar ke Cloudflare R2, atau isi path/URL manual (mis. /assets/Placeholder.png).')
                                                ->maxLength(500),
                                        ])->columnSpanFull(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: GALERI MOMEN (CAROUSEL DEPAN)
                        // ==========================================
                        Tab::make('Galeri Momen (Depan)')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                Section::make('Foto Carousel Halaman Depan')
                                    ->description('Kelola slide foto dokumentasi & momen yang tampil di bagian atas beranda (/).')
                                    ->schema([
                                        Repeater::make('moments')
                                            ->label('Daftar Slide Foto Momen')
                                            ->addActionLabel('Tambah Slide Foto')
                                            ->collapsible()
                                            ->reorderable()
                                            ->itemLabel(fn (array $state): ?string => trim(($state['categoryLabel'] ?? '').' — '.($state['title'] ?? 'Slide Foto Baru'), ' —'))
                                            ->schema([
                                                TextInput::make('categoryLabel')
                                                    ->label('Label Kategori (mis. Prestasi, Kebersamaan)')
                                                    ->required()
                                                    ->maxLength(60),

                                                TextInput::make('meta')
                                                    ->label('Tanggal / Info Singkat (mis. 24 Okt 2026)')
                                                    ->required()
                                                    ->maxLength(60),

                                                TextInput::make('title')
                                                    ->label('Judul Momen')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Singkat')
                                                    ->rows(2)
                                                    ->required()
                                                    ->columnSpanFull(),

                                                Group::make([
                                                    ImageEntry::make('image_preview')
                                                        ->label('Pratinjau Foto Saat Ini')
                                                        ->imageHeight(160)
                                                        ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('image')))
                                                        ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('image')))),

                                                    self::imageUpload('image_upload', 'Unggah Foto Momen'),

                                                    TextInput::make('image')
                                                        ->label('Path Asset / URL R2')
                                                        ->helperText('Terisi otomatis setelah unggah foto ke Cloudflare R2, atau tempel URL/path aset manual.')
                                                        ->maxLength(500),
                                                ])->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: DECODING SECTION
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

                                        Group::make([
                                            ImageEntry::make('decoding_logo_preview')
                                                ->label('Pratinjau Gambar Logo Saat Ini')
                                                ->imageHeight(140)
                                                ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('decoding.logo_placeholder')))
                                                ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('decoding.logo_placeholder')))),

                                            self::imageUpload('decoding.logo_upload', 'Unggah Gambar Logo'),

                                            TextInput::make('decoding.logo_placeholder')
                                                ->label('Path Asset / URL R2 Logo')
                                                ->helperText('Terisi otomatis setelah unggah gambar ke Cloudflare R2, atau isi path/URL manual (mis. /assets/Placeholder.png).')
                                                ->maxLength(500),
                                        ])->columnSpanFull(),
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
    public function save(WebContentService $contentService, ContentImageService $imageService): void
    {
        $state = $this->form->getState();

        try {
            $state = $imageService->processLandingFormData($state, $contentService->getLandingContent());
        } catch (InvalidArgumentException|RuntimeException $exception) {
            Notification::make()
                ->danger()
                ->title('Upload gambar gagal')
                ->body($exception->getMessage())
                ->persistent()
                ->send();

            return;
        }

        $contentService->updateLandingContent($state);
        $this->form->fill($contentService->getLandingContent());

        Notification::make()
            ->title('Konten Beranda Berhasil Diperbarui')
            ->success()
            ->send();
    }

    /**
     * Field upload virtual untuk gambar beranda (disinggahkan di tmp-uploads lalu diunggah ke R2).
     */
    protected static function imageUpload(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('local')
            ->directory('tmp-uploads')
            ->visibility('private')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(5120)
            ->helperText('JPG / PNG / WEBP, maks 5 MB. Kosongkan jika tidak ingin mengganti gambar saat ini.');
    }
}
