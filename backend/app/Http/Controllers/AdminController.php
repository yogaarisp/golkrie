<?php

namespace App\Http\Controllers;

use App\Models\GolkrieMatch;
use App\Models\Member;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        try {
            return response()->json([
                'stats' => [
                    'totalMembers' => Member::count(),
                    'upcomingMatches' => GolkrieMatch::where('status', 'upcoming')->count(),
                    'finishedMatches' => GolkrieMatch::where('status', 'finished')->count(),
                ],
                'pendingRegistrations' => Registration::where('is_accepted', false)
                    ->with(['match', 'member'])
                    ->orderBy('created_at', 'desc')
                    ->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Dashboard Error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function accept(Registration $registration)
    {
        $registration->update(['is_accepted' => true]);
        return response()->json(['message' => 'Pendaftaran diterima!']);
    }

    public function reject(Registration $registration)
    {
        $registration->delete();
        return response()->json(['message' => 'Pendaftaran ditolak.']);
    }

    public function matches()
    {
        return response()->json([
            'matches' => GolkrieMatch::withCount('registrations')->orderBy('date_time', 'desc')->get()
        ]);
    }

    public function storeMatch(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'match_name' => 'required|string',
            'date_time' => 'required|date',
            'end_time' => 'nullable|date',
            'location' => 'required|string',
            'location_url' => 'nullable|string',
            'quota' => 'required|integer',
            'quota_gk' => 'required|integer',
            'quota_df' => 'required|integer',
            'quota_mf' => 'required|integer',
            'quota_fw' => 'required|integer',
            'price' => 'required|string',
            'price_gk' => 'required|string',
        ]);

        $match = GolkrieMatch::create($validated);
        return response()->json($match);
    }

    public function updateMatch(Request $request, GolkrieMatch $match)
    {
        $validated = $request->validate([
            'title' => 'string',
            'match_name' => 'string',
            'date_time' => 'date',
            'end_time' => 'nullable|date',
            'location' => 'string',
            'location_url' => 'nullable|string',
            'quota' => 'integer',
            'quota_gk' => 'integer',
            'quota_df' => 'integer',
            'quota_mf' => 'integer',
            'quota_fw' => 'integer',
            'price' => 'string',
            'price_gk' => 'string',
            'status' => 'string|in:upcoming,finished'
        ]);

        $match->update($validated);
        return response()->json($match);
    }

    public function deleteMatch(GolkrieMatch $match)
    {
        $match->delete();
        return response()->json(['message' => 'Match dihapus!']);
    }

    public function members()
    {
        return response()->json(['members' => \App\Models\Member::orderBy('full_name')->get()]);
    }

    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);
        $member = \App\Models\Member::create($validated);
        return response()->json(['message' => 'Member ditambahkan!', 'member' => $member]);
    }

    public function updateMember(Request $request, \App\Models\Member $member)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);
        $member->update($validated);
        return response()->json(['message' => 'Member diupdate!']);
    }

    public function deleteMember(\App\Models\Member $member)
    {
        $member->delete();
        return response()->json(['message' => 'Member dihapus!']);
    }

    public function sponsors()
    {
        return response()->json(['sponsors' => \App\Models\Sponsor::orderBy('order')->get()]);
    }

    public function storeSponsor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'required|string',
            'link_url' => 'nullable|string',
            'order' => 'integer'
        ]);
        $sponsor = \App\Models\Sponsor::create($validated);
        return response()->json(['message' => 'Sponsor ditambahkan!', 'sponsor' => $sponsor]);
    }

    public function updateSponsor(Request $request, \App\Models\Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'required|string',
            'link_url' => 'nullable|string',
            'order' => 'integer'
        ]);
        $sponsor->update($validated);
        return response()->json(['message' => 'Sponsor diupdate!']);
    }

    public function deleteSponsor(\App\Models\Sponsor $sponsor)
    {
        $sponsor->delete();
        return response()->json(['message' => 'Sponsor dihapus!']);
    }

    public function getSettings()
    {
        return response()->json([
            'settings' => \App\Models\Setting::all()->pluck('value', 'key')
        ]);
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return response()->json(['message' => 'Settings updated!']);
    }
}
