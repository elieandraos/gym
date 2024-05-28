<?php

use App\Http\Controllers\Admin\UsersController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/users/list/{role}', [UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create/{role}', [UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/users/store', [UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/{role}', [UsersController::class, 'show'])->name('admin.users.show');
});

////$users = Users::with(['memberBookings', 'memberBookings.trainer', 'memberBookings.bookingSlots', 'trainerBookings', 'trainerBookings.member', 'trainerBookings.bookingSlots'])->get();
