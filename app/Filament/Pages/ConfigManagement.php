<?php

namespace App\Filament\Pages;

use App\Services\AppConfigService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

/**
 * Halaman admin untuk mengelola konfigurasi sistem di tabel `app_config`:
 *  - mengganti passcode angkatan mahasiswa
 *  - merotasi auth_epoch (+1) agar semua sesi mahasiswa aktif tidak berlaku lagi
 */
class ConfigManagement extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Konfigurasi Sistem';

    protected static ?string $title = 'Konfigurasi Sistem';

    protected string $view = 'filament.pages.config-management';

    /**
     * Terikat ke input form. Selalu kosong saat halaman dibuka karena
     * passcode di database disimpan sebagai hash bcrypt (tidak bisa dibaca balik).
     */
    public ?string $student_passcode = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Passcode Angkatan')
                    ->description('Passcode bersama yang dipakai mahasiswa untuk membuka situs.')
                    ->schema([
                        TextInput::make('student_passcode')
                            ->label('Passcode Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(6)
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->helperText(
                                $this->hasPasscode()
                                    ? 'Passcode saat ini tersimpan aman (terenkripsi) dan tidak bisa ditampilkan. Isi untuk menggantinya.'
                                    : 'Belum ada passcode tersimpan. Isi untuk membuatnya.'
                            ),
                    ]),
            ]);
    }

    /**
     * Simpan passcode baru (sebagai hash bcrypt, sama seperti AuthSeeder).
     * AppConfigService::set() otomatis membersihkan cache key terkait.
     */
    public function save(AppConfigService $config): void
    {
        $data = $this->form->getState();

        $config->set(
            AppConfigService::KEY_STUDENT_PASSCODE,
            Hash::make($data['student_passcode']),
            'Bcrypt hash of the shared student passcode',
        );

        $this->student_passcode = null;
        $this->form->fill();

        Notification::make()
            ->title('Passcode Berhasil Diperbarui')
            ->success()
            ->send();
    }

    /**
     * Naikkan auth_epoch sebesar +1 sehingga seluruh sesi mahasiswa
     * yang diterbitkan dengan epoch lama otomatis dianggap "guest".
     */
    public function rotateEpoch(AppConfigService $config): void
    {
        $config->rotateAuthEpoch();

        Notification::make()
            ->title('Sesi Epoch Berhasil Dirotasi')
            ->success()
            ->send();
    }

    public function rotateEpochAction(): Action
    {
        return Action::make('rotateEpoch')
            ->label('Rotasi Epoch Sekarang')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Rotasi sesi epoch?')
            ->modalDescription('Semua mahasiswa yang sedang login akan otomatis keluar dan harus memasukkan passcode lagi. Tindakan ini tidak bisa dibatalkan.')
            ->modalSubmitActionLabel('Ya, rotasi sekarang')
            ->action(fn () => $this->rotateEpoch(app(AppConfigService::class)));
    }

    public function getCurrentEpoch(): int
    {
        return app(AppConfigService::class)->authEpoch();
    }

    protected function hasPasscode(): bool
    {
        return filled(app(AppConfigService::class)->get(AppConfigService::KEY_STUDENT_PASSCODE));
    }
}
