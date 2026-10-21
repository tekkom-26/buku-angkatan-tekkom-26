<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 120 students (30 per kelas)
        Student::factory()->count(30)->classA()->withPhotos()->create();
        Student::factory()->count(30)->classB()->withPhotos()->create();
        Student::factory()->count(30)->classC()->withPhotos()->create();
        Student::factory()->count(30)->classD()->withPhotos()->create();

        $this->command->info('✅ Created 120 dummy students (30 per class)');
    }
}
