<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }}</title>
    <style>
        body{ margin:0; font-family:Arial,sans-serif; color:white; background:url('/images/back_pic.jpeg') no-repeat center center fixed; background-size:cover; }
        .overlay{ background:rgba(0,0,0,0.78); min-height:100vh; padding:0; }
        .page{ padding:30px; }
        .container{ max-width:1100px; margin:25px auto; display:grid; grid-template-columns:2fr 1fr; gap:16px; }
        .article,.sidebar{ background:rgba(0,0,0,0.55); border:2px solid rgba(255,255,255,0.25); border-radius:12px; padding:18px; }
        .meta{ opacity:.8; font-size:13px; margin-bottom:12px; }
        .content{ line-height:1.7; white-space:pre-wrap; }
        .recent-link{ display:block; color:#fff; text-decoration:none; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.12); }
        @media (max-width:900px){ .container{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <div class="article">
                <div class="meta">Posted: {{ $post->posted_at ?? $post->created_at ?? 'N/A' }}</div>
                <h1 style="margin-top:0;">{{ $post->title }}</h1>
                @if($post->summary)
                    <p style="font-size:18px; opacity:.95;">{{ $post->summary }}</p>
                @endif
                <div class="content">{{ $post->content }}</div>
            </div>
            <div class="sidebar">
                <h3 style="margin-top:0;">Recent Transfer Posts</h3>
                @forelse($recentPosts as $recent)
                    <a class="recent-link" href="{{ route('transfers.show', $recent->transfer_post_id) }}">
                        <div><b>{{ $recent->title }}</b></div>
                        <div class="meta" style="margin:4px 0 0;">{{ $recent->posted_at ?? $recent->created_at ?? 'N/A' }}</div>
                    </a>
                @empty
                    <p>No other posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
</body>
</html>