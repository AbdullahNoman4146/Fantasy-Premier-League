<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teams</title>
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

        .team-row{ cursor:pointer; transition:background 0.18s ease; }
        .team-row:hover{ background:rgba(255,255,255,0.08); }
        .team-row:focus{ outline:2px solid rgba(255,255,255,0.45); outline-offset:-2px; }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <div class="header-row">
                <h2 style="margin:0;">Teams</h2>
                <span class="badge">{{ ($teams ?? collect())->count() }} teams</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('teams.index') }}" class="filter-grid">
                    <div class="filter-field">
                        <label for="manager_status">Manager Status</label>
                        <select name="manager_status" id="manager_status">
                            <option value="all" @selected(($managerStatus ?? 'all') === 'all')>All Teams</option>
                            <option value="with_manager" @selected(($managerStatus ?? '') === 'with_manager')>With Manager</option>
                            <option value="without_manager" @selected(($managerStatus ?? '') === 'without_manager')>Without Manager</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="strength_order">Strength Order</label>
                        <select name="strength_order" id="strength_order">
                            <option value="" @selected(($strengthOrder ?? '') === '')>A-Z Default</option>
                            <option value="high_low" @selected(($strengthOrder ?? '') === 'high_low')>Strength High to Low</option>
                            <option value="low_high" @selected(($strengthOrder ?? '') === 'low_high')>Strength Low to High</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">Apply Filters</button>
                        <a href="{{ route('teams.index') }}" class="btn">Reset</a>
                    </div>
                </form>
            </div>

            @if(($teams ?? collect())->count())
                <table>
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Manager</th>
                            <th>Strength</th>
                            <th>GF</th>
                            <th>GA</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($teams as $t)
                        <tr class="team-row" tabindex="0" onclick="window.location='{{ route('teams.show', $t->team_id) }}'" onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('teams.show', $t->team_id) }}'; }">
                            <td>{{ $t->team_name ?? 'N/A' }}</td>
                            <td>{{ $t->manager_name ?? 'Unassigned' }}</td>
                            <td>{{ $t->strength ?? 'N/A' }}</td>
                            <td>{{ $t->goals_scored ?? 0 }}</td>
                            <td>{{ $t->goals_conceded ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No teams found.</p>
            @endif
        </div>
    </div>
</div>
@include('partials.footer')
</body>
</html>
