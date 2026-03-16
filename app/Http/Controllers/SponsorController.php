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

    // PUBLIC: /sponsors
    public function index()
    {
        $sponsors = collect(DB::select("
            SELECT
                s.sponsor_id,
                s.sponsor_name,
                t.team_id,
                t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
        "));

        return view('sponsors', compact('sponsors'));
    }

    // ADMIN PAGE: /super-admin-fpl-2026/sponsors
    public function admin()
    {
        $teams = collect(DB::select("
            SELECT team_id, team_name
            FROM teams
            ORDER BY team_name ASC
        "));

        $sponsors = collect(DB::select("
            SELECT
                s.sponsor_id,
                s.sponsor_name,
                t.team_id,
                t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
        "));

        $sponsorOptions = $this->sponsorOptions();

        return view('admin.sponsors', compact('teams', 'sponsors', 'sponsorOptions'));
    }

    // ADMIN ACTION: assign sponsor to team (dropdown only)
    public function store(Request $request)
{
    $allowed = $this->sponsorOptions();

    // where to go back after submit (match admin page)
    $redirectTo = $request->input('redirect_to', '/super-admin-fpl-2026');

    $request->validate([
        'sponsor_name' => 'required|string',
        'team_id' => 'required|integer',
    ]);

    if (!in_array($request->sponsor_name, $allowed, true)) {
        return redirect($redirectTo)
            ->withErrors(['sponsor_name' => 'Invalid sponsor selected.'])
            ->withInput();
    }

    $team = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team_id]);
    if (!$team) {
        return redirect($redirectTo)
            ->withErrors(['team_id' => 'Selected team does not exist.'])
            ->withInput();
    }

    $exists = DB::selectOne("
        SELECT sponsor_id
        FROM sponsors
        WHERE sponsor_name = ? AND team_id = ?
    ", [$request->sponsor_name, $request->team_id]);

    if ($exists) {
        return redirect($redirectTo)
            ->withErrors(['sponsor_name' => 'This sponsor is already assigned to this team.'])
            ->withInput();
    }

    DB::insert("
        INSERT INTO sponsors (team_id, sponsor_name)
        VALUES (?, ?)
    ", [$request->team_id, $request->sponsor_name]);

    // show success and stay on admin match page
    return redirect($redirectTo)->with('success', 'Sponsor assigned successfully!');
}
}