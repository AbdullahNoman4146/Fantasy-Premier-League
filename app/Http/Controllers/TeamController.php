<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index()
    {
        $teams = collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                t.strength,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))) AS manager_name
            FROM teams t
            LEFT JOIN managers m ON m.person_id = t.manager_id
            LEFT JOIN persons p ON p.person_id = m.person_id
            ORDER BY t.team_name ASC
        "));

        return view('teams', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => ['required', 'string', 'max:255', 'unique:teams,team_name'],
            'strength' => ['nullable', 'integer', 'min:0'],
            'goals_scored' => ['nullable', 'integer', 'min:0'],
            'goals_conceded' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::insert("
            INSERT INTO teams (team_name, strength, goals_scored, goals_conceded, manager_id)
            VALUES (?, ?, ?, ?, NULL)
        ", [
            $request->team_name,
            $request->strength,
            $request->goals_scored ?? 0,
            $request->goals_conceded ?? 0,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Team added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,team_id'],
            'team_name' => ['required', 'string', 'max:255'],
            'strength' => ['nullable', 'integer', 'min:0'],
            'goals_scored' => ['nullable', 'integer', 'min:0'],
            'goals_conceded' => ['nullable', 'integer', 'min:0'],
        ]);

        $duplicate = DB::table('teams')
            ->where('team_name', $request->team_name)
            ->where('team_id', '!=', $request->team_id)
            ->exists();

        if ($duplicate) {
            return redirect()->route('admin.panel')
                ->withErrors(['team_name' => 'Another team already uses this name.'])
                ->withInput();
        }

        DB::update("
            UPDATE teams
            SET team_name = ?, strength = ?, goals_scored = ?, goals_conceded = ?
            WHERE team_id = ?
        ", [
            $request->team_name,
            $request->strength,
            $request->goals_scored ?? 0,
            $request->goals_conceded ?? 0,
            $request->team_id,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Team updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,team_id'],
        ]);

        try {
            DB::delete('DELETE FROM teams WHERE team_id = ?', [$request->team_id]);
        } catch (QueryException $e) {
            return redirect()->route('admin.panel')
                ->withErrors(['team_delete' => 'This team cannot be deleted because it is still linked to other records, most likely matches.'])
                ->withInput();
        }

        return redirect()->route('admin.panel')->with('success', 'Team deleted successfully.');
    }
}