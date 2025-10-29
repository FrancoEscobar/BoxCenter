<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Coach\DashboardController;
use App\Http\Controllers\Coach\ClassController;
use App\Http\Controllers\Coach\WodController;

Route::middleware(['auth', 'verified'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('classes', ClassController::class);
        Route::resource('wods', WodController::class);
    });
