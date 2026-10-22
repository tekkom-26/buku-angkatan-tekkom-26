<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ---------- 1. Informasi Utama ----------
            Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    TextInput::make('nim')
                        ->label('NIM')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('nickname')
                        ->label('Nama Panggilan')
                        ->required()
                        ->maxLength(255),

                    Select::make('class_name')
                        ->label('Kelas')
                        ->options(Student::CLASSES)
                        ->required(),

                    Select::make('religion')
                        ->label('Agama')
                        ->options(Student::RELIGIONS)
                        ->required(),

                    TextInput::make('origin_city')
                        ->label('Kota Asal')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('birth_place')
                        ->label('Tempat Lahir')
                        ->required()
                        ->maxLength(255),

                    DatePicker::make('birth_date')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->maxDate(now()),
                ]),

            // ---------- 2. Media Sosial & Kontak ----------
            Section::make('Media Sosial & Kontak')
                ->columns(2)
                ->schema([
                    // Model otomatis mengubah ke format 628... saat disimpan
                    TextInput::make('whatsapp_no')
                        ->label('No. WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(20)
                        ->helperText('Boleh 0821..., +62821..., atau 62821... — otomatis jadi 62821...'),

                    // Model otomatis membuang @ dan mengambil username dari link
                    TextInput::make('instagram_username')
                        ->label('Instagram')
                        ->prefix('@')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('spotify_song_url')
                        ->label('Link Lagu Spotify')
                        ->url()
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            // ---------- 3. Foto & Bio ----------
            Section::make('Foto & Bio')
                ->columns(2)
                ->schema([
                    Group::make([
                        ImageEntry::make('formal_photo_url')
                            ->label('Foto Formal Saat Ini')
                            ->imageHeight(140)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                        self::photoUpload('formal_photo_upload', 'Foto Formal'),
                    ]),

                    Group::make([
                        ImageEntry::make('casual_photo_url')
                            ->label('Foto Santai Saat Ini')
                            ->imageHeight(140)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                        self::photoUpload('casual_photo_upload', 'Foto Santai'),
                    ]),

                    Textarea::make('bio_quote')
                        ->label('Bio / Kutipan')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull(),

                    // Kolom hobbies berupa string, mis. "membaca,futsal,memasak"
                    TagsInput::make('hobbies')
                        ->label('Hobi')
                        ->separator(',')
                        ->placeholder('Ketik lalu tekan Enter')
                        ->required(),

                    TextInput::make('favorite_food_place')
                        ->label('Tempat Makan Favorit')
                        ->required()
                        ->maxLength(255),
                ]),

            // ---------- 4. Data Sensitif ----------
            Section::make('Data Sensitif (Admin Only)')
                ->schema([
                    Textarea::make('address')
                        ->label('Alamat Kost / Tinggal')
                        ->rows(2)
                        ->required(),
                ]),
        ]);
    }

    /**
     * Field upload "virtual": BUKAN kolom database. File singgah di disk lokal
     * (sama seperti halaman File Manager), lalu StudentPhotoService yang
     * memindahkannya ke R2 dan mengisi kolom *_photo_url dengan URL publik.
     */
    protected static function photoUpload(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('local')
            ->directory('tmp-uploads')
            ->visibility('private')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(5120) // 5 MB
            ->required(fn (string $operation): bool => $operation === 'create')
            ->helperText('JPG / PNG / WEBP, maks 5 MB. Saat edit, kosongkan jika tidak ingin mengganti foto.');
    }
}