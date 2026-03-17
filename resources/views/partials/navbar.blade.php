<style>
  .navbar{ position:sticky; top:0; z-index:1000; background:rgba(25, 10, 55, 0.95); border-bottom:1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px); }
  .nav-inner{ max-width:1180px; margin:0 auto; padding:14px 18px; display:flex; align-items:center; justify-content:space-between; gap:14px; }
  .nav-left{ display:flex; align-items:center; gap:12px; }
  .hamburger{ width:42px; height:42px; border-radius:10px; border:1px solid rgba(255,255,255,0.25); background:rgba(255,255,255,0.08); cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .hamburger:hover{ background:rgba(255,255,255,0.14); }
  .hamburger .bar{ width:18px; height:2px; background:#fff; margin:2px 0; border-radius:2px; opacity:0.95; }
  .brand{ display:flex; flex-direction:column; line-height:1.1; text-decoration:none; color:white; }
  .brand .title{ font-weight:bold; font-size:16px; letter-spacing:0.2px; color:white; }
  .nav-links{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
  .nav-links a{ text-decoration:none; color:white; font-size:13px; padding:8px 10px; border-radius:999px; border:1px solid rgba(255,255,255,0.18); background:rgba(255,255,255,0.06); opacity:0.95; transition:all 0.2s ease; }
  .nav-links a:hover{ background:rgba(255,255,255,0.12); }
  .sidebar-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:1200; display:none; }
  .sidebar{ position:fixed; top:0; left:0; height:100vh; width:280px; background:rgba(18, 8, 40, 0.98); border-right:1px solid rgba(255,255,255,0.15); z-index:1300; transform:translateX(-100%); transition:transform 0.25s ease; padding:18px; overflow:auto; }
  .sidebar.open{ transform:translateX(0); }
  .sidebar-backdrop.show{ display:block; }
  .sidebar-header{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:14px; }
  .sidebar-title{ font-weight:bold; font-size:15px; color:white; }
  .close-btn{ width:38px; height:38px; border-radius:10px; border:1px solid rgba(255,255,255,0.22); background:rgba(255,255,255,0.08); cursor:pointer; color:white; font-size:18px; }
  .menu{ display:flex; flex-direction:column; gap:10px; margin-top:10px; }
  .menu a{ text-decoration:none; color:white; padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.16); background:rgba(255,255,255,0.06); font-size:14px; transition:all 0.2s ease; }
  .menu a:hover{ background:rgba(255,255,255,0.12); }
  .nav-links a.active, .menu a.active{ background:rgba(255,255,255,0.18); border-color:rgba(255,255,255,0.35); font-weight:bold; }
  @media (max-width: 1080px){ .nav-links{ display:none; } }
</style>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>
<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-title">Menu</div>
        <button id="closeSidebar" class="close-btn" type="button">×</button>
    </div>

    <div class="menu">
        <a href="{{ url('/') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.index') ? 'active' : '' }}">Teams</a>
        <a href="{{ route('matches.index') }}" class="{{ request()->routeIs('matches.index') ? 'active' : '' }}">Match</a>
        <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.index') ? 'active' : '' }}">Results</a>
        <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.index') ? 'active' : '' }}">Standings</a>
        <a href="{{ route('players.index') }}" class="{{ request()->routeIs('players.index') ? 'active' : '' }}">Players</a>
        <a href="{{ route('sponsors.index') }}" class="{{ request()->routeIs('sponsors.index') ? 'active' : '' }}">Sponsors</a>
        <a href="{{ route('market-values.index') }}" class="{{ request()->routeIs('market-values.index') ? 'active' : '' }}">Market Value</a>
        <a href="{{ route('managers.index') }}" class="{{ request()->routeIs('managers.index') ? 'active' : '' }}">Manager</a>
        <a href="{{ route('transfers.index') }}" class="{{ request()->routeIs('transfers.*') ? 'active' : '' }}">Transfer</a>
    </div>
</div>

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

            <a href="{{ url('/') }}" class="brand">
                <div class="title">Fantasy Premier League</div>
            </a>
        </div>

        <div class="nav-links">
            <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.index') ? 'active' : '' }}">Teams</a>
            <a href="{{ route('matches.index') }}" class="{{ request()->routeIs('matches.index') ? 'active' : '' }}">Match</a>
            <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.index') ? 'active' : '' }}">Results</a>
            <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.index') ? 'active' : '' }}">Standings</a>
            <a href="{{ route('players.index') }}" class="{{ request()->routeIs('players.index') ? 'active' : '' }}">Players</a>
            <a href="{{ route('sponsors.index') }}" class="{{ request()->routeIs('sponsors.index') ? 'active' : '' }}">Sponsors</a>
            <a href="{{ route('market-values.index') }}" class="{{ request()->routeIs('market-values.index') ? 'active' : '' }}">Market Value</a>
            <a href="{{ route('managers.index') }}" class="{{ request()->routeIs('managers.index') ? 'active' : '' }}">Manager</a>
            <a href="{{ route('transfers.index') }}" class="{{ request()->routeIs('transfers.*') ? 'active' : '' }}">Transfer</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (!sidebar || !backdrop || !openBtn || !closeBtn) return;

    function openSidebar() {
        sidebar.classList.add('open');
        backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeSidebar();
    });
});
</script>