<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected SupabaseService $sb;

    public function __construct(SupabaseService $sb)
    {
        $this->sb = $sb;
    }

    public function index($matchId)
    {
        $matches = $this->sb->select('matches', ['id' => 'eq.' . $matchId, 'limit' => 1]);
        $match   = $matches[0] ?? null;

        if (!$match) {
            return response()->json(['message' => 'Match not found'], 404);
        }

        $registrations = $this->sb->select('registrations', [
            'match_id'    => 'eq.' . $matchId,
            'is_accepted' => 'eq.true',
            'select'      => '*',
        ]);

        return response()->json([
            'match'         => $match,
            'registrations' => $registrations,
        ]);
    }

    public function shuffle(Request $request, $matchId)
    {
        $request->validate([
            'team_count' => 'required|integer|min:2|max:6',
        ]);

        $registrations = $this->sb->select('registrations', [
            'match_id'    => 'eq.' . $matchId,
            'is_accepted' => 'eq.true',
            'select'      => '*',
        ]);

        shuffle($registrations);

        $teamCount = $request->team_count;
        $teams     = [];
        for ($i = 0; $i < $teamCount; $i++) {
            $teams[] = 'Team ' . chr(65 + $i);
        }

        foreach ($registrations as $index => $reg) {
            $this->sb->update('registrations', ['id' => $reg['id']], [
                'team_name'  => $teams[$index % $teamCount],
                'updated_at' => now()->toISOString(),
            ]);
        }

        $updated = $this->sb->select('registrations', [
            'match_id'    => 'eq.' . $matchId,
            'is_accepted' => 'eq.true',
            'select'      => '*',
        ]);

        return response()->json([
            'message'       => 'Players shuffled successfully',
            'registrations' => $updated,
        ]);
    }

    public function updateTeams(Request $request, $matchId)
    {
        $request->validate([
            'assignments'            => 'required|array',
            'assignments.*.id'       => 'required|integer',
            'assignments.*.team_name' => 'nullable|string',
        ]);

        foreach ($request->assignments as $assignment) {
            $this->sb->update('registrations',
                ['id' => $assignment['id'], 'match_id' => $matchId],
                ['team_name' => $assignment['team_name'], 'updated_at' => now()->toISOString()]
            );
        }

        return response()->json(['message' => 'Teams updated successfully']);
    }

    public function updateConfig(Request $request, $matchId)
    {
        $request->validate(['team_config' => 'required|array']);

        $result = $this->sb->update('matches', ['id' => $matchId], [
            'team_config' => json_encode($request->team_config),
            'updated_at'  => now()->toISOString(),
        ]);

        return response()->json([
            'message' => 'Team configuration updated successfully',
            'match'   => $result[0] ?? null,
        ]);
    }

    public function addPlayer(Request $request, $matchId)
    {
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'position'     => 'required|in:GK,DF,MF,FW',
        ]);

        // Cari atau buat member
        $members = $this->sb->select('members', [
            'full_name' => 'eq.' . $request->full_name,
            'limit'     => 1,
        ]);
        $member = $members[0] ?? null;

        if (!$member) {
            $member = $this->sb->insert('members', [
                'full_name'    => $request->full_name,
                'phone_number' => $request->phone_number ?? '0000000000',
                'created_at'   => now()->toISOString(),
                'updated_at'   => now()->toISOString(),
            ]);
        }

        // Cek sudah terdaftar
        $existing = $this->sb->select('registrations', [
            'match_id'    => 'eq.' . $matchId,
            'member_id'   => 'eq.' . $member['id'],
            'select'      => 'id',
        ]);

        if (!empty($existing)) {
            return response()->json(['message' => 'Pemain sudah terdaftar di match ini!'], 422);
        }

        $registration = $this->sb->insert('registrations', [
            'match_id'    => (int) $matchId,
            'member_id'   => (int) $member['id'],
            'player_name' => $member['full_name'],
            'position'    => $request->position,
            'is_accepted' => true,
            'is_paid'     => false,
            'created_at'  => now()->toISOString(),
            'updated_at'  => now()->toISOString(),
        ]);

        return response()->json([
            'message'      => 'Pemain berhasil ditambahkan!',
            'registration' => $registration,
        ]);
    }

    public function removePlayer($matchId, $registrationId)
    {
        $this->sb->delete('registrations', ['id' => $registrationId, 'match_id' => $matchId]);
        return response()->json(['message' => 'Pemain dihapus dari match.']);
    }
}
