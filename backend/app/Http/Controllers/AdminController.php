<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class AdminController extends Controller
{
    protected SupabaseService $sb;

    public function __construct(SupabaseService $sb)
    {
        $this->sb = $sb;
    }

    public function index()
    {
        $totalMembers    = $this->sb->count('members');
        $upcomingMatches = $this->sb->count('matches', ['status' => 'eq.upcoming']);
        $finishedMatches = $this->sb->count('matches', ['status' => 'eq.finished']);

        $pendingRegs = $this->sb->select('registrations', [
            'is_accepted' => 'eq.false',
            'order'       => 'created_at.desc',
            'select'      => '*',
        ]);

        // Attach match & member data
        foreach ($pendingRegs as &$reg) {
            $matches = $this->sb->select('matches', ['id' => 'eq.' . $reg['match_id'], 'limit' => 1]);
            $members = $this->sb->select('members', ['id' => 'eq.' . $reg['member_id'], 'limit' => 1]);
            $reg['match']  = $matches[0] ?? null;
            $reg['member'] = $members[0] ?? null;
        }

        return response()->json([
            'stats' => [
                'totalMembers'    => $totalMembers,
                'upcomingMatches' => $upcomingMatches,
                'finishedMatches' => $finishedMatches,
            ],
            'pendingRegistrations' => $pendingRegs,
        ]);
    }

    public function accept(Request $request, $registration)
    {
        $this->sb->update('registrations', ['id' => $registration], [
            'is_accepted' => true,
            'updated_at'  => now()->toISOString(),
        ]);
        return response()->json(['message' => 'Pendaftaran diterima!']);
    }

    public function reject(Request $request, $registration)
    {
        $this->sb->delete('registrations', ['id' => $registration]);
        return response()->json(['message' => 'Pendaftaran ditolak.']);
    }

    public function matches()
    {
        $matches = $this->sb->select('matches', [
            'order'  => 'date_time.desc',
            'select' => '*',
        ]);

        foreach ($matches as &$match) {
            $regs = $this->sb->select('registrations', [
                'match_id' => 'eq.' . $match['id'],
                'select'   => 'id',
            ]);
            $match['registrations_count'] = count($regs);
        }

        return response()->json(['matches' => $matches]);
    }

    public function storeMatch(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string',
            'match_name'   => 'required|string',
            'date_time'    => 'required|date',
            'end_time'     => 'nullable|date',
            'location'     => 'required|string',
            'location_url' => 'nullable|string',
            'quota'        => 'required|integer',
            'quota_gk'     => 'required|integer',
            'quota_df'     => 'required|integer',
            'quota_mf'     => 'required|integer',
            'quota_fw'     => 'required|integer',
            'price'        => 'required|string',
            'price_gk'     => 'required|string',
        ]);

        $validated['status']     = 'upcoming';
        $validated['created_at'] = now()->toISOString();
        $validated['updated_at'] = now()->toISOString();

        $match = $this->sb->insert('matches', $validated);
        return response()->json($match);
    }

    public function updateMatch(Request $request, $match)
    {
        $validated = $request->only([
            'title', 'match_name', 'date_time', 'end_time', 'location',
            'location_url', 'quota', 'quota_gk', 'quota_df', 'quota_mf',
            'quota_fw', 'price', 'price_gk', 'status', 'facilities', 'media_url'
        ]);
        $validated['updated_at'] = now()->toISOString();

        $result = $this->sb->update('matches', ['id' => $match], $validated);
        return response()->json($result[0] ?? ['message' => 'Updated']);
    }

    public function deleteMatch($match)
    {
        $this->sb->delete('matches', ['id' => $match]);
        return response()->json(['message' => 'Match dihapus!']);
    }

    public function members()
    {
        $members = $this->sb->select('members', ['order' => 'full_name.asc']);
        return response()->json(['members' => $members]);
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);
        $validated['created_at'] = now()->toISOString();
        $validated['updated_at'] = now()->toISOString();

        $member = $this->sb->insert('members', $validated);
        return response()->json(['message' => 'Member ditambahkan!', 'member' => $member]);
    }

    public function updateMember(Request $request, $member)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);
        $validated['updated_at'] = now()->toISOString();

        $this->sb->update('members', ['id' => $member], $validated);
        return response()->json(['message' => 'Member diupdate!']);
    }

    public function deleteMember($member)
    {
        $this->sb->delete('members', ['id' => $member]);
        return response()->json(['message' => 'Member dihapus!']);
    }

    public function sponsors()
    {
        $sponsors = $this->sb->select('sponsors', ['order' => 'order.asc']);
        return response()->json(['sponsors' => $sponsors]);
    }

    public function storeSponsor(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'logo_url' => 'required|string',
            'link_url' => 'nullable|string',
            'order'    => 'integer',
        ]);
        $validated['is_active']  = true;
        $validated['created_at'] = now()->toISOString();
        $validated['updated_at'] = now()->toISOString();

        $sponsor = $this->sb->insert('sponsors', $validated);
        return response()->json(['message' => 'Sponsor ditambahkan!', 'sponsor' => $sponsor]);
    }

    public function updateSponsor(Request $request, $sponsor)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'logo_url'  => 'required|string',
            'link_url'  => 'nullable|string',
            'order'     => 'integer',
            'is_active' => 'boolean',
        ]);
        $validated['updated_at'] = now()->toISOString();

        $this->sb->update('sponsors', ['id' => $sponsor], $validated);
        return response()->json(['message' => 'Sponsor diupdate!']);
    }

    public function deleteSponsor($sponsor)
    {
        $this->sb->delete('sponsors', ['id' => $sponsor]);
        return response()->json(['message' => 'Sponsor dihapus!']);
    }

    public function getSettings()
    {
        $settingsRaw = $this->sb->select('settings', ['select' => 'key,value']);
        $settings    = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['key']] = $s['value'];
        }
        return response()->json(['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $this->sb->upsert('settings', [
                'key'        => $key,
                'value'      => $value,
                'updated_at' => now()->toISOString(),
            ], 'key');
        }
        return response()->json(['message' => 'Settings updated!']);
    }
}
