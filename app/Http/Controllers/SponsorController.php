<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SponsorController extends Controller
{
    private function sponsorOptions(): array
    {
        return [
            'Nike',
            'Adidas',
            'Puma',
            'Spotify',
            'Emirates',
            'Etihad Airways',
            'Qatar Airways',
            'AIA',
            'Rakuten',
            'Standard Chartered',
            'Three',
            'Vodafone',
            'Chevrolet',
            'Stake',
            'Castore',
            'Umbro',
            'New Balance',
        ];
    }

    public function index(Request $request)
{
    $search = preg_replace('/\s+/', ' ', trim((string) $request->query('q', ''))) ?? '';

    $sponsors = collect(DB::select("
        SELECT
            s.sponsor_id,
            s.sponsor_name,
            t.team_id,
            t.team_name
        FROM sponsors s
        JOIN teams t ON t.team_id = s.team_id
        WHERE (? = '')
           OR s.sponsor_name LIKE ?
           OR t.team_name LIKE ?
        ORDER BY t.team_name ASC, s.sponsor_name ASC
    ", [
        $search,
        '%' . $search . '%',
        '%' . $search . '%',
    ]));

    return view('sponsors', compact('sponsors', 'search'));
}

    public function admin()
    {
        $teams = collect(DB::select("SELECT team_id, team_name FROM teams ORDER BY team_name ASC"));
        $sponsors = collect(DB::select("
            SELECT s.sponsor_id, s.sponsor_name, t.team_id, t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
            ORDER BY t.team_name ASC, s.sponsor_name ASC
        "));
        $sponsorOptions = $this->sponsorOptions();

        return view('admin.sponsors', compact('teams', 'sponsors', 'sponsorOptions'));
    }

    public function store(Request $request)
    {
        $allowed = $this->sponsorOptions();

        $request->validate([
            'sponsor_name' => 'required|string',
            'team_id' => 'required|integer|exists:teams,team_id',
        ]);

        if (!in_array($request->sponsor_name, $allowed, true)) {
            return redirect()->route('admin.panel')
                ->withErrors(['sponsor_name' => 'Invalid sponsor selected.'])
                ->withInput();
        }

        $exists = DB::table('sponsors')
            ->where('sponsor_name', $request->sponsor_name)
            ->where('team_id', $request->team_id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.panel')
                ->withErrors(['sponsor_name' => 'This sponsor is already assigned to this team.'])
                ->withInput();
        }

        DB::insert('INSERT INTO sponsors (team_id, sponsor_name) VALUES (?, ?)', [
            $request->team_id,
            $request->sponsor_name,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Sponsor assigned successfully.');
    }

    public function update(Request $request)
    {
        $allowed = $this->sponsorOptions();

        $request->validate([
            'sponsor_id' => 'required|integer|exists:sponsors,sponsor_id',
            'sponsor_name' => 'required|string',
            'team_id' => 'required|integer|exists:teams,team_id',
        ]);

        if (!in_array($request->sponsor_name, $allowed, true)) {
            return redirect()->route('admin.panel')
                ->withErrors(['sponsor_name' => 'Invalid sponsor selected.'])
                ->withInput();
        }

        $exists = DB::table('sponsors')
            ->where('sponsor_name', $request->sponsor_name)
            ->where('team_id', $request->team_id)
            ->where('sponsor_id', '!=', $request->sponsor_id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.panel')
                ->withErrors(['sponsor_update' => 'That sponsor assignment already exists.'])
                ->withInput();
        }

        DB::update('UPDATE sponsors SET sponsor_name = ?, team_id = ? WHERE sponsor_id = ?', [
            $request->sponsor_name,
            $request->team_id,
            $request->sponsor_id,
        ]);

        return redirect()->route('admin.panel')->with('success', 'Sponsor updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'sponsor_id' => 'required|integer|exists:sponsors,sponsor_id',
        ]);

        DB::delete('DELETE FROM sponsors WHERE sponsor_id = ?', [$request->sponsor_id]);

        return redirect()->route('admin.panel')->with('success', 'Sponsor deleted successfully.');
    }
}