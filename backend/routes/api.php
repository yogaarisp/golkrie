<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GolkrieController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\TeamController;

// Temporary debug - hapus setelah fix
Route::get('/debug-env', function () {
    try {
        $pdo = new PDO(
            'pgsql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        $dbStatus = 'CONNECTED';
    } catch (\Exception $e) {
        $dbStatus = 'ERROR: ' . $e->getMessage();
    }

    return response()->json([
        'db_connection' => env('DB_CONNECTION', 'NOT SET'),
        'db_host' => env('DB_HOST') ? 'SET' : 'NOT SET',
        'db_username' => env('DB_USERNAME') ? 'SET' : 'NOT SET',
        'db_password' => env('DB_PASSWORD') ? 'SET' : 'NOT SET',
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'db_status' => $dbStatus,
        'vercel' => isset($_SERVER['VERCEL']) ? 'YES' : 'NO',
        'php_version' => PHP_VERSION,
    ]);
});

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

    // Team Management
    Route::get('/matches/{match}/teams', [TeamController::class, 'index']);
    Route::post('/matches/{match}/shuffle', [TeamController::class, 'shuffle']);
    Route::post('/matches/{match}/teams', [TeamController::class, 'updateTeams']);
    Route::post('/matches/{match}/teams/config', [TeamController::class, 'updateConfig']);
});
