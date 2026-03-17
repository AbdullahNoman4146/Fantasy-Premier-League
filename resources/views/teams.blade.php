<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teams</title>
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
            margin-top:8px;
        }

        th,td{
            padding:10px;
            border-bottom:1px solid rgba(255,255,255,0.15);
            text-align:left;
        }

        th{ font-size:13px; opacity:.85; }

        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.25);
            font-size:12px;
            opacity:.9;
        }

        .th-big{
            font-weight:800;
            font-size:15px;
            letter-spacing:0.3px;
        }

        .team-row{
            cursor:pointer;
            transition:background 0.18s ease, transform 0.18s ease;
        }

        .team-row:hover{
            background:rgba(255,255,255,0.08);
        }

        .team-row:focus{
            outline:2px solid rgba(255,255,255,0.45);
            outline-offset:-2px;
        }

        .hint{
            margin-top:12px;
            opacity:0.78;
            font-size:13px;
        }
    </style>
</head>

<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">
            <h2 style="margin:0;">Teams</h2>

            @if(($teams ?? collect())->count())
                <table>
                    <thead>
                        <tr style="background:rgba(255,255,255,0.12);">
                            <th class="th-big">Team ID</th>
                            <th class="th-big">Team Name</th>
                            <th class="th-big">Strength</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($teams as $t)
                        <tr class="team-row" tabindex="0" onclick="window.location='{{ route('teams.show', $t->team_id) }}'" onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location='{{ route('teams.show', $t->team_id) }}'; }">
                            <td><span class="badge">Team {{ $t->team_id }}</span></td>
                            <td>{{ $t->team_name ?? 'N/A' }}</td>
                            <td>{{ $t->strength ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="hint">Click any team row to open the team details page.</div>
            @else
                <p>No teams found. Go to Admin and add some teams first.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>