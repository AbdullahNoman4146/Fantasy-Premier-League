<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $managerStatus = (string) $request->query('manager_status', 'all');
        $strengthOrder = (string) $request->query('strength_order', '');

        $teamsQuery = DB::table('teams as t')
            ->leftJoin('managers as m', 'm.person_id', '=', 't.manager_id')
            ->leftJoin('persons as p', 'p.person_id', '=', 'm.person_id')
            ->selectRaw("
                t.team_id,
                t.team_name,
                t.strength,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                NULLIF(TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))), '') AS manager_name
            ");

        if ($managerStatus === 'with_manager') {
            $teamsQuery->whereNotNull('t.manager_id');
        } elseif ($managerStatus === 'without_manager') {
            $teamsQuery->whereNull('t.manager_id');
        } else {
            $managerStatus = 'all';
        }

        if ($strengthOrder === 'high_low') {
            $teamsQuery->orderByRaw('CASE WHEN t.strength IS NULL THEN 1 ELSE 0 END ASC')
                ->orderByDesc('t.strength')
                ->orderBy('t.team_name');
        } elseif ($strengthOrder === 'low_high') {
            $teamsQuery->orderByRaw('CASE WHEN t.strength IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('t.strength')
                ->orderBy('t.team_name');
        } else {
            $strengthOrder = '';
            $teamsQuery->orderBy('t.team_name');
        }

        $teams = $teamsQuery->get();

        return view('teams', compact('teams', 'managerStatus', 'strengthOrder'));
    }

    public function show(int $teamId)
    {
        $team = DB::selectOne("
            SELECT
                t.team_id,
                t.team_name,
                t.strength,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))) AS manager_name,
                COALESCE(p.nationality, 'N/A') AS manager_nationality,
                m.experience_years
            FROM teams t
            LEFT JOIN managers m ON m.person_id = t.manager_id
            LEFT JOIN persons p ON p.person_id = m.person_id
            WHERE t.team_id = ?
            LIMIT 1
        ", [$teamId]);

        abort_if(!$team, 404);

        $squad = collect(DB::select("
            SELECT
                pl.team_id,
                pl.jersey_number,
                pl.position,
                pe.person_id,
                pe.first_name,
                pe.last_name,
                pe.nationality,
                pmv.market_value,
                pmv.currency,
                pmv.season
            FROM players pl
            JOIN persons pe ON pe.person_id = pl.person_id
            LEFT JOIN (
                SELECT pmv1.team_id, pmv1.jersey_number, pmv1.market_value, pmv1.currency, pmv1.season
                FROM player_market_values pmv1
                INNER JOIN (
                    SELECT team_id, jersey_number, MAX(player_market_value_id) AS latest_id
                    FROM player_market_values
                    GROUP BY team_id, jersey_number
                ) latest ON latest.latest_id = pmv1.player_market_value_id
            ) pmv ON pmv.team_id = pl.team_id AND pmv.jersey_number = pl.jersey_number
            WHERE pl.team_id = ?
            ORDER BY pl.jersey_number ASC, pe.first_name ASC, pe.last_name ASC
        ", [$teamId]));

        $sponsors = collect(DB::select("
            SELECT sponsor_id, sponsor_name
            FROM sponsors
            WHERE team_id = ?
            ORDER BY sponsor_name ASC
        ", [$teamId]));

        $standings = collect(DB::select("
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
        "))->values()->map(function ($row, $index) {
            $row->rank = $index + 1;
            return $row;
        });

        $teamStanding = $standings->firstWhere('team_id', $teamId);

        $lastMatch = DB::selectOne("
            SELECT
                m.match_id,
                CASE WHEN m.team_a_id = ? THEN tb.team_name ELSE ta.team_name END AS opponent_name,
                CASE WHEN m.team_a_id = ? THEN 'Home' ELSE 'Away' END AS venue,
                CASE WHEN m.team_a_id = ? THEN COALESCE(r.score_a, 0) ELSE COALESCE(r.score_b, 0) END AS team_score,
                CASE WHEN m.team_a_id = ? THEN COALESCE(r.score_b, 0) ELSE COALESCE(r.score_a, 0) END AS opponent_score,
                CASE
                    WHEN m.status = 'finished' THEN 'finished'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at > NOW() THEN 'upcoming'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at <= NOW() THEN 'current'
                    ELSE m.status
                END AS effective_status,
                m.kickoff_at,
                m.match_time
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            WHERE (m.team_a_id = ? OR m.team_b_id = ?)
              AND CASE
                    WHEN m.status = 'finished' THEN 'finished'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at > NOW() THEN 'upcoming'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at <= NOW() THEN 'current'
                    ELSE m.status
                  END = 'finished'
            ORDER BY COALESCE(m.kickoff_at, m.updated_at, m.created_at) DESC, m.match_id DESC
            LIMIT 1
        ", [$teamId, $teamId, $teamId, $teamId, $teamId, $teamId]);

        $upcomingMatch = DB::selectOne("
            SELECT
                m.match_id,
                CASE WHEN m.team_a_id = ? THEN tb.team_name ELSE ta.team_name END AS opponent_name,
                CASE WHEN m.team_a_id = ? THEN 'Home' ELSE 'Away' END AS venue,
                m.kickoff_at,
                m.match_time,
                CASE
                    WHEN m.status = 'finished' THEN 'finished'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at > NOW() THEN 'upcoming'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at <= NOW() THEN 'current'
                    ELSE m.status
                END AS effective_status
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            WHERE (m.team_a_id = ? OR m.team_b_id = ?)
              AND CASE
                    WHEN m.status = 'finished' THEN 'finished'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at > NOW() THEN 'upcoming'
                    WHEN m.kickoff_at IS NOT NULL AND m.kickoff_at <= NOW() THEN 'current'
                    ELSE m.status
                  END = 'upcoming'
            ORDER BY
                CASE WHEN m.kickoff_at IS NULL THEN 1 ELSE 0 END ASC,
                m.kickoff_at ASC,
                m.match_id ASC
            LIMIT 1
        ", [$teamId, $teamId, $teamId, $teamId]);

        $marketValueTotals = collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                COALESCE(SUM(latest_values.market_value), 0) AS total_market_value,
                COALESCE(MAX(latest_values.currency), 'GBP') AS currency
            FROM teams t
            LEFT JOIN (
                SELECT pmv1.team_id, pmv1.jersey_number, pmv1.market_value, pmv1.currency
                FROM player_market_values pmv1
                INNER JOIN (
                    SELECT team_id, jersey_number, MAX(player_market_value_id) AS latest_id
                    FROM player_market_values
                    GROUP BY team_id, jersey_number
                ) latest ON latest.latest_id = pmv1.player_market_value_id
            ) latest_values ON latest_values.team_id = t.team_id
            GROUP BY t.team_id, t.team_name
            ORDER BY total_market_value DESC, t.team_name ASC
        "))->values()->map(function ($row, $index) {
            $row->market_rank = $index + 1;
            return $row;
        });

        $teamMarketValue = $marketValueTotals->firstWhere('team_id', $teamId);

        return view('team-detail', compact(
            'team',
            'squad',
            'sponsors',
            'teamStanding',
            'lastMatch',
            'upcomingMatch',
            'teamMarketValue'
        ));
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
            DB::delete("DELETE FROM teams WHERE team_id = ?", [$request->team_id]);
        } catch (QueryException $e) {
            return redirect()->route('admin.panel')
                ->withErrors(['team_delete' => 'This team cannot be deleted because it is linked to other records. Remove related matches, players, sponsors, or manager first.'])
                ->withInput();
        }

        return redirect()->route('admin.panel')->with('success', 'Team deleted successfully.');
    }
}
