<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Reads and writes key/value settings stored in the `app_config` table,
 * with a cache layer that is invalidated on every write.
 */
class AppConfigService
{
    public const KEY_STUDENT_PASSCODE = 'student_passcode';

    public const KEY_AUTH_EPOCH = 'auth_epoch';

    public const KEY_COHORT_NAME = 'cohort_name';

    protected const CACHE_PREFIX = 'app_config:';

    /**
     * In-request memoized values keyed by config key.
     *
     * @var array<string, string|null>
     */
    protected array $memo = [];

    public function get(string $key, ?string $default = null): ?string
    {
        if (! array_key_exists($key, $this->memo)) {
            $this->memo[$key] = Cache::rememberForever(
                self::CACHE_PREFIX.$key,
                fn (): ?string => AppConfig::query()->where('key', $key)->value('value'),
            );
        }

        return $this->memo[$key] ?? $default;
    }

    public function set(string $key, string $value, ?string $description = null): AppConfig
    {
        $attributes = ['value' => $value];

        if ($description !== null) {
            $attributes['description'] = $description;
        }

        $config = AppConfig::query()->updateOrCreate(['key' => $key], $attributes);

        $this->forget($key);
        $this->memo[$key] = $value;

        return $config;
    }

    public function forget(string $key): void
    {
        unset($this->memo[$key]);
        Cache::forget(self::CACHE_PREFIX.$key);
    }

    public function clearMemo(): void
    {
        $this->memo = [];
    }

    public function authEpoch(): int
    {
        return (int) ($this->get(self::KEY_AUTH_EPOCH, '1') ?? '1');
    }

    public function cohortName(): string
    {
        return $this->get(self::KEY_COHORT_NAME, config('content.cohort_name', 'TEKNIK KOMPUTER 2026'))
            ?: config('content.cohort_name', 'TEKNIK KOMPUTER 2026');
    }

    public function setCohortName(string $name): AppConfig
    {
        return $this->set(
            self::KEY_COHORT_NAME,
            $name,
            'Nama resmi angkatan Teknik Komputer Undip 2026',
        );
    }

    /**
     * Naikkan auth_epoch sebesar +1 secara atomik (baris dikunci selama
     * transaksi) dan kembalikan nilai epoch yang baru.
     */
    public function rotateAuthEpoch(): int
    {
        $next = DB::transaction(function (): int {
            $current = (int) (AppConfig::query()
                ->where('key', self::KEY_AUTH_EPOCH)
                ->lockForUpdate()
                ->value('value') ?? 1);

            $next = $current + 1;

            $this->set(
                self::KEY_AUTH_EPOCH,
                (string) $next,
                'Incremented to revoke all existing student sessions',
            );

            return $next;
        });

        $this->forget(self::KEY_AUTH_EPOCH);

        return $next;
    }
}
