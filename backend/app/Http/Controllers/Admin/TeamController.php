<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GolkrieMatch;
use App\Models\Registration;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index($matchId)
    {
        $match = GolkrieMatch::findOrFail($matchId);
        $registrations = Registration::where('match_id', $matchId)
            ->where('is_accepted', true)
            ->get();

        return response()->json([
            'match' => $match,
            'registrations' => $registrations
        ]);
    }

    public function shuffle(Request $request, $matchId)
    {
        $request->validate([
            'team_count' => 'required|integer|min:2|max:6'
        ]);

        $registrations = Registration::where('match_id', $matchId)
            ->where('is_accepted', true)
            ->get()
            ->shuffle();

        $teamCount = $request->team_count;
        $teams = [];
        for ($i = 0; $i < $teamCount; $i++) {
            $teams[] = "Team " . chr(65 + $i); // Team A, B, C...
        }

        foreach ($registrations as $index => $registration) {
            $registration->team_name = $teams[$index % $teamCount];
            $registration->save();
        }

        return response()->json([
            'message' => 'Players shuffled successfully',
            'registrations' => Registration::where('match_id', $matchId)->where('is_accepted', true)->get()
        ]);
    }

    public function updateTeams(Request $request, $matchId)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.id' => 'required|exists:registrations,id',
            'assignments.*.team_name' => 'nullable|string'
        ]);

        foreach ($request->assignments as $assignment) {
            Registration::where('id', $assignment['id'])
                ->where('match_id', $matchId)
                ->update(['team_name' => $assignment['team_name']]);
        }

        return response()->json(['message' => 'Teams updated successfully']);
    }

    public function updateConfig(Request $request, $matchId)
    {
        $request->validate([
            'team_config' => 'required|array'
        ]);

        $match = GolkrieMatch::findOrFail($matchId);
        $match->team_config = $request->team_config;
        $match->save();

        return response()->json([
            'message' => 'Team configuration updated successfully',
            'match' => $match
        ]);
    }
}
