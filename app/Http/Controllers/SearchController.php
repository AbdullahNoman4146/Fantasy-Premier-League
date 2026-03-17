<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $term = preg_replace('/\s+/', ' ', trim((string) $request->query('q', ''))) ?? '';

        if ($term === '' || mb_strlen($term) < 2) {
            return response()->json(['items' => []]);
        }

        $term = mb_substr($term, 0, 80);
        $like = '%' . $term . '%';

        $items = collect()
            ->merge($this->buildPageResults($term, $like))
            ->merge($this->buildTeamProfileResults($like))
            ->merge($this->buildTransferArticleResults($like))
            ->sortByDesc('score')
            ->unique(fn (array $item) => $item['type'] . '|' . $item['title'] . '|' . $item['url'])
            ->take(20)
            ->values()
            ->map(fn (array $item) => [
                'type' => $item['type'],
                'title' => $item['title'],
                'subtitle' => $item['subtitle'],
                'url' => $item['url'],
            ]);

        return response()->json(['items' => $items]);
    }

    private function buildPageResults(string $term, string $like): Collection
    {
        $items = collect();

        $teamsCount = DB::table('teams as t')
            ->leftJoin('managers as m', 'm.person_id', '=', 't.manager_id')
            ->leftJoin('persons as p', 'p.person_id', '=', 'm.person_id')
            ->where(function ($query) use ($like) {
                $query->where('t.team_name', 'like', $like)
                    ->orWhereRaw("TRIM(CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, ''))) LIKE ?", [$like]);
            })
            ->count();

        if ($teamsCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Teams',
                'subtitle' => $teamsCount . ' matching result(s) on Teams page',
                'url' => route('teams.index', ['q' => $term]),
                'score' => 100,
            ]);
        }

        $playersCount = DB::table('players as pl')
            ->join('persons as pe', 'pe.person_id', '=', 'pl.person_id')
            ->join('teams as t', 't.team_id', '=', 'pl.team_id')
            ->where(function ($query) use ($like) {
                $query->where('pe.first_name', 'like', $like)
                    ->orWhere('pe.last_name', 'like', $like)
                    ->orWhereRaw("CONCAT(COALESCE(pe.first_name, ''), ' ', COALESCE(pe.last_name, '')) LIKE ?", [$like])
                    ->orWhere('t.team_name', 'like', $like)
                    ->orWhere('pe.nationality', 'like', $like)
                    ->orWhere('pl.position', 'like', $like);
            })
            ->count();

        if ($playersCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Players',
                'subtitle' => $playersCount . ' matching result(s) on Players page',
                'url' => route('players.index', ['q' => $term]),
                'score' => 99,
            ]);
        }

        $managersCount = DB::table('managers as m')
            ->join('persons as p', 'p.person_id', '=', 'm.person_id')
            ->leftJoin('teams as t', 't.team_id', '=', 'm.team_id')
            ->where(function ($query) use ($like) {
                $query->where('p.first_name', 'like', $like)
                    ->orWhere('p.last_name', 'like', $like)
                    ->orWhereRaw("CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, '')) LIKE ?", [$like])
                    ->orWhere('p.nationality', 'like', $like)
                    ->orWhere('t.team_name', 'like', $like);
            })
            ->count();

        if ($managersCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Managers',
                'subtitle' => $managersCount . ' matching result(s) on Managers page',
                'url' => route('managers.index', ['q' => $term]),
                'score' => 98,
            ]);
        }

        $sponsorsCount = DB::table('sponsors as s')
            ->join('teams as t', 't.team_id', '=', 's.team_id')
            ->where(function ($query) use ($like) {
                $query->where('s.sponsor_name', 'like', $like)
                    ->orWhere('t.team_name', 'like', $like);
            })
            ->count();

        if ($sponsorsCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Sponsors',
                'subtitle' => $sponsorsCount . ' matching result(s) on Sponsors page',
                'url' => route('sponsors.index', ['q' => $term]),
                'score' => 97,
            ]);
        }

        $marketValuesCount = DB::table('player_market_values as pmv')
            ->join('players as pl', function ($join) {
                $join->on('pl.team_id', '=', 'pmv.team_id')
                    ->on('pl.jersey_number', '=', 'pmv.jersey_number');
            })
            ->join('persons as p', 'p.person_id', '=', 'pl.person_id')
            ->join('teams as t', 't.team_id', '=', 'pmv.team_id')
            ->where(function ($query) use ($like) {
                $query->where('p.first_name', 'like', $like)
                    ->orWhere('p.last_name', 'like', $like)
                    ->orWhereRaw("CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, '')) LIKE ?", [$like])
                    ->orWhere('t.team_name', 'like', $like)
                    ->orWhere('pl.position', 'like', $like)
                    ->orWhere('pmv.season', 'like', $like)
                    ->orWhere('pmv.notes', 'like', $like);
            })
            ->count();

        if ($marketValuesCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Market Values',
                'subtitle' => $marketValuesCount . ' matching result(s) on Market Value page',
                'url' => route('market-values.index', ['q' => $term]),
                'score' => 96,
            ]);
        }

        $matchesCount = DB::table('matches as m')
            ->join('teams as ta', 'ta.team_id', '=', 'm.team_a_id')
            ->join('teams as tb', 'tb.team_id', '=', 'm.team_b_id')
            ->where('m.status', '=', 'upcoming')
            ->where(function ($query) use ($like) {
                $query->where('ta.team_name', 'like', $like)
                    ->orWhere('tb.team_name', 'like', $like)
                    ->orWhere('m.match_time', 'like', $like)
                    ->orWhere('m.kickoff_at', 'like', $like);
            })
            ->count();

        if ($matchesCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Match',
                'subtitle' => $matchesCount . ' matching result(s) on Match page',
                'url' => route('matches.index', ['q' => $term]),
                'score' => 95,
            ]);
        }

        $resultsCount = DB::table('matches as m')
            ->join('teams as ta', 'ta.team_id', '=', 'm.team_a_id')
            ->join('teams as tb', 'tb.team_id', '=', 'm.team_b_id')
            ->leftJoin('results as r', 'r.match_id', '=', 'm.match_id')
            ->where('m.status', '=', 'finished')
            ->where(function ($query) use ($like) {
                $query->where('ta.team_name', 'like', $like)
                    ->orWhere('tb.team_name', 'like', $like)
                    ->orWhere('m.match_time', 'like', $like)
                    ->orWhere('m.kickoff_at', 'like', $like);
            })
            ->count();

        if ($resultsCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Results',
                'subtitle' => $resultsCount . ' matching result(s) on Results page',
                'url' => route('results.index', ['q' => $term]),
                'score' => 94,
            ]);
        }

        $standingsCount = DB::table('teams as t')
            ->leftJoin('standings as s', 's.team_id', '=', 't.team_id')
            ->where('t.team_name', 'like', $like)
            ->count();

        if ($standingsCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Standings',
                'subtitle' => $standingsCount . ' matching result(s) on Standings page',
                'url' => route('standings.index', ['q' => $term]),
                'score' => 93,
            ]);
        }

        $transfersCount = DB::table('transfer_posts')
            ->where('status', '=', 'published')
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('content', 'like', $like);
            })
            ->count();

        if ($transfersCount > 0) {
            $items->push([
                'type' => 'Page',
                'title' => 'Transfer',
                'subtitle' => $transfersCount . ' matching result(s) on Transfer page',
                'url' => route('transfers.index', ['q' => $term]),
                'score' => 92,
            ]);
        }

        return $items;
    }

    private function buildTeamProfileResults(string $like): Collection
    {
        $teamsByName = collect(DB::select("
            SELECT t.team_id, t.team_name
            FROM teams t
            WHERE t.team_name LIKE ?
            ORDER BY t.team_name ASC
            LIMIT 4
        ", [$like]))->map(fn ($row) => [
            'type' => 'Team Profile',
            'title' => $row->team_name,
            'subtitle' => 'Open team details page',
            'url' => route('teams.show', $row->team_id),
            'score' => 110,
        ]);

        $teamsByPlayer = collect(DB::select("
            SELECT DISTINCT t.team_id, t.team_name
            FROM players pl
            JOIN persons pe ON pe.person_id = pl.person_id
            JOIN teams t ON t.team_id = pl.team_id
            WHERE pe.first_name LIKE ?
               OR pe.last_name LIKE ?
               OR CONCAT(COALESCE(pe.first_name, ''), ' ', COALESCE(pe.last_name, '')) LIKE ?
            ORDER BY t.team_name ASC
            LIMIT 4
        ", [$like, $like, $like]))->map(fn ($row) => [
            'type' => 'Team Profile',
            'title' => $row->team_name,
            'subtitle' => 'Matched from player squad',
            'url' => route('teams.show', $row->team_id),
            'score' => 109,
        ]);

        $teamsByManager = collect(DB::select("
            SELECT DISTINCT t.team_id, t.team_name
            FROM managers m
            JOIN persons p ON p.person_id = m.person_id
            JOIN teams t ON t.team_id = m.team_id
            WHERE p.first_name LIKE ?
               OR p.last_name LIKE ?
               OR CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.last_name, '')) LIKE ?
            ORDER BY t.team_name ASC
            LIMIT 4
        ", [$like, $like, $like]))->map(fn ($row) => [
            'type' => 'Team Profile',
            'title' => $row->team_name,
            'subtitle' => 'Matched from manager record',
            'url' => route('teams.show', $row->team_id),
            'score' => 108,
        ]);

        $teamsBySponsor = collect(DB::select("
            SELECT DISTINCT t.team_id, t.team_name
            FROM sponsors s
            JOIN teams t ON t.team_id = s.team_id
            WHERE s.sponsor_name LIKE ?
            ORDER BY t.team_name ASC
            LIMIT 4
        ", [$like]))->map(fn ($row) => [
            'type' => 'Team Profile',
            'title' => $row->team_name,
            'subtitle' => 'Matched from sponsor record',
            'url' => route('teams.show', $row->team_id),
            'score' => 107,
        ]);

        return $teamsByName
            ->merge($teamsByPlayer)
            ->merge($teamsByManager)
            ->merge($teamsBySponsor);
    }

    private function buildTransferArticleResults(string $like): Collection
    {
        return collect(DB::select("
            SELECT transfer_post_id, title, posted_at, created_at
            FROM transfer_posts
            WHERE status = 'published'
              AND (
                    title LIKE ?
                    OR COALESCE(summary, '') LIKE ?
                    OR content LIKE ?
                  )
            ORDER BY COALESCE(posted_at, created_at) DESC, transfer_post_id DESC
            LIMIT 4
        ", [$like, $like, $like]))->map(fn ($row) => [
            'type' => 'Transfer Article',
            'title' => $row->title,
            'subtitle' => 'Open transfer news article',
            'url' => route('transfers.show', $row->transfer_post_id),
            'score' => 106,
        ]);
    }
}