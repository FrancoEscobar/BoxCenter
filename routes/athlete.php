<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Athlete\DashboardController;
use App\Http\Controllers\Athlete\ClassController;
use App\Http\Controllers\Athlete\MembershipController;

Route::middleware(['auth', 'verified', 'role:atleta'])
    ->prefix('athlete')
    ->name('athlete.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('classes', ClassController::class);
        Route::resource('memberships', MembershipController::class);
    });
