<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer News</title>
    <style>

        body{
            margin:0;
            font-family: Arial, sans-serif;
            color:white;
            background:url('/images/back_pic.jpeg') no-repeat center center fixed;
            background-size:cover;
        }
        .overlay{
            background:rgba(0,0,0,0.75);
            min-height:100vh;
            padding:0;
        }
        .page{ padding:30px; }
        .container{
            max-width:1150px;
            margin:25px auto;
            background:rgba(0,0,0,0.55);
            border:2px solid rgba(255,255,255,0.25);
            border-radius:16px;
            padding:18px;
        }
        .header-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
        }
        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.25);
            font-size:12px;
            opacity:.95;
        }
        .filter-panel,
        .search-panel{
            margin-top:16px;
            padding:16px;
            border-radius:14px;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.12);
        }
        .filter-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
            gap:14px;
            align-items:end;
        }
        .filter-field label{
            display:block;
            font-size:13px;
            margin-bottom:7px;
            color:rgba(255,255,255,0.88);
        }
        .filter-field input,
        .filter-field select{
            width:100%;
            padding:11px 12px;
            border-radius:10px;
            border:1px solid rgba(255,255,255,0.16);
            background:rgba(255,255,255,0.08);
            color:#fff;
            outline:none;
        }
        .filter-field option{
            color:#111;
        }
        .filter-actions{
            display:flex;
            gap:10px;
            align-items:end;
            flex-wrap:wrap;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:44px;
            padding:10px 16px;
            border-radius:12px;
            border:1px solid rgba(255,255,255,0.18);
            background:rgba(255,255,255,0.10);
            color:#fff;
            text-decoration:none;
            cursor:pointer;
            font-weight:700;
        }
        .btn:hover{
            background:rgba(255,255,255,0.16);
        }
        .summary{
            margin-top:14px;
            font-size:13px;
            opacity:.88;
        }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:16px;
        }
        th, td{
            padding:11px 10px;
            border-bottom:1px solid rgba(255,255,255,0.15);
            text-align:left;
        }
        th{
            font-size:14px;
            opacity:.9;
            background:rgba(255,255,255,0.10);
        }
        .empty{
            margin-top:16px;
            opacity:.85;
        }

        .news-grid{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-top:16px; }
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
            <div class="header-row">
                <h2 style="margin:0;">Transfer News & Blogs</h2>
                <span class="badge">{{ ($posts ?? collect())->count() }} posts</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('transfers.index') }}" class="filter-grid">
                    <div class="filter-field">
                        <label for="year">Year</label>
                        <select name="year" id="year">
                            <option value="">All Years</option>
                            @foreach(($years ?? collect()) as $yearOption)
                                <option value="{{ $yearOption }}" @selected((string)($year ?? '') === (string)$yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="month">Month</label>
                        <select name="month" id="month">
                            <option value="">All Months</option>
                            <option value="1" @selected((string)($month ?? '') === '1')>January</option>
                            <option value="2" @selected((string)($month ?? '') === '2')>February</option>
                            <option value="3" @selected((string)($month ?? '') === '3')>March</option>
                            <option value="4" @selected((string)($month ?? '') === '4')>April</option>
                            <option value="5" @selected((string)($month ?? '') === '5')>May</option>
                            <option value="6" @selected((string)($month ?? '') === '6')>June</option>
                            <option value="7" @selected((string)($month ?? '') === '7')>July</option>
                            <option value="8" @selected((string)($month ?? '') === '8')>August</option>
                            <option value="9" @selected((string)($month ?? '') === '9')>September</option>
                            <option value="10" @selected((string)($month ?? '') === '10')>October</option>
                            <option value="11" @selected((string)($month ?? '') === '11')>November</option>
                            <option value="12" @selected((string)($month ?? '') === '12')>December</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="sort_by">Sort By</label>
                        <select name="sort_by" id="sort_by">
                            <option value="latest" @selected(($sortBy ?? 'latest') === 'latest')>Latest First</option>
                            <option value="oldest" @selected(($sortBy ?? '') === 'oldest')>Oldest First</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">Apply Filters</button>
                        <a href="{{ route('transfers.index') }}" class="btn">Reset</a>
                    </div>
                </form>
            </div>

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
                <p class="empty">No transfer posts matched the selected filter.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
