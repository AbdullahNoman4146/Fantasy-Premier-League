<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    // Public page: /teams
   public function index()
{
    $teams = collect(DB::select("
        SELECT team_id, team_name, strength, goals_scored, goals_conceded, manager_id
        FROM teams
        ORDER BY team_id ASC
    "));

    return view('teams', compact('teams'));
}

    // Admin action: add a team
public function store(Request $request)
{
    $request->validate([
        'team_name' => 'required|string|max:255',
        'strength' => 'nullable|integer',
        'goals_scored' => 'nullable|integer',
        'goals_conceded' => 'nullable|integer',
        'manager_id' => 'nullable|integer',
    ]);

    DB::insert("
        INSERT INTO teams (team_name, strength, goals_scored, goals_conceded, manager_id)
        VALUES (?, ?, ?, ?, ?)
    ", [
        $request->team_name,
        $request->strength,
        $request->goals_scored,
        $request->goals_conceded,
        $request->manager_id
    ]);

    // IMPORTANT: redirect to a REAL GET route, not back()
    return redirect('/super-admin-fpl-2026')->with('success', 'Team added successfully!');
}
}