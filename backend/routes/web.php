<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GolkrieController;

use App\Http\Controllers\AdminController;

Route::get('/', [GolkrieController::class, 'index'])->name('home');

// Temporary debug route - hapus setelah fix
Route::get('/debug-env', function () {
    return response()->json([
        'db_connection' => env('DB_CONNECTION'),
        'db_host' => env('DB_HOST') ? 'SET (' . substr(env('DB_HOST'), 0, 10) . '...)' : 'NOT SET',
        'db_username' => env('DB_USERNAME') ? 'SET' : 'NOT SET',
        'db_password' => env('DB_PASSWORD') ? 'SET' : 'NOT SET',
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'vercel' => isset($_SERVER['VERCEL']) ? 'YES' : 'NO',
        'php_version' => PHP_VERSION,
    ]);
});
Route::post('/check-member', [GolkrieController::class, 'checkMember'])->name('check-member');
Route::post('/register', [GolkrieController::class, 'register'])->name('register');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/registrations/{registration}/accept', [AdminController::class, 'accept'])->name('registrations.accept');
    Route::delete('/registrations/{registration}/reject', [AdminController::class, 'reject'])->name('registrations.reject');
    
    Route::get('/matches', [AdminController::class, 'matches'])->name('matches');
    Route::post('/matches', [AdminController::class, 'storeMatch'])->name('matches.store');
    Route::patch('/matches/{match}', [AdminController::class, 'updateMatch'])->name('matches.update');
    Route::delete('/matches/{match}', [AdminController::class, 'deleteMatch'])->name('matches.delete');
});
