<?php

namespace App\Observers;

use App\Models\MbtiType;
use App\Models\Student;
use App\Models\StudentMbtiProfile;
use App\Services\StudentDirectoryService;

class StudentMbtiProfileObserver
{
    public function saved(StudentMbtiProfile $profile): void
    {
        $code = $profile->relationLoaded('mbtiType')
            ? $profile->mbtiType?->code
            : MbtiType::query()->whereKey($profile->mbti_type_id)->value('code');

        Student::query()
            ->whereKey($profile->student_id)
            ->update([
                'mbti_hex_index' => StudentDirectoryService::mbtiCodeToHexIndex($code),
            ]);

        app(StudentDirectoryService::class)->clearCache();
    }

    public function deleted(StudentMbtiProfile $profile): void
    {
        Student::query()
            ->whereKey($profile->student_id)
            ->update([
                'mbti_hex_index' => null,
            ]);

        app(StudentDirectoryService::class)->clearCache();
    }
}
