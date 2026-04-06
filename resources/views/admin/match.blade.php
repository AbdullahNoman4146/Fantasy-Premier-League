<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Fantasy Premier League</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }

        body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: #fff;
    background: #0f172a;
}

        .overlay {
    min-height: 100vh;
    padding: 24px;
    background: transparent;
}

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-card,
        .form-card,
        .table-card {
            background: #111827;
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.25);
        }

        .header-card {
            padding: 22px;
            margin-bottom: 18px;
        }

        .header-card h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header-card p {
            margin: 0;
            opacity: .85;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            padding: 18px;
            border-radius: 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
        }

        .stat-label {
            font-size: 13px;
            opacity: .85;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .tab-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            color: #fff;
            padding: 10px 14px;
            border-radius: 999px;
            cursor: pointer;
            transition: .2s ease;
            font-size: 14px;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: rgba(50,100,199,0.75);
            border-color: rgba(255,255,255,0.22);
        }

        .tab-section {
            display: none;
        }

        .tab-section.active {
            display: block;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-card,
        .table-card {
            padding: 18px;
            margin-bottom: 18px;
        }

        .card-title {
            margin: 0 0 14px;
            font-size: 20px;
        }

        .small-note {
            margin: -4px 0 14px;
            font-size: 13px;
            opacity: .8;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            opacity: .92;
        }

        input, select, textarea, button {
            font-family: inherit;
        }

        input, select, textarea {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.07);
            color: #fff;
            outline: none;
        }

        select {
    background-color: #1f2937;
    color: #fff;
}

select option {
    background-color: #111827;
    color: #fff;
}

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input::placeholder,
        textarea::placeholder {
            color: rgba(255,255,255,0.55);
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            transition: .2s ease;
        }

        .btn-primary { background: #2563eb; }
        .btn-success { background: #16a34a; }
        .btn-warning { background: #d97706; }
        .btn-danger  { background: #dc2626; }

        .btn:hover {
            filter: brightness(1.08);
        }

        .action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 14px;
            font-size: 14px;
        }

        .alert-success {
            background: rgba(22,163,74,.18);
            border: 1px solid rgba(22,163,74,.45);
        }

        .alert-error {
            background: rgba(220,38,38,.18);
            border: 1px solid rgba(220,38,38,.45);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 10px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid rgba(255,255,255,0.10);
        }

        th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: .86;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.08);
            white-space: nowrap;
        }

        .badge-current   { background: rgba(37,99,235,.22); }
        .badge-upcoming  { background: rgba(217,119,6,.22); }
        .badge-finished  { background: rgba(22,163,74,.22); }
        .badge-draft     { background: rgba(100,116,139,.22); }
        .badge-published { background: rgba(22,163,74,.22); }

        .inline-form {
            display: grid;
            grid-template-columns: repeat(6, minmax(120px,1fr));
            gap: 8px;
            align-items: end;
        }

        .inline-form-compact {
            display: grid;
            grid-template-columns: repeat(5, minmax(100px,1fr));
            gap: 8px;
            align-items: end;
        }

        .muted {
            opacity: .8;
            font-size: 13px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .two-col,
            .form-grid,
            .form-grid-3,
            .inline-form,
            .inline-form-compact {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .stats-grid { grid-template-columns: 1fr; }
            .overlay { padding: 16px; }
            .header-card h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">

        <div class="header-card">
            <h1>Fantasy Premier League Admin Panel</h1>
            <p>Manage matches, teams, players, sponsors, managers, player market values, transfer posts, and standings from one page.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix these issues:</strong>
                <ul style="margin:8px 0 0 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Current Matches</div>
                <div class="stat-value">{{ $matchStats['current'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Upcoming Matches</div>
                <div class="stat-value">{{ $matchStats['upcoming'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Finished Matches</div>
                <div class="stat-value">{{ $matchStats['finished'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Teams / Players</div>
                <div class="stat-value">{{ count($teams) }} / {{ count($players) }}</div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-tab="matches">Matches</button>
            <button class="tab-btn" data-tab="teams">Teams</button>
            <button class="tab-btn" data-tab="players">Players</button>
            <button class="tab-btn" data-tab="sponsors">Sponsors</button>
            <button class="tab-btn" data-tab="managers">Managers</button>
            <button class="tab-btn" data-tab="market-values">Market Values</button>
            <button class="tab-btn" data-tab="transfers">Transfers</button>
            <button class="tab-btn" data-tab="standings">Standings</button>
        </div>

        {{-- MATCHES --}}
        <div class="tab-section active" id="tab-matches">
            <div class="two-col">
                <div class="form-card">
                    <h3 class="card-title">Add Match</h3>
                    <p class="small-note">Create future, current, or finished matches.</p>

                    <form method="POST" action="{{ route('admin.match.create') }}">
                        @csrf
                        <div class="form-grid">
    <div class="form-group">
        <label>Team A</label>
        <select name="team1" required>
            <option value="">Select Team A</option>
            @foreach($teams as $team)
                <option value="{{ $team->team_id }}">{{ $team->team_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Team B</label>
        <select name="team2" required>
            <option value="">Select Team B</option>
            @foreach($teams as $team)
                <option value="{{ $team->team_id }}">{{ $team->team_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" required>
            <option value="upcoming">Upcoming</option>
            <option value="current">Current</option>
            <option value="finished">Finished</option>
        </select>
    </div>

    <div class="form-group">
        <label>Kickoff Date & Time</label>
        <input type="datetime-local" name="kickoff_at">
    </div>

    <div class="form-group">
        <label>Match Time Label</label>
        <input type="text" name="match_time" placeholder="Example: FT, HT, 75', 18 Mar 8:00 PM">
    </div>

    <div class="form-group">
        <label>Live Phase</label>
        <select name="live_phase">
            <option value="">Select Live Phase</option>
            <option value="first_half">1st Half</option>
            <option value="break">Break</option>
            <option value="second_half">2nd Half</option>
            <option value="finished">Finished</option>
        </select>
    </div>

    <div class="form-group">
        <label>1st Half Added Minutes</label>
        <input type="number" name="first_half_added_minutes" min="0" max="30" value="0">
    </div>

    <div class="form-group">
        <label>2nd Half Added Minutes</label>
        <input type="number" name="second_half_added_minutes" min="0" max="30" value="0">
    </div>

    <div class="form-group">
        <label>2nd Half Started At</label>
        <input type="datetime-local" name="second_half_started_at">
    </div>

    <div class="form-group">
        <label>Score Team A</label>
        <input type="number" name="score1" min="0" value="0">
    </div>

    <div class="form-group">
        <label>Score Team B</label>
        <input type="number" name="score2" min="0" value="0">
    </div>

    <div class="form-group full">
        <label>Team A Goal Scorers</label>
        <textarea name="team_a_scorers" placeholder="Optional. Use jersey numbers separated by commas. Example: 11@14', 7@62'"></textarea>
    </div>

    <div class="form-group full">
        <label>Team B Goal Scorers</label>
        <textarea name="team_b_scorers" placeholder="Optional. Use jersey numbers separated by commas. Example: 9@33', 9@88'"></textarea>
    </div>
</div>

                        <div class="muted" style="margin-top:10px;">
                            Add scorer details only for current or finished matches. Each entry should match the score count.
                        </div>

                        <div class="action-row">
                            <button type="submit" class="btn btn-success">Create Match</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <h3 class="card-title">Standings Tools</h3>
                    <p class="small-note">Finished match results automatically affect standings.</p>

                    <form method="POST" action="{{ route('admin.standings.recalculate') }}">
                        @csrf
                        <div class="action-row">
                            <button type="submit" class="btn btn-warning">Recalculate Standings</button>
                        </div>
                    </form>

                    <div class="muted" style="margin-top:14px;">
                        <ul>
                            <li>Only finished matches affect points.</li>
                            <li>Winner is auto-calculated from scores.</li>
                            <li>Win = 3 points, Draw = 1 point, Loss = 0 points.</li>
                            <li>Goals scored, conceded, and goal difference are rebuilt from results.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Matches</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Match</th>
                                <th>Status</th>
                                <th>Kickoff</th>
                                <th>Score</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($matches as $match)
                            <tr>
                                <td>{{ $match->id }}</td>
                                <td><strong>{{ $match->team1_name }}</strong> vs <strong>{{ $match->team2_name }}</strong></td>
                                <td><span class="badge badge-{{ $match->status }}">{{ ucfirst($match->status) }}</span></td>
                                <td>{{ $match->kickoff_at ?? 'N/A' }}</td>
                                <td>{{ $match->score1 }} - {{ $match->score2 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.match.update') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $match->id }}">

                                        <div class="inline-form">
    <div class="form-group">
        <label>Team A</label>
        <select name="team1" required>
            @foreach($teams as $team)
                <option value="{{ $team->team_id }}" {{ (int)$team->team_id === (int)$match->team1_id ? 'selected' : '' }}>
                    {{ $team->team_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Team B</label>
        <select name="team2" required>
            @foreach($teams as $team)
                <option value="{{ $team->team_id }}" {{ (int)$team->team_id === (int)$match->team2_id ? 'selected' : '' }}>
                    {{ $team->team_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" required>
            <option value="upcoming" {{ $match->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
            <option value="current" {{ $match->status === 'current' ? 'selected' : '' }}>Current</option>
            <option value="finished" {{ $match->status === 'finished' ? 'selected' : '' }}>Finished</option>
        </select>
    </div>

    <div class="form-group">
        <label>Kickoff</label>
        <input type="datetime-local" name="kickoff_at" value="{{ $match->kickoff_at ? \Carbon\Carbon::parse($match->kickoff_at)->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="form-group">
        <label>Match Time Label</label>
        <input type="text" name="match_time" value="{{ $match->match_time }}" placeholder="Example: FT, HT, 75'">
    </div>

    <div class="form-group">
        <label>Live Phase</label>
        <select name="live_phase">
            <option value="">Select Live Phase</option>
            <option value="first_half" {{ ($match->live_phase ?? '') === 'first_half' ? 'selected' : '' }}>1st Half</option>
            <option value="break" {{ ($match->live_phase ?? '') === 'break' ? 'selected' : '' }}>Break</option>
            <option value="second_half" {{ ($match->live_phase ?? '') === 'second_half' ? 'selected' : '' }}>2nd Half</option>
            <option value="finished" {{ ($match->live_phase ?? '') === 'finished' ? 'selected' : '' }}>Finished</option>
        </select>
    </div>

    <div class="form-group">
        <label>1st Half Added Minutes</label>
        <input type="number" name="first_half_added_minutes" min="0" max="30" value="{{ (int)($match->first_half_added_minutes ?? 0) }}">
    </div>

    <div class="form-group">
        <label>2nd Half Added Minutes</label>
        <input type="number" name="second_half_added_minutes" min="0" max="30" value="{{ (int)($match->second_half_added_minutes ?? 0) }}">
    </div>

    <div class="form-group">
        <label>2nd Half Started At</label>
        <input type="datetime-local" name="second_half_started_at" value="{{ $match->second_half_started_at ? \Carbon\Carbon::parse($match->second_half_started_at)->format('Y-m-d\TH:i') : '' }}">
    </div>

    <div class="form-group">
        <label>Score A</label>
        <input type="number" name="score1" min="0" value="{{ $match->score1 }}">
    </div>

    <div class="form-group">
        <label>Score B</label>
        <input type="number" name="score2" min="0" value="{{ $match->score2 }}">
    </div>

    <div class="form-group full">
        <label>Team A Goal Scorers</label>
        <textarea name="team_a_scorers" placeholder="Example: 11@14', 7@62'">{{ $match->team_a_scorers_input ?? '' }}</textarea>
    </div>

    <div class="form-group full">
        <label>Team B Goal Scorers</label>
        <textarea name="team_b_scorers" placeholder="Example: 9@33', 9@88'">{{ $match->team_b_scorers_input ?? '' }}</textarea>
    </div>
</div>

                                        <div class="muted" style="margin-top:10px;">
                                            Use jersey numbers, optionally with minutes. Example: 11@14', 11@55', 7@90+2'
                                        </div>

                                        <div class="action-row">
                                            <button type="submit" class="btn btn-primary">Update Match</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.match.delete') }}" onsubmit="return confirm('Delete this match?');">
                                        @csrf
                                        <input type="hidden" name="match_id" value="{{ $match->match_id ?? $match->id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No matches found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TEAMS --}}
        <div class="tab-section" id="tab-teams">
            <div class="two-col">
                <div class="form-card">
                    <h3 class="card-title">Add Team</h3>
                    <form method="POST" action="{{ route('admin.team.create') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Team Name</label>
                                <input type="text" name="team_name" required>
                            </div>

                            <div class="form-group">
                                <label>Strength</label>
                                <input type="number" name="strength" min="0">
                            </div>

                            <div class="form-group">
                                <label>Goals Scored</label>
                                <input type="number" name="goals_scored" min="0" value="0">
                            </div>

                            <div class="form-group">
                                <label>Goals Conceded</label>
                                <input type="number" name="goals_conceded" min="0" value="0">
                            </div>
                        </div>

                        <div class="action-row">
                            <button type="submit" class="btn btn-success">Add Team</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <h3 class="card-title">Team Notes</h3>
                    <div class="muted">
                        <ul>
                            <li>Each team can have only one manager at a time.</li>
                            <li>Deleting a team may fail if that team is still linked to matches or other records.</li>
                            <li>Manager name shown here comes from the linked manager record.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Teams</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Team</th>
                                <th>Strength</th>
                                <th>Goals</th>
                                <th>Manager</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($teams as $team)
                            <tr>
                                <td>{{ $team->team_id }}</td>
                                <td><strong>{{ $team->team_name }}</strong></td>
                                <td>{{ $team->strength ?? 'N/A' }}</td>
                                <td>{{ $team->goals_scored ?? 0 }} / {{ $team->goals_conceded ?? 0 }}</td>
                                <td>{{ $team->manager_name ?: 'Unassigned' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.team.update') }}">
                                        @csrf
                                        <input type="hidden" name="team_id" value="{{ $team->team_id }}">

                                        <div class="inline-form-compact">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" name="team_name" value="{{ $team->team_name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Strength</label>
                                                <input type="number" name="strength" min="0" value="{{ $team->strength }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Goals Scored</label>
                                                <input type="number" name="goals_scored" min="0" value="{{ $team->goals_scored ?? 0 }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Goals Conceded</label>
                                                <input type="number" name="goals_conceded" min="0" value="{{ $team->goals_conceded ?? 0 }}">
                                            </div>
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary">Update Team</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.team.delete') }}" onsubmit="return confirm('Delete this team?');">
                                        @csrf
                                        <input type="hidden" name="team_id" value="{{ $team->team_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No teams found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PLAYERS --}}
        <div class="tab-section" id="tab-players">
            <div class="form-card">
                <h3 class="card-title">Add Player</h3>
                <form method="POST" action="{{ route('admin.player.create') }}">
                    @csrf
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" required>
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name">
                        </div>

                        <div class="form-group">
                            <label>Nationality</label>
                            <input type="text" name="nationality" required>
                        </div>

                        <div class="form-group">
                            <label>Team</label>
                            <select name="team_id" required>
                                <option value="">Select Team</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->team_id }}">{{ $team->team_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jersey Number</label>
                            <input type="number" name="jersey_number" min="1" max="99" required>
                        </div>

                        <div class="form-group">
                            <label>Position</label>
                            <select name="position" required>
                                <option value="">Select Position</option>
                                @foreach($playerPositions as $position)
                                    <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="action-row">
                        <button type="submit" class="btn btn-success">Add Player</button>
                    </div>
                </form>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Players</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Team</th>
                                <th>Jersey</th>
                                <th>Position</th>
                                <th>Nationality</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($players as $player)
                            <tr>
                                <td><strong>{{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) }}</strong></td>
                                <td>{{ $player->team_name }}</td>
                                <td>#{{ $player->jersey_number }}</td>
                                <td>{{ $player->position }}</td>
                                <td>{{ $player->nationality }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.player.update') }}">
                                        @csrf
                                        <input type="hidden" name="person_id" value="{{ $player->person_id }}">
                                        <input type="hidden" name="old_team_id" value="{{ $player->team_id }}">
                                        <input type="hidden" name="old_jersey_number" value="{{ $player->jersey_number }}">

                                        <div class="inline-form">
                                            <div class="form-group">
                                                <label>First</label>
                                                <input type="text" name="first_name" value="{{ $player->first_name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Last</label>
                                                <input type="text" name="last_name" value="{{ $player->last_name }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <input type="text" name="nationality" value="{{ $player->nationality }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Team</label>
                                                <select name="team_id" required>
                                                    @foreach($teams as $team)
                                                        <option value="{{ $team->team_id }}" {{ (int)$team->team_id === (int)$player->team_id ? 'selected' : '' }}>
                                                            {{ $team->team_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Jersey</label>
                                                <input type="number" name="jersey_number" min="1" max="99" value="{{ $player->jersey_number }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Position</label>
                                                <select name="position" required>
                                                    @foreach($playerPositions as $position)
                                                        <option value="{{ $position }}" {{ $position === $player->position ? 'selected' : '' }}>
                                                            {{ $position }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="action-row">
                                            <button type="submit" class="btn btn-primary">Update Player</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.player.delete') }}" onsubmit="return confirm('Delete this player?');">
                                        @csrf
                                        <input type="hidden" name="person_id" value="{{ $player->person_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No players found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SPONSORS --}}
        <div class="tab-section" id="tab-sponsors">
            <div class="two-col">
                <div class="form-card">
                    <h3 class="card-title">Assign Sponsor</h3>
                    <form method="POST" action="{{ route('admin.sponsors.create') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Team</label>
                                <select name="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->team_id }}">{{ $team->team_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Sponsor</label>
                                <select name="sponsor_name" required>
                                    <option value="">Select Sponsor</option>
                                    @foreach($sponsorOptions as $sponsorOption)
                                        <option value="{{ $sponsorOption }}">{{ $sponsorOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="action-row">
                            <button type="submit" class="btn btn-success">Assign Sponsor</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <h3 class="card-title">Sponsor Rules</h3>
                    <div class="muted">
                        A sponsor cannot be assigned twice to the same team. Update or remove existing sponsor records below.
                    </div>
                </div>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Sponsors</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sponsor</th>
                                <th>Team</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($sponsors as $sponsor)
                            <tr>
                                <td>{{ $sponsor->sponsor_id }}</td>
                                <td>{{ $sponsor->sponsor_name }}</td>
                                <td>{{ $sponsor->team_name }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.sponsors.update') }}">
                                        @csrf
                                        <input type="hidden" name="sponsor_id" value="{{ $sponsor->sponsor_id }}">
                                        <div class="inline-form-compact">
                                            <div class="form-group">
                                                <label>Team</label>
                                                <select name="team_id" required>
                                                    @foreach($teams as $team)
                                                        <option value="{{ $team->team_id }}" {{ (int)$team->team_id === (int)$sponsor->team_id ? 'selected' : '' }}>
                                                            {{ $team->team_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Sponsor</label>
                                                <select name="sponsor_name" required>
                                                    @foreach($sponsorOptions as $sponsorOption)
                                                        <option value="{{ $sponsorOption }}" {{ $sponsorOption === $sponsor->sponsor_name ? 'selected' : '' }}>
                                                            {{ $sponsorOption }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary">Update Sponsor</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.sponsors.delete') }}" onsubmit="return confirm('Delete this sponsor assignment?');">
                                        @csrf
                                        <input type="hidden" name="sponsor_id" value="{{ $sponsor->sponsor_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No sponsors found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MANAGERS --}}
        <div class="tab-section" id="tab-managers">
            <div class="two-col">
                <div class="form-card">
                    <h3 class="card-title">Assign Manager to Team</h3>
                    <p class="small-note">Each team can have only one manager at a time.</p>

                    <form method="POST" action="{{ route('admin.managers.create') }}">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" required>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name">
                            </div>

                            <div class="form-group">
                                <label>Nationality</label>
                                <input type="text" name="nationality">
                            </div>

                            <div class="form-group">
                                <label>Experience Years</label>
                                <input type="number" name="experience_years" min="0" value="0">
                            </div>

                            <div class="form-group full">
                                <label>Team</label>
                                <select name="team_id" required>
                                    <option value="">Select Team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->team_id }}">
                                            {{ $team->team_name }}{{ $team->manager_name ? ' (Current: ' . $team->manager_name . ')' : ' (No manager)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="action-row">
                            <button type="submit" class="btn btn-success">Assign Manager</button>
                        </div>
                    </form>
                </div>

                <div class="form-card">
                    <h3 class="card-title">Manager Rule</h3>
                    <div class="muted">
                        Assigning a new manager to a team will replace the current manager of that team automatically.
                    </div>
                </div>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Managers</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Manager</th>
                                <th>Team</th>
                                <th>Nationality</th>
                                <th>Experience</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($managers as $manager)
                            <tr>
                                <td><strong>{{ trim(($manager->first_name ?? '') . ' ' . ($manager->last_name ?? '')) }}</strong></td>
                                <td>{{ $manager->team_name ?? 'Unassigned' }}</td>
                                <td>{{ $manager->nationality ?? 'N/A' }}</td>
                                <td>{{ $manager->experience_years ?? 0 }} years</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.managers.update') }}">
                                        @csrf
                                        <input type="hidden" name="person_id" value="{{ $manager->person_id }}">

                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" value="{{ $manager->first_name }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" value="{{ $manager->last_name }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Nationality</label>
                                                <input type="text" name="nationality" value="{{ $manager->nationality }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Experience Years</label>
                                                <input type="number" name="experience_years" min="0" value="{{ $manager->experience_years ?? 0 }}">
                                            </div>

                                            <div class="form-group full">
                                                <label>Team</label>
                                                <select name="team_id">
                                                    <option value="">Unassigned</option>
                                                    @foreach($teams as $team)
                                                        <option value="{{ $team->team_id }}" {{ (int)($manager->team_id ?? 0) === (int)$team->team_id ? 'selected' : '' }}>
                                                            {{ $team->team_name }}{{ $team->manager_name ? ' (Current: ' . $team->manager_name . ')' : ' (No manager)' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="action-row">
                                            <button type="submit" class="btn btn-primary">Update Manager</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.managers.delete') }}" onsubmit="return confirm('Delete this manager?');">
                                        @csrf
                                        <input type="hidden" name="person_id" value="{{ $manager->person_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No managers found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MARKET VALUES --}}
        <div class="tab-section" id="tab-market-values">
            <div class="form-card">
                <h3 class="card-title">Add Player Market Value</h3>
                <p class="small-note">Select the player directly from the dropdown.</p>

                <form method="POST" action="{{ route('admin.market-values.create') }}">
                    @csrf

                    <input type="hidden" name="team_id">
                    <input type="hidden" name="jersey_number">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Player</label>
                            <select class="player-select" required>
                                <option value="">Select Player</option>
                                @foreach($playerOptions as $playerOption)
                                    <option value="{{ $playerOption->team_id }}|{{ $playerOption->jersey_number }}">
                                        {{ $playerOption->team_name }} - {{ $playerOption->player_name }} (#{{ $playerOption->jersey_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Season</label>
                            <input type="text" name="season" placeholder="Example: 2025/26" required>
                        </div>

                        <div class="form-group">
                            <label>Market Value</label>
                            <input type="number" step="0.01" min="0" name="market_value" required>
                        </div>

                        <div class="form-group">
                            <label>Currency</label>
                            <input type="text" name="currency" value="GBP" required>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <input type="text" name="notes" placeholder="Optional note">
                        </div>
                    </div>

                    <div class="action-row">
                        <button type="submit" class="btn btn-success">Add Market Value</button>
                    </div>
                </form>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Player Market Values</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Team</th>
                                <th>Season</th>
                                <th>Value</th>
                                <th>Notes</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($playerMarketValues as $row)
                            <tr>
                                <td>
                                    <strong>{{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) }}</strong>
                                    <span class="badge">#{{ $row->jersey_number }}</span>
                                </td>
                                <td>{{ $row->team_name }}</td>
                                <td>{{ $row->season }}</td>
                                <td>{{ $row->currency }} {{ number_format((float)$row->market_value, 2) }}</td>
                                <td>{{ $row->notes ?: 'N/A' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.market-values.update') }}">
                                        @csrf
                                        <input type="hidden" name="player_market_value_id" value="{{ $row->player_market_value_id }}">
                                        <input type="hidden" name="team_id" value="{{ $row->team_id }}">
                                        <input type="hidden" name="jersey_number" value="{{ $row->jersey_number }}">

                                        <div class="form-grid">
                                            <div class="form-group full">
                                                <label>Player</label>
                                                <select class="player-select" required>
                                                    <option value="">Select Player</option>
                                                    @foreach($playerOptions as $playerOption)
                                                        <option value="{{ $playerOption->team_id }}|{{ $playerOption->jersey_number }}"
                                                            {{ ((int)$playerOption->team_id === (int)$row->team_id && (int)$playerOption->jersey_number === (int)$row->jersey_number) ? 'selected' : '' }}>
                                                            {{ $playerOption->team_name }} - {{ $playerOption->player_name }} (#{{ $playerOption->jersey_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Season</label>
                                                <input type="text" name="season" value="{{ $row->season }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Value</label>
                                                <input type="number" step="0.01" min="0" name="market_value" value="{{ $row->market_value }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Currency</label>
                                                <input type="text" name="currency" value="{{ $row->currency }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <input type="text" name="notes" value="{{ $row->notes }}">
                                            </div>
                                        </div>

                                        <div class="action-row">
                                            <button type="submit" class="btn btn-primary">Update Value</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.market-values.delete') }}" onsubmit="return confirm('Delete this market value?');">
                                        @csrf
                                        <input type="hidden" name="player_market_value_id" value="{{ $row->player_market_value_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No player market values found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TRANSFERS --}}
        <div class="tab-section" id="tab-transfers">
            <div class="form-card">
                <h3 class="card-title">Create Transfer News / Blog Post</h3>
                <form method="POST" action="{{ route('admin.transfers.create') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>

                        <div class="form-group full">
                            <label>Summary</label>
                            <textarea name="summary" placeholder="Short summary for transfer list page"></textarea>
                        </div>

                        <div class="form-group full">
                            <label>Content</label>
                            <textarea name="content" placeholder="Full transfer content" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Published Time</label>
                            <input type="text" value="Assigned automatically from current server time when status is Published" readonly>
                        </div>
                    </div>

                    <div class="action-row">
                        <button type="submit" class="btn btn-success">Create Post</button>
                    </div>
                </form>
            </div>

            <div class="table-card">
                <h3 class="card-title">Manage Transfer Posts</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Posted</th>
                                <th>Preview</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($transferPosts as $post)
                            <tr>
                                <td><strong>{{ $post->title }}</strong></td>
                                <td><span class="badge badge-{{ $post->status }}">{{ ucfirst($post->status) }}</span></td>
                                <td>{{ $post->posted_at ?? $post->created_at ?? 'N/A' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($post->summary ?: $post->content), 120) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.transfers.update') }}">
                                        @csrf
                                        <input type="hidden" name="transfer_post_id" value="{{ $post->transfer_post_id }}">

                                        <div class="form-grid">
                                            <div class="form-group full">
                                                <label>Title</label>
                                                <input type="text" name="title" value="{{ $post->title }}" required>
                                            </div>

                                            <div class="form-group full">
                                                <label>Summary</label>
                                                <textarea name="summary">{{ $post->summary }}</textarea>
                                            </div>

                                            <div class="form-group full">
                                                <label>Content</label>
                                                <textarea name="content" required>{{ $post->content }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" required>
                                                    <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Published</option>
                                                    <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Published Time</label>
                                                <input type="text" value="{{ $post->posted_at ? \Carbon\Carbon::parse($post->posted_at)->format('d M Y, h:i A') : 'Will be assigned automatically when published' }}" readonly>
                                            </div>
                                        </div>

                                        <div class="action-row">
                                            <button type="submit" class="btn btn-primary">Update Post</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.transfers.delete') }}" onsubmit="return confirm('Delete this transfer post?');">
                                        @csrf
                                        <input type="hidden" name="transfer_post_id" value="{{ $post->transfer_post_id }}">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No transfer posts found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- STANDINGS --}}
        <div class="tab-section" id="tab-standings">
            <div class="table-card">
                <h3 class="card-title">Current Standings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Team</th>
                                <th>Played</th>
                                <th>Wins</th>
                                <th>Draws</th>
                                <th>Losses</th>
                                <th>GF</th>
                                <th>GA</th>
                                <th>GD</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($standings as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $row->team_name }}</strong></td>
                                <td>{{ $row->played }}</td>
                                <td>{{ $row->wins }}</td>
                                <td>{{ $row->draws }}</td>
                                <td>{{ $row->losses }}</td>
                                <td>{{ $row->goals_scored }}</td>
                                <td>{{ $row->goals_conceded }}</td>
                                <td>{{ $row->goal_diff }}</td>
                                <td><strong>{{ $row->points }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No standings data available.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="action-row" style="margin-top:16px;">
                    <form method="POST" action="{{ route('admin.standings.recalculate') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">Recalculate Standings</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.tab-btn');
    const sections = document.querySelectorAll('.tab-section');
    const adminTabStorageKey = 'fpl_admin_active_tab';

    function getTabName(tabName) {
        if (!tabName) {
            return 'matches';
        }

        const normalized = String(tabName).replace(/^#?tab-?/, '').trim();
        return normalized || 'matches';
    }

    function activateTab(tabName) {
        const activeTab = getTabName(tabName);

        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === activeTab);
        });

        sections.forEach(section => {
            section.classList.toggle('active', section.id === 'tab-' + activeTab);
        });

        sessionStorage.setItem(adminTabStorageKey, activeTab);
        if (window.location.hash !== '#tab-' + activeTab) {
            history.replaceState(null, '', '#tab-' + activeTab);
        }
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            activateTab(this.dataset.tab);
        });
    });

    const initialTab = getTabName(window.location.hash || sessionStorage.getItem(adminTabStorageKey) || 'matches');
    activateTab(initialTab);

    function syncPlayerSelect(selectEl) {
        const form = selectEl.closest('form');
        if (!form) return;

        const teamInput = form.querySelector('input[name="team_id"]');
        const jerseyInput = form.querySelector('input[name="jersey_number"]');
        if (!teamInput || !jerseyInput) return;

        const value = selectEl.value || '';
        if (!value.includes('|')) {
            teamInput.value = '';
            jerseyInput.value = '';
            return;
        }

        const parts = value.split('|');
        teamInput.value = parts[0] || '';
        jerseyInput.value = parts[1] || '';
    }

    document.querySelectorAll('.player-select').forEach(selectEl => {
        syncPlayerSelect(selectEl);

        selectEl.addEventListener('change', function () {
            syncPlayerSelect(this);
        });
    });
});
</script>
</body>
</html>