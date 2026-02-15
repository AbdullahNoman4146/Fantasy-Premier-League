<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>
<body>

<h1>Admin Match Update</h1>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="/super-admin-fpl-2026/update">
    @csrf

    <label>Team 1:</label><br>
    <input type="text" name="team1" value="{{ $match->team1 ?? '' }}"><br><br>

    <label>Team 2:</label><br>
    <input type="text" name="team2" value="{{ $match->team2 ?? '' }}"><br><br>

    <label>Score 1:</label><br>
    <input type="number" name="score1" value="{{ $match->score1 ?? 0 }}"><br><br>

    <label>Score 2:</label><br>
    <input type="number" name="score2" value="{{ $match->score2 ?? 0 }}"><br><br>

    

    <label>Match Time:</label><br>
    <input type="text" name="match_time" value="{{ $match->match_time ?? '' }}"><br><br>

    <button type="submit">Update Match</button>
</form>

</body>
</html>
