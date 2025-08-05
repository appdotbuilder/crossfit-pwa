<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Booking routes
    Route::post('bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::delete('bookings/{booking}', [App\Http\Controllers\BookingController::class, 'destroy'])->name('bookings.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
