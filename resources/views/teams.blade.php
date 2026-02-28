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
        .navbar{
            position:sticky;
            top:0;
            z-index:1000;
            background:rgba(25, 10, 55, 0.96);
            border-bottom:1px solid rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
        }
        .nav-inner{
            max-width:1100px;
            margin:0 auto;
            padding:18px 18px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:18px;
        }
        .brand{
            font-weight:900;
            font-size:20px;
            letter-spacing:0.4px;
            text-transform:uppercase;
            text-shadow: 0 0 12px rgba(255,255,255,0.12);
        }
        .nav-links{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
            justify-content:flex-end;
        }
        .nav-links a{
            text-decoration:none;
            color:white;
            font-size:15px;
            padding:10px 14px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.20);
            background:rgba(255,255,255,0.07);
        }
        .nav-links a:hover{
            background:rgba(255,255,255,0.14);
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
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 0.3px;
}
    </style>
</head>

<body>
<div class="overlay">

    <div class="navbar">
        <div class="nav-inner">
            <div class="brand">Fantasy Premier League</div>
            <div class="nav-links">
                <a href="{{ url('/') }}">Home</a>
                
              
            </div>
        </div>
    </div>

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
                            <!--
                            <th>Goals Scored</th>
                            <th>Goals Conceded</th>
                            <th>Manager ID</th>
                            -->
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teams as $t)
                        <tr>
                            <td><span class="badge">Team {{ $t->team_id }}</span></td>
                           <td>{{ $t->team_name ?? 'N/A' }}</td>
                           <td>{{ $t->strength ?? 'N/A' }}</td>   
                          <!-- PAGE CONTENT  
                               
                            <td>{{ $t->goals_scored ?? 'N/A' }}</td>
                          <td>{{ $t->goals_conceded ?? 'N/A' }}</td>
                           <td>{{ $t->manager_id ?? 'N/A' }}</td>
                           -->
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>No teams found. Go to Admin and add some teams first.</p>
            @endif
        </div>
    </div>

</div>
</body>
</html>