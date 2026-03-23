<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Results</title>
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

        .result-grid{ display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-top:16px; }
        .card{ border:1px solid rgba(255,255,255,0.2); border-radius:14px; padding:16px; background:rgba(255,255,255,0.06); }
        .teams{ font-size:20px; font-weight:bold; margin:10px 0; }
        .score{ font-size:34px; font-weight:bold; margin:8px 0; }
        .muted{ opacity:.85; font-size:13px; }
        @media (max-width: 900px){ .result-grid{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <div class="header-row">
                <h2 style="margin:0;">Finished Match Results</h2>
                <span class="badge">{{ ($results ?? collect())->count() }} matches</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('results.index') }}" class="filter-grid">
                    <div class="filter-field">
                        <label for="team_id">Team</label>
                        <select name="team_id" id="team_id">
                            <option value="">All Teams</option>
                            @foreach(($teams ?? collect()) as $team)
                                <option value="{{ $team->team_id }}" @selected((string)($teamId ?? '') === (string)$team->team_id)>{{ $team->team_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="result_type">Result Type</label>
                        <select name="result_type" id="result_type">
                            <option value="all" @selected(($resultType ?? 'all') === 'all')>All Results</option>
                            <option value="home_win" @selected(($resultType ?? '') === 'home_win')>Home Win</option>
                            <option value="away_win" @selected(($resultType ?? '') === 'away_win')>Away Win</option>
                            <option value="draw" @selected(($resultType ?? '') === 'draw')>Draw</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="sort_by">Sort By</label>
                        <select name="sort_by" id="sort_by">
                            <option value="latest" @selected(($sortBy ?? 'latest') === 'latest')>Latest First</option>
                            <option value="oldest" @selected(($sortBy ?? '') === 'oldest')>Oldest First</option>
                            <option value="goal_high" @selected(($sortBy ?? '') === 'goal_high')>Most Goals</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">Apply Filters</button>
                        <a href="{{ route('results.index') }}" class="btn">Reset</a>
                    </div>
                </form>
            </div>

            @if(($results ?? collect())->count())
                <div class="result-grid">
                    @foreach($results as $result)
                        <div class="card">
                            <div class="muted">Played: {{ $result->kickoff_at ?? 'N/A' }}</div>
                            <div class="teams">{{ $result->team1 ?? 'N/A' }} vs {{ $result->team2 ?? 'N/A' }}</div>
                            <div class="score">{{ $result->score1 ?? 0 }} - {{ $result->score2 ?? 0 }}</div>
                            <div class="muted">Match Time: <span class="badge">{{ $result->match_time ?: 'Not set' }}</span></div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="empty">No completed results matched the selected filter.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
