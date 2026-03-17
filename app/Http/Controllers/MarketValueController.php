<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketValueController extends Controller
{
    public function index(Request $request)
{
    $search = preg_replace('/\s+/', ' ', trim((string) $request->query('q', ''))) ?? '';

    $marketValues = collect(DB::select("
        SELECT
            pmv.player_market_value_id,
            pmv.season,
            pmv.market_value,
            pmv.currency,
            pmv.notes,
            pmv.team_id,
            pmv.jersey_number,
            t.team_name,
            p.first_name,
            p.last_name,
            pl.position
        FROM player_market_values pmv
        JOIN players pl ON pl.team_id = pmv.team_id AND pl.jersey_number = pmv.jersey_number
        JOIN persons p ON p.person_id = pl.person_id
        JOIN teams t ON t.team_id = pmv.team_id
        WHERE (? = '')
           OR p.first_name LIKE ?
           OR p.last_name LIKE ?
           OR CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, '')) LIKE ?
           OR t.team_name LIKE ?
           OR COALESCE(pl.position, '') LIKE ?
           OR pmv.season LIKE ?
           OR COALESCE(pmv.notes, '') LIKE ?
        ORDER BY pmv.season DESC, pmv.market_value DESC, t.team_name ASC, pmv.jersey_number ASC
    ", [
        $search,
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
    ]));

    return view('market-values', compact('marketValues', 'search'));
}

    public function store(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'integer'],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99'],
            'season' => ['required', 'string', 'max:20'],
            'market_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $playerExists = DB::table('players')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->exists();

        if (!$playerExists) {
            return redirect()->route('admin.panel')
                ->withErrors(['market_value' => 'Selected player was not found.'])
                ->withInput();
        }

        $exists = DB::table('player_market_values')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->where('season', $request->season)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.panel')
                ->withErrors(['market_value' => 'A market value already exists for that player and season.'])
                ->withInput();
        }

        DB::table('player_market_values')->insert([
            'team_id' => $request->team_id,
            'jersey_number' => $request->jersey_number,
            'season' => $request->season,
            'market_value' => $request->market_value,
            'currency' => strtoupper($request->currency),
            'notes' => $request->notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.panel')->with('success', 'Player market value added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'player_market_value_id' => ['required', 'integer', 'exists:player_market_values,player_market_value_id'],
            'team_id' => ['required', 'integer'],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:99'],
            'season' => ['required', 'string', 'max:20'],
            'market_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $playerExists = DB::table('players')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->exists();

        if (!$playerExists) {
            return redirect()->route('admin.panel')
                ->withErrors(['market_value_update' => 'Selected player was not found.'])
                ->withInput();
        }

        $exists = DB::table('player_market_values')
            ->where('team_id', $request->team_id)
            ->where('jersey_number', $request->jersey_number)
            ->where('season', $request->season)
            ->where('player_market_value_id', '!=', $request->player_market_value_id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.panel')
                ->withErrors(['market_value_update' => 'Another market value already exists for that player and season.'])
                ->withInput();
        }

        DB::table('player_market_values')
            ->where('player_market_value_id', $request->player_market_value_id)
            ->update([
                'team_id' => $request->team_id,
                'jersey_number' => $request->jersey_number,
                'season' => $request->season,
                'market_value' => $request->market_value,
                'currency' => strtoupper($request->currency),
                'notes' => $request->notes,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.panel')->with('success', 'Player market value updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'player_market_value_id' => ['required', 'integer', 'exists:player_market_values,player_market_value_id'],
        ]);

        DB::delete('DELETE FROM player_market_values WHERE player_market_value_id = ?', [$request->player_market_value_id]);

        return redirect()->route('admin.panel')->with('success', 'Player market value deleted successfully.');
    }
}