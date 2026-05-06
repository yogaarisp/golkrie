<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GolkrieController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;

Route::get('/landing', [GolkrieController::class, 'index']);
Route::post('/check-member', [GolkrieController::class, 'checkMember']);
Route::post('/register', [GolkrieController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/debug', function() {
    $results = ['php_version' => PHP_VERSION];
    
    // Test 1: Database
    try {
        \DB::connection()->getPdo();
        $results['database'] = 'CONNECTED!';
        $results['match_count'] = \App\Models\GolkrieMatch::count();
        $results['total_members'] = \App\Models\Member::count();
    } catch (\Throwable $e) {
        $results['database'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Test 2: User model + Sanctum
    try {
        $user = \App\Models\User::first();
        $results['user_model'] = $user ? 'OK - ' . $user->email : 'NO USERS';
        $results['user_has_api_tokens'] = method_exists($user, 'createToken') ? 'YES' : 'NO';
    } catch (\Throwable $e) {
        $results['user_model'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Test 3: Personal Access Tokens table
    try {
        $tokenCount = \DB::table('personal_access_tokens')->count();
        $results['tokens_table'] = 'OK - ' . $tokenCount . ' tokens';
    } catch (\Throwable $e) {
        $results['tokens_table'] = 'FAILED: ' . $e->getMessage();
    }

    // Test 4: AdminController direct call
    try {
        $controller = new \App\Http\Controllers\AdminController();
        $results['admin_controller'] = 'LOADED OK';
    } catch (\Throwable $e) {
        $results['admin_controller'] = 'FAILED: ' . $e->getMessage();
    }

    // Test 5: AuthController direct call
    try {
        $controller = new \App\Http\Controllers\AuthController();
        $results['auth_controller'] = 'LOADED OK';
    } catch (\Throwable $e) {
        $results['auth_controller'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Test 6: Session driver
    try {
        $results['session_driver'] = config('session.driver');
        $results['app_key_set'] = !empty(config('app.key')) ? 'YES' : 'NO';
    } catch (\Throwable $e) {
        $results['session_config'] = 'FAILED: ' . $e->getMessage();
    }

    return response()->json($results);
});

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
