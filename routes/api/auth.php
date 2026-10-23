<?php

use App\Http\Controllers\PasscodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Passcode Auth API
|--------------------------------------------------------------------------
|
| Rate limiting for passcode attempts (5 failures / 15 minutes / IP) is
| handled inside PasscodeController@verify.
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/passcode', [PasscodeController::class, 'verify'])->name('auth.passcode.verify');
    Route::delete('/passcode', [PasscodeController::class, 'logout'])->name('auth.passcode.logout');
    Route::get('/session', [PasscodeController::class, 'session'])->name('auth.session');
});
