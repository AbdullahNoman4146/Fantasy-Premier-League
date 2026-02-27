<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    private function getOrCreateTeamId(string $teamName): int
    {
        $teamName = trim($teamName);

        $team = DB::selectOne("SELECT team_id FROM teams WHERE team_name = ?", [$teamName]);

        if ($team) {
            return (int) $team->team_id;
        }

        DB::insert("INSERT INTO teams (team_name) VALUES (?)", [$teamName]);

        return (int) DB::getPdo()->lastInsertId();
    }

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
            ORDER BY m.kickoff_at DESC
        "));

        return view('admin.match', compact('matches'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'team1' => 'required|string',
            'team2' => 'required|string',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
        ]);

        $teamAId = $this->getOrCreateTeamId($request->team1);
        $teamBId = $this->getOrCreateTeamId($request->team2);

        DB::insert("
            INSERT INTO matches (team_a_id, team_b_id, status, kickoff_at, match_time, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $teamAId,
            $teamBId,
            $request->status,
            $request->kickoff_at,
            ''
        ]);

        $matchId = (int) DB::getPdo()->lastInsertId();

        // Create default result row (scores start at 0)
        DB::insert("
            INSERT INTO results (match_id, score_a, score_b, winner_team_id)
            VALUES (?, 0, 0, NULL)
        ", [$matchId]);

        return back()->with('success', 'Match created!');
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

        $match = DB::selectOne("SELECT match_id FROM matches WHERE match_id = ?", [$request->id]);
        if (!$match) {
            abort(404);
        }

        $teamAId = $this->getOrCreateTeamId($request->team1);
        $teamBId = $this->getOrCreateTeamId($request->team2);

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
            $teamAId,
            $teamBId,
            $request->status,
            $request->kickoff_at,
            $request->match_time ?? '',
            $request->id
        ]);

        $scoreA = $request->score1 ?? 0;
        $scoreB = $request->score2 ?? 0;

        // Upsert result using ON DUPLICATE KEY UPDATE (raw SQL)
        DB::insert("
            INSERT INTO results (match_id, score_a, score_b, winner_team_id)
            VALUES (?, ?, ?, NULL)
            ON DUPLICATE KEY UPDATE
                score_a = VALUES(score_a),
                score_b = VALUES(score_b)
        ", [
            $request->id,
            $scoreA,
            $scoreB
        ]);

        return back()->with('success', 'Match updated successfully!');
    }
}