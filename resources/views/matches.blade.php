<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match Fixtures</title>
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

        .fixture-grid{
            display:grid;
            grid-template-columns:repeat(2, 1fr);
            gap:14px;
            margin-top:14px;
        }

        .card{
            border:1px solid rgba(255,255,255,0.2);
            border-radius:12px;
            padding:16px;
            background:rgba(255,255,255,0.06);
        }

        .teams{ font-size:22px; font-weight:bold; margin:10px 0; }
        .muted{ opacity:.85; font-size:13px; }
        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.25);
            font-size:12px;
            opacity:.9;
        }

        @media (max-width: 900px){
            .fixture-grid{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">
            <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; flex-wrap:wrap;">
                <h2 style="margin:0;">Upcoming Match Fixtures</h2>
                <span class="badge">{{ ($fixtures ?? collect())->count() }} fixtures</span>
            </div>

            @if(($fixtures ?? collect())->count())
                <div class="fixture-grid">
                    @foreach($fixtures as $fixture)
                        <div class="card">
                            <div class="muted">Kickoff</div>
                            <div>{{ $fixture->kickoff_at ?? 'N/A' }}</div>
                            <div class="teams">{{ $fixture->team1 ?? 'N/A' }} vs {{ $fixture->team2 ?? 'N/A' }}</div>
                            <div class="muted">Status: <span class="badge">{{ ucfirst($fixture->status ?? 'upcoming') }}</span></div>
                            <div class="muted" style="margin-top:10px;">This page only shows future fixtures added from the admin panel.</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin-top:16px;">No future fixtures are available yet. Add them from the admin panel.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>
