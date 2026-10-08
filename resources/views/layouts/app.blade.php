<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ATLAS IPS')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<header class="topbar">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="ATLAS IPS"><span class="brand-icon">🗺️</span><span>ATLAS IPS</span></a>
        <div class="nav-actions">
            <form class="nav-search" method="get" action="{{ route('search') }}">
                <input type="search" name="q" placeholder="Cari materi..." aria-label="Cari materi">
                <button aria-label="Cari">⌕</button>
            </form>
            <button class="nav-menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="Buka menu">☰</button>
            <nav id="site-navigation" class="main-nav">
                <a href="{{ route('home') }}">Beranda</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                        <form method="post" action="{{ route('logout') }}" class="inline-form">@csrf<button class="link-button">Keluar</button></form>
                    @endif
                @else
                    <a href="{{ route('login') }}">Admin</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
<main class="container main-content">
    @if(session('ok')) <div class="alert success">{{ session('ok') }}</div> @endif
    @if($errors->any()) <div class="alert error"><strong>Periksa data:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="footer-brand" href="{{ route('home') }}">🗺️ ATLAS IPS</a>
            <p>Media pembelajaran IPS yang mengumpulkan materi, video, dan referensi dalam satu tempat.</p>
        </div>
        <div>
            <strong>Navigasi</strong>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('search') }}">Cari Materi</a>
        </div>
        <div>
            <strong>Tentang</strong>
            <p>Proyek Pengabdian kepada Masyarakat · {{ date('Y') }}</p>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>ATLAS IPS</span>
        <span>Proyek Pengabdian kepada Masyarakat · {{ date('Y') }}</span>
    </div>
</footer>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
