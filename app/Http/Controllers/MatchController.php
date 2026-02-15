<?php

namespace App\Http\Controllers;

use App\Models\MatchFixture;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $currentMatches = MatchFixture::where('status', 'current')
            ->orderByDesc('kickoff_at')
            ->limit(2)
            ->get();

        $upcomingFixtures = MatchFixture::where('status', 'upcoming')
            ->orderBy('kickoff_at')
            ->limit(8)
            ->get();
        $finishedMatches = MatchFixture::where('status', 'finished')
           ->orderByDesc('kickoff_at')
          ->limit(6)
          ->get();    

        return view('welcome', compact('currentMatches', 'finishedMatches', 'upcomingFixtures'));
    }

    public function admin()
    {
        $matches = MatchFixture::orderByDesc('kickoff_at')->get();
        return view('admin.match', compact('matches'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'team1' => 'required|string',
            'team2' => 'required|string',
            'score1' => 'nullable|integer',
            'score2' => 'nullable|integer',
            'match_time' => 'nullable|string',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
        ]);

        $match = MatchFixture::findOrFail($request->id);

        $match->update([
            'team1' => $request->team1,
            'team2' => $request->team2,
            'score1' => $request->score1 ?? 0,
            'score2' => $request->score2 ?? 0,
            'match_time' => $request->match_time ?? '',
            'status' => $request->status,
            'kickoff_at' => $request->kickoff_at,
        ]);

        return back()->with('success', 'Match updated successfully!');
    }

    public function create(Request $request)
    {
        $request->validate([
            'team1' => 'required|string',
            'team2' => 'required|string',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
        ]);

        MatchFixture::create([
            'team1' => $request->team1,
            'team2' => $request->team2,
            'score1' => 0,
            'score2' => 0,
            'match_time' => '',
            'status' => $request->status,
            'kickoff_at' => $request->kickoff_at,
        ]);

        return back()->with('success', 'Match created!');
    }
}
