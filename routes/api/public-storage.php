<?php

use App\Http\Controllers\Api\PublicStorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storage API (Angkatan Passcode protected)
|--------------------------------------------------------------------------
|
| File manager over the "r2" disk scoped under the public root prefix
| (config('angkatan.public_storage.root_prefix')). Guarded by the shared
| Angkatan Passcode middleware — see app/Http/Middleware/EnsureAngkatanPasscode.php.
|
*/

Route::prefix('storage/public')
    ->middleware(['throttle:60,1', 'angkatan.passcode'])
    ->group(function () {
        Route::get('/', [PublicStorageController::class, 'index']);
        Route::post('/directories', [PublicStorageController::class, 'createDirectory']);
        Route::post('/files', [PublicStorageController::class, 'store']);
        Route::patch('/files/{storageFile}', [PublicStorageController::class, 'update']);
        Route::delete('/files/{storageFile}', [PublicStorageController::class, 'destroy']);
    });
