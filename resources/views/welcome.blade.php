<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fantasy Premier League</title>
</head>
<body style="background:url('https://wallpaperaccess.com/full/317501.jpg'); color:white; text-align:center;">

<h1>Fantasy Premier League</h1>
<hr>

<h2>Welcome to the Fantasy Premier League!</h2>
<p>Create your team and enjoy the season.</p>

@if($match)
    <div style="border:2px solid white; padding:20px; width:300px; margin:auto; background:rgba(0,0,0,0.6);">
        <h3>Current Match</h3>

        <h2>{{ $match->team1 }} vs {{ $match->team2 }}</h2>

        <h1>{{ $match->score1 }} - {{ $match->score2 }}</h1>

        <p>Time: {{ $match->match_time }}</p>
    </div>
@else
    <p>No match available</p>
@endif

</body>
</html>
