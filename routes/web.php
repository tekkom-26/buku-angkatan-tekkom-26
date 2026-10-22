<?php

use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\PasscodeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index']);
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/students/{nim}', [StudentController::class, 'show'])->name('students.show');
Route::get('/unlock', [PasscodeController::class, 'showForm'])->name('unlock');
