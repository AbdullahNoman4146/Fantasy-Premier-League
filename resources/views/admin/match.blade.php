<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body{ font-family:Arial,sans-serif; padding:20px; }
        .card{ border:1px solid #ddd; padding:15px; margin-bottom:12px; border-radius:10px; }
        label{ font-weight:bold; }
        input, select{ width:100%; padding:8px; margin:6px 0 10px; }
        button{ padding:10px 14px; cursor:pointer; }
        .success{ color:green; }
        .error{ color:#b00020; }
        .error-box{ border:1px solid #f2b8c6; background:#fff0f3; padding:10px; border-radius:10px; margin:12px 0; }
        .hint{ font-size:12px; opacity:.75; margin-top:-6px; margin-bottom:10px; }

        table{ width:100%; border-collapse:collapse; margin-top:10px; }
        th,td{ padding:10px; border-bottom:1px solid #eee; text-align:left; }
        th{ font-weight:800; font-size:14px; }
        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid #ddd;
            font-size:12px;
        }
    </style>
</head>
<body>

<h1>Admin Match Manager</h1>

@if(session('success'))
    <p class="success">{{ session('success') }}</p>
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

@php
    // Fallback sponsor list (in case controller did not pass $sponsorOptions)
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
@endphp


{{-- -------------------- ADD TEAM -------------------- --}}
<h2>Add New Team</h2>
<form method="POST" action="{{ route('admin.team.create') }}">
    @csrf

    <label>Team Name</label>
    <input type="text" name="team_name" placeholder="e.g. Manchester United" required>

    <label>Strength (optional)</label>
    <input type="number" name="strength" placeholder="e.g. 80">

    <label>Goals Scored (optional)</label>
    <input type="number" name="goals_scored" placeholder="e.g. 0">

    <label>Goals Conceded (optional)</label>
    <input type="number" name="goals_conceded" placeholder="e.g. 0">

    <label>Manager ID (optional)</label>
    <input type="number" name="manager_id" placeholder="e.g. 1">

    <button type="submit">Add Team</button>
</form>

<hr>


{{-- -------------------- SPONSOR ADMIN -------------------- --}}
<h2>Sponsor Admin</h2>
<p class="hint">Assign a sponsor to a team using dropdowns (no typing).</p>

@if(($teams ?? collect())->count() == 0)
    <p class="error"><b>No teams found.</b> Add teams first, then assign sponsors.</p>
@else
<form method="POST" action="{{ route('admin.sponsors.create') }}">
    @csrf

    <!-- ✅ so controller redirects back to THIS admin page -->
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
            <tr style="background:#f7f7f7;">
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

<hr>


{{-- -------------------- CREATE MATCH -------------------- --}}
<h2>Create New Match</h2>

@if(($teams ?? collect())->count() == 0)
    <p class="error"><b>No teams found.</b> Add teams first, then create matches.</p>
@else
<form method="POST" action="/super-admin-fpl-2026/create">
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

    <p class="hint">Tip: Team 1 and Team 2 should be different.</p>

    <label>Status</label>
    <select name="status" required>
        <option value="current" @selected(old('status') === 'current')>current</option>
        <option value="upcoming" @selected(old('status', 'upcoming') === 'upcoming')>upcoming</option>
        <option value="finished" @selected(old('status') === 'finished')>finished</option>
    </select>

    <label>Kickoff (YYYY-MM-DD HH:MM:SS)</label>
    <input name="kickoff_at" value="{{ old('kickoff_at') }}" placeholder="2026-02-20 18:00:00">

    <button type="submit">Create</button>
</form>
@endif

<hr>


{{-- -------------------- EDIT MATCHES -------------------- --}}
<h2>Edit Matches</h2>

@foreach($matches as $match)
<div class="card">
    <form method="POST" action="/super-admin-fpl-2026/update">
        @csrf

        <input type="hidden" name="id" value="{{ $match->id }}">

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

        <label>Score 1</label>
        <input type="number" name="score1" value="{{ $match->score1 ?? 0 }}">

        <label>Score 2</label>
        <input type="number" name="score2" value="{{ $match->score2 ?? 0 }}">

        <label>Match Time</label>
        <input type="text" name="match_time" value="{{ $match->match_time ?? '' }}">

        <label>Status</label>
        <select name="status" required>
            <option value="current" @selected(($match->status ?? '') === 'current')>current</option>
            <option value="upcoming" @selected(($match->status ?? '') === 'upcoming')>upcoming</option>
            <option value="finished" @selected(($match->status ?? '') === 'finished')>finished</option>
        </select>

        <label>Kickoff (YYYY-MM-DD HH:MM:SS)</label>
        <input name="kickoff_at" value="{{ $match->kickoff_at ?? '' }}">

        <button type="submit">Update</button>
    </form>
</div>
@endforeach

</body>
</html>