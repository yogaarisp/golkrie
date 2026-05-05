<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GolkrieController;

use App\Http\Controllers\AdminController;

Route::get('/', [GolkrieController::class, 'index'])->name('home');
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
