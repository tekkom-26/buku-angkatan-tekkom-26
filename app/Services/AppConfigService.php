<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Cache;

/**
 * Reads and writes key/value settings stored in the `app_config` table,
 * with a cache layer that is invalidated on every write.
 */
class AppConfigService
{
    public const KEY_STUDENT_PASSCODE = 'student_passcode';

    public const KEY_AUTH_EPOCH = 'auth_epoch';

    protected const CACHE_PREFIX = 'app_config:';

    public function get(string $key, ?string $default = null): ?string
    {
        $value = Cache::rememberForever(
            self::CACHE_PREFIX.$key,
            fn (): ?string => AppConfig::query()->where('key', $key)->value('value'),
        );

        return $value ?? $default;
    }

    public function set(string $key, string $value, ?string $description = null): AppConfig
    {
        $attributes = ['value' => $value];

        if ($description !== null) {
            $attributes['description'] = $description;
        }

        $config = AppConfig::query()->updateOrCreate(['key' => $key], $attributes);

        $this->forget($key);

        return $config;
    }

    public function forget(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX.$key);
    }

    public function authEpoch(): int
    {
        $value = AppConfig::query()
            ->where('key', self::KEY_AUTH_EPOCH)
            ->value('value');

        return (int) ($value ?? '1');
    }
}
