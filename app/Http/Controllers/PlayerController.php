<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function index()
    {
        $players = DB::table('players as p')
            ->join('persons as pe', 'pe.person_id', '=', 'p.person_id')
            ->join('teams as t', 't.team_id', '=', 'p.team_id')
            ->select(
                'p.team_id',
                'p.jersey_number',
                'p.person_id',
                'p.position',
                't.team_name',
                'pe.first_name',
                'pe.last_name',
                'pe.nationality'
            )
            ->orderBy('t.team_name')
            ->orderBy('p.jersey_number')
            ->get();

        return view('players', compact('players'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'team_id' => ['required', 'integer', 'exists:teams,team_id'],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99'],
            'nationality' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:50'],
        ]);

        $jerseyExists = DB::table('players')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->exists();

        if ($jerseyExists) {
            return redirect()->route('admin.panel')
                ->withErrors(['jersey_number' => 'This jersey number is already used in the selected team.'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $personId = DB::table('persons')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'nationality' => $request->nationality,
                'attribute' => null,
            ]);

            DB::table('players')->insert([
                'team_id' => $request->team_id,
                'jersey_number' => $request->jersey_number,
                'person_id' => $personId,
                'position' => $request->position,
            ]);
        });

        return redirect()->route('admin.panel')->with('success', 'Player added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'person_id' => ['required', 'integer', 'exists:persons,person_id'],
            'old_team_id' => ['required', 'integer'],
            'old_jersey_number' => ['required', 'integer'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'team_id' => ['required', 'integer', 'exists:teams,team_id'],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99'],
            'nationality' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:50'],
        ]);

        $targetExists = DB::table('players')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->where(function ($query) use ($request) {
                $query->where('team_id', '!=', $request->old_team_id)
                    ->orWhere('jersey_number', '!=', $request->old_jersey_number);
            })
            ->exists();

        if ($targetExists) {
            return redirect()->route('admin.panel')
                ->withErrors(['player_update' => 'Another player already uses that jersey number in the selected team.'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            DB::update("
                UPDATE persons
                SET first_name = ?, last_name = ?, nationality = ?
                WHERE person_id = ?
            ", [
                $request->first_name,
                $request->last_name,
                $request->nationality,
                $request->person_id,
            ]);

            DB::update("
                UPDATE players
                SET team_id = ?, jersey_number = ?, position = ?
                WHERE team_id = ? AND jersey_number = ? AND person_id = ?
            ", [
                $request->team_id,
                $request->jersey_number,
                $request->position,
                $request->old_team_id,
                $request->old_jersey_number,
                $request->person_id,
            ]);
        });

        return redirect()->route('admin.panel')->with('success', 'Player updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'person_id' => ['required', 'integer', 'exists:persons,person_id'],
        ]);

        DB::transaction(function () use ($request) {
            DB::delete('DELETE FROM persons WHERE person_id = ?', [$request->person_id]);
        });

        return redirect()->route('admin.panel')->with('success', 'Player deleted successfully.');
    }
}