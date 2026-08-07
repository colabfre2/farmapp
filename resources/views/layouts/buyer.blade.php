<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>ALMS - Marketplace Buyer</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('images/fav.ico') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
            background-color: #f8f9fa;
            /* padding-top dihapus karena kita pakai sticky-top */
        }

        /* Navbar Sticky di atas (Lebih aman dari Fixed) */
        .navbar-full {
            background-color: #0d1b2a !important;
            width: 100% !important;
            position: sticky !important; /* Berubah jadi sticky */
            top: 0 !important;
            z-index: 1030 !important;
            border-radius: 0 !important;
        }
        .navbar-full .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 18px;
        }
        .navbar-full .nav-link {
            color: #a0a0a0 !important;
        }
        .navbar-full .nav-link:hover {
            color: #ffffff !important;
        }
        .navbar-full .nav-link.active {
            color: #4caf50 !important;
        }
        .product-card:hover {
            transform: translateY(-4px);
            transition: transform 0.2s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .product-card {
            transition: transform 0.2s ease;
        }
        .navbar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #2d7a2d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
                    <span style="letter-spacing: 1px; font-size: 1.15rem; font-weight: 700;">ALMS</span>
                    <span class="text-white-50 d-none d-md-block" style="font-size: 0.65rem; font-weight: 400;">Agriculture Livestock Management</span>
                </div>
            </a>

            {{-- Menu Kanan & Profile Dropdown (Kanan, Order 2 di HP, Order 3 di Desktop) --}}
            <div class="navbar-nav d-flex flex-row align-items-center gap-2 gap-md-3 order-2 order-lg-3">
                
                <a href="{{ route('buyer.marketplace') }}" class="nav-link px-2 {{ request()->routeIs('buyer.marketplace*') ? 'active' : '' }}" title="Marketplace">
                    <span class="fs-5">🛒</span> <span class="d-none d-md-inline ms-1">Marketplace</span>
                </a>
                
                <a href="{{ route('buyer.cart') }}" class="nav-link px-2 position-relative {{ request()->routeIs('buyer.cart*') ? 'active' : '' }}" title="Cart">
                    <span class="fs-5">🛍️</span> <span class="d-none d-md-inline ms-1">Cart</span>
                    @php $cartCount = count(session()->get('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="font-size: 0.65rem;">
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
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width:36px;height:36px;object-fit:cover;border-radius:50%; border: 2px solid #2d7a2d;">
                        @else
                            <div class="navbar-avatar shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 mt-2 position-absolute" aria-labelledby="dropdownBuyerUser" style="min-width: 220px; border-radius: 12px;">
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
                    <input type="text" name="search" class="form-control rounded-pill py-2 px-3 px-md-4 shadow-sm border-0 bg-white small w-100"
                        placeholder="Cari produk..." value="{{ request('search') ?? request('q') }}">
                    <button type="submit" class="btn btn-success rounded-pill px-3 px-md-4 shadow-sm fw-bold small">Cari</button>
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