<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatIfController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RewardsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    # What-If Routes
    Route::get('/whatif', [WhatIfController::class, 'index'])->name('whatif');
    //

    #Finance Routes
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance');
    //

    # Reward Routes
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
    //

    # Community Routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community');
    //
});

require __DIR__.'/auth.php';
