<?php

namespace App\Observers;

use App\Models\Student;
use App\Services\StudentDirectoryService;
use App\Services\StudentPhotoService;
use Throwable;

class StudentObserver
{
    /**
     * Saat data mahasiswa disimpan atau diperbarui, bersihkan cache direktori publik.
     */
    public function saved(Student $student): void
    {
        app(StudentDirectoryService::class)->clearCache();
    }

    /**
     * Saat mahasiswa dihapus, bersihkan cache direktori publik serta hapus foto di R2 dan catatan storage_files.
     * Kegagalan hapus foto dilaporkan tapi tidak menggagalkan penghapusan data.
     */
    public function deleted(Student $student): void
    {
        app(StudentDirectoryService::class)->clearCache();

        try {
            app(StudentPhotoService::class)->deletePhotos($student);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
