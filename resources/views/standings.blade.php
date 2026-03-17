<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Standings</title>
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
            max-width:1100px;
            margin:25px auto;
            background:rgba(0,0,0,0.55);
            border:2px solid rgba(255,255,255,0.25);
            border-radius:12px;
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:14px;
        }

        th, td{
            padding:10px;
            border-bottom:1px solid rgba(255,255,255,0.15);
            text-align:left;
        }

        th{ font-size:13px; opacity:.85; }

        .rank{
            display:inline-block;
            min-width:32px;
            padding:4px 8px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.25);
            text-align:center;
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">
            <h2 style="margin:0;">League Standings</h2>
            <p style="opacity:.85; margin:10px 0 0;">Standings are calculated from matches marked as finished in the admin panel.</p>

            @if(($standings ?? collect())->count())
                <table>
                    <thead>
                        <tr style="background:rgba(255,255,255,0.12);">
                            <th>Rank</th>
                            <th>Team</th>
                            <th>P</th>
                            <th>W</th>
                            <th>D</th>
                            <th>L</th>
                            <th>GF</th>
                            <th>GA</th>
                            <th>GD</th>
                            <th>PTS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($standings as $index => $row)
                            <tr>
                                <td><span class="rank">{{ $index + 1 }}</span></td>
                                <td>{{ $row->team_name ?? 'N/A' }}</td>
                                <td>{{ $row->played ?? 0 }}</td>
                                <td>{{ $row->wins ?? 0 }}</td>
                                <td>{{ $row->draws ?? 0 }}</td>
                                <td>{{ $row->losses ?? 0 }}</td>
                                <td>{{ $row->goals_scored ?? 0 }}</td>
                                <td>{{ $row->goals_conceded ?? 0 }}</td>
                                <td>{{ $row->goal_diff ?? 0 }}</td>
                                <td><b>{{ $row->points ?? 0 }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="margin-top:16px;">No standings data yet. Add teams and finish some matches first.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>
