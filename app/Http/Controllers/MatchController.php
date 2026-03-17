<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    public function index()
    {
        $currentMatches = $this->getMatchesByStatus('current', 'm.kickoff_at DESC', 2);
        $upcomingFixtures = $this->getMatchesByStatus('upcoming', 'm.kickoff_at ASC', 8);
        $finishedMatches = $this->getMatchesByStatus('finished', 'm.kickoff_at DESC', 6);

        return view('welcome', compact('currentMatches', 'finishedMatches', 'upcomingFixtures'));
    }

    public function fixtures()
    {
        $fixtures = $this->getMatchesByStatus('upcoming', 'm.kickoff_at ASC');

        return view('matches', compact('fixtures'));
    }

    public function results()
    {
        $results = $this->getMatchesByStatus('finished', 'm.kickoff_at DESC');

        return view('results', compact('results'));
    }

    public function standings()
    {
        $standings = $this->getStandingsTable();

        return view('standings', compact('standings'));
    }

    public function admin()
    {
        $teams = collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                t.strength,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                t.manager_id,
                TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))) AS manager_name
            FROM teams t
            LEFT JOIN managers m ON m.person_id = t.manager_id
            LEFT JOIN persons p ON p.person_id = m.person_id
            ORDER BY t.team_name ASC
        "));

        $players = collect(DB::select("
            SELECT
                p.team_id,
                p.jersey_number,
                p.person_id,
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

        $managers = collect(DB::select("
            SELECT
                m.person_id,
                m.team_id,
                m.experience_years,
                p.first_name,
                p.last_name,
                p.nationality,
                t.team_name
            FROM managers m
            JOIN persons p ON p.person_id = m.person_id
            LEFT JOIN teams t ON t.team_id = m.team_id
            ORDER BY t.team_name ASC, p.first_name ASC, p.last_name ASC
        "));

        $playerMarketValues = collect(DB::select("
            SELECT
                pmv.player_market_value_id,
                pmv.team_id,
                pmv.jersey_number,
                pmv.season,
                pmv.market_value,
                pmv.currency,
                pmv.notes,
                t.team_name,
                pe.first_name,
                pe.last_name
            FROM player_market_values pmv
            JOIN players pl ON pl.team_id = pmv.team_id AND pl.jersey_number = pmv.jersey_number
            JOIN persons pe ON pe.person_id = pl.person_id
            JOIN teams t ON t.team_id = pmv.team_id
            ORDER BY pmv.season DESC, pmv.market_value DESC, t.team_name ASC
        "));

        $transferPosts = collect(DB::select("
            SELECT
                transfer_post_id,
                title,
                summary,
                content,
                status,
                posted_at,
                created_at,
                updated_at
            FROM transfer_posts
            ORDER BY COALESCE(posted_at, created_at) DESC, transfer_post_id DESC
        "));

        $sponsors = collect(DB::select("
            SELECT
                s.sponsor_id,
                s.sponsor_name,
                s.team_id,
                t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
            ORDER BY t.team_name ASC, s.sponsor_name ASC
        "));

        $matches = collect(DB::select("
            SELECT
                m.match_id AS id,
                m.team_a_id AS team1_id,
                m.team_b_id AS team2_id,
                ta.team_name AS team1_name,
                tb.team_name AS team2_name,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2,
                m.match_time,
                m.status,
                m.kickoff_at
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            ORDER BY
                CASE m.status
                    WHEN 'current' THEN 1
                    WHEN 'upcoming' THEN 2
                    WHEN 'finished' THEN 3
                    ELSE 4
                END,
                m.kickoff_at DESC,
                m.match_id DESC
        "));

        $standings = $this->getStandingsTable();

        $matchStats = collect(DB::select("SELECT status, COUNT(*) AS total FROM matches GROUP BY status"))->pluck('total', 'status');

        $playerOptions = $players->map(function ($player) {
            $fullName = trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? ''));

            return (object) [
                'team_id' => $player->team_id,
                'jersey_number' => $player->jersey_number,
                'team_name' => $player->team_name,
                'player_name' => $fullName !== '' ? $fullName : 'Unknown Player',
            ];
        });

        $playerPositions = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];
        $sponsorOptions = [
            'Nike', 'Adidas', 'Puma', 'Spotify', 'Emirates', 'Etihad Airways', 'Qatar Airways',
            'AIA', 'Rakuten', 'Standard Chartered', 'Three', 'Vodafone', 'Chevrolet',
            'Stake', 'Castore', 'Umbro', 'New Balance'
        ];

        return view('admin.match', compact(
            'teams',
            'players',
            'managers',
            'playerMarketValues',
            'transferPosts',
            'sponsors',
            'matches',
            'standings',
            'matchStats',
            'playerOptions',
            'playerPositions',
            'sponsorOptions'
        ));
    }

    public function create(Request $request)
    {
        $request->validate([
            'team1' => 'required|integer|different:team2',
            'team2' => 'required|integer',
            'status' => 'required|in:current,upcoming,finished',
            'kickoff_at' => 'nullable|date',
            'score1' => 'nullable|integer|min:0',
            'score2' => 'nullable|integer|min:0',
            'match_time' => 'nullable|string|max:50',
        ]);

        $t1 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team1]);
        $t2 = DB::selectOne("SELECT team_id FROM teams WHERE team_id = ?", [$request->team2]);

        if (!$t1 || !$t2) {
            return redirect('/super-admin-fpl-2026')
                ->withErrors(['team1' => 'Selected teams do not exist in the database.'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            DB::insert("
                INSERT INTO matches (team_a_id, team_b_id, status, kickoff_at, match_time, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->team1,
                $request->team2,
                $request->status,
                $request->kickoff_at,
                $request->match_time ?? ''
            ]);

            $matchId = (int) DB::getPdo()->lastInsertId();
            $score1 = (int) ($request->score1 ?? 0);
            $score2 = (int) ($request->score2 ?? 0);

            DB::insert("
                INSERT INTO results (match_id, score_a, score_b, winner_team_id)
                VALUES (?, ?, ?, ?)
            ", [
                $matchId,
                $score1,
                $score2,
                $this->determineWinnerTeamId($request->status, $request->team1, $request->team2, $score1, $score2),
            ]);
        });

        $this->recalculateStandingsTable();

        return redirect('/super-admin-fpl-2026')->with('success', 'Match created successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'team1' => 'required|integer|different:team2',
            'team2' => 'required|integer',
            'score1' => 'nullable|integer|min:0',
            'score2' => 'nullable|integer|min:0',
            'match_time' => 'nullable|string|max:50',
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

        $score1 = (int) ($request->score1 ?? 0);
        $score2 = (int) ($request->score2 ?? 0);

        DB::transaction(function () use ($request, $score1, $score2) {
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
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    score_a = VALUES(score_a),
                    score_b = VALUES(score_b),
                    winner_team_id = VALUES(winner_team_id)
            ", [
                $request->id,
                $score1,
                $score2,
                $this->determineWinnerTeamId($request->status, $request->team1, $request->team2, $score1, $score2),
            ]);
        });

        $this->recalculateStandingsTable();

        return redirect('/super-admin-fpl-2026')->with('success', 'Match updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {
            DB::delete("DELETE FROM matches WHERE match_id = ?", [$request->id]);
        });

        $this->recalculateStandingsTable();

        return redirect('/super-admin-fpl-2026')->with('success', 'Match deleted successfully.');
    }

    public function recalculate()
    {
        $this->recalculateStandingsTable();

        return redirect('/super-admin-fpl-2026')->with('success', 'Standings recalculated successfully.');
    }

    private function getMatchesByStatus(string $status, string $orderBy, ?int $limit = null)
    {
        $sql = "
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
            ORDER BY {$orderBy}
        ";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }

        return collect(DB::select($sql, [$status]));
    }

    private function getStandingsTable()
    {
        return collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                COALESCE(s.played, 0) AS played,
                COALESCE(s.wins, 0) AS wins,
                COALESCE(s.draws, 0) AS draws,
                COALESCE(s.losses, 0) AS losses,
                COALESCE(s.goal_diff, 0) AS goal_diff,
                COALESCE(s.points, 0) AS points,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded
            FROM teams t
            LEFT JOIN standings s ON s.team_id = t.team_id
            ORDER BY
                COALESCE(s.points, 0) DESC,
                COALESCE(s.goal_diff, 0) DESC,
                COALESCE(t.goals_scored, 0) DESC,
                t.team_name ASC
        "));
    }

    private function determineWinnerTeamId(string $status, int $team1Id, int $team2Id, int $score1, int $score2): ?int
    {
        if ($status !== 'finished') {
            return null;
        }

        if ($score1 > $score2) {
            return $team1Id;
        }

        if ($score2 > $score1) {
            return $team2Id;
        }

        return null;
    }

    private function recalculateStandingsTable(): void
    {
        DB::transaction(function () {
            $teams = collect(DB::select("SELECT team_id FROM teams"));
            $stats = [];

            foreach ($teams as $team) {
                $stats[$team->team_id] = [
                    'played' => 0,
                    'points' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'draws' => 0,
                    'goal_diff' => 0,
                    'goals_scored' => 0,
                    'goals_conceded' => 0,
                ];
            }

            $finishedMatches = DB::select("
                SELECT
                    m.team_a_id,
                    m.team_b_id,
                    COALESCE(r.score_a, 0) AS score_a,
                    COALESCE(r.score_b, 0) AS score_b
                FROM matches m
                JOIN results r ON r.match_id = m.match_id
                WHERE m.status = 'finished'
            ");

            foreach ($finishedMatches as $match) {
                $teamA = (int) $match->team_a_id;
                $teamB = (int) $match->team_b_id;
                $scoreA = (int) $match->score_a;
                $scoreB = (int) $match->score_b;

                if (!isset($stats[$teamA]) || !isset($stats[$teamB])) {
                    continue;
                }

                $stats[$teamA]['played']++;
                $stats[$teamB]['played']++;

                $stats[$teamA]['goals_scored'] += $scoreA;
                $stats[$teamA]['goals_conceded'] += $scoreB;
                $stats[$teamB]['goals_scored'] += $scoreB;
                $stats[$teamB]['goals_conceded'] += $scoreA;

                if ($scoreA > $scoreB) {
                    $stats[$teamA]['wins']++;
                    $stats[$teamA]['points'] += 3;
                    $stats[$teamB]['losses']++;
                } elseif ($scoreB > $scoreA) {
                    $stats[$teamB]['wins']++;
                    $stats[$teamB]['points'] += 3;
                    $stats[$teamA]['losses']++;
                } else {
                    $stats[$teamA]['draws']++;
                    $stats[$teamB]['draws']++;
                    $stats[$teamA]['points']++;
                    $stats[$teamB]['points']++;
                }
            }

            DB::statement("DELETE FROM standings");

            foreach ($stats as $teamId => $row) {
                $row['goal_diff'] = $row['goals_scored'] - $row['goals_conceded'];

                DB::insert("
                    INSERT INTO standings (team_id, played, points, wins, losses, draws, goal_diff)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ", [
                    $teamId,
                    $row['played'],
                    $row['points'],
                    $row['wins'],
                    $row['losses'],
                    $row['draws'],
                    $row['goal_diff'],
                ]);

                DB::update("
                    UPDATE teams
                    SET goals_scored = ?, goals_conceded = ?
                    WHERE team_id = ?
                ", [
                    $row['goals_scored'],
                    $row['goals_conceded'],
                    $teamId,
                ]);
            }
        });
    }
}