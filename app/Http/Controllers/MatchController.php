<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

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

    public function liveData()
{
    $currentMatches = $this->getMatchesByStatus('current', 'm.kickoff_at DESC', 20)
        ->map(function ($match) {
            return [
                'match_id' => (int) $match->match_id,
                'team1' => $match->team1,
                'team2' => $match->team2,
                'kickoff_at' => $match->kickoff_at,
                'match_time' => $match->match_time,
                'status' => $match->status,
                'live_phase' => $match->live_phase,
                'first_half_added_minutes' => (int) ($match->first_half_added_minutes ?? 0),
                'second_half_added_minutes' => (int) ($match->second_half_added_minutes ?? 0),
                'second_half_started_at' => $match->second_half_started_at,
                'score1' => (int) ($match->score1 ?? 0),
                'score2' => (int) ($match->score2 ?? 0),
                'team1_scorers_text' => $match->team1_scorers_text ?? '',
                'team2_scorers_text' => $match->team2_scorers_text ?? '',
            ];
        })
        ->values();

    return response()->json([
        'matches' => $currentMatches,
        'count' => $currentMatches->count(),
        'server_time' => now()->format('Y-m-d H:i:s'),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
      ->header('Pragma', 'no-cache');
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

        $results = $this->attachGoalEventData($resultsQuery->get());
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
        $matchStatsRaw = DB::table('matches')
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $matchStats = [
            'current' => (int) ($matchStatsRaw['current'] ?? 0),
            'upcoming' => (int) ($matchStatsRaw['upcoming'] ?? 0),
            'finished' => (int) ($matchStatsRaw['finished'] ?? 0),
        ];

        $teams = collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                t.strength,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                t.manager_id,
                NULLIF(TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))), '') AS manager_name
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

        $matches = $this->attachGoalEventData(collect(DB::select("
    SELECT
        m.match_id,
        m.match_id AS id,
        m.team_a_id,
        m.team_a_id AS team1_id,
        m.team_b_id,
        m.team_b_id AS team2_id,
        ta.team_name AS team1,
        ta.team_name AS team1_name,
        tb.team_name AS team2,
        tb.team_name AS team2_name,
        m.kickoff_at,
        m.match_time,
        m.status,
        m.live_phase,
        m.first_half_added_minutes,
        m.second_half_added_minutes,
        m.second_half_started_at,
        COALESCE(r.score_a, 0) AS score1,
        COALESCE(r.score_b, 0) AS score2
    FROM matches m
    JOIN teams ta ON ta.team_id = m.team_a_id
    JOIN teams tb ON tb.team_id = m.team_b_id
    LEFT JOIN results r ON r.match_id = m.match_id
    ORDER BY COALESCE(m.kickoff_at, m.created_at) DESC, m.match_id DESC
")));

        $standings = collect(DB::select("
            SELECT
                t.team_id,
                t.team_name,
                COALESCE(s.played, 0) AS played,
                COALESCE(s.wins, 0) AS wins,
                COALESCE(s.draws, 0) AS draws,
                COALESCE(s.losses, 0) AS losses,
                COALESCE(t.goals_scored, 0) AS goals_scored,
                COALESCE(t.goals_conceded, 0) AS goals_conceded,
                COALESCE(s.goal_diff, 0) AS goal_diff,
                COALESCE(s.points, 0) AS points
            FROM teams t
            LEFT JOIN standings s ON s.team_id = t.team_id
            ORDER BY COALESCE(s.points, 0) DESC, COALESCE(s.goal_diff, 0) DESC, COALESCE(t.goals_scored, 0) DESC, t.team_name ASC
        "));

        $playerPositions = $this->playerPositions();
        $sponsorOptions = $this->sponsorOptions();

        $playerOptions = collect(DB::select("
            SELECT
                p.team_id,
                p.jersey_number,
                t.team_name,
                TRIM(CONCAT(COALESCE(pe.first_name, ''), ' ', COALESCE(pe.last_name, ''))) AS player_name
            FROM players p
            JOIN persons pe ON pe.person_id = p.person_id
            JOIN teams t ON t.team_id = p.team_id
            ORDER BY t.team_name ASC, p.jersey_number ASC
        "));

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
            'playerPositions',
            'sponsorOptions',
            'playerOptions'
        ));
    }

    public function create(Request $request)
    {
        $payload = $this->matchPayload($request, false);

        $request->merge($payload);
$request->validate([
    'team_a_id' => ['required', 'different:team_b_id', 'exists:teams,team_id'],
    'team_b_id' => ['required', 'different:team_a_id', 'exists:teams,team_id'],
    'kickoff_at' => ['nullable', 'date'],
    'match_time' => ['nullable', 'string', 'max:50'],
    'status' => ['required', 'in:upcoming,current,finished'],
    'live_phase' => ['nullable', 'in:first_half,break,second_half,finished'],
    'first_half_added_minutes' => ['nullable', 'integer', 'min:0', 'max:30'],
    'second_half_added_minutes' => ['nullable', 'integer', 'min:0', 'max:30'],
    'second_half_started_at' => ['nullable', 'date'],
    'score_a' => ['nullable', 'integer', 'min:0'],
    'score_b' => ['nullable', 'integer', 'min:0'],
    'team_a_scorers' => ['nullable', 'string', 'max:1000'],
    'team_b_scorers' => ['nullable', 'string', 'max:1000'],
]);

        [$teamAGoalEvents, $teamBGoalEvents] = $this->parseGoalEventPayload($payload);

        DB::transaction(function () use ($payload, $teamAGoalEvents, $teamBGoalEvents) {
            $matchId = DB::table('matches')->insertGetId([
    'team_a_id' => $payload['team_a_id'],
    'team_b_id' => $payload['team_b_id'],
    'kickoff_at' => $payload['kickoff_at'],
    'match_time' => $payload['match_time'],
    'status' => $payload['status'],
    'live_phase' => $payload['live_phase'],
    'first_half_added_minutes' => $payload['first_half_added_minutes'],
    'second_half_added_minutes' => $payload['second_half_added_minutes'],
    'second_half_started_at' => $payload['second_half_started_at'],
    'created_at' => now(),
    'updated_at' => now(),
]);

            if ($this->shouldPersistResult($payload['status'])) {
                $this->syncResult($matchId, $payload['score_a'], $payload['score_b']);
                $this->syncGoalEvents($matchId, $teamAGoalEvents, $teamBGoalEvents);
            } else {
                DB::table('results')->where('match_id', $matchId)->delete();

                if ($this->goalEventsEnabled()) {
                    DB::table('match_goal_events')->where('match_id', $matchId)->delete();
                }
            }
        });

        $this->rebuildStandings();

        return redirect()->route('admin.panel')->with('success', 'Match created successfully.');
    }

    public function update(Request $request)
    {
        $payload = $this->matchPayload($request, true);

        $request->merge($payload);
$request->validate([
    'match_id' => ['required', 'exists:matches,match_id'],
    'team_a_id' => ['required', 'different:team_b_id', 'exists:teams,team_id'],
    'team_b_id' => ['required', 'different:team_a_id', 'exists:teams,team_id'],
    'kickoff_at' => ['nullable', 'date'],
    'match_time' => ['nullable', 'string', 'max:50'],
    'status' => ['required', 'in:upcoming,current,finished'],
    'live_phase' => ['nullable', 'in:first_half,break,second_half,finished'],
    'first_half_added_minutes' => ['nullable', 'integer', 'min:0', 'max:30'],
    'second_half_added_minutes' => ['nullable', 'integer', 'min:0', 'max:30'],
    'second_half_started_at' => ['nullable', 'date'],
    'score_a' => ['nullable', 'integer', 'min:0'],
    'score_b' => ['nullable', 'integer', 'min:0'],
    'team_a_scorers' => ['nullable', 'string', 'max:1000'],
    'team_b_scorers' => ['nullable', 'string', 'max:1000'],
]);

        [$teamAGoalEvents, $teamBGoalEvents] = $this->parseGoalEventPayload($payload);

        DB::transaction(function () use ($payload, $teamAGoalEvents, $teamBGoalEvents) {
            DB::table('matches')
    ->where('match_id', $payload['match_id'])
    ->update([
        'team_a_id' => $payload['team_a_id'],
        'team_b_id' => $payload['team_b_id'],
        'kickoff_at' => $payload['kickoff_at'],
        'match_time' => $payload['match_time'],
        'status' => $payload['status'],
        'live_phase' => $payload['live_phase'],
        'first_half_added_minutes' => $payload['first_half_added_minutes'],
        'second_half_added_minutes' => $payload['second_half_added_minutes'],
        'second_half_started_at' => $payload['second_half_started_at'],
        'updated_at' => now(),
    ]);

            if ($this->shouldPersistResult($payload['status'])) {
                $this->syncResult((int) $payload['match_id'], $payload['score_a'], $payload['score_b']);
                $this->syncGoalEvents((int) $payload['match_id'], $teamAGoalEvents, $teamBGoalEvents);
            } else {
                DB::table('results')->where('match_id', $payload['match_id'])->delete();

                if ($this->goalEventsEnabled()) {
                    DB::table('match_goal_events')->where('match_id', $payload['match_id'])->delete();
                }
            }
        });

        $this->rebuildStandings();

        return redirect()->route('admin.panel')->with('success', 'Match updated successfully.');
    }

    public function destroy(Request $request)
    {
        $matchId = $request->input('match_id', $request->input('id'));
        $request->merge(['match_id' => $matchId]);

        $request->validate([
            'match_id' => ['required', 'exists:matches,match_id'],
        ]);

        DB::transaction(function () use ($matchId) {
            if ($this->goalEventsEnabled()) {
                DB::table('match_goal_events')->where('match_id', $matchId)->delete();
            }

            DB::table('results')->where('match_id', $matchId)->delete();
            DB::table('matches')->where('match_id', $matchId)->delete();
        });

        $this->rebuildStandings();

        return redirect()->route('admin.panel')->with('success', 'Match deleted successfully.');
    }

    public function recalculate()
    {
        $this->rebuildStandings();

        return redirect()->route('admin.panel')->with('success', 'Standings recalculated successfully.');
    }

    private function getMatchesByStatus(string $status, string $orderBy, int $limit)
    {
        return $this->attachGoalEventData(
            $this->buildMatchesQuery($status)
                ->orderByRaw($orderBy)
                ->limit($limit)
                ->get()
        );
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
            'm.live_phase',
            'm.first_half_added_minutes',
            'm.second_half_added_minutes',
            'm.second_half_started_at',
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

    private function playerPositions(): array
    {
        $defaults = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];

        $existing = DB::table('players')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->map(function ($position) {
                return trim((string) $position);
            })
            ->filter()
            ->values()
            ->all();

        return array_values(array_unique(array_merge($defaults, $existing)));
    }

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

    private function matchPayload(Request $request, bool $withId = false): array
{
    $payload = [
        'team_a_id' => $request->input('team_a_id', $request->input('team1')),
        'team_b_id' => $request->input('team_b_id', $request->input('team2')),
        'kickoff_at' => $this->normalizeDateTime($request->input('kickoff_at')),
        'match_time' => $request->filled('match_time') ? $request->input('match_time') : null,
        'status' => $request->input('status'),
        'live_phase' => $request->filled('live_phase') ? $request->input('live_phase') : null,
        'first_half_added_minutes' => (int) $request->input('first_half_added_minutes', 0),
        'second_half_added_minutes' => (int) $request->input('second_half_added_minutes', 0),
        'second_half_started_at' => $this->normalizeDateTime($request->input('second_half_started_at')),
        'score_a' => $request->input('score_a', $request->input('score1')),
        'score_b' => $request->input('score_b', $request->input('score2')),
        'team_a_scorers' => $request->input('team_a_scorers', ''),
        'team_b_scorers' => $request->input('team_b_scorers', ''),
    ];

    if ($withId) {
        $payload['match_id'] = $request->input('match_id', $request->input('id'));
    }

    return $payload;
}

    private function normalizeDateTime($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return str_replace('T', ' ', $value);
    }

    private function shouldPersistResult(string $status): bool
    {
        return in_array($status, ['current', 'finished'], true);
    }

    private function goalEventsEnabled(): bool
    {
        try {
            return Schema::hasTable('match_goal_events');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function syncResult(int $matchId, $scoreA, $scoreB): void
    {
        $scoreA = $scoreA === null || $scoreA === '' ? 0 : (int) $scoreA;
        $scoreB = $scoreB === null || $scoreB === '' ? 0 : (int) $scoreB;

        $match = DB::table('matches')
            ->select('team_a_id', 'team_b_id')
            ->where('match_id', $matchId)
            ->first();

        if (!$match) {
            return;
        }

        $winnerTeamId = $this->winnerTeamId((int) $match->team_a_id, (int) $match->team_b_id, $scoreA, $scoreB);

        $exists = DB::table('results')->where('match_id', $matchId)->exists();

        if ($exists) {
            DB::table('results')
                ->where('match_id', $matchId)
                ->update([
                    'score_a' => $scoreA,
                    'score_b' => $scoreB,
                    'winner_team_id' => $winnerTeamId,
                ]);
        } else {
            DB::table('results')->insert([
                'match_id' => $matchId,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'winner_team_id' => $winnerTeamId,
            ]);
        }
    }

    private function parseGoalEventPayload(array $payload): array
    {
        $teamAGoals = $payload['score_a'] === null || $payload['score_a'] === '' ? 0 : (int) $payload['score_a'];
        $teamBGoals = $payload['score_b'] === null || $payload['score_b'] === '' ? 0 : (int) $payload['score_b'];

        return [
            $this->parseGoalEventsForTeam(
                (int) $payload['team_a_id'],
                (string) ($payload['team_a_scorers'] ?? ''),
                $teamAGoals,
                'team_a_scorers',
                'Team A goal scorers'
            ),
            $this->parseGoalEventsForTeam(
                (int) $payload['team_b_id'],
                (string) ($payload['team_b_scorers'] ?? ''),
                $teamBGoals,
                'team_b_scorers',
                'Team B goal scorers'
            ),
        ];
    }

    private function parseGoalEventsForTeam(
        int $teamId,
        string $input,
        int $expectedGoals,
        string $fieldName,
        string $fieldLabel
    ): array {
        $input = trim($input);

        if ($input === '') {
            return [];
        }

        $tokens = preg_split('/[\r\n,]+/', $input) ?: [];
        $tokens = array_values(array_filter(array_map(static function ($token) {
            return trim((string) $token);
        }, $tokens)));

        if (count($tokens) !== $expectedGoals) {
            throw ValidationException::withMessages([
                $fieldName => $fieldLabel . ' must contain exactly ' . $expectedGoals . ' scorer entr' . ($expectedGoals === 1 ? 'y' : 'ies') . ' to match the score.',
            ]);
        }

        $players = DB::table('players')
            ->where('team_id', $teamId)
            ->pluck('person_id', 'jersey_number')
            ->map(static function ($personId) {
                return (int) $personId;
            })
            ->all();

        $events = [];

        foreach ($tokens as $index => $token) {
            if (!preg_match('/^(\d{1,2})(?:\s*@\s*(.+))?$/', $token, $matches)) {
                throw ValidationException::withMessages([
                    $fieldName => 'Format is invalid. Use jersey numbers like 11, 7@45\', 9@90+2\'.',
                ]);
            }

            $jerseyNumber = (int) $matches[1];
            $minuteLabel = isset($matches[2]) ? trim((string) $matches[2]) : null;

            if (!array_key_exists($jerseyNumber, $players)) {
                throw ValidationException::withMessages([
                    $fieldName => 'Jersey #' . $jerseyNumber . ' does not exist in the selected team.',
                ]);
            }

            $events[] = [
                'team_id' => $teamId,
                'person_id' => $players[$jerseyNumber],
                'jersey_number' => $jerseyNumber,
                'minute_label' => $minuteLabel !== '' ? $minuteLabel : null,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $events;
    }

    private function syncGoalEvents(int $matchId, array $teamAGoalEvents, array $teamBGoalEvents): void
    {
        if (!$this->goalEventsEnabled()) {
            return;
        }

        DB::table('match_goal_events')->where('match_id', $matchId)->delete();

        $rows = [];

        foreach (array_merge($teamAGoalEvents, $teamBGoalEvents) as $event) {
            $rows[] = [
                'match_id' => $matchId,
                'team_id' => $event['team_id'],
                'person_id' => $event['person_id'],
                'jersey_number' => $event['jersey_number'],
                'minute_label' => $event['minute_label'],
                'sort_order' => $event['sort_order'],
                'created_at' => $event['created_at'],
                'updated_at' => $event['updated_at'],
            ];
        }

        if (!empty($rows)) {
            DB::table('match_goal_events')->insert($rows);
        }
    }

    private function rebuildStandings(): void
    {
        DB::table('standings')->delete();

        DB::table('teams')->update([
            'goals_scored' => 0,
            'goals_conceded' => 0,
        ]);

        $teamIds = DB::table('teams')
            ->orderBy('team_id')
            ->pluck('team_id')
            ->map(static function ($teamId) {
                return (int) $teamId;
            })
            ->all();

        if (empty($teamIds)) {
            return;
        }

        $table = [];

        foreach ($teamIds as $teamId) {
            $table[$teamId] = [
                'team_id' => $teamId,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goal_diff' => 0,
                'points' => 0,
            ];
        }

        $teamGoals = [];

        foreach ($teamIds as $teamId) {
            $teamGoals[$teamId] = [
                'goals_scored' => 0,
                'goals_conceded' => 0,
            ];
        }

        $finishedMatches = DB::table('matches as m')
            ->join('results as r', 'r.match_id', '=', 'm.match_id')
            ->where('m.status', 'finished')
            ->select('m.match_id', 'm.team_a_id', 'm.team_b_id', 'r.score_a', 'r.score_b')
            ->orderBy('m.match_id')
            ->get();

        foreach ($finishedMatches as $match) {
            $teamAId = (int) $match->team_a_id;
            $teamBId = (int) $match->team_b_id;
            $scoreA = (int) $match->score_a;
            $scoreB = (int) $match->score_b;

            if (!isset($table[$teamAId]) || !isset($table[$teamBId])) {
                continue;
            }

            $table[$teamAId]['played']++;
            $table[$teamBId]['played']++;

            $teamGoals[$teamAId]['goals_scored'] += $scoreA;
            $teamGoals[$teamAId]['goals_conceded'] += $scoreB;
            $teamGoals[$teamBId]['goals_scored'] += $scoreB;
            $teamGoals[$teamBId]['goals_conceded'] += $scoreA;

            $table[$teamAId]['goal_diff'] += ($scoreA - $scoreB);
            $table[$teamBId]['goal_diff'] += ($scoreB - $scoreA);

            if ($scoreA > $scoreB) {
                $table[$teamAId]['wins']++;
                $table[$teamAId]['points'] += 3;
                $table[$teamBId]['losses']++;
            } elseif ($scoreB > $scoreA) {
                $table[$teamBId]['wins']++;
                $table[$teamBId]['points'] += 3;
                $table[$teamAId]['losses']++;
            } else {
                $table[$teamAId]['draws']++;
                $table[$teamBId]['draws']++;
                $table[$teamAId]['points']++;
                $table[$teamBId]['points']++;
            }

            DB::table('results')
                ->where('match_id', $match->match_id)
                ->update([
                    'winner_team_id' => $this->winnerTeamId($teamAId, $teamBId, $scoreA, $scoreB),
                ]);
        }

        DB::table('standings')->insert(array_values($table));

        foreach ($teamGoals as $teamId => $goalData) {
            DB::table('teams')
                ->where('team_id', $teamId)
                ->update([
                    'goals_scored' => $goalData['goals_scored'],
                    'goals_conceded' => $goalData['goals_conceded'],
                ]);
        }
    }

    private function attachGoalEventData($matches)
    {
        $matches = collect($matches);

        if ($matches->isEmpty()) {
            return $matches;
        }

        if (!$this->goalEventsEnabled()) {
            return $matches->map(function ($match) {
                $match->team1_scorers_text = '';
                $match->team2_scorers_text = '';
                $match->team_a_scorers_input = '';
                $match->team_b_scorers_input = '';

                return $match;
            });
        }

        $matchIds = $matches->pluck('match_id')
            ->filter()
            ->map(static function ($matchId) {
                return (int) $matchId;
            })
            ->unique()
            ->values()
            ->all();

        if (empty($matchIds)) {
            return $matches;
        }

        try {
            $goalEvents = DB::table('match_goal_events as ge')
                ->join('persons as pe', 'pe.person_id', '=', 'ge.person_id')
                ->select(
                    'ge.match_id',
                    'ge.team_id',
                    'ge.person_id',
                    'ge.jersey_number',
                    'ge.minute_label',
                    'ge.sort_order',
                    'pe.first_name',
                    'pe.last_name'
                )
                ->whereIn('ge.match_id', $matchIds)
                ->orderBy('ge.match_id')
                ->orderBy('ge.team_id')
                ->orderBy('ge.sort_order')
                ->orderBy('ge.goal_event_id')
                ->get()
                ->groupBy('match_id');
        } catch (\Throwable $e) {
            return $matches->map(function ($match) {
                $match->team1_scorers_text = '';
                $match->team2_scorers_text = '';
                $match->team_a_scorers_input = '';
                $match->team_b_scorers_input = '';

                return $match;
            });
        }

        return $matches->map(function ($match) use ($goalEvents) {
            $teamAId = (int) ($match->team_a_id ?? $match->team1_id ?? 0);
            $teamBId = (int) ($match->team_b_id ?? $match->team2_id ?? 0);
            $events = collect($goalEvents->get($match->match_id, collect()));

            $teamAEvents = $events->where('team_id', $teamAId)->values();
            $teamBEvents = $events->where('team_id', $teamBId)->values();

            $match->team1_scorers_text = $this->formatGoalScorerText($teamAEvents);
            $match->team2_scorers_text = $this->formatGoalScorerText($teamBEvents);
            $match->team_a_scorers_input = $this->formatGoalScorerInput($teamAEvents);
            $match->team_b_scorers_input = $this->formatGoalScorerInput($teamBEvents);

            return $match;
        });
    }

    private function formatGoalScorerText($events): string
    {
        return collect($events)
            ->map(function ($event) {
                $name = trim((string) (($event->first_name ?? '') . ' ' . ($event->last_name ?? '')));
                $name = $name !== '' ? $name : ('#' . ($event->jersey_number ?? '?'));

                if (!empty($event->minute_label)) {
                    return $name . ' ' . $event->minute_label;
                }

                return $name;
            })
            ->implode(', ');
    }

    private function formatGoalScorerInput($events): string
    {
        return collect($events)
            ->map(function ($event) {
                $jersey = (string) ($event->jersey_number ?? '');

                if ($jersey === '') {
                    return '';
                }

                if (!empty($event->minute_label)) {
                    return $jersey . '@' . $event->minute_label;
                }

                return $jersey;
            })
            ->filter()
            ->implode(', ');
    }

    private function winnerTeamId(int $teamAId, int $teamBId, int $scoreA, int $scoreB): ?int
    {
        if ($scoreA > $scoreB) {
            return $teamAId;
        }

        if ($scoreB > $scoreA) {
            return $teamBId;
        }

        return null;
    }
}