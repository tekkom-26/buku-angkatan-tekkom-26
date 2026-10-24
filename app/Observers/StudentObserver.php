<?php

namespace App\Observers;

use App\Models\Student;
use App\Services\StudentPhotoService;
use Throwable;

class StudentObserver
{
    /**
     * Saat mahasiswa dihapus, hapus juga foto di R2 dan catatan storage_files.
     * Kegagalan hapus foto dilaporkan tapi tidak menggagalkan penghapusan data.
     */
    public function deleted(Student $student): void
    {
        try {
            app(StudentPhotoService::class)->deletePhotos($student);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
