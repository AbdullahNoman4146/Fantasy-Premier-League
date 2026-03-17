<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Results</title>
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

        .result-grid{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:14px;
            margin-top:14px;
        }

        .card{
            border:1px solid rgba(255,255,255,0.2);
            border-radius:12px;
            padding:16px;
            background:rgba(255,255,255,0.06);
        }

        .teams{ font-size:20px; font-weight:bold; margin:10px 0; }
        .score{ font-size:34px; font-weight:bold; margin:8px 0; }
        .muted{ opacity:.85; font-size:13px; }

        @media (max-width: 900px){
            .result-grid{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">
            <h2 style="margin:0;">Finished Match Results</h2>

            @if(($results ?? collect())->count())
                <div class="result-grid">
                    @foreach($results as $result)
                        <div class="card">
                            <div class="muted">Played: {{ $result->kickoff_at ?? 'N/A' }}</div>
                            <div class="teams">{{ $result->team1 ?? 'N/A' }} vs {{ $result->team2 ?? 'N/A' }}</div>
                            <div class="score">{{ $result->score1 ?? 0 }} - {{ $result->score2 ?? 0 }}</div>
                            <div class="muted">Only matches marked as <b>finished</b> by admin appear here.</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin-top:16px;">No completed results are available yet.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>
