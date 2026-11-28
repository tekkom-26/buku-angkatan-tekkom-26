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
 * Halaman admin Filament untuk mengelola konten dinamis Halaman Filosofi & Identitas Angkatan (/identity).
 */
class ManageIdentityContent extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Kelola Konten Web';

    protected static ?string $navigationLabel = 'Konten Identitas';

    protected static ?string $title = 'Pengelolaan Konten Filosofi & Identitas';

    protected string $view = 'filament.pages.manage-identity-content';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(WebContentService $contentService): void
    {
        $this->form->fill($this->prepareFormData($contentService));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('IdentityContentTabs')
                    ->tabs([
                        // ==========================================
                        // TAB 1: HEADER BANNER
                        // ==========================================
                        Tab::make('Header Banner')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                Section::make('Header & Banner 21:9')
                                    ->description('Judul utama dan path aset foto kolaboratif angkatan')
                                    ->schema([
                                        TextInput::make('header_banner.title')
                                            ->label('Judul Halaman')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('header_banner.subhead')
                                            ->label('Deskripsi Subhead')
                                            ->rows(3)
                                            ->required(),

                                        Group::make([
                                            ImageEntry::make('header_banner_preview')
                                                ->label('Pratinjau Banner Saat Ini')
                                                ->imageHeight(140)
                                                ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('header_banner.banner_placeholder'), ignorePlaceholder: true))
                                                ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('header_banner.banner_placeholder'), ignorePlaceholder: true))),

                                            self::imageUpload(
                                                'header_banner.banner_upload',
                                                'Unggah Gambar Banner 21:9',
                                            ),

                                            TextInput::make('header_banner.banner_placeholder')
                                                ->label('Path Aset / URL R2 Banner 21:9')
                                                ->helperText('Terisi otomatis setelah unggah gambar ke Cloudflare R2, atau isi path/URL manual (mis. /assets/Placeholder.png).')
                                                ->maxLength(500),
                                        ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: VISI & MISI
                        // ==========================================
                        Tab::make('Visi & Misi')
                            ->icon(Heroicon::OutlinedFlag)
                            ->schema([
                                Section::make('Visi Angkatan')
                                    ->schema([
                                        TextInput::make('vision_mission.motto')
                                            ->label('Motto Angkatan')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('vision_mission.vision_body')
                                            ->label('Pernyataan Visi')
                                            ->rows(4)
                                            ->required(),
                                    ]),

                                Section::make('Misi Angkatan (Eksekusi)')
                                    ->schema([
                                        Repeater::make('vision_mission.missions')
                                            ->label('Daftar Poin Misi')
                                            ->schema([
                                                TextInput::make('number')
                                                    ->label('Nomor (e.g. 01.)')
                                                    ->required()
                                                    ->maxLength(10),

                                                TextInput::make('title')
                                                    ->label('Judul Misi')
                                                    ->required()
                                                    ->maxLength(255),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Misi')
                                                    ->rows(2)
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->reorderable(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: EMPAT PILAR WARNA
                        // ==========================================
                        Tab::make('Pilar Warna')
                            ->icon(Heroicon::OutlinedSwatch)
                            ->schema([
                                Section::make('Empat Pilar Warna Resmi')
                                    ->description('Sistem kromatik identitas visual angkatan')
                                    ->schema([
                                        Repeater::make('color_pillars.pillars')
                                            ->label('Daftar Pilar Warna')
                                            ->schema([
                                                TextInput::make('index')
                                                    ->label('Indeks (01-04)')
                                                    ->required()
                                                    ->maxLength(10),

                                                TextInput::make('name')
                                                    ->label('Nama Warna (e.g. Sky Wash)')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('role')
                                                    ->label('Peran Desain')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('hexLabel')
                                                    ->label('Kode HEX Tampilan (e.g. #80bab2)')
                                                    ->required()
                                                    ->maxLength(20),

                                                TextInput::make('tokenName')
                                                    ->label('Semantic Token (e.g. secondary-500)')
                                                    ->required()
                                                    ->maxLength(50),

                                                TextInput::make('swatchClass')
                                                    ->label('Swatch Tailwind Classes')
                                                    ->required()
                                                    ->maxLength(255),

                                                Textarea::make('philosophy')
                                                    ->label('Filosofi Warna')
                                                    ->rows(2)
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->reorderable(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 4: SIMBOLISME LOGO
                        // ==========================================
                        Tab::make('Simbolisme Logo')
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema([
                                Section::make('Header Seksi Logo')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('logo_symbolism.eyebrow')
                                            ->label('Eyebrow Monospace')
                                            ->maxLength(255),

                                        TextInput::make('logo_symbolism.title')
                                            ->label('Judul Seksi')
                                            ->maxLength(255),

                                        TextInput::make('logo_symbolism.subtitle')
                                            ->label('Sub-judul Seksi')
                                            ->maxLength(255),

                                        Textarea::make('logo_symbolism.description')
                                            ->label('Deskripsi Seksi')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Lapisan Simbol Logo (Carousel)')
                                    ->schema([
                                        Repeater::make('logo_symbolism.slides')
                                            ->label('Slide Elemen Simbol')
                                            ->addActionLabel('Tambah Slide Logo')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => trim(($state['number'] ?? '').' — '.($state['title'] ?? $state['part'] ?? 'Slide Baru'), ' —'))
                                            ->schema([
                                                TextInput::make('number')
                                                    ->label('Nomor (mis. 01-05)')
                                                    ->required()
                                                    ->maxLength(10),

                                                TextInput::make('eyebrow')
                                                    ->label('Eyebrow Lapisan')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('title')
                                                    ->label('Judul Elemen')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('detail')
                                                    ->label('Detail Singkat')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('part')
                                                    ->label('Part Tag (mis. Lingkaran)')
                                                    ->required()
                                                    ->maxLength(50),

                                                Group::make([
                                                    ImageEntry::make('image_preview')
                                                        ->label('Pratinjau Gambar Saat Ini')
                                                        ->imageHeight(140)
                                                        ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('image')))
                                                        ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('image')))),

                                                    self::imageUpload(
                                                        'image_upload',
                                                        'Unggah Gambar Elemen Logo',
                                                    ),

                                                    TextInput::make('image')
                                                        ->label('Path Asset / URL R2')
                                                        ->helperText('Terisi otomatis setelah unggah gambar ke Cloudflare R2, atau tempel URL/path aset manual.')
                                                        ->maxLength(500),
                                                ])->columnSpanFull(),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Filosofis')
                                                    ->rows(2)
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->reorderable(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 5: MASKOT ANGKATAN
                        // ==========================================
                        Tab::make('Maskot Angkatan')
                            ->icon(Heroicon::OutlinedHeart)
                            ->schema([
                                Section::make('Header Seksi Maskot')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('mascot.eyebrow')
                                            ->label('Eyebrow Monospace')
                                            ->maxLength(255),

                                        TextInput::make('mascot.title')
                                            ->label('Judul Seksi')
                                            ->maxLength(255),

                                        TextInput::make('mascot.subtitle')
                                            ->label('Sub-judul Seksi')
                                            ->maxLength(255),

                                        Textarea::make('mascot.description')
                                            ->label('Deskripsi Seksi')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Atribut Maskot (Carousel)')
                                    ->schema([
                                        Repeater::make('mascot.slides')
                                            ->label('Slide Atribut Maskot')
                                            ->addActionLabel('Tambah Slide Maskot')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => trim(($state['number'] ?? '').' — '.($state['title'] ?? $state['part'] ?? 'Slide Baru'), ' —'))
                                            ->schema([
                                                TextInput::make('number')
                                                    ->label('Nomor (mis. 01-05)')
                                                    ->required()
                                                    ->maxLength(10),

                                                TextInput::make('eyebrow')
                                                    ->label('Eyebrow Atribut')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('title')
                                                    ->label('Nama Bagian / Atribut')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('detail')
                                                    ->label('Detail Singkat')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('part')
                                                    ->label('Part Tag (mis. Strip Hitam-Emas)')
                                                    ->required()
                                                    ->maxLength(50),

                                                Group::make([
                                                    ImageEntry::make('image_preview')
                                                        ->label('Pratinjau Gambar Saat Ini')
                                                        ->imageHeight(140)
                                                        ->state(fn (Get $get): ?string => app(ContentImageService::class)->previewUrl($get('image'), ignorePlaceholder: true))
                                                        ->visible(fn (Get $get): bool => filled(app(ContentImageService::class)->previewUrl($get('image'), ignorePlaceholder: true))),

                                                    self::imageUpload(
                                                        'image_upload',
                                                        'Unggah Gambar Atribut Maskot',
                                                    ),

                                                    TextInput::make('image')
                                                        ->label('Path Asset / URL R2')
                                                        ->helperText('Terisi otomatis setelah unggah gambar ke Cloudflare R2, atau tempel URL/path aset manual.')
                                                        ->maxLength(500),
                                                ])->columnSpanFull(),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Karakter & Makna')
                                                    ->rows(2)
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->reorderable(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * Simpan pembaruan konten identitas ke database dan bersihkan cache.
     */
    public function save(WebContentService $contentService, ContentImageService $imageService): void
    {
        $state = $this->form->getState();

        try {
            $state = $imageService->processIdentityFormData($state, $contentService->getIdentityContent());
        } catch (InvalidArgumentException|RuntimeException $exception) {
            Notification::make()
                ->danger()
                ->title('Upload gambar gagal')
                ->body($exception->getMessage())
                ->persistent()
                ->send();

            return;
        }

        $contentService->updateIdentityContent($state);
        $this->form->fill($this->prepareFormData($contentService));

        Notification::make()
            ->title('Konten Identitas Berhasil Diperbarui')
            ->success()
            ->send();
    }

    /**
     * Siapkan data form awal, sekaligus mengganti template slide legacy yang belum pernah
     * diedit dengan 5 slide resmi COMETS 2026 & Bee-ner.
     *
     * @return array<string, mixed>
     */
    protected function prepareFormData(WebContentService $contentService): array
    {
        $content = $contentService->getIdentityContent();

        $content['logo_symbolism']['slides'] = $contentService->resolveIdentitySlides(
            $content['logo_symbolism']['slides'] ?? null,
            'logo_symbolism',
        );

        $content['mascot']['slides'] = $contentService->resolveIdentitySlides(
            $content['mascot']['slides'] ?? null,
            'mascot',
        );

        return $content;
    }

    /**
     * Field upload virtual: file singgah di disk lokal (tmp-uploads), lalu
     * ContentImageService mengunggahnya ke Cloudflare R2 dan mengisi URL publiknya.
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
