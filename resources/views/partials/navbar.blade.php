<style>
  * { box-sizing: border-box; }

  .navbar{
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(20, 10, 45, 0.92);
    border-bottom: 1px solid rgba(255,255,255,0.12);
    backdrop-filter: blur(12px);
  }

  .nav-inner{
    max-width: 1240px;
    margin: 0 auto;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  .nav-left{
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
  }

  .nav-actions{
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .hamburger,
  .search-toggle,
  .close-btn{
    width: 42px;
    height: 42px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.16);
    background: rgba(255,255,255,0.08);
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .hamburger:hover,
  .search-toggle:hover,
  .close-btn:hover{
    background: rgba(255,255,255,0.14);
    transform: translateY(-1px);
  }

  .hamburger .bar{
    width: 18px;
    height: 2px;
    background: #fff;
    margin: 2px 0;
    border-radius: 2px;
    opacity: 0.95;
  }

  .brand{
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-decoration: none;
    color: #fff;
    min-width: 0;
  }

  .brand .title{
    font-weight: 800;
    font-size: 18px;
    letter-spacing: 0.2px;
    color: #fff;
    white-space: nowrap;
  }

  .brand .subtitle{
    font-size: 11px;
    color: rgba(255,255,255,0.68);
    margin-top: 2px;
    letter-spacing: 0.3px;
  }

  .nav-links{
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .nav-links a{
    text-decoration: none;
    color: #fff;
    font-size: 13px;
    padding: 9px 12px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.05);
    transition: all 0.2s ease;
  }

  .nav-links a:hover{
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.22);
  }

  .nav-links a.active,
  .menu a.active{
    background: rgba(255,255,255,0.18);
    border-color: rgba(255,255,255,0.28);
    font-weight: 700;
  }

  .sidebar-backdrop{
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 1200;
    display: none;
  }

  .sidebar{
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 290px;
    background: rgba(18, 8, 40, 0.98);
    border-right: 1px solid rgba(255,255,255,0.14);
    z-index: 1300;
    transform: translateX(-100%);
    transition: transform 0.25s ease;
    padding: 18px;
    overflow: auto;
    backdrop-filter: blur(12px);
  }

  .sidebar.open{
    transform: translateX(0);
  }

  .sidebar-backdrop.show{
    display: block;
  }

  .sidebar-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
  }

  .sidebar-title{
    font-weight: 700;
    font-size: 15px;
    color: #fff;
  }

  .menu{
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
  }

  .menu a{
    text-decoration: none;
    color: #fff;
    padding: 11px 12px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.05);
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .menu a:hover{
    background: rgba(255,255,255,0.12);
  }

  /* SEARCH OVERLAY */
  .search-overlay{
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: none;
    align-items: flex-start;
    justify-content: center;
    padding: 90px 18px 24px;
    background: rgba(7, 8, 18, 0.28);
    backdrop-filter: blur(14px);
  }

  .search-overlay.show{
    display: flex;
  }

  .search-panel{
    width: min(760px, 100%);
    background: rgba(19, 12, 42, 0.94);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 24px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
    overflow: hidden;
    animation: searchPanelIn 0.18s ease;
  }

  @keyframes searchPanelIn{
    from{
      opacity: 0;
      transform: translateY(-10px) scale(0.985);
    }
    to{
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .search-topbar{
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 16px 12px;
    border-bottom: 1px solid rgba(255,255,255,0.10);
  }

  .search-icon-wrap{
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: 14px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.90);
  }

  .search-input{
    flex: 1;
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    color: #fff;
    font-size: 17px;
    font-weight: 600;
  }

  .search-input::placeholder{
    color: rgba(255,255,255,0.56);
    font-weight: 500;
  }

  .search-close{
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.14);
    background: rgba(255,255,255,0.08);
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .search-close:hover{
    background: rgba(255,255,255,0.14);
  }

  .search-hint{
    padding: 0 18px 12px;
    font-size: 12px;
    color: rgba(255,255,255,0.62);
  }

  .search-results{
    max-height: 420px;
    overflow-y: auto;
    padding: 0 10px 12px;
  }

  .search-results::-webkit-scrollbar{
    width: 8px;
  }

  .search-results::-webkit-scrollbar-thumb{
    background: rgba(255,255,255,0.14);
    border-radius: 999px;
  }

  .search-item{
    display: block;
    text-decoration: none;
    color: #fff;
    border-radius: 16px;
    padding: 14px 14px;
    border: 1px solid transparent;
    transition: all 0.18s ease;
    margin-bottom: 8px;
    background: rgba(255,255,255,0.03);
  }

  .search-item:hover{
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.12);
    transform: translateY(-1px);
  }

  .search-top{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .search-title{
    font-weight: 700;
    font-size: 15px;
    line-height: 1.35;
  }

  .search-type{
    display: inline-flex;
    align-items: center;
    padding: 5px 9px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.08);
    font-size: 11px;
    color: rgba(255,255,255,0.85);
    white-space: nowrap;
  }

  .search-subtitle{
    margin-top: 7px;
    font-size: 12px;
    color: rgba(255,255,255,0.72);
    line-height: 1.5;
  }

  .search-state{
    padding: 18px 14px 20px;
    color: rgba(255,255,255,0.72);
    font-size: 14px;
  }

  body.search-open{
    overflow: hidden;
  }

  @media (max-width: 1100px){
    .nav-links{
      display: none;
    }
  }

  @media (max-width: 680px){
    .nav-inner{
      padding: 12px 14px;
    }

    .brand .title{
      font-size: 16px;
    }

    .brand .subtitle{
      display: none;
    }

    .search-topbar{
      padding: 14px 12px 10px;
    }

    .search-input{
      font-size: 15px;
    }
  }
</style>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<div id="sidebar" class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-title">Menu</div>
    <button id="closeSidebar" class="close-btn" type="button">×</button>
  </div>

  <div class="menu">
    <a href="{{ url('/') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
    <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">Teams</a>
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

<div class="search-overlay" id="searchOverlay">
  <div class="search-panel" role="dialog" aria-modal="true" aria-labelledby="globalSearchInput">
    <div class="search-topbar">
      <div class="search-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M21 21L16.65 16.65M18 10.5C18 14.6421 14.6421 18 10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <input
        id="globalSearchInput"
        class="search-input"
        type="text"
        placeholder="Search teams, players, managers, matches, standings, transfers..."
        autocomplete="off"
      >

      <button type="button" class="search-close" id="closeSearchOverlay">×</button>
    </div>

    <div class="search-hint">
      Type at least 2 letters. Press Enter to open the first result. Press Esc to close.
    </div>

    <div id="globalSearchResults" class="search-results">
      <div class="search-state">Search across the whole site.</div>
    </div>
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
        <div class="subtitle">Match • Results • Standings • Teams</div>
      </a>
    </div>

    <div class="nav-links">
      <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">Teams</a>
      <a href="{{ route('matches.index') }}" class="{{ request()->routeIs('matches.index') ? 'active' : '' }}">Match</a>
      <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.index') ? 'active' : '' }}">Results</a>
      <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.index') ? 'active' : '' }}">Standings</a>
      <a href="{{ route('players.index') }}" class="{{ request()->routeIs('players.index') ? 'active' : '' }}">Players</a>
      <a href="{{ route('sponsors.index') }}" class="{{ request()->routeIs('sponsors.index') ? 'active' : '' }}">Sponsors</a>
      <a href="{{ route('market-values.index') }}" class="{{ request()->routeIs('market-values.index') ? 'active' : '' }}">Market Value</a>
      <a href="{{ route('managers.index') }}" class="{{ request()->routeIs('managers.index') ? 'active' : '' }}">Manager</a>
      <a href="{{ route('transfers.index') }}" class="{{ request()->routeIs('transfers.*') ? 'active' : '' }}">Transfer</a>
    </div>

    <div class="nav-actions">
      <button id="openSearchOverlay" class="search-toggle" type="button" aria-label="Open search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M21 21L16.65 16.65M18 10.5C18 14.6421 14.6421 18 10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const openSidebarBtn = document.getElementById('openSidebar');
  const closeSidebarBtn = document.getElementById('closeSidebar');

  const searchOverlay = document.getElementById('searchOverlay');
  const openSearchBtn = document.getElementById('openSearchOverlay');
  const closeSearchBtn = document.getElementById('closeSearchOverlay');
  const searchInput = document.getElementById('globalSearchInput');
  const searchResults = document.getElementById('globalSearchResults');
  const searchUrl = @json(route('search.global'));

  let debounceTimer = null;
  let activeController = null;

  function openSidebar() {
    sidebar.classList.add('open');
    backdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
    if (!searchOverlay.classList.contains('show')) {
      document.body.style.overflow = '';
    }
  }

  function openSearchOverlay() {
    searchOverlay.classList.add('show');
    document.body.classList.add('search-open');
    document.body.style.overflow = 'hidden';
    setTimeout(function () {
      searchInput.focus();
    }, 50);
  }

  function closeSearchOverlay() {
    searchOverlay.classList.remove('show');
    document.body.classList.remove('search-open');
    if (!backdrop.classList.contains('show')) {
      document.body.style.overflow = '';
    }
  }

  openSidebarBtn?.addEventListener('click', openSidebar);
  closeSidebarBtn?.addEventListener('click', closeSidebar);
  backdrop?.addEventListener('click', closeSidebar);

  openSearchBtn?.addEventListener('click', openSearchOverlay);
  closeSearchBtn?.addEventListener('click', closeSearchOverlay);

  searchOverlay?.addEventListener('click', function (event) {
    if (event.target === searchOverlay) {
      closeSearchOverlay();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeSidebar();
      closeSearchOverlay();
    }
  });

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function showSearchState(message) {
    searchResults.innerHTML = '<div class="search-state">' + escapeHtml(message) + '</div>';
  }

  function renderSearchItems(items) {
    if (!Array.isArray(items) || items.length === 0) {
      showSearchState('No related content found.');
      return;
    }

    searchResults.innerHTML = items.map(function (item) {
      return `
        <a class="search-item" href="${escapeHtml(item.url)}">
          <div class="search-top">
            <div class="search-title">${escapeHtml(item.title)}</div>
            <span class="search-type">${escapeHtml(item.type)}</span>
          </div>
          <div class="search-subtitle">${escapeHtml(item.subtitle)}</div>
        </a>
      `;
    }).join('');
  }

  async function performSearch(query) {
    if (query.length < 2) {
      showSearchState('Type at least 2 letters to search.');
      return;
    }

    showSearchState('Searching...');

    if (activeController) {
      activeController.abort();
    }

    activeController = new AbortController();

    try {
      const response = await fetch(searchUrl + '?q=' + encodeURIComponent(query), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        signal: activeController.signal
      });

      if (!response.ok) {
        throw new Error('Search request failed');
      }

      const data = await response.json();
      renderSearchItems(data.items || []);
    } catch (error) {
      if (error.name === 'AbortError') {
        return;
      }

      showSearchState('Search is temporarily unavailable.');
    }
  }

  searchInput?.addEventListener('input', function (event) {
    const query = event.target.value.trim();

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      performSearch(query);
    }, 250);
  });

  searchInput?.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      const firstResult = searchResults.querySelector('.search-item');
      if (firstResult) {
        window.location.href = firstResult.getAttribute('href');
        event.preventDefault();
      }
    }
  });
});
</script>