<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        *{ box-sizing:border-box; }

        body{
            font-family:Arial,sans-serif;
            padding:0;
            margin:0;
            background:#f6f7fb;
            color:#1e1e1e;
        }

        .page-wrap{
            padding:20px;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:16px;
        }

        .toolbar-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .toolbar-actions button{
            padding:10px 14px;
            border:none;
            border-radius:10px;
            background:#141b34;
            color:#fff;
            cursor:pointer;
        }

        .admin-shell{
            max-width:1200px;
            margin:0 auto;
            transition:all .25s ease;
        }

        .admin-shell.wide{
            max-width:100%;
        }

        details.panel{
            background:#fff;
            border:1px solid #ddd;
            border-radius:14px;
            margin-bottom:16px;
            overflow:hidden;
            box-shadow:0 2px 10px rgba(0,0,0,0.04);
        }

        details.panel > summary{
            list-style:none;
            cursor:pointer;
            padding:16px 18px;
            font-weight:800;
            font-size:18px;
            background:#f9fafc;
            border-bottom:1px solid #eee;
        }

        details.panel > summary::-webkit-details-marker{
            display:none;
        }

        .panel-body{
            padding:18px;
        }

        .card{
            border:1px solid #ddd;
            padding:15px;
            margin-bottom:12px;
            border-radius:10px;
            background:#fff;
        }

        label{
            font-weight:bold;
            display:block;
            margin-bottom:4px;
        }

        input, select{
            width:100%;
            padding:10px;
            margin:6px 0 12px;
            border:1px solid #ccc;
            border-radius:10px;
            background:#fff;
        }

        button[type="submit"]{
            padding:10px 14px;
            cursor:pointer;
            border:none;
            border-radius:10px;
            background:#1e3a8a;
            color:#fff;
        }

        .success{ color:green; }
        .error{ color:#b00020; }

        .error-box{
            border:1px solid #f2b8c6;
            background:#fff0f3;
            padding:10px;
            border-radius:10px;
            margin:12px 0;
        }

        .hint{
            font-size:12px;
            opacity:.75;
            margin-top:-6px;
            margin-bottom:10px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
            background:#fff;
        }

        th, td{
            padding:10px;
            border-bottom:1px solid #eee;
            text-align:left;
            vertical-align:top;
        }

        th{
            font-weight:800;
            font-size:14px;
            background:#fafafa;
        }

        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid #ddd;
            font-size:12px;
            background:#f8f8f8;
        }

        .grid-2{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:14px;
        }

        @media (max-width: 900px){
            .grid-2{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>
<body>



@php
    $defaultSponsorOptions = [
        'Puma','Nike','Spotify','Adidas',
        'Emirates','Etihad Airways','Qatar Airways',
        'Rakuten','AIA','Standard Chartered',
        'Three','Vodafone','Chevrolet',
        'New Balance','Umbro','Castore'
    ];

    $finalSponsorOptions = (isset($sponsorOptions) && is_array($sponsorOptions) && count($sponsorOptions))
        ? $sponsorOptions
        : $defaultSponsorOptions;

    $playerPositions = [
        'Goalkeeper',
        'Defender',
        'Midfielder',
        'Forward'
    ];
@endphp

<div class="page-wrap">

    <div class="toolbar">
        <h1 style="margin:0;">Admin Match Manager</h1>

        <div class="toolbar-actions">
            <button type="button" id="workspaceToggle">Wide Workspace</button>
            <button type="button" id="expandAllBtn">Expand All</button>
            <button type="button" id="collapseAllBtn">Collapse All</button>
        </div>
    </div>

    @if(session('success'))
        <p class="success"><b>{{ session('success') }}</b></p>
    @endif

    @if($errors->any())
        <div class="error-box">
            <p class="error" style="margin:0 0 6px;"><b>Validation Errors:</b></p>
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $err)
                    <li class="error">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="adminShell" class="admin-shell">

        {{-- -------------------- ADD TEAM -------------------- --}}
        <details class="panel" open>
            <summary>Add New Team</summary>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.team.create') }}">
                    @csrf

                    <label>Team Name</label>
                    <input type="text" name="team_name" value="{{ old('team_name') }}" placeholder="e.g. Manchester United" required>

                    <div class="grid-2">
                        <div>
                            <label>Strength (optional)</label>
                            <input type="number" name="strength" value="{{ old('strength') }}" placeholder="e.g. 80">
                        </div>

                        <div>
                            <label>Manager ID (optional)</label>
                            <input type="number" name="manager_id" value="{{ old('manager_id') }}" placeholder="e.g. 1">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div>
                            <label>Goals Scored (optional)</label>
                            <input type="number" name="goals_scored" value="{{ old('goals_scored') }}" placeholder="e.g. 0">
                        </div>

                        <div>
                            <label>Goals Conceded (optional)</label>
                            <input type="number" name="goals_conceded" value="{{ old('goals_conceded') }}" placeholder="e.g. 0">
                        </div>
                    </div>

                    <button type="submit">Add Team</button>
                </form>
            </div>
        </details>

        {{-- -------------------- SPONSOR ADMIN -------------------- --}}
        <details class="panel" open>
            <summary>Sponsor Admin</summary>
            <div class="panel-body">
                <p class="hint">Assign a sponsor to a team using dropdowns.</p>

                @if(($teams ?? collect())->count() == 0)
                    <p class="error"><b>No teams found.</b> Add teams first, then assign sponsors.</p>
                @else
                    <form method="POST" action="{{ route('admin.sponsors.create') }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

                        <label>Select Sponsor</label>
                        <select name="sponsor_name" required>
                            <option value="" disabled {{ old('sponsor_name') ? '' : 'selected' }}>Select sponsor</option>
                            @foreach($finalSponsorOptions as $opt)
                                <option value="{{ $opt }}" @selected(old('sponsor_name') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>

                        <label>Select Team</label>
                        <select name="team_id" required>
                            <option value="" disabled {{ old('team_id') ? '' : 'selected' }}>Select team</option>
                            @foreach(($teams ?? collect()) as $t)
                                <option value="{{ $t->team_id }}" @selected((string)old('team_id') === (string)$t->team_id)>
                                    {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                </option>
                            @endforeach
                        </select>

                        <button type="submit">Assign Sponsor</button>
                    </form>
                @endif

                @if(($sponsors ?? collect())->count())
                    <h3 style="margin-top:18px;">Current Sponsor Assignments</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Sponsor ID</th>
                                <th>Sponsor Name</th>
                                <th>Team Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sponsors as $s)
                                <tr>
                                    <td><span class="badge">{{ $s->sponsor_id }}</span></td>
                                    <td>{{ $s->sponsor_name ?? 'N/A' }}</td>
                                    <td>{{ $s->team_name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </details>

        {{-- -------------------- PLAYER ADMIN -------------------- --}}
        <details class="panel" open>
            <summary>Player Admin</summary>
            <div class="panel-body">
                <p class="hint">Add a player, assign a team, jersey number, nationality, and position.</p>

                @if(($teams ?? collect())->count() == 0)
                    <p class="error"><b>No teams found.</b> Add teams first, then add players.</p>
                @else
                    <form method="POST" action="{{ route('admin.player.create') }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

                        <div class="grid-2">
                            <div>
                                <label>First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Mohamed" required>
                            </div>

                            <div>
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Salah">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label>Select Team</label>
                                <select name="team_id" required>
                                    <option value="" disabled {{ old('team_id') ? '' : 'selected' }}>Select team</option>
                                    @foreach(($teams ?? collect()) as $t)
                                        <option value="{{ $t->team_id }}" @selected((string)old('team_id') === (string)$t->team_id)>
                                            {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label>Jersey Number</label>
                                <input type="number" name="jersey_number" value="{{ old('jersey_number') }}" min="1" max="99" placeholder="e.g. 10" required>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label>Nationality</label>
                                <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Egyptian" required>
                            </div>

                            <div>
                                <label>Position</label>
                                <select name="position" required>
                                    <option value="" disabled {{ old('position') ? '' : 'selected' }}>Select position</option>
                                    @foreach($playerPositions as $pos)
                                        <option value="{{ $pos }}" @selected(old('position') === $pos)>
                                            {{ $pos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <button type="submit">Add Player</button>
                    </form>
                @endif

                @if(($players ?? collect())->count())
                    <h3 style="margin-top:18px;">Current Players</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Player Name</th>
                                <th>Team</th>
                                <th>Jersey</th>
                                <th>Nationality</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($players as $p)
                                @php
                                    $fullName = trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? ''));
                                @endphp
                                <tr>
                                    <td>{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                                    <td>{{ $p->team_name ?? 'N/A' }}</td>
                                    <td><span class="badge">#{{ $p->jersey_number ?? 'N/A' }}</span></td>
                                    <td>{{ $p->nationality ?? 'N/A' }}</td>
                                    <td>{{ $p->position ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </details>

        {{-- -------------------- CREATE MATCH -------------------- --}}
        <details class="panel" open>
            <summary>Create New Match</summary>
            <div class="panel-body">
                @if(($teams ?? collect())->count() == 0)
                    <p class="error"><b>No teams found.</b> Add teams first, then create matches.</p>
                @else
                    <form method="POST" action="{{ route('admin.match.create') }}">
                        @csrf

                        <label>Team 1</label>
                        <select name="team1" required>
                            <option value="" disabled {{ old('team1') ? '' : 'selected' }}>Select Team 1</option>
                            @foreach(($teams ?? collect()) as $t)
                                <option value="{{ $t->team_id }}" @selected((string)old('team1') === (string)$t->team_id)>
                                    {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                </option>
                            @endforeach
                        </select>

                        <label>Team 2</label>
                        <select name="team2" required>
                            <option value="" disabled {{ old('team2') ? '' : 'selected' }}>Select Team 2</option>
                            @foreach(($teams ?? collect()) as $t)
                                <option value="{{ $t->team_id }}" @selected((string)old('team2') === (string)$t->team_id)>
                                    {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                </option>
                            @endforeach
                        </select>

                        <p class="hint">Team 1 and Team 2 should be different.</p>

                        <label>Status</label>
                        <select name="status" required>
                            <option value="current" @selected(old('status') === 'current')>current</option>
                            <option value="upcoming" @selected(old('status', 'upcoming') === 'upcoming')>upcoming</option>
                            <option value="finished" @selected(old('status') === 'finished')>finished</option>
                        </select>

                        <label>Kickoff (YYYY-MM-DD HH:MM:SS)</label>
                        <input type="text" name="kickoff_at" value="{{ old('kickoff_at') }}" placeholder="2026-02-20 18:00:00">

                        <button type="submit">Create Match</button>
                    </form>
                @endif
            </div>
        </details>

        {{-- -------------------- EDIT MATCHES -------------------- --}}
        <details class="panel" open>
            <summary>Edit Matches</summary>
            <div class="panel-body">
                @foreach($matches as $match)
                    @php
                        $matchId = $match->id ?? $match->match_id ?? null;
                    @endphp

                    <div class="card">
                        <form method="POST" action="{{ route('admin.match.update') }}">
                            @csrf

                            <input type="hidden" name="id" value="{{ $matchId }}">

                            <label>Team 1</label>
                            <select name="team1" required>
                                @foreach(($teams ?? collect()) as $t)
                                    @php
                                        $isSelected =
                                            ((string)($match->team1 ?? '') === (string)$t->team_id) ||
                                            ((string)($match->team1 ?? '') === (string)($t->team_name ?? ''));
                                    @endphp
                                    <option value="{{ $t->team_id }}" @selected($isSelected)>
                                        {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                    </option>
                                @endforeach
                            </select>

                            <label>Team 2</label>
                            <select name="team2" required>
                                @foreach(($teams ?? collect()) as $t)
                                    @php
                                        $isSelected =
                                            ((string)($match->team2 ?? '') === (string)$t->team_id) ||
                                            ((string)($match->team2 ?? '') === (string)($t->team_name ?? ''));
                                    @endphp
                                    <option value="{{ $t->team_id }}" @selected($isSelected)>
                                        {{ $t->team_name ?? ('Team '.$t->team_id) }} (ID: {{ $t->team_id }})
                                    </option>
                                @endforeach
                            </select>

                            <div class="grid-2">
                                <div>
                                    <label>Score 1</label>
                                    <input type="number" name="score1" value="{{ $match->score1 ?? 0 }}">
                                </div>

                                <div>
                                    <label>Score 2</label>
                                    <input type="number" name="score2" value="{{ $match->score2 ?? 0 }}">
                                </div>
                            </div>

                            <label>Match Time</label>
                            <input type="text" name="match_time" value="{{ $match->match_time ?? '' }}">

                            <label>Status</label>
                            <select name="status" required>
                                <option value="current" @selected(($match->status ?? '') === 'current')>current</option>
                                <option value="upcoming" @selected(($match->status ?? '') === 'upcoming')>upcoming</option>
                                <option value="finished" @selected(($match->status ?? '') === 'finished')>finished</option>
                            </select>

                            <label>Kickoff (YYYY-MM-DD HH:MM:SS)</label>
                            <input type="text" name="kickoff_at" value="{{ $match->kickoff_at ?? '' }}">

                            <button type="submit">Update Match</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </details>

    </div>
</div>

<script>
    const adminShell = document.getElementById('adminShell');
    const workspaceToggle = document.getElementById('workspaceToggle');
    const expandAllBtn = document.getElementById('expandAllBtn');
    const collapseAllBtn = document.getElementById('collapseAllBtn');
    const panels = document.querySelectorAll('details.panel');

    workspaceToggle.addEventListener('click', function () {
        adminShell.classList.toggle('wide');
    });

    expandAllBtn.addEventListener('click', function () {
        panels.forEach(panel => panel.open = true);
    });

    collapseAllBtn.addEventListener('click', function () {
        panels.forEach(panel => panel.open = false);
    });
</script>

</body>
</html>