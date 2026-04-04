<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Standings</title>
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

        .rank{ display:inline-block; min-width:32px; padding:4px 8px; border-radius:999px; border:1px solid rgba(255,255,255,0.25); text-align:center; }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <div class="header-row">
                <h2 style="margin:0;">League Standings</h2>
                <span class="badge">{{ ($standings ?? collect())->count() }} rows</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('standings.index') }}" class="filter-grid">
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
                        <label for="min_points">Minimum Points</label>
                        <input type="number" name="min_points" id="min_points" min="0" value="{{ $minPoints ?? '' }}" placeholder="e.g. 10">
                    </div>

                    <div class="filter-field">
                        <label for="sort_by">Sort By</label>
                        <select name="sort_by" id="sort_by">
                            <option value="table" @selected(($sortBy ?? 'table') === 'table')>League Table Order</option>
                            <option value="points_asc" @selected(($sortBy ?? '') === 'points_asc')>Points Low to High</option>
                            <option value="goal_diff_desc" @selected(($sortBy ?? '') === 'goal_diff_desc')>Goal Difference High to Low</option>
                            <option value="team_asc" @selected(($sortBy ?? '') === 'team_asc')>Team Name</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">Apply Filters</button>
                        <a href="{{ route('standings.index') }}" class="btn">Reset</a>
                    </div>
                </form>
            </div>

            @if(($standings ?? collect())->count())
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Team</th>
                            <th>P</th>
                            <th>W</th>
                            <th>D</th>
                            <th>L</th>
                            <th>GF</th>
                            <th>GA</th>
                            <th>GD</th>
                            <th>PTS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($standings as $index => $row)
                            <tr>
                                <td><span class="rank">{{ $index + 1 }}</span></td>
                                <td>{{ $row->team_name ?? 'N/A' }}</td>
                                <td>{{ $row->played ?? 0 }}</td>
                                <td>{{ $row->wins ?? 0 }}</td>
                                <td>{{ $row->draws ?? 0 }}</td>
                                <td>{{ $row->losses ?? 0 }}</td>
                                <td>{{ $row->goals_scored ?? 0 }}</td>
                                <td>{{ $row->goals_conceded ?? 0 }}</td>
                                <td>{{ $row->goal_diff ?? 0 }}</td>
                                <td><b>{{ $row->points ?? 0 }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No standings data matched the selected filter.</p>
            @endif
        </div>
    </div>
</div>
@include('partials.footer')
</body>
</html>
