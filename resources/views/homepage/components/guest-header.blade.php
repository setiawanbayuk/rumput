<!-- Guest Mobile First Navbar -->
<nav class="pkk-guest-nav fixed-top" aria-label="Navigasi utama SIM PKK">
    <div class="container-fluid pkk-guest-nav-inner">
        <a class="pkk-guest-brand" href="{{ route('beranda') }}" aria-label="SIM PKK Kota Kediri">
            <span class="pkk-guest-brand-logo">
                <img src="{{ asset('assets/img/logo/logo-pkk.png') }}" alt="PKK">
            </span>
            <span class="pkk-guest-brand-text">
                <strong>SIM PKK</strong>
                <small>Kota Kediri</small>
            </span>
        </a>

        <button class="pkk-guest-menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="pkkGuestMenu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="pkk-guest-menu" id="pkkGuestMenu">
            <div class="pkk-guest-menu-head d-lg-none">
                <div>
                    <strong>Menu</strong>
                    <small>Navigasi SIM PKK</small>
                </div>
                <button type="button" class="pkk-guest-menu-close" aria-label="Tutup menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="pkk-guest-menu-links">
                @foreach ($appNavigation as $item)
                    @if (Auth::check() && ($item->kondisi == 'auth' || $item->kondisi == 'general'))
                        @if ($item->route == 'logout')
                            <a href="{{ route($item->route) }}" class="pkk-guest-link pkk-guest-link-danger"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>{{ $item->title }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        @else
                            <a href="{{ route($item->route) }}" class="pkk-guest-link">
                                <i class="fas fa-chevron-right"></i>
                                <span>{{ $item->title }}</span>
                            </a>
                        @endif
                    @elseif (Auth::guest() && ($item->kondisi == 'guest' || $item->kondisi == 'general'))
                        <a href="{{ route($item->route) }}" class="pkk-guest-link {{ $item->route == 'login' ? 'pkk-guest-link-primary' : '' }}">
                            <i class="{{ $item->route == 'login' ? 'fas fa-lock' : 'fas fa-chevron-right' }}"></i>
                            <span>{{ $item->title }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</nav>
<div class="pkk-guest-menu-backdrop"></div>
