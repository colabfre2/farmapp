<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <title>FarmApp - Seller</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="wrapper">

        {{-- Sidebar --}}
        <aside class="navbar navbar-vertical navbar-expand-lg">
            <div class="container-fluid">
                <a href="#" class="navbar-brand">
                    <span>🌿 FarmApp</span>
                </a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('seller.dashboard') }}">
                                <span class="nav-link-icon">🏠</span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon">📦</span>
                                <span class="nav-link-title">Products</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon">🌱</span>
                                <span class="nav-link-title">Crops</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon">🐄</span>
                                <span class="nav-link-title">Livestock</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon">🌾</span>
                                <span class="nav-link-title">Harvests</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon">💰</span>
                                <span class="nav-link-title">Finance</span>
                            </a>
                        </li>
                    </ul>

                    {{-- Logout --}}
                    <div class="mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost-danger w-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Content --}}
        <div class="page-wrapper">
            <div class="page-header">
                <div class="container-xl">
                    <div class="page-pretitle">FarmApp</div>
                    <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                </div>
            </div>
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>
</body>

</html>
