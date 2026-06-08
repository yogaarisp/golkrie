<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SupabaseService;

class SupabaseAuth
{
    protected SupabaseService $sb;

    public function __construct(SupabaseService $sb)
    {
        $this->sb = $sb;
    }

    public function handle(Request $request, Closure $next)
    {
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

        return $next($request);
    }
}
