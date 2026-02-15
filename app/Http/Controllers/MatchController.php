<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchFixture;

class MatchController extends Controller
{
    // Homepage
    public function index()
    {
        $match = MatchFixture::latest()->first();
        return view('welcome', compact('match'));
    }

    // Admin panel
    public function admin()
    {
        $match = MatchFixture::latest()->first();
        return view('admin.match', compact('match'));
    }

    // Update match
    public function update(Request $request)
    {
        $match = MatchFixture::first();

        if (!$match) {
            $match = new MatchFixture();
        }

        $match->team1 = $request->team1;
        $match->team2 = $request->team2;
        $match->score1 = $request->score1;
        $match->score2 = $request->score2;
        $match->match_time = $request->match_time;
        $match->save();

        return redirect()->back()->with('success', 'Match updated!');
    }
}
