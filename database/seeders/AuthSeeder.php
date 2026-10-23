<?php

namespace Database\Seeders;

use App\Services\AppConfigService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthSeeder extends Seeder
{
    /**
     * Seed the initial student passcode (bcrypt hash) and auth epoch.
     */
    public function run(AppConfigService $config): void
    {
        $passcode = config('angkatan.passcode');

        if (! is_string($passcode) || $passcode === '') {
            $passcode = Str::random(12);
            $this->command?->warn("ANGKATAN_PASSCODE is not set. Generated passcode: {$passcode}");
        }

        $config->set(
            AppConfigService::KEY_STUDENT_PASSCODE,
            Hash::make($passcode),
            'Bcrypt hash of the shared student passcode',
        );

        $config->set(
            AppConfigService::KEY_AUTH_EPOCH,
            '1',
            'Incremented to revoke all existing student sessions',
        );
    }
}
