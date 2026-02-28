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
        padding:0; /* changed because navbar will sit at top */
      }

      /* ---------- NAVBAR ---------- */
      .navbar{
        position:sticky;
        top:0;
        z-index:1000;
        background:rgba(25, 10, 55, 0.95);
        border-bottom:1px solid rgba(255,255,255,0.15);
        backdrop-filter: blur(8px);
      }

      .nav-inner{
        max-width:1100px;
        margin:0 auto;
        padding:14px 18px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
      }

      .nav-left{
        display:flex;
        align-items:center;
        gap:12px;
      }

      .hamburger{
        width:42px;
        height:42px;
        border-radius:10px;
        border:1px solid rgba(255,255,255,0.25);
        background:rgba(255,255,255,0.08);
        cursor:pointer;
        display:flex;
        align-items:center;
        justify-content:center;
      }

      .hamburger:hover{
        background:rgba(255,255,255,0.14);
      }

      .hamburger .bar{
        width:18px;
        height:2px;
        background:#fff;
        margin:2px 0;
        border-radius:2px;
        opacity:0.95;
      }

      .brand{
        display:flex;
        flex-direction:column;
        line-height:1.1;
      }

      .brand .title{
        font-weight:bold;
        font-size:16px;
        letter-spacing:0.2px;
      }

      .brand .sub{
        font-size:12px;
        opacity:0.85;
      }

      .nav-links{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        justify-content:flex-end;
      }

      .nav-links a{
        text-decoration:none;
        color:white;
        font-size:13px;
        padding:8px 10px;
        border-radius:999px;
        border:1px solid rgba(255,255,255,0.18);
        background:rgba(255,255,255,0.06);
        opacity:0.95;
      }

      .nav-links a:hover{
        background:rgba(255,255,255,0.12);
      }

      /* ---------- SIDEBAR ---------- */
      .sidebar-backdrop{
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.55);
        z-index:1200;
        display:none;
      }

      .sidebar{
        position:fixed;
        top:0;
        left:0;
        height:100vh;
        width:280px;
        background:rgba(18, 8, 40, 0.98);
        border-right:1px solid rgba(255,255,255,0.15);
        z-index:1300;
        transform:translateX(-100%);
        transition:transform 0.25s ease;
        padding:18px;
      }

      .sidebar.open{
        transform:translateX(0);
      }

      .sidebar-backdrop.show{
        display:block;
      }

      .sidebar-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:14px;
      }

      .sidebar-title{
        font-weight:bold;
        font-size:15px;
      }

      .close-btn{
        width:38px;
        height:38px;
        border-radius:10px;
        border:1px solid rgba(255,255,255,0.22);
        background:rgba(255,255,255,0.08);
        cursor:pointer;
        color:white;
        font-size:18px;
      }

      .menu{
        display:flex;
        flex-direction:column;
        gap:10px;
        margin-top:10px;
      }

      .menu a{
        text-decoration:none;
        color:white;
        padding:10px 12px;
        border-radius:10px;
        border:1px solid rgba(255,255,255,0.16);
        background:rgba(255,255,255,0.06);
        font-size:14px;
      }

      .menu a:hover{
        background:rgba(255,255,255,0.12);
      }

      /* ---------- PAGE CONTENT ---------- */
      .page{
        padding:30px; /* moved from overlay */
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

      /* Responsive: hide top links and rely on sidebar */
      @media (max-width: 900px){
        .nav-links{ display:none; }
      }
    </style>
</head>

<body>
<div class="overlay">

    <!-- Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">Menu</div>
            <button id="closeSidebar" class="close-btn" type="button">×</button>
        </div>

        <!-- Put your real routes later. For now these are placeholders (#) -->
        <div class="menu">
            <a href="{{ url('/teams') }}">Teams</a>
            <a href="#">Match</a>
            <a href="#">Results</a>
            <a href="#">Standings</a>
            <a href="#">Players</a>
            <a href="#">Sponsors</a>
            <a href="#">Market Values</a>
            <a href="#">Managers</a>
            <a href="#">Transfer</a>
        </div>
    </div>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-inner">

            <div class="nav-left">
                <button id="openSidebar" class="hamburger" type="button" aria-label="Open menu">
                    <div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </div>
                </button>

                <div class="brand">
                    <div class="title">Fantasy Premier League</div>
                    
                </div>
            </div>

            <!-- Top links for desktop -->
            <div class="nav-links">
                <a href="{{ url('/teams') }}">Teams</a>
                <a href="#">Match</a>
                <a href="#">Results</a>
                <a href="#">Standings</a>
                <a href="#">Players</a>
                <a href="#">Sponsors</a>
                <a href="#">Market Values</a>
                <a href="#">Managers</a>
                <a href="#">Transfer</a>
            </div>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page">

        
     

        <div class="container">

            <!-- ROW 1 -->
            <div class="top-row">

                <!-- LEFT: Current Matches -->
                <div class="left">
                    <div class="row">
                        <h2 style="margin:0;">Current Matches</h2>
                        <span class="badge">
                            {{ ($currentMatches ?? collect())->count() }}
                        </span>
                    </div>

                    @if(($currentMatches ?? collect())->count())
                        @foreach($currentMatches as $match)
                            <div class="card">
                                <div class="muted">Kickoff: {{ $match->kickoff_at ?? 'N/A' }}</div>
                                <div class="teams">{{ $match->team1 ?? 'N/A' }} vs {{ $match->team2 ?? 'N/A' }}</div>
                                <div class="score">{{ $match->score1 ?? 0 }} - {{ $match->score2 ?? 0 }}</div>
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

                    @if(($upcomingFixtures ?? collect())->count())
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
                                    <td>{{ $fx->team1 ?? 'N/A' }} vs {{ $fx->team2 ?? 'N/A' }}</td>
                                    <td><span class="badge">{{ $fx->status ?? 'N/A' }}</span></td>
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

                    @if(($finishedMatches ?? collect())->count())
                        <div class="finished-grid">

                            @foreach($finishedMatches as $index => $m)
                                <div class="card finished-item"
                                     style="{{ $index >= 3 ? 'display:none;' : '' }}">
                                    <div class="muted">Played: {{ $m->kickoff_at ?? 'N/A' }}</div>
                                    <div class="teams">{{ $m->team1 ?? 'N/A' }} vs {{ $m->team2 ?? 'N/A' }}</div>
                                    <div class="score">{{ $m->score1 ?? 0 }} - {{ $m->score2 ?? 0 }}</div>
                                </div>
                            @endforeach

                        </div>

                        @if(($finishedMatches ?? collect())->count() > 3)
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
                // Finished results See More / See Less
                document.addEventListener("DOMContentLoaded", function () {
                    const btn = document.getElementById("seeMoreBtn");

                    if (btn) {
                        let expanded = false;

                        btn.addEventListener("click", function () {
                            const items = document.querySelectorAll(".finished-item");

                            items.forEach((item, index) => {
                                if (index >= 3) {
                                    item.style.display = expanded ? "none" : "block";
                                }
                            });

                            btn.textContent = expanded ? "See More" : "See Less";
                            expanded = !expanded;
                        });
                    }

                    // Sidebar open/close
                    const sidebar = document.getElementById("sidebar");
                    const backdrop = document.getElementById("sidebarBackdrop");
                    const openBtn = document.getElementById("openSidebar");
                    const closeBtn = document.getElementById("closeSidebar");

                    function openSidebar() {
                        sidebar.classList.add("open");
                        backdrop.classList.add("show");
                    }

                    function closeSidebar() {
                        sidebar.classList.remove("open");
                        backdrop.classList.remove("show");
                    }

                    openBtn.addEventListener("click", openSidebar);
                    closeBtn.addEventListener("click", closeSidebar);
                    backdrop.addEventListener("click", closeSidebar);

                    // ESC key closes sidebar
                    document.addEventListener("keydown", function (e) {
                        if (e.key === "Escape") closeSidebar();
                    });
                });
            </script>

        </div>
    </div>

</div>
</body>
</html>