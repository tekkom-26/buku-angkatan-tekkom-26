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
     * In-request memoized content sections.
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $memo = [];

    /**
     * Get dynamic content for the Landing page.
     *
     * @return array<string, mixed>
     */
    public function getLandingContent(): array
    {
        if (isset($this->memo['landing'])) {
            return $this->memo['landing'];
        }

        $cached = Cache::rememberForever(
            self::CACHE_PREFIX.'landing',
            fn (): array => $this->fetchFromDatabase(self::KEY_LANDING, 'content.landing'),
        );

        $defaults = config('content.landing', []);

        return $this->memo['landing'] = is_array($cached) ? $this->mergeRecursive($defaults, $cached) : $defaults;
    }

    /**
     * Get dynamic content for the Identity & Philosophy page.
     *
     * @return array<string, mixed>
     */
    public function getIdentityContent(): array
    {
        if (isset($this->memo['identity'])) {
            return $this->memo['identity'];
        }

        $cached = Cache::rememberForever(
            self::CACHE_PREFIX.'identity',
            fn (): array => $this->fetchFromDatabase(self::KEY_IDENTITY, 'content.identity'),
        );

        $defaults = config('content.identity', []);

        return $this->memo['identity'] = is_array($cached) ? $this->mergeRecursive($defaults, $cached) : $defaults;
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
     * Deteksi apakah data slide masih berupa template gajah/legacy lama yang belum pernah
     * diisi gambar maupun diedit oleh admin setelah revamp COMETS 2026.
     *
     * @param  array<int, mixed>  $slides
     */
    public function isUntouchedLegacySlides(array $slides, string $section): bool
    {
        $legacyParts = match ($section) {
            'logo_symbolism' => ['WINGS', 'NUMERAL 26', 'CIRCUIT', 'FULL EMBLEM'],
            'mascot' => ['TUSK', 'TRUNK', 'JACKET', 'FULL BODY'],
            default => null,
        };

        if ($legacyParts === null || count($slides) !== count($legacyParts)) {
            return false;
        }

        $parts = [];

        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                return false;
            }

            $image = (string) ($slide['image'] ?? '');
            if ($image !== '' && ! str_contains($image, 'Placeholder.png')) {
                return false;
            }

            $parts[] = $slide['part'] ?? null;
        }

        return $parts === $legacyParts;
    }

    /**
     * Resolusi daftar slide Identitas (Logo / Maskot) dengan fallback otomatis
     * ke default COMETS 2026 jika data kosong atau masih berupa template legacy lama.
     *
     * @param  array<int, mixed>|null  $slides
     * @return array<int, array<string, mixed>>
     */
    public function resolveIdentitySlides(?array $slides, string $section): array
    {
        $normalized = array_values(array_filter(
            $slides ?? [],
            static fn (mixed $slide): bool => is_array($slide) && filled($slide['title'] ?? null),
        ));

        if ($normalized === [] || $this->isUntouchedLegacySlides($normalized, $section)) {
            /** @var array<int, array<string, mixed>> $defaults */
            $defaults = config("content.identity.{$section}.slides", []);

            return $defaults;
        }

        return $normalized;
    }

    /**
     * Invalidate content cache.
     * If $section is null, clear all content caches.
     */
    public function clearCache(?string $section = null): void
    {
        if ($section === null) {
            $this->memo = [];
            Cache::forget(self::CACHE_PREFIX.'landing');
            Cache::forget(self::CACHE_PREFIX.'identity');
            app(AppConfigService::class)->bumpStateVersion();

            return;
        }

        unset($this->memo[$section]);
        Cache::forget(self::CACHE_PREFIX.$section);
        app(AppConfigService::class)->bumpStateVersion();
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
