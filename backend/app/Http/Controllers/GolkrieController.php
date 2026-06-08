<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class GolkrieController extends Controller
{
    protected SupabaseService $sb;

    public function __construct(SupabaseService $sb)
    {
        $this->sb = $sb;
    }

    public function index(Request $request)
    {
        // Upcoming matches
        $upcomingMatches = $this->sb->select('matches', [
            'status'   => 'eq.upcoming',
            'order'    => 'date_time.asc',
            'select'   => '*',
        ]);

        // Finished matches (last 6)
        $matchHistory = $this->sb->select('matches', [
            'status'   => 'eq.finished',
            'order'    => 'date_time.desc',
            'limit'    => 6,
            'select'   => '*',
        ]);

        // Hitung registrations_count per match
        foreach ($upcomingMatches as &$match) {
            $regs = $this->sb->select('registrations', [
                'match_id'    => 'eq.' . $match['id'],
                'is_accepted' => 'eq.true',
                'select'      => 'id',
            ]);
            $match['registrations_count'] = count($regs);
        }

        foreach ($matchHistory as &$match) {
            $regs = $this->sb->select('registrations', [
                'match_id'    => 'eq.' . $match['id'],
                'is_accepted' => 'eq.true',
                'select'      => 'id',
            ]);
            $match['registrations_count'] = count($regs);
        }

        // Squad untuk match aktif
        $activeMatchId = $request->query('match_id') ?? ($upcomingMatches[0]['id'] ?? null);
        $squad = [];
        if ($activeMatchId) {
            $squad = $this->sb->select('registrations', [
                'match_id' => 'eq.' . $activeMatchId,
                'order'    => 'is_accepted.desc,created_at.asc',
                'select'   => '*',
            ]);
        }

        // Settings
        $settingsRaw = $this->sb->select('settings', ['select' => 'key,value']);
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }

        // Sponsors
        $sponsors = $this->sb->select('sponsors', [
            'is_active' => 'eq.true',
            'order'     => 'order.asc',
            'select'    => 'id,name,logo_url',
        ]);

        return response()->json([
            'upcomingMatches' => $upcomingMatches,
            'matchHistory'    => $matchHistory,
            'initialSquad'    => $squad,
            'activeMatchId'   => $activeMatchId,
            'settings'        => $settings,
            'sponsors'        => $sponsors,
        ])->header('Cache-Control', 'public, s-maxage=300, stale-while-revalidate=600');
    }

    public function checkMember(Request $request)
    {
        $name    = $request->input('name');
        $members = $this->sb->select('members', [
            'full_name' => 'eq.' . $name,
            'limit'     => 1,
        ]);
        $member = $members[0] ?? null;

        return response()->json([
            'exists' => (bool) $member,
            'member' => $member,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'match_id'     => 'required|integer',
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'position'     => 'required|in:GK,DF,MF,FW',
        ]);

        // Cek sudah terdaftar
        $existing = $this->sb->select('registrations', [
            'match_id'    => 'eq.' . $request->match_id,
            'player_name' => 'eq.' . $request->full_name,
            'select'      => 'id',
        ]);

        if (!empty($existing)) {
            return response()->json(['message' => 'Kamu sudah terdaftar di match ini!'], 422);
        }

        // Cari atau buat member
        $members = $this->sb->select('members', [
            'full_name' => 'eq.' . $request->full_name,
            'limit'     => 1,
        ]);
        $member = $members[0] ?? null;

        if (!$member) {
            if (!$request->phone_number) {
                return response()->json(['message' => 'Nomor WhatsApp wajib untuk member baru.'], 422);
            }
            $member = $this->sb->insert('members', [
                'full_name'    => $request->full_name,
                'phone_number' => $request->phone_number,
                'created_at'   => now()->toISOString(),
                'updated_at'   => now()->toISOString(),
            ]);
        }

        $this->sb->insert('registrations', [
            'match_id'    => (int) $request->match_id,
            'member_id'   => (int) $member['id'],
            'player_name' => $member['full_name'],
            'position'    => $request->position,
            'is_accepted' => false,
            'is_paid'     => false,
            'created_at'  => now()->toISOString(),
            'updated_at'  => now()->toISOString(),
        ]);

        return response()->json(['message' => 'Pendaftaran berhasil! Menunggu persetujuan admin.']);
    }
}
