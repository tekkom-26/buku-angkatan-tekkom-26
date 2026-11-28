<?php

namespace App\Console\Commands;

use App\Models\MbtiType;
use App\Models\Student;
use App\Models\StudentMbtiProfile;
use App\Services\StudentDirectoryService;
use Database\Seeders\MbtiTypeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use League\Csv\Reader;

class ImportStudentMbtiCommand extends Command
{
    /**
     * Verified NIM typo overrides from the cohort MBTI Google Form CSV.
     *
     * @var array<string, array{expected_name_fragment: string, target_nim: string}>
     */
    private const NIM_TYPO_MAP = [
        '21120126120016' => [
            'expected_name_fragment' => 'ayudya',
            'target_nim' => '21120126120015',
        ],
        '2112012614200' => [
            'expected_name_fragment' => 'rasya',
            'target_nim' => '21120126140200',
        ],
        '21130136140131' => [
            'expected_name_fragment' => 'ammar',
            'target_nim' => '211201261240131',
        ],
        '21120126130052' => [
            'expected_name_fragment' => 'hannan',
            'target_nim' => '21120126120052',
        ],
    ];

    /**
     * Known non-student troll submissions to skip safely.
     *
     * @var list<string>
     */
    private const SKIPPED_TROLL_NIMS = [
        '26969696969',
    ];

    protected $signature = 'mbti:import
        {--path=Database & Rekap Persentase MBTI .csv : Path to the MBTI CSV file}
        {--dry-run : Validate and preview import results without writing to the database}';

    protected $description = 'Import and normalize cohort MBTI percentage profiles from the verified CSV export';

    public function handle(StudentDirectoryService $directory): int
    {
        $filePath = $this->resolveCsvPath((string) $this->option('path'));

        if ($filePath === null) {
            $this->error("CSV file not found: {$this->option('path')}");

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        if (MbtiType::query()->count() === 0 && ! $dryRun) {
            $this->call('db:seed', [
                '--class' => MbtiTypeSeeder::class,
                '--force' => true,
            ]);
        }

        $mbtiTypesByCode = MbtiType::query()
            ->pluck('id', 'code')
            ->all();

        if ($mbtiTypesByCode === []) {
            $this->error('No mbti_types records found. Run MbtiTypeSeeder first.');

            return self::FAILURE;
        }

        $studentsByNim = Student::query()
            ->select(['id', 'nim', 'name', 'class_name'])
            ->get()
            ->keyBy('nim');

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $stats = [
            'rows' => 0,
            'saved' => 0,
            'typo_remapped' => 0,
            'skipped_troll' => 0,
            'deduplicated' => 0,
            'unmatched' => 0,
            'invalid' => 0,
        ];

        /** @var array<int, array{class_matched: bool, payload: array<string, mixed>, nim: string, code: string}> $resolvedByStudent */
        $resolvedByStudent = [];

        foreach ($csv->getRecords() as $rawRow) {
            $stats['rows']++;
            $row = $this->normalizeRowKeys($rawRow);

            $rawNim = trim((string) ($row['NIM'] ?? ''));
            $rawName = trim((string) ($row['Nama'] ?? ''));
            $rawClass = $this->normalizeClassName((string) ($row['Kelas'] ?? ''));
            $mbtiCode = strtoupper(trim((string) ($row['MBTI'] ?? '')));

            if (in_array($rawNim, self::SKIPPED_TROLL_NIMS, true) || Str::lower($rawName) === 'kak mau gak') {
                $stats['skipped_troll']++;

                continue;
            }

            $resolvedNim = $this->resolveNim($rawNim, $rawName, $stats);
            /** @var Student|null $student */
            $student = $studentsByNim->get($resolvedNim);

            if ($student === null) {
                $stats['unmatched']++;
                $this->warn("Unmatched NIM skipped: {$rawNim} ({$rawName})");

                continue;
            }

            $mbtiTypeId = $mbtiTypesByCode[$mbtiCode] ?? null;
            $percentages = $this->parseAndValidatePercentages($row);

            if ($mbtiTypeId === null || $percentages === null) {
                $stats['invalid']++;
                $this->warn("Invalid MBTI data skipped: {$resolvedNim} ({$rawName}) -> {$mbtiCode}");

                continue;
            }

            $classMatched = $rawClass !== '' && strtoupper((string) $student->class_name) === $rawClass;
            $screenshotUrl = trim((string) ($row['Screenshot'] ?? ''));

            $payload = array_merge($percentages, [
                'student_id' => $student->id,
                'mbti_type_id' => (int) $mbtiTypeId,
                'screenshot_url' => $screenshotUrl !== '' ? $screenshotUrl : null,
            ]);

            if (isset($resolvedByStudent[$student->id])) {
                $stats['deduplicated']++;

                // Prefer the duplicate row whose class matches the authoritative database record.
                if (! $resolvedByStudent[$student->id]['class_matched'] && $classMatched) {
                    $resolvedByStudent[$student->id] = [
                        'class_matched' => $classMatched,
                        'payload' => $payload,
                        'nim' => (string) $student->nim,
                        'code' => $mbtiCode,
                    ];
                }

                continue;
            }

            $resolvedByStudent[$student->id] = [
                'class_matched' => $classMatched,
                'payload' => $payload,
                'nim' => (string) $student->nim,
                'code' => $mbtiCode,
            ];
        }

        if (! $dryRun) {
            foreach ($resolvedByStudent as $entry) {
                $payload = $entry['payload'];
                StudentMbtiProfile::query()->updateOrCreate(
                    ['student_id' => $payload['student_id']],
                    collect($payload)->except('student_id')->all(),
                );
                $stats['saved']++;
            }

            $directory->clearCache();
        } else {
            $stats['saved'] = count($resolvedByStudent);
        }

        $prefix = $dryRun ? '[DRY RUN] ' : '';
        $this->info("{$prefix}MBTI CSV import complete.");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total CSV rows read', $stats['rows']],
                ['Profiles '.($dryRun ? 'ready to save' : 'saved/updated'), $stats['saved']],
                ['NIM typos remapped', $stats['typo_remapped']],
                ['Troll rows skipped', $stats['skipped_troll']],
                ['Duplicate NIMs deduplicated', $stats['deduplicated']],
                ['Unmatched NIMs', $stats['unmatched']],
                ['Invalid MBTI/percentage rows', $stats['invalid']],
            ],
        );

        return self::SUCCESS;
    }

    private function resolveCsvPath(string $inputPath): ?string
    {
        if (is_file($inputPath)) {
            return $inputPath;
        }

        $fromBase = base_path($inputPath);

        return is_file($fromBase) ? $fromBase : null;
    }

    /**
     * @param  array<array-key, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRowKeys(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[trim((string) $key)] = $value;
        }

        return $normalized;
    }

    private function normalizeClassName(string $rawClass): string
    {
        $clean = strtoupper(trim(str_ireplace('Kelas', '', $rawClass)));

        return in_array($clean, ['A', 'B', 'C', 'D'], true) ? $clean : '';
    }

    /**
     * @param  array<string, int>  $stats
     */
    private function resolveNim(string $rawNim, string $rawName, array &$stats): string
    {
        if (! isset(self::NIM_TYPO_MAP[$rawNim])) {
            return $rawNim;
        }

        $mapping = self::NIM_TYPO_MAP[$rawNim];
        if (Str::contains(Str::lower($rawName), $mapping['expected_name_fragment'])) {
            $stats['typo_remapped']++;

            return $mapping['target_nim'];
        }

        return $rawNim;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, int>|null
     */
    private function parseAndValidatePercentages(array $row): ?array
    {
        $keys = ['E', 'I', 'N', 'S', 'T', 'F', 'J', 'P'];
        $values = [];

        foreach ($keys as $key) {
            $raw = trim((string) ($row[$key] ?? ''));
            if ($raw === '' || ! ctype_digit($raw)) {
                return null;
            }
            $int = (int) $raw;
            if ($int < 0 || $int > 100) {
                return null;
            }
            $values[$key] = $int;
        }

        if (
            ($values['E'] + $values['I']) !== 100 ||
            ($values['N'] + $values['S']) !== 100 ||
            ($values['T'] + $values['F']) !== 100 ||
            ($values['J'] + $values['P']) !== 100
        ) {
            return null;
        }

        return [
            'pct_e' => $values['E'],
            'pct_i' => $values['I'],
            'pct_n' => $values['N'],
            'pct_s' => $values['S'],
            'pct_t' => $values['T'],
            'pct_f' => $values['F'],
            'pct_j' => $values['J'],
            'pct_p' => $values['P'],
        ];
    }
}
