<?php

return [

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
