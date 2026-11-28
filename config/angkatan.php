<?php

$rawAdminEmails = array_map(
    static fn (string $email): string => strtolower(trim($email)),
    explode(',', (string) env('ADMIN_ALLOWED_EMAILS', '')),
);

$adminAllowedEmails = array_values(array_filter(
    $rawAdminEmails,
    static fn (string $email): bool => $email !== '',
));

$adminPanelPath = trim((string) env('ADMIN_PANEL_PATH', 'admin'), '/');

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Security Configuration
    |--------------------------------------------------------------------------
    |
    | - admin_panel_path: URL path prefix for the Filament admin panel (default: "admin").
    | - admin_allowed_emails: Optional comma-separated allowlist of admin emails
    |   permitted to enter the panel via User::canAccessPanel(). When left empty,
    |   dummy/example emails are still blocked outside local/testing environments.
    |
    */

    'admin_panel_path' => $adminPanelPath !== '' ? $adminPanelPath : 'admin',

    'admin_allowed_emails' => $adminAllowedEmails,

    /*
    |--------------------------------------------------------------------------
    | Angkatan Passcode (bootstrap value)
    |--------------------------------------------------------------------------
    |
    | Fallback/bootstrap passcode read from env, used by seeders and by the
    | public file-manager API guard until `app_config.angkatan_passcode` is
    | populated in the database. In production, prefer rotating the passcode
    | via the DB (app_config) rather than this env value.
    |
    */

    'passcode' => env('ANGKATAN_PASSCODE'),

    /*
    |--------------------------------------------------------------------------
    | Public File Manager
    |--------------------------------------------------------------------------
    |
    | Settings for the passcode-protected public file manager API
    | (upload/list/edit/delete on the "r2" disk), scoped under a dedicated
    | top-level prefix so it never collides with admin-only/private uploads.
    |
    */

    'public_storage' => [
        'disk' => env('R2_DISK', 'r2'),
        'root_prefix' => env('ANGKATAN_PUBLIC_PREFIX', 'public'),
        'max_upload_size_kb' => env('ANGKATAN_PUBLIC_MAX_UPLOAD_KB', 10240), // 10 MB
    ],

];
