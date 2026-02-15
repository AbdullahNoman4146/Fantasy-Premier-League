<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fantasy Premier League</title>
    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            color:white;
            background:url('https://wallpaperaccess.com/full/317501.jpg') no-repeat center center fixed;
            background-size:cover;
        }
        .overlay{
            background:rgba(0,0,0,0.75);
            min-height:100vh;
            padding:30px;
        }
        h1{ text-align:center; margin:0; }
        .container{
            max-width:1100px;
            margin:25px auto;
            display:flex;
            gap:20px;
        }
        .left, .right{
            flex:1;
            background:rgba(0,0,0,0.55);
            border:2px solid rgba(255,255,255,0.25);
            border-radius:12px;
            padding:18px;
        }
        .card{
            border:1px solid rgba(255,255,255,0.2);
            border-radius:12px;
            padding:14px;
            margin-bottom:14px;
            background:rgba(255,255,255,0.06);
        }
        .muted{ opacity:.85; font-size:13px; }
        .score{ font-size:34px; font-weight:bold; margin:6px 0; }
        .teams{ font-size:20px; font-weight:bold; }
        table{ width:100%; border-collapse:collapse; margin-top:8px; }
        th,td{ padding:10px; border-bottom:1px solid rgba(255,255,255,0.15); text-align:left; }
        th{ font-size:13px; opacity:.85; }
        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.25);
            font-size:12px;
            opacity:.9;
        }
        .row{ display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    </style>
</head>
<body>
<div class="overlay">

    <h1>Fantasy Premier League</h1>
    <hr style="opacity:.25; max-width:1100px;">

    <div class="container">

        <!--  2 CURRENT MATCHES -->
        <div class="left">
            <div class="row">
                <h2 style="margin:0;">Current Matches</h2>
                <span class="badge">Showing 2</span>
            </div>

            @if($currentMatches->count())
                @foreach($currentMatches as $match)
                    <div class="card">
                        <div class="muted">Kickoff: {{ $match->kickoff_at ?? 'N/A' }}</div>
                        <div class="teams">{{ $match->team1 }} vs {{ $match->team2 }}</div>
                        <div class="score">{{ $match->score1 }} - {{ $match->score2 }}</div>
                        <div class="muted">Time: {{ $match->match_time ?? 'N/A' }}</div>
                    </div>
                @endforeach
            @else
                <p>No current matches available.</p>
            @endif
        </div>

        <!-- UPCOMING FIXTURES -->
        <div class="right">
            <div class="row">
                <h2 style="margin:0;">Upcoming Fixtures</h2>
                <span class="badge">Next</span>
            </div>

            @if($upcomingFixtures->count())
                <table>
                    <thead>
                        <tr>
                            <th>Kickoff</th>
                            <th>Fixture</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($upcomingFixtures as $fx)
                        <tr>
                            <td>{{ $fx->kickoff_at ?? 'N/A' }}</td>
                            <td>{{ $fx->team1 }} vs {{ $fx->team2 }}</td>
                            <td><span class="badge">{{ $fx->status }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>No upcoming fixtures available.</p>
            @endif
        </div>

    </div>
</div>
</body>
</html>
