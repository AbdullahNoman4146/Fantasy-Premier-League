<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    public function index()
    {
        $currentMatches = collect(DB::select("
            SELECT
                m.match_id AS id,
                ta.team_name AS team1,
                tb.team_name AS team2,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2,
                m.match_time,
                m.status,
                m.kickoff_at
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            WHERE m.status = ?
            ORDER BY m.kickoff_at DESC
            LIMIT 2
        ", ['current']));

        $upcomingFixtures = collect(DB::select("
            SELECT
                m.match_id AS id,
                ta.team_name AS team1,
                tb.team_name AS team2,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2,
                m.match_time,
                m.status,
                m.kickoff_at
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            WHERE m.status = ?
            ORDER BY m.kickoff_at ASC
            LIMIT 8
        ", ['upcoming']));

        $finishedMatches = collect(DB::select("
            SELECT
                m.match_id AS id,
                ta.team_name AS team1,
                tb.team_name AS team2,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2,
                m.match_time,
                m.status,
                m.kickoff_at
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            WHERE m.status = ?
            ORDER BY m.kickoff_at DESC
            LIMIT 6
        ", ['finished']));

        return view('welcome', compact('currentMatches', 'finishedMatches', 'upcomingFixtures'));
    }

    public function admin()
    {
        $matches = collect(DB::select("
            SELECT
                m.match_id AS id,
                m.team_a_id AS team1,
                m.team_b_id AS team2,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2,
                m.match_time,
                m.status,
                m.kickoff_at
            FROM matches m
            LEFT JOIN results r ON r.match_id = m.match_id
            ORDER BY m.kickoff_at DESC
        "));

        $teams = collect(DB::select("
            SELECT
                team_id,
                team_name
            FROM teams
            ORDER BY team_name ASC
        "));

        $sponsors = collect(DB::select("
            SELECT
                s.sponsor_id,
                s.sponsor_name,
                t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
            ORDER BY s.sponsor_id DESC
        "));

        $players = collect(DB::select("
            SELECT
                p.team_id,
                p.jersey_number,
                p.position,
                t.team_name,
                pe.first_name,
                pe.last_name,
                pe.nationality
            FROM players p
            JOIN persons pe ON pe.person_id = p.person_id
            JOIN teams t ON t.team_id = p.team_id
            ORDER BY t.team_name ASC, p.jersey_number ASC
        "));

        return view('admin.match', compact('matches', 'teams', 'sponsors', 'players'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'team1' => 'required|integer|different:team2',
            'team2' => 'required|integer',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
        ]);

        $t1 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team1]);
        $t2 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team2]);

        if (!$t1 || !$t2) {
            return redirect('/super-admin-fpl-2026')
                ->withErrors(['team1' => 'Selected teams do not exist in the database.'])
                ->withInput();
        }

        DB::insert("
            INSERT INTO matches (team_a_id, team_b_id, status, kickoff_at, match_time, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->team1,
            $request->team2,
            $request->status,
            $request->kickoff_at,
            ''
        ]);

        $matchId = (int) DB::getPdo()->lastInsertId();

        DB::insert("
            INSERT INTO results (match_id, score_a, score_b, winner_team_id)
            VALUES (?, 0, 0, NULL)
        ", [$matchId]);

        return redirect('/super-admin-fpl-2026')->with('success', 'Match created!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'team1' => 'required|integer|different:team2',
            'team2' => 'required|integer',
            'score1' => 'nullable|integer',
            'score2' => 'nullable|integer',
            'match_time' => 'nullable|string',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
        ]);

        $match = DB::selectOne("SELECT match_id FROM matches WHERE match_id = ?", [$request->id]);
        if (!$match) {
            abort(404);
        }

        $t1 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team1]);
        $t2 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team2]);

        if (!$t1 || !$t2) {
            return redirect('/super-admin-fpl-2026')
                ->withErrors(['team1' => 'Selected teams do not exist in the database.'])
                ->withInput();
        }

        DB::update("
            UPDATE matches
            SET team_a_id = ?,
                team_b_id = ?,
                status = ?,
                kickoff_at = ?,
                match_time = ?,
                updated_at = NOW()
            WHERE match_id = ?
        ", [
            $request->team1,
            $request->team2,
            $request->status,
            $request->kickoff_at,
            $request->match_time ?? '',
            $request->id
        ]);

        DB::insert("
            INSERT INTO results (match_id, score_a, score_b, winner_team_id)
            VALUES (?, ?, ?, NULL)
            ON DUPLICATE KEY UPDATE
                score_a = VALUES(score_a),
                score_b = VALUES(score_b)
        ", [
            $request->id,
            $request->score1 ?? 0,
            $request->score2 ?? 0
        ]);

        return redirect('/super-admin-fpl-2026')->with('success', 'Match updated successfully!');
    }
}