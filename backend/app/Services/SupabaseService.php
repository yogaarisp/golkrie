<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Supabase REST API Service
 * Menggantikan koneksi PostgreSQL langsung dengan HTTP requests
 */
class SupabaseService
{
    protected string $url;
    protected string $key;
    protected string $base;

    public function __construct()
    {
        $this->url  = rtrim(env('SUPABASE_URL'), '/');
        $this->key  = env('SUPABASE_KEY');
        $this->base = $this->url . '/rest/v1';
    }

    protected function headers(bool $serviceRole = false): array
    {
        $key = $serviceRole
            ? (env('SUPABASE_SERVICE_ROLE_KEY') ?: $this->key)
            : $this->key;

        return [
            'apikey'        => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type'  => 'application/json',
            'Prefer'        => 'return=representation',
        ];
    }

    // ─── SELECT ──────────────────────────────────────────────
    public function select(string $table, array $params = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->base}/{$table}", $params);

        return $response->json() ?? [];
    }

    // ─── INSERT ──────────────────────────────────────────────
    public function insert(string $table, array $data): array|null
    {
        $response = Http::withHeaders($this->headers(true))
            ->post("{$this->base}/{$table}", $data);

        $result = $response->json();
        return is_array($result) ? ($result[0] ?? $result) : null;
    }

    // ─── UPDATE ──────────────────────────────────────────────
    public function update(string $table, array $filters, array $data): array
    {
        $response = Http::withHeaders($this->headers(true))
            ->patch("{$this->base}/{$table}?" . $this->buildQuery($filters), $data);

        return $response->json() ?? [];
    }

    // ─── DELETE ──────────────────────────────────────────────
    public function delete(string $table, array $filters): bool
    {
        $response = Http::withHeaders(array_merge($this->headers(true), ['Prefer' => 'return=minimal']))
            ->delete("{$this->base}/{$table}?" . $this->buildQuery($filters));

        return $response->successful();
    }

    // ─── UPSERT ──────────────────────────────────────────────
    public function upsert(string $table, array $data, string $onConflict = 'id'): array|null
    {
        $response = Http::withHeaders(array_merge($this->headers(true), [
            'Prefer' => 'return=representation,resolution=merge-duplicates',
        ]))->post("{$this->base}/{$table}?on_conflict={$onConflict}", $data);

        $result = $response->json();
        return is_array($result) ? ($result[0] ?? $result) : null;
    }

    // ─── COUNT ───────────────────────────────────────────────
    public function count(string $table, array $params = []): int
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'count=exact',
        ]))->get("{$this->base}/{$table}", array_merge($params, ['select' => 'id']));

        $range = $response->header('Content-Range');
        if ($range && str_contains($range, '/')) {
            return (int) explode('/', $range)[1];
        }
        return count($response->json() ?? []);
    }

    // ─── HELPER ──────────────────────────────────────────────
    protected function buildQuery(array $filters): string
    {
        $parts = [];
        foreach ($filters as $key => $value) {
            $parts[] = urlencode($key) . '=eq.' . urlencode($value);
        }
        return implode('&', $parts);
    }
}
