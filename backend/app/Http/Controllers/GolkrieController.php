<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GolkrieMatch;
use App\Models\Member;
use App\Models\Registration;

class GolkrieController extends Controller
{
    public function index(Request $request)
    {
        $upcomingMatches = GolkrieMatch::where('status', 'upcoming')
            ->withCount(['registrations' => function ($query) {
                $query->where('is_accepted', true);
            }])
            ->orderBy('date_time', 'asc')
            ->get();

        $matchHistory = GolkrieMatch::where('status', 'finished')
            ->withCount(['registrations' => function ($query) {
                $query->where('is_accepted', true);
            }])
            ->orderBy('date_time', 'desc')
            ->limit(6)
            ->get();

        $activeMatchId = $request->query('match_id') ?? $upcomingMatches->first()?->id;
        $squad = [];
        if ($activeMatchId) {
            $squad = Registration::where('match_id', $activeMatchId)
                ->orderBy('is_accepted', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json([
            'upcomingMatches' => $upcomingMatches,
            'matchHistory' => $matchHistory,
            'initialSquad' => $squad,
            'activeMatchId' => $activeMatchId,
            'settings' => \App\Models\Setting::select('key', 'value')->get()->pluck('value', 'key'),
            'sponsors' => \App\Models\Sponsor::where('is_active', true)->select('id', 'name', 'logo_url')->orderBy('order')->get()
        ]);
    }

    public function checkMember(Request $request)
    {
        $name = $request->input('name');
        $member = Member::where('full_name', 'LIKE', $name)->first();

        return response()->json([
            'exists' => !!$member,
            'member' => $member
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'required|in:GK,DF,MF,FW',
        ]);

        $existing = Registration::where('match_id', $request->match_id)
            ->where('player_name', $request->full_name)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah terdaftar di match ini!'], 422);
        }

        $member = Member::where('full_name', 'LIKE', $request->full_name)->first();

        if (!$member) {
            if (!$request->phone_number) {
                return response()->json(['message' => 'Nomor WhatsApp wajib untuk member baru.'], 422);
            }
            $member = Member::create([
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number
            ]);
        }

        Registration::create([
            'match_id' => $request->match_id,
            'member_id' => $member->id,
            'player_name' => $member->full_name,
            'position' => $request->position,
            'is_accepted' => false
        ]);

        return response()->json(['message' => 'Pendaftaran berhasil! Menunggu persetujuan admin.']);
    }
}
