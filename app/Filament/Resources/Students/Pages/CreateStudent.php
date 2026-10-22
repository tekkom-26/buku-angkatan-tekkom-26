<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Services\StudentPhotoService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use InvalidArgumentException;
use RuntimeException;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            return app(StudentPhotoService::class)->processFormData($data);
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
}