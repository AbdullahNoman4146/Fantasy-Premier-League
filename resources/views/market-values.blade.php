<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Market Values</title>
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

    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <div class="header-row">
                <h2 style="margin:0;">Player Market Values</h2>
                <span class="badge">{{ ($marketValues ?? collect())->count() }} entries</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('market-values.index') }}" class="filter-grid">
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
                        <label for="position">Position</label>
                        <select name="position" id="position">
                            <option value="">All Positions</option>
                            @foreach(($positions ?? collect()) as $positionOption)
                                <option value="{{ $positionOption }}" @selected(($position ?? '') === $positionOption)>{{ $positionOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="season">Season</label>
                        <select name="season" id="season">
                            <option value="">All Seasons</option>
                            @foreach(($seasons ?? collect()) as $seasonOption)
                                <option value="{{ $seasonOption }}" @selected(($season ?? '') === $seasonOption)>{{ $seasonOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="sort_by">Sort By</label>
                        <select name="sort_by" id="sort_by">
                            <option value="value_desc" @selected(($sortBy ?? 'value_desc') === 'value_desc')>Highest Value First</option>
                            <option value="value_asc" @selected(($sortBy ?? '') === 'value_asc')>Lowest Value First</option>
                            <option value="season_desc" @selected(($sortBy ?? '') === 'season_desc')>Latest Season First</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn">Apply Filters</button>
                        <a href="{{ route('market-values.index') }}" class="btn">Reset</a>
                    </div>
                </form>
            </div>

            @if(($marketValues ?? collect())->count())
                <table>
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Team</th>
                            <th>Position</th>
                            <th>Season</th>
                            <th>Value</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($marketValues as $row)
                        @php $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')); @endphp
                        <tr>
                            <td>{{ $fullName !== '' ? $fullName : 'N/A' }} <span class="badge">#{{ $row->jersey_number }}</span></td>
                            <td>{{ $row->team_name ?? 'N/A' }}</td>
                            <td>{{ $row->position ?? 'N/A' }}</td>
                            <td>{{ $row->season ?? 'N/A' }}</td>
                            <td><b>{{ $row->currency }} {{ number_format((float)$row->market_value, 2) }}</b></td>
                            <td>{{ $row->notes ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No market value entries matched the selected filter.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
