<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\PasscodeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('landing');
Route::view('/identity', 'identity')->name('identity');
Route::middleware(['student.epoch', 'student.role'])->group(function () {
    Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
    Route::get('/students/{nim}', [StudentController::class, 'show'])->name('students.show');
});
Route::get('/unlock', [PasscodeController::class, 'showForm'])->name('unlock');
Route::match(['get', 'post'], '/lock', [PasscodeController::class, 'lock'])->name('lock');
