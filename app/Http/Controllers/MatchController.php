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

        $transferPosts = DB::table('transfer_posts')
            ->select('transfer_post_id', 'title', 'summary', 'content', 'posted_at', 'created_at')
            ->where('status', 'published')
            ->orderByRaw('COALESCE(posted_at, created_at) DESC')
            ->orderByDesc('transfer_post_id')
            ->limit(4)
            ->get();

        $sponsorLogos = DB::table('sponsors as s')
            ->leftJoin('teams as t', 't.team_id', '=', 's.team_id')
            ->select('s.sponsor_name', 't.team_name')
            ->orderBy('s.sponsor_name')
            ->limit(8)
            ->get();

        return view('welcome', compact(
            'currentMatches',
            'finishedMatches',
            'upcomingFixtures',
            'transferPosts',
            'sponsorLogos'
        ));
    }

    public function fixtures(Request $request)
    {
        $teamId = $request->query('team_id');
        $matchTime = (string) $request->query('match_time', '');
        $sortBy = (string) $request->query('sort_by', 'kickoff_asc');

        $fixturesQuery = $this->buildMatchesQuery('upcoming');

        if ($teamId !== null && $teamId !== '') {
            $fixturesQuery->where(function ($query) use ($teamId) {
                $query->where('m.team_a_id', $teamId)
                    ->orWhere('m.team_b_id', $teamId);
            });
        } else {
            $teamId = '';
        }

        if ($matchTime !== '') {
            $fixturesQuery->where('m.match_time', $matchTime);
        }

        switch ($sortBy) {
            case 'kickoff_desc':
                $fixturesQuery->orderByDesc('m.kickoff_at')->orderByDesc('m.match_id');
                break;
            case 'team_asc':
                $fixturesQuery->orderBy('ta.team_name')->orderBy('tb.team_name');
                break;
            default:
                $sortBy = 'kickoff_asc';
                $fixturesQuery->orderBy('m.kickoff_at')->orderBy('m.match_id');
                break;
        }

        $fixtures = $fixturesQuery->get();
        $teams = $this->getTeamOptions();
        $matchTimes = DB::table('matches')
            ->where('status', 'upcoming')
            ->whereNotNull('match_time')
            ->where('match_time', '!=', '')
            ->distinct()
            ->orderBy('match_time')
            ->pluck('match_time');

        return view('matches', compact('fixtures', 'teams', 'matchTimes', 'teamId', 'matchTime', 'sortBy'));
    }

    public function results(Request $request)
    {
        $teamId = $request->query('team_id');
        $resultType = (string) $request->query('result_type', 'all');
        $sortBy = (string) $request->query('sort_by', 'latest');

        $resultsQuery = $this->buildMatchesQuery('finished');

        if ($teamId !== null && $teamId !== '') {
            $resultsQuery->where(function ($query) use ($teamId) {
                $query->where('m.team_a_id', $teamId)
                    ->orWhere('m.team_b_id', $teamId);
            });
        } else {
            $teamId = '';
        }

        if ($resultType === 'home_win') {
            $resultsQuery->whereRaw('COALESCE(r.score_a, 0) > COALESCE(r.score_b, 0)');
        } elseif ($resultType === 'away_win') {
            $resultsQuery->whereRaw('COALESCE(r.score_b, 0) > COALESCE(r.score_a, 0)');
        } elseif ($resultType === 'draw') {
            $resultsQuery->whereRaw('COALESCE(r.score_a, 0) = COALESCE(r.score_b, 0)');
        } else {
            $resultType = 'all';
        }

        switch ($sortBy) {
            case 'oldest':
                $resultsQuery->orderBy('m.kickoff_at')->orderBy('m.match_id');
                break;
            case 'goal_high':
                $resultsQuery->orderByRaw('(COALESCE(r.score_a, 0) + COALESCE(r.score_b, 0)) DESC')
                    ->orderByDesc('m.kickoff_at');
                break;
            default:
                $sortBy = 'latest';
                $resultsQuery->orderByDesc('m.kickoff_at')->orderByDesc('m.match_id');
                break;
        }

        $results = $resultsQuery->get();
        $teams = $this->getTeamOptions();

        return view('results', compact('results', 'teams', 'teamId', 'resultType', 'sortBy'));
    }

    public function standings(Request $request)
    {
        $teamId = $request->query('team_id');
        $minPoints = $request->query('min_points', '');
        $sortBy = (string) $request->query('sort_by', 'table');

        $standingsQuery = DB::table('teams as t')
            ->leftJoin('standings as s', 's.team_id', '=', 't.team_id')
            ->selectRaw("
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
            ");

        if ($teamId !== null && $teamId !== '') {
            $standingsQuery->where('t.team_id', $teamId);
        } else {
            $teamId = '';
        }

        if ($minPoints !== '' && is_numeric($minPoints)) {
            $standingsQuery->whereRaw('COALESCE(s.points, 0) >= ?', [(int) $minPoints]);
        } else {
            $minPoints = '';
        }

        switch ($sortBy) {
            case 'points_asc':
                $standingsQuery->orderByRaw('COALESCE(s.points, 0) ASC')
                    ->orderByRaw('COALESCE(s.goal_diff, 0) ASC')
                    ->orderBy('t.team_name');
                break;
            case 'goal_diff_desc':
                $standingsQuery->orderByRaw('COALESCE(s.goal_diff, 0) DESC')
                    ->orderByRaw('COALESCE(s.points, 0) DESC')
                    ->orderBy('t.team_name');
                break;
            case 'team_asc':
                $standingsQuery->orderBy('t.team_name');
                break;
            default:
                $sortBy = 'table';
                $standingsQuery->orderByRaw('COALESCE(s.points, 0) DESC')
                    ->orderByRaw('COALESCE(s.goal_diff, 0) DESC')
                    ->orderByRaw('COALESCE(t.goals_scored, 0) DESC')
                    ->orderBy('t.team_name');
                break;
        }

        $standings = $standingsQuery->get();
        $teams = $this->getTeamOptions();

        return view('standings', compact('standings', 'teams', 'teamId', 'minPoints', 'sortBy'));
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
            LEFT JOIN teams t ON t.team_id = s.team_id
            ORDER BY s.sponsor_name ASC
        "));

        $matches = collect(DB::select("
            SELECT
                m.match_id,
                m.team_a_id,
                m.team_b_id,
                ta.team_name AS team1,
                tb.team_name AS team2,
                m.kickoff_at,
                m.match_time,
                m.status,
                COALESCE(r.score_a, 0) AS score1,
                COALESCE(r.score_b, 0) AS score2
            FROM matches m
            JOIN teams ta ON ta.team_id = m.team_a_id
            JOIN teams tb ON tb.team_id = m.team_b_id
            LEFT JOIN results r ON r.match_id = m.match_id
            ORDER BY m.kickoff_at DESC, m.match_id DESC
        "));

        $standings = collect(DB::select("
            SELECT
                t.team_name,
                COALESCE(s.played, 0) AS played,
                COALESCE(s.wins, 0) AS wins,
                COALESCE(s.draws, 0) AS draws,
                COALESCE(s.losses, 0) AS losses,
                COALESCE(s.goal_diff, 0) AS goal_diff,
                COALESCE(s.points, 0) AS points
            FROM teams t
            LEFT JOIN standings s ON s.team_id = t.team_id
            ORDER BY COALESCE(s.points, 0) DESC, COALESCE(s.goal_diff, 0) DESC, t.team_name ASC
        "));

        return view('admin.match', compact(
            'teams',
            'players',
            'managers',
            'playerMarketValues',
            'transferPosts',
            'sponsors',
            'matches',
            'standings'
        ));
    }

    public function create(Request $request)
    {
        $request->validate([
            'team_a_id' => ['required', 'different:team_b_id', 'exists:teams,team_id'],
            'team_b_id' => ['required', 'different:team_a_id', 'exists:teams,team_id'],
            'kickoff_at' => ['required', 'date'],
            'match_time' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:upcoming,current,finished'],
            'score_a' => ['nullable', 'integer', 'min:0'],
            'score_b' => ['nullable', 'integer', 'min:0'],
        ]);

        $matchId = DB::table('matches')->insertGetId([
            'team_a_id' => $request->team_a_id,
            'team_b_id' => $request->team_b_id,
            'kickoff_at' => $request->kickoff_at,
            'match_time' => $request->match_time,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->status === 'finished') {
            DB::table('results')->updateOrInsert(
                ['match_id' => $matchId],
                [
                    'score_a' => $request->score_a ?? 0,
                    'score_b' => $request->score_b ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->route('admin.panel')->with('success', 'Match created successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'match_id' => ['required', 'exists:matches,match_id'],
            'team_a_id' => ['required', 'different:team_b_id', 'exists:teams,team_id'],
            'team_b_id' => ['required', 'different:team_a_id', 'exists:teams,team_id'],
            'kickoff_at' => ['required', 'date'],
            'match_time' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:upcoming,current,finished'],
            'score_a' => ['nullable', 'integer', 'min:0'],
            'score_b' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::table('matches')
            ->where('match_id', $request->match_id)
            ->update([
                'team_a_id' => $request->team_a_id,
                'team_b_id' => $request->team_b_id,
                'kickoff_at' => $request->kickoff_at,
                'match_time' => $request->match_time,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        if ($request->status === 'finished') {
            DB::table('results')->updateOrInsert(
                ['match_id' => $request->match_id],
                [
                    'score_a' => $request->score_a ?? 0,
                    'score_b' => $request->score_b ?? 0,
                    'updated_at' => now(),
                ]
            );
        } else {
            DB::table('results')->where('match_id', $request->match_id)->delete();
        }

        return redirect()->route('admin.panel')->with('success', 'Match updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'match_id' => ['required', 'exists:matches,match_id'],
        ]);

        DB::table('results')->where('match_id', $request->match_id)->delete();
        DB::table('matches')->where('match_id', $request->match_id)->delete();

        return redirect()->route('admin.panel')->with('success', 'Match deleted successfully.');
    }

    public function recalculate()
    {
        DB::table('standings')->truncate();

        $teams = DB::table('teams')->select('team_id')->get();

        foreach ($teams as $team) {
            DB::table('standings')->insert([
                'team_id' => $team->team_id,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goal_diff' => 0,
                'points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $finishedMatches = DB::table('matches as m')
            ->join('results as r', 'r.match_id', '=', 'm.match_id')
            ->where('m.status', 'finished')
            ->select(
                'm.team_a_id',
                'm.team_b_id',
                'r.score_a',
                'r.score_b'
            )
            ->get();

        foreach ($finishedMatches as $match) {
            $teamAId = $match->team_a_id;
            $teamBId = $match->team_b_id;
            $scoreA = (int) $match->score_a;
            $scoreB = (int) $match->score_b;

            DB::table('standings')->where('team_id', $teamAId)->increment('played');
            DB::table('standings')->where('team_id', $teamBId)->increment('played');

            DB::table('teams')->where('team_id', $teamAId)->update([
                'goals_scored' => DB::raw('COALESCE(goals_scored, 0) + ' . $scoreA),
                'goals_conceded' => DB::raw('COALESCE(goals_conceded, 0) + ' . $scoreB),
            ]);

            DB::table('teams')->where('team_id', $teamBId)->update([
                'goals_scored' => DB::raw('COALESCE(goals_scored, 0) + ' . $scoreB),
                'goals_conceded' => DB::raw('COALESCE(goals_conceded, 0) + ' . $scoreA),
            ]);

            DB::table('standings')->where('team_id', $teamAId)->update([
                'goal_diff' => DB::raw('COALESCE(goal_diff, 0) + ' . ($scoreA - $scoreB)),
            ]);

            DB::table('standings')->where('team_id', $teamBId)->update([
                'goal_diff' => DB::raw('COALESCE(goal_diff, 0) + ' . ($scoreB - $scoreA)),
            ]);

            if ($scoreA > $scoreB) {
                DB::table('standings')->where('team_id', $teamAId)->increment('wins');
                DB::table('standings')->where('team_id', $teamAId)->increment('points', 3);
                DB::table('standings')->where('team_id', $teamBId)->increment('losses');
            } elseif ($scoreB > $scoreA) {
                DB::table('standings')->where('team_id', $teamBId)->increment('wins');
                DB::table('standings')->where('team_id', $teamBId)->increment('points', 3);
                DB::table('standings')->where('team_id', $teamAId)->increment('losses');
            } else {
                DB::table('standings')->where('team_id', $teamAId)->increment('draws');
                DB::table('standings')->where('team_id', $teamBId)->increment('draws');
                DB::table('standings')->where('team_id', $teamAId)->increment('points');
                DB::table('standings')->where('team_id', $teamBId)->increment('points');
            }
        }

        return redirect()->route('admin.panel')->with('success', 'Standings recalculated successfully.');
    }

    private function getMatchesByStatus(string $status, string $orderBy, int $limit)
    {
        return $this->buildMatchesQuery($status)
            ->orderByRaw($orderBy)
            ->limit($limit)
            ->get();
    }

    private function buildMatchesQuery(string $status)
    {
        return DB::table('matches as m')
            ->join('teams as ta', 'ta.team_id', '=', 'm.team_a_id')
            ->join('teams as tb', 'tb.team_id', '=', 'm.team_b_id')
            ->leftJoin('results as r', 'r.match_id', '=', 'm.match_id')
            ->where('m.status', $status)
            ->select(
                'm.match_id',
                'm.team_a_id',
                'm.team_b_id',
                'ta.team_name as team1',
                'tb.team_name as team2',
                'm.kickoff_at',
                'm.match_time',
                'm.status',
                DB::raw('COALESCE(r.score_a, 0) as score1'),
                DB::raw('COALESCE(r.score_b, 0) as score2')
            );
    }

    private function getTeamOptions()
    {
        return DB::table('teams')
            ->select('team_id', 'team_name')
            ->orderBy('team_name')
            ->get();
    }
}