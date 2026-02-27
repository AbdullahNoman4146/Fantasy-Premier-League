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

<h2>Create New Match</h2>
<form method="POST" action="/super-admin-fpl-2026/create">
    @csrf

    <label>Team 1</label>
    <input name="team1" value="{{ old('team1') }}" required>

    <label>Team 2</label>
    <input name="team2" value="{{ old('team2') }}" required>

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

<hr>

<h2>Edit Matches</h2>

@foreach($matches as $match)
<div class="card">
    <form method="POST" action="/super-admin-fpl-2026/update">
        @csrf

        <input type="hidden" name="id" value="{{ $match->id }}">

        <label>Team 1</label>
        <input type="text" name="team1" value="{{ $match->team1 ?? '' }}" required>

        <label>Team 2</label>
        <input type="text" name="team2" value="{{ $match->team2 ?? '' }}" required>

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