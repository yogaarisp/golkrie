<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GolkrieController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;

Route::get('/landing', [GolkrieController::class, 'index']);
Route::post('/check-member', [GolkrieController::class, 'checkMember']);
Route::post('/register', [GolkrieController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::post('/upload', [UploadController::class, 'upload']);
    Route::get('/dashboard', [AdminController::class, 'index']);
    Route::post('/registrations/{registration}/accept', [AdminController::class, 'accept']);
    Route::delete('/registrations/{registration}/reject', [AdminController::class, 'reject']);
    
    Route::get('/matches', [AdminController::class, 'matches']);
    Route::post('/matches', [AdminController::class, 'storeMatch']);
    Route::patch('/matches/{match}', [AdminController::class, 'updateMatch']);
    Route::delete('/matches/{match}', [AdminController::class, 'deleteMatch']);

    Route::get('/members', [AdminController::class, 'members']);
    Route::post('/members', [AdminController::class, 'storeMember']);
    Route::patch('/members/{member}', [AdminController::class, 'updateMember']);
    Route::delete('/members/{member}', [AdminController::class, 'deleteMember']);

    Route::get('/sponsors', [AdminController::class, 'sponsors']);
    Route::post('/sponsors', [AdminController::class, 'storeSponsor']);
    Route::patch('/sponsors/{sponsor}', [AdminController::class, 'updateSponsor']);
    Route::delete('/sponsors/{sponsor}', [AdminController::class, 'deleteSponsor']);

    Route::get('/settings', [AdminController::class, 'getSettings']);
    Route::post('/settings', [AdminController::class, 'updateSettings']);
});
