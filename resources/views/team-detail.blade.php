<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->team_name }}</title>
    <style>
        body{
            margin:0;
            font-family:Arial, sans-serif;
            color:#fff;
            background:url('/images/back_pic.jpeg') no-repeat center center fixed;
            background-size:cover;
        }

        .overlay{
            min-height:100vh;
            background:rgba(0,0,0,0.78);
            padding-bottom:40px;
        }

        .page{
            max-width:1180px;
            margin:0 auto;
            padding:28px 18px 40px;
        }

        .header-card,
        .card{
            background:rgba(14,18,32,0.82);
            border:1px solid rgba(255,255,255,0.15);
            border-radius:16px;
            box-shadow:0 12px 30px rgba(0,0,0,0.24);
        }

        .header-card{
            padding:22px;
            margin-bottom:18px;
        }

        .header-top{
            display:flex;
            justify-content:space-between;
            gap:16px;
            align-items:flex-start;
            flex-wrap:wrap;
        }

        .title{
            margin:0;
            font-size:32px;
            font-weight:800;
            letter-spacing:0.3px;
        }

        .sub{
            margin-top:8px;
            color:rgba(255,255,255,0.78);
            font-size:14px;
        }

        .back-link{
            display:inline-flex;
            align-items:center;
            gap:8px;
            text-decoration:none;
            color:#fff;
            background:rgba(255,255,255,0.08);
            border:1px solid rgba(255,255,255,0.16);
            padding:10px 14px;
            border-radius:999px;
            transition:background 0.2s ease;
        }

        .back-link:hover{ background:rgba(255,255,255,0.14); }

        .grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:16px;
            margin-bottom:18px;
        }

        .card{
            padding:18px;
        }

        .stat-label{
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:1px;
            opacity:0.7;
            margin-bottom:8px;
        }

        .stat-value{
            font-size:28px;
            font-weight:800;
            line-height:1.1;
        }

        .stat-sub{
            margin-top:8px;
            font-size:13px;
            color:rgba(255,255,255,0.76);
        }

        .section-grid{
            display:grid;
            grid-template-columns:1.2fr 0.8fr;
            gap:18px;
            margin-bottom:18px;
        }

        .section-title{
            margin:0 0 14px;
            font-size:18px;
            font-weight:800;
        }

        .meta-row{
            display:flex;
            justify-content:space-between;
            gap:16px;
            padding:10px 0;
            border-bottom:1px solid rgba(255,255,255,0.1);
        }

        .meta-row:last-child{ border-bottom:none; }

        .meta-key{
            color:rgba(255,255,255,0.74);
            font-size:14px;
        }

        .meta-value{
            text-align:right;
            font-weight:700;
        }

        .pill-wrap{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            padding:7px 12px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.18);
            background:rgba(255,255,255,0.08);
            font-size:13px;
        }

        .match-box{
            border:1px solid rgba(255,255,255,0.12);
            border-radius:14px;
            padding:14px;
            background:rgba(255,255,255,0.03);
            margin-top:8px;
        }

        .match-opponent{
            font-size:20px;
            font-weight:800;
            margin-bottom:8px;
        }

        .muted{
            color:rgba(255,255,255,0.72);
            font-size:14px;
        }

        .score-line{
            font-size:26px;
            font-weight:800;
            margin:10px 0 6px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        th,td{
            padding:12px 10px;
            text-align:left;
            border-bottom:1px solid rgba(255,255,255,0.1);
            font-size:14px;
        }

        th{
            color:rgba(255,255,255,0.76);
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.8px;
        }

        .right{ text-align:right; }

        .empty{
            padding:16px;
            border:1px dashed rgba(255,255,255,0.18);
            border-radius:12px;
            color:rgba(255,255,255,0.72);
            background:rgba(255,255,255,0.03);
        }

        @media (max-width: 980px){
            .grid{ grid-template-columns:repeat(2, minmax(0,1fr)); }
            .section-grid{ grid-template-columns:1fr; }
        }

        @media (max-width: 640px){
            .grid{ grid-template-columns:1fr; }
            .title{ font-size:28px; }
            .meta-row{ flex-direction:column; }
            .meta-value{ text-align:left; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="header-card">
            <div class="header-top">
                <div>
                    <a class="back-link" href="{{ route('teams.index') }}">← Back to Teams</a>
                    <h1 class="title">{{ $team->team_name }}</h1>
                    <div class="sub">Team details, squad, manager, sponsors, standing, latest result, next match, and total market value.</div>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <div class="stat-label">League Standing</div>
                <div class="stat-value">#{{ $teamStanding->rank ?? '-' }}</div>
                <div class="stat-sub">{{ $teamStanding->points ?? 0 }} points • {{ $teamStanding->played ?? 0 }} played</div>
            </div>

            <div class="card">
                <div class="stat-label">Manager</div>
                <div class="stat-value" style="font-size:22px;">{{ $team->manager_name ?: 'No manager assigned' }}</div>
                <div class="stat-sub">{{ $team->manager_nationality ?? 'N/A' }} @if(!is_null($team->experience_years)) • {{ $team->experience_years }} years experience @endif</div>
            </div>

            <div class="card">
                <div class="stat-label">Squad Size</div>
                <div class="stat-value">{{ $squad->count() }}</div>
                <div class="stat-sub">Registered players in this team</div>
            </div>

            <div class="card">
                <div class="stat-label">Team Market Value</div>
                <div class="stat-value" style="font-size:22px;">{{ $teamMarketValue->currency ?? 'GBP' }} {{ number_format((float) ($teamMarketValue->total_market_value ?? 0), 2) }}</div>
                <div class="stat-sub">League rank #{{ $teamMarketValue->market_rank ?? '-' }} by total latest player values</div>
            </div>
        </div>

        <div class="section-grid">
            <div class="card">
                <h3 class="section-title">Team Squad</h3>
                @if($squad->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Jersey</th>
                                <th>Player</th>
                                <th>Position</th>
                                <th>Nationality</th>
                                <th class="right">Latest Market Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($squad as $player)
                                <tr>
                                    <td>#{{ $player->jersey_number }}</td>
                                    <td>{{ trim(($player->first_name ?? '') . ' ' . ($player->last_name ?? '')) ?: 'Unknown Player' }}</td>
                                    <td>{{ $player->position ?? 'N/A' }}</td>
                                    <td>{{ $player->nationality ?? 'N/A' }}</td>
                                    <td class="right">
                                        @if(!is_null($player->market_value))
                                            {{ $player->currency ?? 'GBP' }} {{ number_format((float) $player->market_value, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No squad data found for this team yet.</div>
                @endif
            </div>

            <div>
                <div class="card" style="margin-bottom:18px;">
                    <h3 class="section-title">Sponsors</h3>
                    @if($sponsors->count())
                        <div class="pill-wrap">
                            @foreach($sponsors as $sponsor)
                                <span class="pill">{{ $sponsor->sponsor_name }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">No sponsor assigned yet.</div>
                    @endif
                </div>

                <div class="card" style="margin-bottom:18px;">
                    <h3 class="section-title">Standing Details</h3>
                    <div class="meta-row">
                        <div class="meta-key">Rank</div>
                        <div class="meta-value">#{{ $teamStanding->rank ?? '-' }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Points</div>
                        <div class="meta-value">{{ $teamStanding->points ?? 0 }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Played</div>
                        <div class="meta-value">{{ $teamStanding->played ?? 0 }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Wins / Draws / Losses</div>
                        <div class="meta-value">{{ $teamStanding->wins ?? 0 }} / {{ $teamStanding->draws ?? 0 }} / {{ $teamStanding->losses ?? 0 }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Goal Difference</div>
                        <div class="meta-value">{{ $teamStanding->goal_diff ?? 0 }}</div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="section-title">Team Info</h3>
                    <div class="meta-row">
                        <div class="meta-key">Strength</div>
                        <div class="meta-value">{{ $team->strength ?? 'N/A' }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Goals Scored</div>
                        <div class="meta-value">{{ $team->goals_scored ?? 0 }}</div>
                    </div>
                    <div class="meta-row">
                        <div class="meta-key">Goals Conceded</div>
                        <div class="meta-value">{{ $team->goals_conceded ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-grid">
            <div class="card">
                <h3 class="section-title">Last Match Result</h3>
                @if($lastMatch)
                    <div class="match-box">
                        <div class="match-opponent">vs {{ $lastMatch->opponent_name }}</div>
                        <div class="muted">{{ $lastMatch->venue }} @if($lastMatch->kickoff_at) • {{ \Carbon\Carbon::parse($lastMatch->kickoff_at)->format('d M Y, h:i A') }} @endif</div>
                        <div class="score-line">{{ $team->team_name }} {{ $lastMatch->team_score }} - {{ $lastMatch->opponent_score }} {{ $lastMatch->opponent_name }}</div>
                        <div class="muted">Status: Finished</div>
                    </div>
                @else
                    <div class="empty">No finished match found for this team yet.</div>
                @endif
            </div>

            <div class="card">
                <h3 class="section-title">Upcoming Match</h3>
                @if($upcomingMatch)
                    <div class="match-box">
                        <div class="match-opponent">vs {{ $upcomingMatch->opponent_name }}</div>
                        <div class="muted">{{ $upcomingMatch->venue }}</div>
                        <div class="score-line" style="font-size:22px; margin-top:12px;">
                            {{ $upcomingMatch->kickoff_at ? \Carbon\Carbon::parse($upcomingMatch->kickoff_at)->format('d M Y, h:i A') : 'Kickoff time not set' }}
                        </div>
                        <div class="muted">Match time label: {{ $upcomingMatch->match_time ?: 'N/A' }}</div>
                    </div>
                @else
                    <div class="empty">No upcoming match scheduled for this team yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>