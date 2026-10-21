<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StorageFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ['A', 'B', 'C', 'D'];
        $totalStudents = 0;
        $totalFiles = 0;
        $errors = [];

        foreach ($classes as $class) {
            $this->command->info("Processing class {$class}...");

            try {
                Student::factory()
                    ->count(2)
                    ->state(['class_name' => $class])
                    ->create()
                    ->each(function (Student $student) use (&$totalFiles, &$errors) {
                        try {
                            DB::transaction(function () use ($student, &$totalFiles) {
                                // Create formal photo storage file
                                StorageFile::factory()
                                    ->forStudent($student->nim, 'formal')
                                    ->public()
                                    ->create();
                                $totalFiles++;

                                // Create casual photo storage file
                                StorageFile::factory()
                                    ->forStudent($student->nim, 'casual')
                                    ->public()
                                    ->create();
                                $totalFiles++;

                                $this->command->comment("  ✓ {$student->nim} - {$student->name} (2 files)");
                            });
                        } catch (\Throwable $e) {
                            $error = "Failed to create storage files for student {$student->nim}: {$e->getMessage()}";
                            $errors[] = $error;
                            Log::error($error, [
                                'student_id' => $student->id,
                                'nim' => $student->nim,
                                'exception' => $e,
                            ]);
                            $this->command->error("  ✗ {$student->nim} - Storage files failed: {$e->getMessage()}");
                        }
                    });

                $totalStudents += 30;
                $this->command->info("✓ Class {$class} completed (30 students)\n");
            } catch (\Throwable $e) {
                $error = "Failed to create students for class {$class}: {$e->getMessage()}";
                $errors[] = $error;
                Log::error($error, ['class' => $class, 'exception' => $e]);
                $this->command->error("✗ Class {$class} failed: {$e->getMessage()}\n");
                break; // Stop seeding on class failure
            }
        }

        // Summary
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ Seeding Summary');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info("  Students created: {$totalStudents} / 120");
        $this->command->info("  Storage files created: {$totalFiles} / 240");

        $actualStudents = Student::count();
        $actualFiles = StorageFile::count();
        $this->command->info("  DB Students: {$actualStudents}");
        $this->command->info("  DB Storage Files: {$actualFiles}");

        if (count($errors) > 0) {
            $this->command->newLine();
            $this->command->error("⚠️  " . count($errors) . " error(s) occurred:");
            foreach ($errors as $i => $error) {
                $this->command->error("  " . ($i + 1) . ". {$error}");
            }
            $this->command->newLine();
            $this->command->warn("Check logs for details: storage/logs/laravel.log");
        } else {
            $this->command->info('  No errors!');
        }

        $this->command->info('═══════════════════════════════════════');
    }
}
