<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatIfController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // What-If Routes
    Route::get('/whatif', [WhatIfController::class, 'index'])->name('whatif');

    // Finance Routes
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance');

    // Reward Routes
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');

    # Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    //

    // Community Routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');

    // Friend Routes
    Route::post('/friends/add/{user}', [FriendController::class, 'addFriend'])->name('friends.add');
    Route::delete('/friends/remove/{user}', [FriendController::class, 'removeFriend'])->name('friends.remove');

    // Group Routes
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index'); // View all groups
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store'); // Create a group
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show'); // View specific group
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join'); // Join a group
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave'); // Leave a group
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy'); // Delete a group

    // Search for users
    Route::get('/friends/search', [FriendRequestController::class, 'search'])->name('friends.search');

    // Send a friend request
    Route::post('/friends/request/{user}', [FriendRequestController::class, 'sendRequest'])->name('friends.sendRequest');

    // View received friend requests
    Route::get('/friends/requests', [FriendRequestController::class, 'receivedRequests'])->name('friends.requests');

    // Accept or decline friend requests
    Route::post('/friends/accept/{friendRequest}', [FriendRequestController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/decline/{friendRequest}', [FriendRequestController::class, 'declineRequest'])->name('friends.decline');

    // Search for Friends
    Route::get('/friends/search', [FriendRequestController::class, 'search'])->name('friends.search');

    //Rewards route
    Route::get('/game/{id}', [GameController::class, 'showGame']);


    // Create Message in Group
    Route::post('/groups/{group}/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
});

require __DIR__.'/auth.php';