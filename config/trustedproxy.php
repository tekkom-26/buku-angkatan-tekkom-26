<?php

declare(strict_types=1);

$rawProxies = array_map(
    trim(...),
    explode(',', (string) env('TRUSTED_PROXIES', '')),
);

$disallowedWildcards = ['*', '**', '0.0.0.0/0', '::/0', 'REMOTE_ADDR'];

$trustedProxies = array_values(array_filter(
    $rawProxies,
    static fn (string $proxy): bool => $proxy !== ''
        && ! in_array(strtoupper($proxy), $disallowedWildcards, true),
));

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Reverse Proxy IPs / CIDRs
    |--------------------------------------------------------------------------
    |
    | Daftar IP atau CIDR reverse proxy resmi yang diizinkan meneruskan header
    | X-Forwarded-*. Kosongkan (default []) untuk menolak semua X-Forwarded-For
    | dari klien. Wildcard ('*', '**', '0.0.0.0/0', '::/0', 'REMOTE_ADDR')
    | ditolak secara eksplisit untuk mencegah IP spoofing (GHSA-cwwq-rxw5-753v).
    |
    */

    'proxies' => $trustedProxies,

];
