<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Players - Fantasy Premier League</title>
    <style>
        body{
            margin:0;
            font-family:Arial, sans-serif;
            color:white;
            background:url('/images/back_pic.jpeg') no-repeat center center fixed;
            background-size:cover;
        }

        .overlay{
            min-height:100vh;
            background:rgba(0,0,0,0.78);
            padding:30px 20px;
        }

        .container{
            max-width:1100px;
            margin:0 auto;
        }

        .page-title{
            margin:0 0 20px;
        }

        .panel{
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.16);
            border-radius:16px;
            padding:20px;
            overflow:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        th, td{
            padding:12px;
            border-bottom:1px solid rgba(255,255,255,0.12);
            text-align:left;
        }

        th{
            font-size:14px;
            opacity:.9;
        }

        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.20);
            font-size:12px;
        }

        .empty{
            opacity:.85;
            margin-top:10px;
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="container">
        <h1 class="page-title">Players</h1>

        <div class="panel">
            @if(($players ?? collect())->count())
                <table>
                    <thead>
                        <tr>
                            <th>Player Name</th>
                            <th>Team</th>
                            <th>Jersey Number</th>
                            <th>Nationality</th>
                            <th>Position</th>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">No players available yet.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>