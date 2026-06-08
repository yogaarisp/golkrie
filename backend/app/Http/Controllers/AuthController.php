<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\SupabaseService;

class AuthController extends Controller
{
    protected SupabaseService $sb;

    public function __construct(SupabaseService $sb)
    {
        $this->sb = $sb;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cari user di Supabase
        $users = $this->sb->select('users', [
            'email'  => 'eq.' . $request->email,
            'limit'  => 1,
            'select' => '*',
        ]);

        $user = $users[0] ?? null;

        if (!$user || !Hash::check($request->password, $user['password'])) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 422);
        }

        // Buat token manual (simpan di Supabase personal_access_tokens)
        $token = Str::random(64);
        $hashedToken = hash('sha256', $token);

        $this->sb->insert('personal_access_tokens', [
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id'   => (int) $user['id'],
            'name'           => 'auth_token',
            'token'          => $hashedToken,
            'abilities'      => '["*"]',
            'created_at'     => now()->toISOString(),
            'updated_at'     => now()->toISOString(),
        ]);

        return response()->json([
            'access_token' => $user['id'] . '|' . $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token dari Supabase
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $rawToken = substr($authHeader, 7);
            if (str_contains($rawToken, '|')) {
                [, $token] = explode('|', $rawToken, 2);
                $hashedToken = hash('sha256', $token);
                $this->sb->delete('personal_access_tokens', ['token' => $hashedToken]);
            }
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        // Ambil user dari token
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $rawToken = substr($authHeader, 7);
        if (!str_contains($rawToken, '|')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        [$userId, $token] = explode('|', $rawToken, 2);
        $hashedToken = hash('sha256', $token);

        $tokens = $this->sb->select('personal_access_tokens', [
            'token'          => 'eq.' . $hashedToken,
            'tokenable_id'   => 'eq.' . $userId,
            'tokenable_type' => 'eq.App\\Models\\User',
            'limit'          => 1,
        ]);

        if (empty($tokens)) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $users = $this->sb->select('users', [
            'id'     => 'eq.' . $userId,
            'limit'  => 1,
            'select' => 'id,name,email,role',
        ]);

        return response()->json($users[0] ?? null);
    }
}
