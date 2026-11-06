<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Service managing dynamic web content for Landing and Identity pages,
 * with a fail-safe 3-layer fallback: Cache -> Database (app_config) -> Default Config (config/content.php).
 */
class WebContentService
{
    public const KEY_LANDING = 'web_content:landing';

    public const KEY_IDENTITY = 'web_content:identity';

    protected const CACHE_PREFIX = 'web_content:';

    /**
     * Get dynamic content for the Landing page.
     *
     * @return array<string, mixed>
     */
    public function getLandingContent(): array
    {
        $cached = Cache::rememberForever(
            self::CACHE_PREFIX.'landing',
            fn (): array => $this->fetchFromDatabase(self::KEY_LANDING, 'content.landing'),
        );

        $defaults = config('content.landing', []);

        return is_array($cached) ? $this->mergeRecursive($defaults, $cached) : $defaults;
    }

    /**
     * Get dynamic content for the Identity & Philosophy page.
     *
     * @return array<string, mixed>
     */
    public function getIdentityContent(): array
    {
        $cached = Cache::rememberForever(
            self::CACHE_PREFIX.'identity',
            fn (): array => $this->fetchFromDatabase(self::KEY_IDENTITY, 'content.identity'),
        );

        $defaults = config('content.identity', []);

        return is_array($cached) ? $this->mergeRecursive($defaults, $cached) : $defaults;
    }

    /**
     * Persist updated Landing page content and invalidate related cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateLandingContent(array $data): void
    {
        $this->saveToDatabase(self::KEY_LANDING, $data, 'Dynamic web content for landing page');
        $this->clearCache('landing');
    }

    /**
     * Persist updated Identity page content and invalidate related cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateIdentityContent(array $data): void
    {
        $this->saveToDatabase(self::KEY_IDENTITY, $data, 'Dynamic web content for identity page');
        $this->clearCache('identity');
    }

    /**
     * Invalidate content cache.
     * If $section is null, clear all content caches.
     */
    public function clearCache(?string $section = null): void
    {
        if ($section === null) {
            Cache::forget(self::CACHE_PREFIX.'landing');
            Cache::forget(self::CACHE_PREFIX.'identity');

            return;
        }

        Cache::forget(self::CACHE_PREFIX.$section);
    }

    /**
     * Fetch and decode JSON content from database with fallback to default config.
     *
     * @return array<string, mixed>
     */
    protected function fetchFromDatabase(string $dbKey, string $configKey): array
    {
        $defaults = config($configKey, []);

        try {
            $raw = AppConfig::query()->where('key', $dbKey)->value('value');

            if (! $raw) {
                return $defaults;
            }

            $decoded = json_decode($raw, true);

            if (! is_array($decoded)) {
                return $defaults;
            }

            return $this->mergeRecursive($defaults, $decoded);
        } catch (Throwable) {
            return $defaults;
        }
    }

    /**
     * Save content array as JSON into app_config table.
     *
     * @param  array<string, mixed>  $data
     */
    protected function saveToDatabase(string $dbKey, array $data, ?string $description = null): void
    {
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $attributes = ['value' => $encoded ?: '{}'];
        if ($description !== null) {
            $attributes['description'] = $description;
        }

        AppConfig::query()->updateOrCreate(['key' => $dbKey], $attributes);
    }

    /**
     * Deep merge defaults with overrides.
     * Associative arrays are merged recursively.
     * Indexed/list arrays in overrides replace defaults entirely (e.g. repeaters).
     *
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function mergeRecursive(array $defaults, array $overrides): array
    {
        $result = $defaults;

        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                if ($this->isAssoc($value) && $this->isAssoc($result[$key])) {
                    $result[$key] = $this->mergeRecursive($result[$key], $value);
                } else {
                    $result[$key] = $value;
                }
            } else {
                $result[$key] = $value ?? ($result[$key] ?? null);
            }
        }

        return $result;
    }

    /**
     * Determine if an array is associative (not a numeric list).
     *
     * @param  array<mixed>  $arr
     */
    protected function isAssoc(array $arr): bool
    {
        if ($arr === []) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
