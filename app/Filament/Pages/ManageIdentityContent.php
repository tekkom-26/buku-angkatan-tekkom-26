<?php

namespace App\Filament\Pages;

use App\Services\WebContentService;
use BackedEnum;
use Filament\Forms\Components\Repeater;
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
        $this->form->fill($contentService->getIdentityContent());
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

                                        TextInput::make('header_banner.banner_placeholder')
                                            ->label('Path Aset Banner 21:9')
                                            ->helperText('Contoh: /assets/Placeholder.png atau URL publik Cloudflare R2')
                                            ->maxLength(500),
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
                                            ->schema([
                                                TextInput::make('number')
                                                    ->label('Nomor (01-04)')
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
                                                    ->label('Part Tag (e.g. WINGS)')
                                                    ->required()
                                                    ->maxLength(50),

                                                TextInput::make('image')
                                                    ->label('Path Asset / URL R2')
                                                    ->helperText('Contoh: /assets/Placeholder.png')
                                                    ->maxLength(500),

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
                                            ->schema([
                                                TextInput::make('number')
                                                    ->label('Nomor (01-04)')
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
                                                    ->label('Part Tag (e.g. TUSK)')
                                                    ->required()
                                                    ->maxLength(50),

                                                TextInput::make('image')
                                                    ->label('Path Asset / URL R2')
                                                    ->helperText('Contoh: /assets/Placeholder.png')
                                                    ->maxLength(500),

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
    public function save(WebContentService $contentService): void
    {
        $state = $this->form->getState();

        $contentService->updateIdentityContent($state);

        Notification::make()
            ->title('Konten Identitas Berhasil Diperbarui')
            ->success()
            ->send();
    }
}
