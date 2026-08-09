<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>ALMS - Marketplace Buyer</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/fav.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --color-forest-deep: #43695a;
            --color-forest: #5c8570;
            --color-forest-soft: #eef2ee;
            --color-earth: #c1946a;
            --color-earth-deep: #a97a4f;
            --color-cream: #faf8f4;
            --color-ink: #26332c;
        }

        /* Hilangkan panah dropdown bawaan bootstrap jika ada */
        .dropdown-toggle::after {
            display: none !important;
        }

        /* Reset total margin & padding body */
        html, body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: hidden;
            background-color: var(--color-cream);
            font-family: var(--font-body);
            color: var(--color-ink);
        }

        /* ── Navbar ──────────────────────────────────────── */
        .navbar-full {
            background: linear-gradient(120deg, var(--color-forest-deep) 0%, var(--color-forest) 55%, #8ca997 100%) !important;
            width: 100% !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1030 !important;
            border-radius: 0 !important;
        }
        .navbar-full .navbar-brand {
            color: #ffffff !important;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.01em;
        }
        .navbar-full .navbar-brand span:first-child {
            font-size: 1.15rem;
        }
        .navbar-full .navbar-brand .text-white-50 {
            color: rgba(255,255,255,0.55) !important;
        }
        .navbar-full .nav-link {
            color: rgba(255,255,255,0.75) !important;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 999px;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .navbar-full .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255,255,255,0.08);
        }
        .navbar-full .nav-link.active {
            color: #ffffff !important;
            background-color: var(--color-earth-deep) !important;
            font-weight: 700;
        }

        /* ── Badge angka di ikon Cart ─────────────────────── */
        .cart-count-badge {
            background-color: #ffffff !important;
            color: var(--color-forest-deep) !important;
            border: 2px solid var(--color-forest-deep);
            font-size: 0.65rem !important;
            font-weight: 800;
            min-width: 20px;
            height: 20px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 0 4px !important;
            line-height: 1;
        }

        /* ── Search bar ──────────────────────────────────── */
        .navbar-search-input {
            border: 1px solid transparent !important;
            transition: box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .navbar-search-input:focus {
            outline: none;
            border-color: var(--color-earth) !important;
            box-shadow: 0 0 0 0.2rem rgba(201, 138, 60, 0.25) !important;
        }
        .navbar-search-btn {
            background-color: var(--color-earth) !important;
            border-color: var(--color-earth) !important;
            color: #fff !important;
        }
        .navbar-search-btn:hover {
            background-color: var(--color-earth-deep) !important;
            border-color: var(--color-earth-deep) !important;
        }

        /* ── Avatar & cart badge ─────────────────────────── */
        .navbar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-forest) 0%, #3a7a58 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid rgba(201, 138, 60, 0.55);
        }

        /* ── Dropdown profil ─────────────────────────────── */
        .dropdown-menu {
            border-radius: 14px !important;
        }
        .dropdown-item {
            border-radius: 8px;
            margin: 0 4px;
            width: calc(100% - 8px) !important;
        }
        .dropdown-item:hover {
            background-color: var(--color-forest-soft) !important;
        }
        .dropdown-item.text-danger:hover {
            background-color: #fbeae9 !important;
        }

        /* ── Kartu produk (global, dipakai lintas halaman) ── */
        .product-card:hover {
            transform: translateY(-4px);
            transition: transform 0.2s ease;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
        }
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        }
    </style>
</head>
<body class="antialiased m-0 p-0">

    {{-- NAVBAR FULL-WIDTH --}}
    <nav class="navbar navbar-expand-lg navbar-full shadow-sm border-0 px-3 px-md-4 py-2 py-md-3">
        {{-- Tambahan flex-wrap agar elemen bisa turun ke baris baru saat di HP --}}
        <div class="container-fluid px-0 px-md-3 align-items-center flex-wrap">

            {{-- Brand & Logo (Kiri, Order 1) --}}
            <a href="{{ route('buyer.home') }}" class="navbar-brand text-decoration-none d-flex align-items-center gap-2 me-auto order-1">
                <img src="{{ asset('images/logo.png') }}" alt="FarmApp" style="height: 36px; width: auto; object-fit: contain;">
                <div class="d-flex flex-column lh-sm">
                    <span style="letter-spacing: 1px;">ALMS</span>
                    <span class="text-white-50 d-none d-md-block" style="font-size: 0.65rem; font-weight: 500;">Agriculture Livestock Management</span>
                </div>
            </a>

            {{-- Menu Kanan & Profile Dropdown (Kanan, Order 2 di HP, Order 3 di Desktop) --}}
            <div class="navbar-nav d-flex flex-row align-items-center gap-2 gap-md-3 order-2 order-lg-3">

                <a href="{{ route('buyer.marketplace') }}" class="nav-link px-2 px-md-3 {{ request()->routeIs('buyer.marketplace*') ? 'active' : '' }}" title="Marketplace">
                    <span class="fs-5">🛒</span> <span class="d-none d-md-inline ms-1">Marketplace</span>
                </a>

                <a href="{{ route('buyer.cart') }}" class="nav-link px-2 px-md-3 position-relative {{ request()->routeIs('buyer.cart*') ? 'active' : '' }}" title="Cart">
                    <span class="fs-5">🛍️</span> <span class="d-none d-md-inline ms-1">Cart</span>
                    @php $cartCount = count(session()->get('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle cart-count-badge">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- PROFILE DROPDOWN UNTUK BUYER --}}
                <div class="dropdown ps-1 ps-md-2">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" id="dropdownBuyerUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-bold small mb-0 text-white" style="line-height: 1.2;">{{ auth()->user()->name }}</div>
                        </div>
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width:38px;height:38px;object-fit:cover;border-radius:50%; border: 2px solid rgba(201, 138, 60, 0.55);">
                        @else
                            <div class="navbar-avatar shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 mt-2 position-absolute" aria-labelledby="dropdownBuyerUser" style="min-width: 220px;">
                        <li>
                            <div class="px-3 py-1 mb-1">
                                <span class="text-muted d-block" style="font-size: 0.70rem;">Masuk sebagai</span>
                                <strong class="text-dark text-truncate d-block" style="font-size: 0.80rem;">{{ auth()->user()->email }}</strong>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fw-semibold text-dark" href="{{ route('buyer.orders') }}">
                                📦 <span class="flex-grow-1">Pesanan Saya</span>
                                <span class="badge bg-success-subtle text-success rounded-pill small">Cek Paket</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 fw-semibold text-dark" href="{{ route('buyer.addresses.index') }}">
                                📍 <span class="flex-grow-1">Alamat Saya</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                👤 Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2 w-100 bg-transparent border-0">
                                    🚪 Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- SEARCH BAR (Order 3 di HP alias Paling Bawah, Order 2 di Desktop alias di Tengah) --}}
            <div class="w-100 order-3 order-lg-2 mx-auto mt-3 mt-lg-0 flex-grow-1" style="max-width: 480px;">
                <form action="{{ route('buyer.marketplace') }}" method="GET" class="d-flex gap-2 m-0">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" class="form-control navbar-search-input rounded-pill py-2 px-3 px-md-4 shadow-sm bg-white small w-100"
                        placeholder="Cari produk..." value="{{ request('search') ?? request('q') }}">
                    <button type="submit" class="btn navbar-search-btn rounded-pill px-3 px-md-4 shadow-sm fw-bold small">Cari</button>
                </form>
            </div>

        </div>
    </nav>

    {{-- KONTEN UTAMA --}}
    <div class="container-fluid px-3 py-4">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>