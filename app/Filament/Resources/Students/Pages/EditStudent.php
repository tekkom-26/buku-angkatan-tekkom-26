<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Services\StudentPhotoService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use InvalidArgumentException;
use RuntimeException;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            return app(StudentPhotoService::class)->processFormData($data, $this->getRecord());
        } catch (InvalidArgumentException|RuntimeException $exception) {
            Notification::make()
                ->danger()
                ->title('Upload foto gagal')
                ->body($exception->getMessage())
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    /**
     * Isi ulang form dari database setelah simpan, supaya field upload
     * dikosongkan dan pratinjau foto menampilkan URL terbaru.
     */
    protected function afterSave(): void
    {
        $this->fillForm();
    }
}