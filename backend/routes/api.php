<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GolkrieController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\TeamController;

// Temporary debug - hapus setelah fix
Route::get('/debug-env', function () {
    $host = env('DB_HOST', '');
    $user = env('DB_USERNAME', '');
    $pass = env('DB_PASSWORD', '');
    
    try {
        $dsn = "pgsql:host=$host;port=" . env('DB_PORT', '5432') . ";dbname=" . env('DB_DATABASE', 'postgres') . ";sslmode=require";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        $dbStatus = 'CONNECTED';
    } catch (\Exception $e) {
        $dbStatus = $e->getMessage();
    }

    return response()->json([
        'db_connection' => env('DB_CONNECTION', 'NOT SET'),
        'db_host' => $host ?: 'NOT SET',
        'db_port' => env('DB_PORT', 'NOT SET'),
        'db_username' => $user ?: 'NOT SET',
        'db_username_len' => strlen($user),
        'db_password_set' => $pass ? 'YES (len:' . strlen($pass) . ')' : 'NOT SET',
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'db_status' => $dbStatus,
        'vercel' => getenv('VERCEL') ?: (isset($_SERVER['VERCEL_URL']) ? 'YES' : 'NO'),
        'php_version' => PHP_VERSION,
    ]);
});

Route::get('/landing', [GolkrieController::class, 'index']);
Route::post('/check-member', [GolkrieController::class, 'checkMember']);
Route::post('/register', [GolkrieController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('supabase.auth')->prefix('admin')->group(function () {
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
    Route::post('/matches/{match}/registrations', [TeamController::class, 'addPlayer']);
    Route::delete('/matches/{match}/registrations/{registration}', [TeamController::class, 'removePlayer']);
});
