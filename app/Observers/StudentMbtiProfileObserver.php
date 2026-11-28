<?php

namespace App\Observers;

use App\Models\StudentMbtiProfile;
use App\Services\StudentDirectoryService;

class StudentMbtiProfileObserver
{
    public function saved(StudentMbtiProfile $profile): void
    {
        app(StudentDirectoryService::class)->clearCache();
    }

    public function deleted(StudentMbtiProfile $profile): void
    {
        app(StudentDirectoryService::class)->clearCache();
    }
}
