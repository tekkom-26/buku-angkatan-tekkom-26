<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $nim = '21' . str_pad((string) fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT);
        $religion = fake()->randomElement(array_keys(Student::RELIGIONS));

        $hobbies = fake()->randomElements(
            ['Membaca', 'Olahraga', 'Gaming', 'Musik', 'Fotografi', 'Traveling', 'Memasak', 'Coding'],
            fake()->numberBetween(2, 4)
        );

        return [
            'nim' => $nim,
            'name' => fake()->name(),
            'nickname' => fake()->firstName(),
            'class_name' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'religion' => $religion,
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-25 years', '-20 years'),
            'origin_city' => fake()->city(),
            'address' => fake()->address(),
            'whatsapp_no' => '0' . fake()->numerify('8##########'),
            'instagram_username' => fake()->userName(),
            'hobbies' => implode(', ', $hobbies),
            'bio_quote' => fake()->sentence(),
            'favorite_food_place' => fake()->company() . ' ' . fake()->streetName(),
            'spotify_song_url' => 'https://open.spotify.com/track/' . fake()->bothify('??????????'),
            'formal_photo_url' => "https://picsum.photos/seed/{$nim}-formal/400/600",
            'casual_photo_url' => "https://picsum.photos/seed/{$nim}-casual/400/600",
        ];
    }
}
