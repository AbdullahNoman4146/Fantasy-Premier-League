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
        padding:0;
      }

      .page{
        padding:30px;
      }

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

      .live-card{
        background:linear-gradient(135deg, rgba(120, 18, 32, 0.32), rgba(255,255,255,0.05));
        border-color:rgba(255,255,255,0.24);
      }

      .live-top{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:10px;
      }

      .live-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:6px 12px;
        border-radius:999px;
        background:rgba(255, 59, 92, 0.15);
        border:1px solid rgba(255, 59, 92, 0.45);
        color:#ffd7df;
        font-size:12px;
        font-weight:bold;
        letter-spacing:.8px;
      }

      .live-dot{
        width:9px;
        height:9px;
        border-radius:50%;
        background:#ff4d6d;
        box-shadow:0 0 0 rgba(255,77,109,0.8);
        animation: livePulse 1.2s infinite;
      }

      .live-minute{
        font-size:14px;
        font-weight:bold;
        color:#ffffff;
      }

      @keyframes livePulse{
        0%{ transform:scale(1); box-shadow:0 0 0 0 rgba(255,77,109,0.75); }
        70%{ transform:scale(1.06); box-shadow:0 0 0 10px rgba(255,77,109,0); }
        100%{ transform:scale(1); box-shadow:0 0 0 0 rgba(255,77,109,0); }
      }

      .muted{ opacity:.85; font-size:13px; }
      .score{ font-size:34px; font-weight:bold; margin:6px 0; }
      .scorers{ font-size:13px; line-height:1.5; color:rgba(255,255,255,.88); }
      .scorers strong{ color:#fff; }
      .teams{ font-size:20px; font-weight:bold; }
      .vs-text{ opacity:.7; font-size:16px; font-weight:normal; }

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

      .auto-refresh-note{
        margin-top:8px;
        font-size:12px;
        opacity:.75;
      }

      @media (max-width: 900px){
        .top-row{
          flex-direction:column;
        }

        .finished-grid{
          grid-template-columns:1fr;
        }
      }
    </style>
</head>

<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">

            <div class="top-row">

                <div class="left">
                    <div class="row">
                        <h2 style="margin:0;">Current Matches</h2>
                        <span class="badge" id="liveMatchCountBadge">{{ ($currentMatches ?? collect())->count() }}</span>
                    </div>

                    <div id="currentMatchesList" data-live-endpoint="{{ url('/live/current-matches') }}">
                        @if(($currentMatches ?? collect())->count())
                            @foreach($currentMatches as $match)
                                <div class="card live-card" data-match-id="{{ $match->match_id }}">
                                    <div class="live-top">
                                        <span class="live-badge"><span class="live-dot"></span> LIVE</span>
                                        <span class="live-minute"
                                           data-kickoff="{{ $match->kickoff_at ?? '' }}"
                                           data-fallback="{{ $match->match_time ?? '' }}"
                                           data-status="{{ $match->status ?? '' }}"
                                           data-live-phase="{{ $match->live_phase ?? '' }}"
                                           data-first-half-added="{{ (int)($match->first_half_added_minutes ?? 0) }}"
                                           data-second-half-added="{{ (int)($match->second_half_added_minutes ?? 0) }}"
                                           data-second-half-started-at="{{ $match->second_half_started_at ?? '' }}">
                                             LIVE
                                        </span>
                                    </div>
                                    <div class="muted">Kickoff: {{ $match->kickoff_at ?? 'N/A' }}</div>
                                    <div class="teams">{{ $match->team1 ?? 'N/A' }} <span class="vs-text">vs</span> {{ $match->team2 ?? 'N/A' }}</div>
                                    <div class="score">{{ $match->score1 ?? 0 }} - {{ $match->score2 ?? 0 }}</div>
                                    @if(!empty($match->team1_scorers_text) || !empty($match->team2_scorers_text))
                                        <div class="scorers">
                                            @if(!empty($match->team1_scorers_text))
                                                <div><strong>{{ $match->team1 ?? 'Team A' }}:</strong> {{ $match->team1_scorers_text }}</div>
                                            @endif
                                            @if(!empty($match->team2_scorers_text))
                                                <div><strong>{{ $match->team2 ?? 'Team B' }}:</strong> {{ $match->team2_scorers_text }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p id="noCurrentMatchesMessage">No current matches available.</p>
                        @endif
                    </div>

                    <div class="auto-refresh-note">Live scores refresh automatically.</div>
                </div>

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

            <div class="bottom-row">
                <div class="finished">
                    <div class="row">
                        <h2 style="margin:0;">Finished Results</h2>
                        <span class="badge">Latest</span>
                    </div>

                    @if(($finishedMatches ?? collect())->count())
                        <div class="finished-grid">
                            @foreach($finishedMatches as $index => $m)
                                <div class="card finished-item" style="{{ $index >= 3 ? 'display:none;' : '' }}">
                                    <div class="muted">Played: {{ $m->kickoff_at ?? 'N/A' }}</div>
                                    <div class="teams">{{ $m->team1 ?? 'N/A' }} vs {{ $m->team2 ?? 'N/A' }}</div>
                                    <div class="score">{{ $m->score1 ?? 0 }} - {{ $m->score2 ?? 0 }}</div>
                                    @if(!empty($m->team1_scorers_text) || !empty($m->team2_scorers_text))
                                        <div class="scorers">
                                            @if(!empty($m->team1_scorers_text))
                                                <div><strong>{{ $m->team1 ?? 'Team A' }}:</strong> {{ $m->team1_scorers_text }}</div>
                                            @endif
                                            @if(!empty($m->team2_scorers_text))
                                                <div><strong>{{ $m->team2 ?? 'Team B' }}:</strong> {{ $m->team2_scorers_text }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if(($finishedMatches ?? collect())->count() > 3)
                            <div style="text-align:center; margin-top:15px;">
                                <button id="seeMoreBtn" style="padding:10px 18px; border-radius:8px; border:none; cursor:pointer;">
                                    See More
                                </button>
                            </div>
                        @endif
                    @else
                        <p>No finished results yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("seeMoreBtn");
    const currentMatchesList = document.getElementById("currentMatchesList");
    const liveCountBadge = document.getElementById("liveMatchCountBadge");
    const liveEndpoint = currentMatchesList ? currentMatchesList.getAttribute("data-live-endpoint") : "";

    if (btn) {
        let expanded = false;

        btn.addEventListener("click", function () {
            const items = document.querySelectorAll(".finished-item");

            items.forEach(function (item, index) {
                if (index >= 3) {
                    item.style.display = expanded ? "none" : "block";
                }
            });

            btn.textContent = expanded ? "See More" : "See Less";
            expanded = !expanded;
        });
    }

    function escapeHtml(value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function parseLocalDateTime(value) {
        if (!value) return null;

        const normalized = String(value).trim().replace("T", " ");
        const match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})(?::(\d{2}))?$/);

        if (!match) return null;

        return new Date(
            Number(match[1]),
            Number(match[2]) - 1,
            Number(match[3]),
            Number(match[4]),
            Number(match[5]),
            Number(match[6] || 0)
        );
    }

    function formatLiveMinute(matchData) {
    const fallbackLabel = matchData.fallbackLabel || "";
    const status = (matchData.status || "").toLowerCase();
    const livePhase = (matchData.livePhase || "").toLowerCase();

    if (status === "finished" || livePhase === "finished") {
        return fallbackLabel || "FT";
    }

    if (livePhase === "break") {
        return "Break";
    }

    if (livePhase === "second_half") {
        const secondHalfStart = parseLocalDateTime(matchData.secondHalfStartedAt);

        if (!secondHalfStart || isNaN(secondHalfStart.getTime())) {
            return fallbackLabel || "46'";
        }

        const diffMs = Date.now() - secondHalfStart.getTime();
        const rawMinutes = Math.max(0, Math.floor(diffMs / 60000));
        let elapsedMinute = 46 + rawMinutes;

        const secondHalfAdded = Math.max(0, parseInt(matchData.secondHalfAddedMinutes || 0, 10));
        const finalSecondHalfMinute = 90 + secondHalfAdded;

        if (elapsedMinute > finalSecondHalfMinute) {
            elapsedMinute = finalSecondHalfMinute;
        }

        if (elapsedMinute > 90) {
            return "90+" + (elapsedMinute - 90) + "'";
        }

        return elapsedMinute + "'";
    }

    const kickoffDate = parseLocalDateTime(matchData.kickoffAt);

    if (!kickoffDate || isNaN(kickoffDate.getTime())) {
        return fallbackLabel || "LIVE";
    }

    const diffMs = Date.now() - kickoffDate.getTime();
    const rawMinutes = Math.max(0, Math.floor(diffMs / 60000));
    let elapsedMinute = diffMs >= 0 ? Math.max(1, rawMinutes) : 0;

    const firstHalfAdded = Math.max(0, parseInt(matchData.firstHalfAddedMinutes || 0, 10));
    const finalFirstHalfMinute = 45 + firstHalfAdded;

    if (elapsedMinute > finalFirstHalfMinute) {
        return "Break";
    }

    if (elapsedMinute > 45) {
        return "45+" + (elapsedMinute - 45) + "'";
    }

    return elapsedMinute + "'";
}

    function updateLiveMinuteLabels() {
    const labels = document.querySelectorAll(".live-minute");

    labels.forEach(function (element) {
        const matchData = {
            kickoffAt: element.getAttribute("data-kickoff") || "",
            fallbackLabel: element.getAttribute("data-fallback") || "",
            status: element.getAttribute("data-status") || "",
            livePhase: element.getAttribute("data-live-phase") || "",
            firstHalfAddedMinutes: element.getAttribute("data-first-half-added") || "0",
            secondHalfAddedMinutes: element.getAttribute("data-second-half-added") || "0",
            secondHalfStartedAt: element.getAttribute("data-second-half-started-at") || "",
        };

        element.textContent = formatLiveMinute(matchData);
    });
}
    function renderCurrentMatches(matches) {
        if (!currentMatchesList) return;

        if (!Array.isArray(matches) || matches.length === 0) {
            currentMatchesList.innerHTML = '<p id="noCurrentMatchesMessage">No current matches available.</p>';
            if (liveCountBadge) liveCountBadge.textContent = "0";
            return;
        }

        let html = "";

        matches.forEach(function (match) {
            const score1 = match.score1 !== undefined && match.score1 !== null ? match.score1 : 0;
            const score2 = match.score2 !== undefined && match.score2 !== null ? match.score2 : 0;
            const kickoffAt = match.kickoff_at || "";
            const matchTime = match.match_time || "";
            const team1Scorers = match.team1_scorers_text || "";
            const team2Scorers = match.team2_scorers_text || "";
            const scorerHtml = (team1Scorers || team2Scorers)
                ? `<div class="scorers">
                        ${team1Scorers ? `<div><strong>${escapeHtml(match.team1 || "Team A")}:</strong> ${escapeHtml(team1Scorers)}</div>` : ""}
                        ${team2Scorers ? `<div><strong>${escapeHtml(match.team2 || "Team B")}:</strong> ${escapeHtml(team2Scorers)}</div>` : ""}
                   </div>`
                : "";

            html += `
                <div class="card live-card" data-match-id="${escapeHtml(match.match_id)}">
                    <div class="live-top">
                        <span class="live-badge"><span class="live-dot"></span> LIVE</span>
                        <span class="live-minute"
                             data-kickoff="${escapeHtml(kickoffAt || "")}"
                             data-fallback="${escapeHtml(match.match_time || "")}"
                             data-status="${escapeHtml(match.status || "")}"
                             data-live-phase="${escapeHtml(match.live_phase || "")}"
                             data-first-half-added="${escapeHtml(match.first_half_added_minutes || 0)}"
                             data-second-half-added="${escapeHtml(match.second_half_added_minutes || 0)}"
                             data-second-half-started-at="${escapeHtml(match.second_half_started_at || "")}">
                                    LIVE
                        </span>
                    </div>
                    <div class="muted">Kickoff: ${escapeHtml(kickoffAt || "N/A")}</div>
                    <div class="teams">${escapeHtml(match.team1 || "N/A")} <span class="vs-text">vs</span> ${escapeHtml(match.team2 || "N/A")}</div>
                    <div class="score">${escapeHtml(score1)} - ${escapeHtml(score2)}</div>
                    ${scorerHtml}
                </div>
            `;
        });

        currentMatchesList.innerHTML = html;

        if (liveCountBadge) {
            liveCountBadge.textContent = String(matches.length);
        }

        updateLiveMinuteLabels();
    }

    let pollingInProgress = false;

    async function refreshCurrentMatches() {
        if (!liveEndpoint || pollingInProgress) return;

        pollingInProgress = true;

        try {
            const response = await fetch(liveEndpoint, {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                cache: "no-store"
            });

            if (!response.ok) {
                throw new Error("Live fetch failed");
            }

            const payload = await response.json();
            renderCurrentMatches(payload.matches || []);
        } catch (error) {
            console.error(error);
        } finally {
            pollingInProgress = false;
        }
    }

    updateLiveMinuteLabels();
    setInterval(updateLiveMinuteLabels, 1000);

    refreshCurrentMatches();
    setInterval(refreshCurrentMatches, 10000);
});
</script>

@include('partials.footer')
</body>
</html>