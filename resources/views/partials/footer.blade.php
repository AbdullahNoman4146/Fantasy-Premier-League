<style>
  .site-footer{
    margin-top: 0 !important;
    clear: both;
    background: #050b18;
    border-top: 1px solid rgba(255,255,255,0.10);
    color: #fff;
    position: relative;
  }

  .site-footer-inner{
    max-width: 1240px;
    margin: 0 auto;
    padding: 22px 20px 18px;
  }

  .site-footer-top{
    display: grid;
    grid-template-columns: minmax(260px, 1.35fr) repeat(3, minmax(150px, 1fr));
    gap: 28px;
    align-items: start;
  }

  .site-footer-brand-title{
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.3px;
    color: #fff;
  }

  .site-footer-brand-text{
    margin: 14px 0 0;
    color: rgba(255,255,255,0.78);
    font-size: 14px;
    line-height: 1.8;
    max-width: 430px;
  }

  .site-footer-heading{
    margin: 0 0 14px;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,0.74);
  }

  .site-footer-links{
    display: grid;
    gap: 10px;
  }

  .site-footer-links a{
    color: rgba(255,255,255,0.92);
    text-decoration: none;
    font-size: 14px;
    line-height: 1.6;
    transition: all 0.2s ease;
  }

  .site-footer-links a:hover{
    color: #fff;
    transform: translateX(2px);
  }

  .site-footer-sponsors{
    margin-top: 26px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.08);
  }

  .site-footer-sponsor-grid{
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 14px;
    margin-top: 14px;
  }

  .site-footer-sponsor-card{
    min-height: 108px;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,0.10);
    background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 16px 12px;
  }

  .site-footer-sponsor-logo-wrap{
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
  }

  .site-footer-sponsor-logo{
    max-width: 120px;
    max-height: 38px;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
  }

  .site-footer-sponsor-fallback{
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.12);
    color: #fff;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.4px;
  }

  .site-footer-sponsor-name{
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    line-height: 1.4;
  }

  .site-footer-sponsor-label{
    margin-top: 4px;
    font-size: 11px;
    color: rgba(255,255,255,0.65);
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }

  .site-footer-bottom{
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.08);
    text-align: center;
  }

  .site-footer-copy{
    color: rgba(255,255,255,0.76);
    font-size: 13px;
    line-height: 1.7;
    margin: 0;
    width: 100%;
    text-align: center;
  }

  @media (max-width: 1100px){
    .site-footer-top{
      grid-template-columns: 1fr 1fr;
    }

    .site-footer-sponsor-grid{
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }

  @media (max-width: 720px){
    .site-footer-inner{
      padding: 20px 16px 16px;
    }

    .site-footer-top{
      grid-template-columns: 1fr;
      gap: 22px;
    }

    .site-footer-sponsor-grid{
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .site-footer-sponsor-card{
      min-height: 96px;
      padding: 14px 10px;
    }

    .site-footer-brand-title{
      font-size: 20px;
    }
  }
</style>

<footer class="site-footer">
  <div class="site-footer-inner">
    <div class="site-footer-top">
      <div>
        <h2 class="site-footer-brand-title">Fantasy Premier League</h2>
        <p class="site-footer-brand-text">
          A modern football experience with teams, fixtures, results, standings, players, managers,
          sponsors, market values, and transfer updates in one place.
        </p>
      </div>

      <div>
        <div class="site-footer-heading">Quick Links</div>
        <div class="site-footer-links">
          <a href="{{ route('home') }}">Home</a>
          <a href="{{ route('teams.index') }}">Teams</a>
          <a href="{{ route('matches.index') }}">Match Fixtures</a>
          <a href="{{ route('results.index') }}">Results</a>
          <a href="{{ route('standings.index') }}">Standings</a>
        </div>
      </div>

      <div>
        <div class="site-footer-heading">Club Hub</div>
        <div class="site-footer-links">
          <a href="{{ route('players.index') }}">Players</a>
          <a href="{{ route('managers.index') }}">Managers</a>
          <a href="{{ route('market-values.index') }}">Market Value</a>
          <a href="{{ route('sponsors.index') }}">Sponsors</a>
          <a href="{{ route('transfers.index') }}">Transfers</a>
        </div>
      </div>

      <div>
        <div class="site-footer-heading">More</div>
        <div class="site-footer-links">
          <a href="{{ route('transfers.index') }}">Latest Transfers</a>
          <a href="{{ route('teams.index') }}">Browse Clubs</a>
        </div>
      </div>
    </div>

    <div class="site-footer-sponsors">
      <div class="site-footer-heading">Official Partners</div>

      <div class="site-footer-sponsor-grid">
        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/ea-sports.png') }}"
                 alt="EA Sports logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">EA</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Technology</div>
        </div>

        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/nike.png') }}"
                 alt="Nike logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">N</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Kit Partner</div>
        </div>

        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/oracle.png') }}"
                 alt="Oracle logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">OR</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Data Partner</div>
        </div>

        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/barclays.png') }}"
                 alt="Barclays logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">B</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Banking</div>
        </div>

        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/adobe.png') }}"
                 alt="Adobe logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">AD</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Creative</div>
        </div>

        <div class="site-footer-sponsor-card">
          <div class="site-footer-sponsor-logo-wrap">
            <img class="site-footer-sponsor-logo"
                 src="{{ asset('images/sponsors/omega.png') }}"
                 alt="Omega logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="site-footer-sponsor-fallback">OM</div>
          </div>
          <div class="site-footer-sponsor-name"></div>
          <div class="site-footer-sponsor-label">Timekeeper</div>
        </div>
      </div>
    </div>

    <div class="site-footer-bottom">
      <p class="site-footer-copy">© {{ now()->year }} Fantasy Premier League. All rights reserved.</p>
    </div>
  </div>
</footer>