<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>FarmApp - Marketplace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .navbar-top {
            background-color: #0d1b2a !important;
        }
        .navbar-top .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 18px;
        }
        .navbar-top .nav-link {
            color: #a0a0a0 !important;
        }
        .navbar-top .nav-link:hover {
            color: #ffffff !important;
        }
        .navbar-top .nav-link.active {
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
    </style>
</head>
<body class="antialiased">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-top">
        <div class="container">
            <a href="{{ route('buyer.home') }}" class="navbar-brand">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#2d7a2d;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;">
                        🌿
                    </div>
                    FarmApp
                </div>
            </a>
            <div class="navbar-nav ms-auto d-flex align-items-center gap-3">
                
                <a href="{{ route('buyer.marketplace') }}" class="nav-link {{ request()->routeIs('buyer.marketplace*') ? 'active' : '' }}">
                    🛒 Marketplace
                </a>
                <a href="{{ route('buyer.cart') }}" class="nav-link {{ request()->routeIs('buyer.cart*') ? 'active' : '' }}">
                    🛍️ Cart
                    @php $cartCount = count(session()->get('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="badge bg-success">{{ $cartCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <a href="{{ route('profile.edit') }}">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            style="width:36px;height:36px;object-fit:cover;border-radius:50%;">
                    @else
                        <div class="navbar-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <div class="container py-4">
        @yield('content')
    </div>

</body>
</html>