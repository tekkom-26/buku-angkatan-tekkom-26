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
 *  - mengubah nama resmi angkatan mahasiswa (cohort_name)
 *  - mengganti passcode angkatan mahasiswa
 *  - merotasi auth_epoch (+1) agar semua sesi mahasiswa aktif tidak berlaku lagi
 */
class ConfigManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Konfigurasi Sistem';

    protected static ?string $title = 'Konfigurasi Sistem';

    protected string $view = 'filament.pages.config-management';

    /**
     * Nama resmi angkatan mahasiswa yang tampil di seluruh platform.
     */
    public ?string $cohort_name = null;

    /**
     * Terikat ke input form. Selalu kosong saat halaman dibuka karena
     * passcode di database disimpan sebagai hash bcrypt (tidak bisa dibaca balik).
     */
    public ?string $student_passcode = null;

    public function mount(AppConfigService $config): void
    {
        $this->cohort_name = $config->cohortName();

        $this->form->fill([
            'cohort_name' => $this->cohort_name,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Resmi Angkatan')
                    ->description('Nama angkatan resmi yang ditampilkan pada header, judul halaman, dan seluruh platform.')
                    ->schema([
                        TextInput::make('cohort_name')
                            ->label('Nama Resmi Angkatan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Contoh: TEKKOM 2026 atau nama resmi hasil asistensi.'),
                    ]),

                Section::make('Passcode Angkatan')
                    ->description('Passcode bersama yang dipakai mahasiswa untuk membuka direktori.')
                    ->schema([
                        TextInput::make('student_passcode')
                            ->label('Ganti Passcode')
                            ->password()
                            ->revealable()
                            ->nullable()
                            ->minLength(6)
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->helperText(
                                $this->hasPasscode()
                                    ? 'Passcode saat ini tersimpan aman. Kosongkan jika tidak ingin mengubah passcode.'
                                    : 'Belum ada passcode tersimpan. Isi untuk membuatnya.'
                            ),
                    ]),
            ]);
    }

    /**
     * Simpan pembaruan identitas angkatan dan passcode baru (jika diisi).
     */
    public function save(AppConfigService $config): void
    {
        $data = $this->form->getState();

        if (! empty($data['cohort_name'])) {
            $config->setCohortName($data['cohort_name']);
            $this->cohort_name = $data['cohort_name'];
        }

        if (! empty($data['student_passcode'])) {
            $config->set(
                AppConfigService::KEY_STUDENT_PASSCODE,
                Hash::make($data['student_passcode']),
                'Bcrypt hash of the shared student passcode',
            );
        }

        $this->student_passcode = null;
        $this->form->fill([
            'cohort_name' => $this->cohort_name,
        ]);

        Notification::make()
            ->title('Konfigurasi Berhasil Diperbarui')
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
