<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer News</title>
    <style>
        body{ margin:0; font-family:Arial,sans-serif; color:white; background:url('/images/back_pic.jpeg') no-repeat center center fixed; background-size:cover; }
        .overlay{ background:rgba(0,0,0,0.75); min-height:100vh; padding:0; }
        .page{ padding:30px; }
        .container{ max-width:1100px; margin:25px auto; background:rgba(0,0,0,0.55); border:2px solid rgba(255,255,255,0.25); border-radius:12px; padding:18px; }
        .news-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-top:14px; }
        .card{ border:1px solid rgba(255,255,255,0.2); border-radius:12px; padding:16px; background:rgba(255,255,255,0.06); }
        .meta{ opacity:.8; font-size:13px; margin-bottom:10px; }
        .link{ display:inline-block; margin-top:10px; color:#fff; text-decoration:none; border:1px solid rgba(255,255,255,0.25); padding:8px 12px; border-radius:999px; }
        @media (max-width:900px){ .news-grid{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <h2 style="margin:0;">Transfer News & Blogs</h2>
            <p style="opacity:.85; margin-top:6px;">Published transfer stories posted from the admin panel.</p>

            @if(($posts ?? collect())->count())
                <div class="news-grid">
                    @foreach($posts as $post)
                        <div class="card">
                            <div class="meta">Posted: {{ $post->posted_at ?? $post->created_at ?? 'N/A' }}</div>
                            <h3 style="margin:0 0 10px;">{{ $post->title }}</h3>
                            <p style="margin:0;">{{ $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 180) }}</p>
                            <a class="link" href="{{ route('transfers.show', $post->transfer_post_id) }}">Read More</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin-top:12px;">No transfer posts have been published yet.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>