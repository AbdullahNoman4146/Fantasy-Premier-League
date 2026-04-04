<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Players</title>
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
        .page{
            padding:30px;
        }

        .search-shell{
            max-width:1150px;
            margin:25px auto 16px auto;
            padding:18px;
            border-radius:18px;
            background:rgba(8,12,28,0.78);
            border:1px solid rgba(255,255,255,0.14);
            box-shadow:0 16px 40px rgba(0,0,0,0.28);
            backdrop-filter:blur(8px);
        }
        .search-shell-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:14px;
        }
        .search-shell-title{
            margin:0;
            font-size:22px;
            font-weight:700;
        }
        .search-shell-subtitle{
            margin:4px 0 0 0;
            font-size:13px;
            color:rgba(255,255,255,0.76);
        }

        .search-form{
            display:flex;
            gap:12px;
            align-items:center;
            flex-wrap:wrap;
        }
        .search-input-wrap{
            position:relative;
            flex:1 1 620px;
            min-width:260px;
        }
        .search-input-wrap .search-icon{
            position:absolute;
            left:16px;
            top:50%;
            transform:translateY(-50%);
            font-size:17px;
            opacity:0.82;
            pointer-events:none;
        }
        .search-input{
            width:100%;
            height:52px;
            padding:0 18px 0 46px;
            border-radius:14px;
            border:1px solid rgba(255,255,255,0.16);
            background:rgba(255,255,255,0.10);
            color:#fff;
            outline:none;
            font-size:15px;
            transition:all .2s ease;
            box-sizing:border-box;
        }
        .search-input::placeholder{
            color:rgba(255,255,255,0.62);
        }
        .search-input:focus{
            border-color:rgba(255,255,255,0.34);
            background:rgba(255,255,255,0.14);
            box-shadow:0 0 0 4px rgba(255,255,255,0.05);
        }

        .action-buttons{
            display:flex;
            gap:10px;
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
        .btn-primary{
            background:linear-gradient(135deg, rgba(49,130,246,0.95), rgba(37,99,235,0.92));
            color:#fff;
            border-color:rgba(255,255,255,0.10);
        }
        .btn-primary:hover{
            transform:translateY(-1px);
            box-shadow:0 10px 22px rgba(37,99,235,0.28);
        }
        .btn-secondary{
            background:rgba(255,255,255,0.10);
            color:#fff;
        }
        .btn-secondary:hover{
            background:rgba(255,255,255,0.17);
        }

        .search-note{
            margin-top:10px;
            font-size:12px;
            color:rgba(255,255,255,0.72);
        }

        .container{
            max-width:1150px;
            margin:0 auto 25px auto;
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

        .filter-panel{
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
            box-sizing:border-box;
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

        @media (max-width: 768px){
            .page{
                padding:18px;
            }
            .search-shell,
            .container{
                margin-left:auto;
                margin-right:auto;
            }
            .search-form{
                flex-direction:column;
                align-items:stretch;
            }
            .action-buttons{
                width:100%;
            }
            .action-buttons .btn{
                flex:1;
            }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">

        <div class="search-shell">
            <div class="search-shell-header">
                <div>
                    <h2 class="search-shell-title">Search Players</h2>
                </div>
                <span class="badge">{{ ($players ?? collect())->count() }} players found</span>
            </div>

            <form method="GET" action="{{ route('players.index') }}" class="search-form" id="playerSearchForm">
                <input type="hidden" name="team_id" value="{{ $teamId ?? '' }}">
                <input type="hidden" name="position" value="{{ $position ?? '' }}">
                <input type="hidden" name="nationality" value="{{ $nationality ?? '' }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'team' }}">

                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input
                        type="text"
                        name="player_search"
                        id="player_search"
                        class="search-input"
                        value="{{ $playerSearch ?? '' }}"
                        placeholder="Search any player by first name or last name"
                        autocomplete="off"
                    >
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('players.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>

        </div>

        <div class="container">
            <div class="header-row">
                <h2 style="margin:0;">Players</h2>
                <span class="badge">{{ ($players ?? collect())->count() }} players</span>
            </div>

            <div class="filter-panel">
                <form method="GET" action="{{ route('players.index') }}" class="filter-grid">
                    <input type="hidden" name="player_search" value="{{ $playerSearch ?? '' }}">

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
                        <label for="nationality">Nationality</label>
                        <select name="nationality" id="nationality">
                            <option value="">All Nationalities</option>
                            @foreach(($nationalities ?? collect()) as $nationalityOption)
                                <option value="{{ $nationalityOption }}" @selected(($nationality ?? '') === $nationalityOption)>{{ $nationalityOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="sort_by">Sort By</label>
                        <select name="sort_by" id="sort_by">
                            <option value="team" @selected(($sortBy ?? 'team') === 'team')>Team Then Jersey</option>
                            <option value="name" @selected(($sortBy ?? '') === 'name')>Player Name</option>
                            <option value="jersey" @selected(($sortBy ?? '') === 'jersey')>Jersey Number</option>
                            <option value="goals" @selected(($sortBy ?? '') === 'goals')>Most Goals</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-secondary">Apply Filters</button>
                        <a href="{{ route('players.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            @if(($players ?? collect())->count())
                <table>
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Team</th>
                            <th>Jersey Number</th>
                            <th>Nationality</th>
                            <th>Position</th>
                            <th>Goals</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                            @php
                                $fullName = trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? ''));
                            @endphp
                            <tr>
                                <td>{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                                <td>{{ $player->team_name ?? 'N/A' }}</td>
                                <td><span class="badge">#{{ $player->jersey_number ?? 'N/A' }}</span></td>
                                <td>{{ $player->nationality ?? 'N/A' }}</td>
                                <td>{{ $player->position ?? 'N/A' }}</td>
                                <td><span class="badge">{{ $player->goals ?? 0 }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No players matched the current search and filter selection.</p>
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('playerSearchForm');
        const input = document.getElementById('player_search');

        if (!form || !input) return;

        let timer = null;

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                form.submit();
            }, 350);
        });
    })();
</script>
@include('partials.footer')
</body>
</html>