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
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'team_id'       => ['required', 'integer', 'exists:teams,team_id'],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99'],
            'nationality'   => ['required', 'string', 'max:100'],
            'position'      => ['required', 'string', 'max:50'],
        ]);

        $jerseyExists = DB::table('players')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->exists();

        if ($jerseyExists) {
            return back()
                ->withErrors([
                    'jersey_number' => 'This jersey number is already used in the selected team.'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $personId = DB::table('persons')->insertGetId([
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'nationality' => $request->nationality,
                'attribute'   => null,
            ]);

            DB::table('players')->insert([
                'team_id'       => $request->team_id,
                'jersey_number' => $request->jersey_number,
                'person_id'     => $personId,
                'position'      => $request->position,
            ]);
        });

        return redirect()
            ->to($request->input('redirect_to', route('admin.panel')))
            ->with('success', 'Player added successfully.');
    }
}