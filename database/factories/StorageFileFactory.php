<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StorageFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageFile>
 */
class StorageFileFactory extends Factory
{
    protected $model = StorageFile::class;

    public function definition(): array
    {
        $filename = fake()->uuid() . '.jpg';

        return [
            'disk' => 'r2',
            'path' => 'students/' . $filename,
            'prefix' => 'students',
            'alias' => null,
            'is_directory' => false,
            'visibility' => StorageFile::VISIBILITY_PUBLIC,
            'uploaded_by' => 'system',
            'original_name' => fake()->word() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(50000, 500000),
            'metadata' => [
                'width' => 400,
                'height' => 600,
            ],
        ];
    }

    public function forStudent(string $nim, string $type = 'formal'): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => "students/{$nim}-{$type}.jpg",
            'prefix' => 'students',
            'alias' => "{$nim}-{$type}",
            'original_name' => "{$nim}-{$type}.jpg",
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => StorageFile::VISIBILITY_PUBLIC,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => StorageFile::VISIBILITY_PRIVATE,
        ]);
    }
}
