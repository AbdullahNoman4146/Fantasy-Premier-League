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
    background:url('/images/back_pic.jpeg') no-repeat center center fixed;
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
        }

        .top-row{
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

        .bottom-row{
            margin-top:20px;
        }

        .finished{
            background:rgba(0,0,0,0.55);
            border:2px solid rgba(255,255,255,0.25);
            border-radius:12px;
            padding:18px;
        }

        .finished-grid{
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap:14px;
            margin-top:12px;
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

        .row{
            display:flex;
            justify-content:space-between;
            gap:10px;
            flex-wrap:wrap;
        }
    </style>
</head>

<body>
<div class="overlay">

    <h1>Fantasy Premier League</h1>
    <hr style="opacity:.25; max-width:1100px;">

    <div class="container">

        <!-- ROW 1 -->
        <div class="top-row">

            <!-- LEFT: Current Matches -->
            <div class="left">
                <div class="row">
                    <h2 style="margin:0;">Current Matches</h2>
                    <span class="badge">
                        {{ isset($currentMatches) ? $currentMatches->count() : 0 }}
                    </span>
                </div>

                @if(isset($currentMatches) && $currentMatches->count())
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

            <!-- RIGHT: Upcoming Fixtures -->
            <div class="right">
                <div class="row">
                    <h2 style="margin:0;">Upcoming Fixtures</h2>
                    <span class="badge">Next</span>
                </div>

                @if(isset($upcomingFixtures) && $upcomingFixtures->count())
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

        <!-- ROW 2: Finished Results -->
<div class="bottom-row">
    <div class="finished">
        <div class="row">
            <h2 style="margin:0;">Finished Results</h2>
            <span class="badge">Latest</span>
        </div>

        @if(isset($finishedMatches) && $finishedMatches->count())
            <div class="finished-grid">

                @foreach($finishedMatches as $index => $m)
                    <div class="card finished-item"
                         style="{{ $index >= 3 ? 'display:none;' : '' }}">
                        <div class="muted">Played: {{ $m->kickoff_at ?? 'N/A' }}</div>
                        <div class="teams">{{ $m->team1 }} vs {{ $m->team2 }}</div>
                        <div class="score">{{ $m->score1 }} - {{ $m->score2 }}</div>
                    </div>
                @endforeach

            </div>

            @if($finishedMatches->count() > 3)
                <div style="text-align:center; margin-top:15px;">
                    <button id="seeMoreBtn"
                        style="padding:10px 18px; border-radius:8px; border:none; cursor:pointer;">
                        See More
                    </button>
                </div>
            @endif

        @else
            <p>No finished results yet.</p>
        @endif
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("seeMoreBtn");
    if (!btn) return;

    let expanded = false;

    btn.addEventListener("click", function () {
        const hiddenItems = document.querySelectorAll(".finished-item");

        hiddenItems.forEach((item, index) => {
            if (index >= 3) {
                item.style.display = expanded ? "none" : "block";
            }
        });

        btn.textContent = expanded ? "See More" : "See Less";
        expanded = !expanded;
    });
});
</script>

    </div>
</div>
</body>
</html>
