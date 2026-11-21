<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Provides cached, zero-DB-hit access to public student directory and profile data.
 * Stores plain PHP arrays in cache to remain compatible with `serializable_classes => false`.
 */
class StudentDirectoryService
{
    public const CACHE_KEY = 'students:public_directory';

    /**
     * @var array<int, array<string, mixed>>|null
     */
    protected ?array $memo = null;

    /**
     * Retrieve all public students ordered by class_name ASC, name ASC.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function allPublicStudents(): Collection
    {
        if ($this->memo === null) {
            $cached = Cache::rememberForever(self::CACHE_KEY, function (): array {
                return Student::query()
                    ->public()
                    ->orderBy('class_name', 'asc')
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(fn (Student $student): array => [
                        'id' => $student->id,
                        'nim' => (string) $student->nim,
                        'name' => (string) $student->name,
                        'nickname' => $student->nickname,
                        'class_name' => (string) $student->class_name,
                        'religion' => $student->religion,
                        'birth_place' => $student->birth_place,
                        'birth_date' => $student->birth_date ? $student->birth_date->format('Y-m-d') : null,
                        'origin_city' => $student->origin_city,
                        'instagram_username' => $student->instagram_username,
                        'hobbies' => $student->hobbies,
                        'bio_quote' => $student->bio_quote,
                        'favorite_food_place' => $student->favorite_food_place,
                        'spotify_song_url' => $student->spotify_song_url,
                        'formal_photo_url' => $student->formal_photo_url,
                        'casual_photo_url' => $student->casual_photo_url,
                    ])
                    ->all();
            });

            $this->memo = is_array($cached) ? $cached : [];
        }

        return collect($this->memo);
    }

    /**
     * Build paginated directory payload, class counts, and total student count in memory.
     *
     * @param  array<string, mixed>  $query
     * @return array{
     *     students: LengthAwarePaginator,
     *     filters: array<int, string>,
     *     classCounts: Collection<string, int>,
     *     totalStudents: int
     * }
     */
    public function getDirectoryPayload(
        ?string $search = null,
        ?string $classFilter = null,
        int $perPage = 15,
        int $page = 1,
        array $query = [],
    ): array {
        $all = $this->allPublicStudents();
        $filters = ['A', 'B', 'C', 'D'];

        $groupedCounts = $all->countBy('class_name');
        $classCounts = collect($filters)->mapWithKeys(
            fn (string $class): array => [$class => (int) ($groupedCounts[$class] ?? 0)]
        );

        $filtered = $all;

        if (filled($search)) {
            $needle = Str::lower(trim((string) $search));
            $filtered = $filtered->filter(function (array $student) use ($needle): bool {
                return Str::contains(Str::lower((string) ($student['nim'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['name'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['nickname'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['origin_city'] ?? '')), $needle);
            });
        }

        if (filled($classFilter)) {
            $filtered = $filtered->where('class_name', $classFilter);
        }

        $filtered = $filtered->values();
        $total = $filtered->count();
        $currentPage = max(1, $page);

        $items = $filtered
            ->forPage($currentPage, $perPage)
            ->values()
            ->map(function (array $student): array {
                $hobbies = $student['hobbies'] ?? null;
                $parsedHobbies = $hobbies
                    ? (is_string($hobbies) ? array_values(array_filter(array_map('trim', explode(',', $hobbies)))) : (array) $hobbies)
                    : ['Hardware', 'Sistem Digital'];

                $photoUrl = ! empty($student['formal_photo_url'])
                    ? (string) $student['formal_photo_url']
                    : asset('/assets/Placeholder.png');

                return [
                    'nim' => $student['nim'],
                    'name' => $student['name'],
                    'nickname' => $student['nickname'] ?? $student['name'],
                    'class_name' => $student['class_name'],
                    'origin_city' => $student['origin_city'] ?? 'Semarang',
                    'hobbies' => array_slice($parsedHobbies, 0, 3),
                    'formal_photo_url' => $photoUrl,
                    'avatar_url' => $photoUrl,
                ];
            });

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
            ],
        );

        return [
            'students' => $paginator,
            'filters' => $filters,
            'classCounts' => $classCounts,
            'totalStudents' => $all->count(),
        ];
    }

    /**
     * Build a single student's profile payload along with in-class prev/next navigation.
     *
     * @return array{
     *     student: array<string, mixed>,
     *     prevStudent: array{nim: string, nickname: string}|null,
     *     nextStudent: array{nim: string, nickname: string}|null,
     *     currentPosition: int,
     *     totalInClass: int
     * }|null
     */
    public function getStudentProfilePayload(string $nim): ?array
    {
        $all = $this->allPublicStudents();
        $student = $all->firstWhere('nim', $nim);

        if ($student === null) {
            return null;
        }

        $spotify = $this->extractSpotifyTrackId($student['spotify_song_url'] ?? null);
        $birthDate = ! empty($student['birth_date']) ? Carbon::parse($student['birth_date']) : null;
        $nickname = $student['nickname'] ?? $student['name'];

        $studentData = [
            'nim' => $student['nim'],
            'name' => $student['name'],
            'nickname' => $nickname,
            'class_name' => $student['class_name'],
            'religion' => $student['religion'] ?? 'Tidak tersedia',
            'birth_place' => $student['birth_place'] ?? 'Tidak tersedia',
            'birth_date' => $birthDate ? $birthDate->format('d F Y') : 'Tidak tersedia',
            'age' => $birthDate ? $birthDate->age : null,
            'origin_city' => $student['origin_city'] ?? 'Tidak tersedia',
            'instagram_username' => $student['instagram_username'] ?? 'unknown',
            'instagram_url' => ! empty($student['instagram_username'])
                ? 'https://instagram.com/'.$student['instagram_username']
                : 'https://instagram.com',
            'formal_photo_url' => $student['formal_photo_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode((string) $nickname).'&background=176B70&color=fff&size=800',
            'casual_photo_url' => $student['casual_photo_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode((string) $nickname).'&background=4A8B8C&color=fff&size=800',
            'bio_quote' => $student['bio_quote'] ?? 'Hidup ini indah, nikmati setiap momennya.',
            'hobbies' => ! empty($student['hobbies'])
                ? (is_string($student['hobbies']) ? array_map('trim', explode(',', $student['hobbies'])) : $student['hobbies'])
                : ['Membaca', 'Olahraga'],
            'favorite_food_place' => $student['favorite_food_place'] ?? 'Warung favorit',
            'spotify_song_url' => $student['spotify_song_url'] ?? null,
            'spotify_track_id' => $spotify['id'] ?? null,
            'spotify_type' => $spotify['type'] ?? null,
            'spotify_item' => $spotify,
        ];

        $classMates = $all
            ->where('class_name', $student['class_name'])
            ->sortBy('nim', SORT_STRING)
            ->values();

        $currentIndex = $classMates->search(fn (array $item): bool => $item['nim'] === $nim);
        $currentPosition = is_int($currentIndex) ? $currentIndex + 1 : 1;
        $totalInClass = $classMates->count();

        $prev = is_int($currentIndex) && $currentIndex > 0 ? $classMates->get($currentIndex - 1) : null;
        $next = is_int($currentIndex) && $currentIndex < ($totalInClass - 1) ? $classMates->get($currentIndex + 1) : null;

        return [
            'student' => $studentData,
            'prevStudent' => $prev ? [
                'nim' => $prev['nim'],
                'nickname' => $prev['nickname'] ?? 'Student',
            ] : null,
            'nextStudent' => $next ? [
                'nim' => $next['nim'],
                'nickname' => $next['nickname'] ?? 'Student',
            ] : null,
            'currentPosition' => $currentPosition,
            'totalInClass' => $totalInClass,
        ];
    }

    /**
     * Invalidate both in-request memoization and persistent cache.
     */
    public function clearCache(): void
    {
        $this->memo = null;
        Cache::forget(self::CACHE_KEY);
        app(AppConfigService::class)->bumpStateVersion();
    }

    /**
     * Extract Spotify item ID and type (track, playlist, or album) from a Spotify URL or URI.
     *
     * @return array{id: string, type: string}|null
     */
    private function extractSpotifyTrackId(?string $url): ?array
    {
        if (! $url) {
            return null;
        }

        if (preg_match('/(track|playlist|album)\/([a-zA-Z0-9]+)/i', $url, $matches)) {
            return [
                'id' => $matches[2],
                'type' => strtolower($matches[1]),
            ];
        }

        if (preg_match('/spotify:(track|playlist|album):([a-zA-Z0-9]+)/i', $url, $matches)) {
            return [
                'id' => $matches[2],
                'type' => strtolower($matches[1]),
            ];
        }

        return null;
    }
}
