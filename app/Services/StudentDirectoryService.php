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
     * Canonical opposing poles per MBTI axis.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    public const ASPECT_AXES = [
        ['E', 'I'],
        ['N', 'S'],
        ['T', 'F'],
        ['J', 'P'],
    ];

    /**
     * Allowed single-letter MBTI aspect codes.
     *
     * @var list<string>
     */
    public const VALID_ASPECTS = ['E', 'I', 'N', 'S', 'T', 'F', 'J', 'P'];

    /**
     * Metadata & SVG hub layout positions for the 8 aspect nodes (opposite poles face across center 500, 280).
     *
     * @var array<string, array{label: string, axis: string, angle: float, x: int, y: int}>
     */
    private const ASPECT_GRAPH_NODES = [
        'N' => ['label' => 'Intuition', 'axis' => 'N/S', 'angle' => -90.0, 'x' => 500, 'y' => 142],
        'T' => ['label' => 'Thinking', 'axis' => 'T/F', 'angle' => -45.0, 'x' => 631, 'y' => 182],
        'J' => ['label' => 'Judging', 'axis' => 'J/P', 'angle' => 0.0, 'x' => 685, 'y' => 280],
        'I' => ['label' => 'Introvert', 'axis' => 'E/I', 'angle' => 45.0, 'x' => 631, 'y' => 378],
        'S' => ['label' => 'Sensing', 'axis' => 'N/S', 'angle' => 90.0, 'x' => 500, 'y' => 418],
        'F' => ['label' => 'Feeling', 'axis' => 'T/F', 'angle' => 135.0, 'x' => 369, 'y' => 378],
        'P' => ['label' => 'Perceiving', 'axis' => 'J/P', 'angle' => 180.0, 'x' => 315, 'y' => 280],
        'E' => ['label' => 'Extravert', 'axis' => 'E/I', 'angle' => -135.0, 'x' => 369, 'y' => 182],
    ];

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
                    ->map(function (Student $student): array {
                        $mbtiProfile = $student->mbtiProfile;
                        $mbtiType = $mbtiProfile?->mbtiType;
                        $mbtiCode = $mbtiType ? (string) $mbtiType->code : null;

                        return [
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
                            'mbti_code' => $mbtiCode,
                            'mbti_title' => $mbtiType ? (string) $mbtiType->title : null,
                            'temperament_code' => $mbtiType ? (string) $mbtiType->temperament_code : null,
                            'temperament_name' => $mbtiType ? (string) $mbtiType->temperament_name : null,
                            'mbti_description' => $mbtiType ? (string) $mbtiType->description : null,
                            'mbti_aspects' => $mbtiCode !== null ? str_split($mbtiCode) : [],
                            'mbti_pcts' => $mbtiProfile ? $mbtiProfile->toPercentageMap() : null,
                        ];
                    })
                    ->all();
            });

            $this->memo = is_array($cached) ? $cached : [];
        }

        return collect($this->memo);
    }

    /**
     * Normalize aspect filters to at most one pole per axis in canonical [E/I, N/S, T/F, J/P] order.
     *
     * @param  array<int, string>|string|null  $aspects
     * @return list<string>
     */
    public static function normalizeAspects(array|string|null $aspects): array
    {
        $items = is_string($aspects)
            ? explode(',', $aspects)
            : (is_array($aspects) ? $aspects : []);

        $selectedByAxis = [];

        foreach ($items as $raw) {
            $letter = strtoupper(trim((string) $raw));
            foreach (self::ASPECT_AXES as $axisIndex => $pair) {
                if (in_array($letter, $pair, true)) {
                    $selectedByAxis[$axisIndex] = $letter;
                    break;
                }
            }
        }

        ksort($selectedByAxis);

        return array_values($selectedByAxis);
    }

    /**
     * Toggle an aspect in an active filter list:
     * - clicking active pole deactivates it
     * - clicking opposing pole switches to the opposing pole
     * - clicking inactive pole on an unused axis adds it
     *
     * @param  array<int, string>  $currentAspects
     * @return list<string>
     */
    public static function toggleAspectInList(array $currentAspects, string $aspect): array
    {
        $letter = strtoupper(trim($aspect));
        $normalized = self::normalizeAspects($currentAspects);

        $targetAxisIndex = null;
        foreach (self::ASPECT_AXES as $axisIndex => $pair) {
            if (in_array($letter, $pair, true)) {
                $targetAxisIndex = $axisIndex;
                break;
            }
        }

        if ($targetAxisIndex === null) {
            return $normalized;
        }

        if (in_array($letter, $normalized, true)) {
            return array_values(array_filter(
                $normalized,
                fn (string $item): bool => $item !== $letter
            ));
        }

        $opposingPair = self::ASPECT_AXES[$targetAxisIndex];
        $withoutAxis = array_values(array_filter(
            $normalized,
            fn (string $item): bool => ! in_array($item, $opposingPair, true)
        ));
        $withoutAxis[] = $letter;

        return self::normalizeAspects($withoutAxis);
    }

    /**
     * Build paginated directory payload, class counts, lightweight MBTI pill counts, and total count in memory.
     *
     * @param  array<int, string>|string  $aspectsFilter
     * @param  array<string, mixed>  $query
     * @return array{
     *     students: LengthAwarePaginator,
     *     filters: array<int, string>,
     *     classCounts: Collection<string, int>,
     *     totalStudents: int,
     *     activeAspects: list<string>,
     *     aspectStats: array<string, array{letter: string, label: string, axis: string, active: bool, count: int}>
     * }
     */
    public function getDirectoryPayload(
        ?string $search = null,
        ?string $classFilter = null,
        array|string $aspectsFilter = [],
        int $perPage = 15,
        int $page = 1,
        array $query = [],
    ): array {
        $all = $this->allPublicStudents();
        $filters = ['A', 'B', 'C', 'D'];
        $normalizedAspects = self::normalizeAspects($aspectsFilter);

        $groupedCounts = $all->countBy('class_name');
        $classCounts = collect($filters)->mapWithKeys(
            fn (string $class): array => [$class => (int) ($groupedCounts[$class] ?? 0)]
        );

        $searchAndClassFiltered = $all;

        if (filled($search)) {
            $needle = Str::lower(trim((string) $search));
            $searchAndClassFiltered = $searchAndClassFiltered->filter(function (array $student) use ($needle): bool {
                return Str::contains(Str::lower((string) ($student['nim'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['name'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['nickname'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['origin_city'] ?? '')), $needle)
                    || Str::contains(Str::lower((string) ($student['mbti_code'] ?? '')), $needle);
            });
        }

        if (filled($classFilter)) {
            $searchAndClassFiltered = $searchAndClassFiltered->where('class_name', $classFilter);
        }

        $filtered = $searchAndClassFiltered;

        if ($normalizedAspects !== []) {
            $filtered = $filtered->filter(function (array $student) use ($normalizedAspects): bool {
                $studentAspects = $student['mbti_aspects'] ?? [];
                if ($studentAspects === []) {
                    return false;
                }

                foreach ($normalizedAspects as $requiredAspect) {
                    if (! in_array($requiredAspect, $studentAspects, true)) {
                        return false;
                    }
                }

                return true;
            });
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
                    'mbti_code' => $student['mbti_code'] ?? null,
                    'temperament_code' => $student['temperament_code'] ?? null,
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
            'activeAspects' => $normalizedAspects,
            'aspectStats' => $this->buildAspectStats($searchAndClassFiltered, $normalizedAspects),
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
            'mbti' => ! empty($student['mbti_code']) ? [
                'code' => $student['mbti_code'],
                'title' => $student['mbti_title'] ?? null,
                'temperament_code' => $student['temperament_code'] ?? null,
                'temperament_name' => $student['temperament_name'] ?? null,
                'description' => $student['mbti_description'] ?? null,
                'pcts' => $student['mbti_pcts'] ?? null,
            ] : null,
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
     * Build interactive Obsidian-style force graph payload for the dedicated /graph view.
     *
     * @param  array<int, string>|string  $aspects
     * @return array<string, mixed>
     */
    public function getGraphPayload(array|string $aspects = []): array
    {
        $all = $this->allPublicStudents();
        $normalizedAspects = self::normalizeAspects($aspects);

        $mappedStudents = $all
            ->filter(fn (array $student): bool => ! empty($student['mbti_code']))
            ->values();

        $studentNodes = $mappedStudents->map(function (array $student): array {
            $nim = (string) $student['nim'];
            $avatar = ! empty($student['formal_photo_url'])
                ? (string) $student['formal_photo_url']
                : asset('/assets/Placeholder.png');

            return [
                'nim' => $nim,
                'name' => (string) $student['name'],
                'nickname' => (string) ($student['nickname'] ?? $student['name']),
                'class_name' => (string) $student['class_name'],
                'mbti_code' => (string) $student['mbti_code'],
                'mbti_title' => (string) ($student['mbti_title'] ?? ''),
                'temperament_code' => (string) ($student['temperament_code'] ?? ''),
                'temperament_name' => (string) ($student['temperament_name'] ?? ''),
                'aspects' => $student['mbti_aspects'] ?? [],
                'pcts' => $student['mbti_pcts'] ?? [],
                'avatar_url' => $avatar,
                'url' => route('students.show', ['nim' => $nim]),
            ];
        })->all();

        return [
            'center' => [
                'label' => 'TEKKOM 26',
                'totalStudents' => $all->count(),
                'mappedCount' => $mappedStudents->count(),
                'unmappedCount' => $all->count() - $mappedStudents->count(),
            ],
            'activeAspects' => $normalizedAspects,
            'axes' => self::ASPECT_AXES,
            'aspectNodes' => $this->buildAspectStats($mappedStudents, $normalizedAspects),
            'studentNodes' => $studentNodes,
            'directoryUrl' => route('directory.index'),
        ];
    }

    /**
     * Compute per-aspect preview intersection counts for a given student pool.
     *
     * @param  Collection<int, array<string, mixed>>  $pool
     * @param  list<string>  $activeAspects
     * @return array<string, array{letter: string, label: string, axis: string, active: bool, count: int}>
     */
    private function buildAspectStats(Collection $pool, array $activeAspects): array
    {
        $aspectNodes = [];

        foreach (self::ASPECT_GRAPH_NODES as $letter => $meta) {
            $previewAspects = in_array($letter, $activeAspects, true)
                ? $activeAspects
                : self::toggleAspectInList($activeAspects, $letter);

            $previewCount = $pool->filter(function (array $student) use ($previewAspects): bool {
                $studentAspects = $student['mbti_aspects'] ?? [];
                if ($studentAspects === []) {
                    return false;
                }

                foreach ($previewAspects as $needed) {
                    if (! in_array($needed, $studentAspects, true)) {
                        return false;
                    }
                }

                return true;
            })->count();

            $aspectNodes[$letter] = [
                'letter' => $letter,
                'label' => $meta['label'],
                'axis' => $meta['axis'],
                'active' => in_array($letter, $activeAspects, true),
                'count' => $previewCount,
            ];
        }

        return $aspectNodes;
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
