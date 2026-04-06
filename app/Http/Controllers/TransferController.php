<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $year = (string) $request->query('year', '');
        $month = (string) $request->query('month', '');
        $sortBy = (string) $request->query('sort_by', 'latest');

        $postsQuery = DB::table('transfer_posts')
            ->select(
                'transfer_post_id',
                'title',
                'summary',
                'content',
                'status',
                'posted_at',
                'created_at'
            )
            ->where('status', 'published');

        if ($year !== '') {
            $postsQuery->whereRaw('YEAR(COALESCE(posted_at, created_at)) = ?', [$year]);
        }

        if ($month !== '') {
            $postsQuery->whereRaw('MONTH(COALESCE(posted_at, created_at)) = ?', [$month]);
        }

        if ($sortBy === 'oldest') {
            $postsQuery->orderByRaw('COALESCE(posted_at, created_at) ASC')
                ->orderBy('transfer_post_id');
        } else {
            $sortBy = 'latest';
            $postsQuery->orderByRaw('COALESCE(posted_at, created_at) DESC')
                ->orderByDesc('transfer_post_id');
        }

        $posts = $postsQuery->get();

        $years = DB::table('transfer_posts')
            ->where('status', 'published')
            ->selectRaw('YEAR(COALESCE(posted_at, created_at)) AS year_value')
            ->whereRaw('COALESCE(posted_at, created_at) IS NOT NULL')
            ->distinct()
            ->orderByDesc('year_value')
            ->pluck('year_value');

        return view('transfers', compact('posts', 'years', 'year', 'month', 'sortBy'));
    }

    public function show(int $id)
    {
        $post = DB::selectOne("
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
            WHERE transfer_post_id = ? AND status = 'published'
            LIMIT 1
        ", [$id]);

        abort_if(!$post, 404);

        $recentPosts = collect(DB::select("
            SELECT transfer_post_id, title, posted_at, created_at
            FROM transfer_posts
            WHERE status = 'published' AND transfer_post_id != ?
            ORDER BY COALESCE(posted_at, created_at) DESC, transfer_post_id DESC
            LIMIT 5
        ", [$id]));

        return view('transfer-show', compact('post', 'recentPosts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'posted_at' => ['prohibited'],
        ]);

        $publishedAt = $validated['status'] === 'published' ? now() : null;

        DB::table('transfer_posts')->insert([
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'status' => $validated['status'],
            'posted_at' => $publishedAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.panel')->with('success', 'Transfer post created successfully.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'transfer_post_id' => ['required', 'integer', 'exists:transfer_posts,transfer_post_id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'posted_at' => ['prohibited'],
        ]);

        $existingPost = DB::table('transfer_posts')
            ->select('posted_at', 'status')
            ->where('transfer_post_id', $validated['transfer_post_id'])
            ->first();

        $publishedAt = null;
        if ($validated['status'] === 'published') {
            $publishedAt = $existingPost?->posted_at ?: now();
        }

        DB::table('transfer_posts')
            ->where('transfer_post_id', $validated['transfer_post_id'])
            ->update([
                'title' => $validated['title'],
                'summary' => $validated['summary'] ?? null,
                'content' => $validated['content'],
                'status' => $validated['status'],
                'posted_at' => $publishedAt,
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.panel')->with('success', 'Transfer post updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'transfer_post_id' => ['required', 'integer', 'exists:transfer_posts,transfer_post_id'],
        ]);

        DB::delete('DELETE FROM transfer_posts WHERE transfer_post_id = ?', [$request->transfer_post_id]);

        return redirect()->route('admin.panel')->with('success', 'Transfer post deleted successfully.');
    }
}