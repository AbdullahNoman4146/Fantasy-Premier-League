<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $posts = collect(DB::select("
            SELECT
                transfer_post_id,
                title,
                summary,
                content,
                status,
                posted_at,
                created_at
            FROM transfer_posts
            WHERE status = 'published'
            ORDER BY COALESCE(posted_at, created_at) DESC, transfer_post_id DESC
        "));

        return view('transfers', compact('posts'));
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
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'posted_at' => ['nullable', 'date'],
        ]);

        DB::table('transfer_posts')->insert([
            'title' => $request->title,
            'summary' => $request->summary,
            'content' => $request->content,
            'status' => $request->status,
            'posted_at' => $request->posted_at,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.panel')->with('success', 'Transfer post created successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'transfer_post_id' => ['required', 'integer', 'exists:transfer_posts,transfer_post_id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:published,draft'],
            'posted_at' => ['nullable', 'date'],
        ]);

        DB::table('transfer_posts')
            ->where('transfer_post_id', $request->transfer_post_id)
            ->update([
                'title' => $request->title,
                'summary' => $request->summary,
                'content' => $request->content,
                'status' => $request->status,
                'posted_at' => $request->posted_at,
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