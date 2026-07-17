<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <link rel="shortcut icon" href="{{ asset('images/fav.ico') }}" type="image/x-icon">
    <title>FarmApp - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* --- PERUBAHAN LOGO STICKY --- */
        .navbar-vertical .navbar-brand {
            color: #ffffff !important;
            padding: 1.5rem 1rem;
            position: sticky;
            top: 0;
            background-color: #0d1b2a;
            z-index: 1020;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* --- CUSTOM SCROLLBAR UNTUK SIDEBAR --- */
        .navbar-collapse {
            overflow-y: auto;
            max-height: calc(100vh - 75px);
        }
        .navbar-collapse::-webkit-scrollbar {
            width: 4px;
        }
        .navbar-collapse::-webkit-scrollbar-thumb {
            background-color: #1a2d42;
            border-radius: 10px;
        }
        .navbar-collapse::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Sidebar */
        .navbar-vertical {
            background-color: #0d1b2a !important;
            border-right: 1px solid #1a2d42 !important;
        }
        .navbar-vertical .nav-link {
            color: #a0a0a0 !important;
            border-radius: 8px;
            margin: 2px 8px;
        }
        .navbar-vertical .nav-link:hover {
            color: #ffffff !important;
            background-color: #1a2d42 !important;
        }
        .navbar-vertical .nav-link.active {
            color: #ffffff !important;
            background-color: #2d7a2d !important;
        }
        .navbar-vertical .nav-link.text-success {
            color: #4caf50 !important;
        }
        .badge.bg-success {
            background-color: #4caf50 !important;
        }

        /* Page background */
        .page-wrapper {
            background-color: #f4f6f8 !important;
        }

        /* Header avatar */
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
<body class="antialiased">
    <div class="wrapper">

        {{-- Sidebar --}}
        <aside class="navbar navbar-vertical navbar-expand-lg">
            <div class="container-fluid">
                <a href="{{ route('admin.dashboard') }}" class="navbar-brand text-decoration-none">
    <div class="d-flex align-items-center gap-2">
        
        {{-- Lingkaran Logo --}}
        <div class="d-flex justify-content-center align-items-center rounded-circle border border-2 shadow-sm" 
             style="width: 40px; height: 40px; background-color: #ffffff; border-color: rgba(255,255,255,0.5) !important;">
            
            {{-- Class d-block ini PENTING buat ngilangin spasi ghaib di bawah gambar --}}
            <img src="{{ asset('images/icon.svg') }}" alt="Logo" class="d-block" style="width: 30px; height: 30px; object-fit: contain;">
            
        </div>
        
        {{-- Teks Brand --}}
        <span class="text-white fw-bold font-quicksand" style="font-size: 19px; letter-spacing: 0.5px;">FarmApp</span>
        
    </div>
</a>

                <div class="collapse navbar-collapse show">
                    <ul class="navbar-nav pt-lg-3">

                        {{-- 1. DASHBOARD --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon">🏠</span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        {{-- --- PEMBATAS MODERN DI SINI --- --}}
                        <li class="nav-item mt-4 mb-2 px-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #4a637d;"></span>
                                <div class="flex-grow-1" style="height: 1px; background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);"></div>
                            </div>
                        </li>

                        {{-- 2. MASTER DATA --}}
                        @php 
                            $isMasterDataActive = request()->routeIs('admin.categories.*', 'admin.units.*', 'admin.crop-types.*', 'admin.livestock-types.*', 'admin.expense-categories.*', 'admin.income-sources.*', 'admin.medicines.*', 'admin.feeds.*', 'admin.plant-cares.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isMasterDataActive ? 'active' : '' }}" href="#master-data" data-bs-toggle="collapse" aria-expanded="{{ $isMasterDataActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon">🗂️</span>
                                <span class="nav-link-title">Master Data</span>
                                <span class="nav-link-icon ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-chevron" style="transition: transform 0.3s ease; {{ $isMasterDataActive ? 'transform: rotate(180deg)' : '' }}" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </span>
                            </a>
                            <div class="collapse {{ $isMasterDataActive ? 'show' : '' }}" id="master-data">
                                <ul class="navbar-nav ps-4">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.categories.index') }}"><span class="nav-link-title">📂 Kategori Produk</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.units.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.units.index') }}"><span class="nav-link-title">📏 Satuan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.crop-types.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.crop-types.index') }}"><span class="nav-link-title">🌱 Jenis Tanaman</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.livestock-types.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.livestock-types.index') }}"><span class="nav-link-title">🐄 Jenis Ternak</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.plant-cares.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.plant-cares.index') }}"><span class="nav-link-title">🧪 Master Perawatan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.medicines.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.medicines.index') }}"><span class="nav-link-title">💊 Obat Ternak</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.feeds.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.feeds.index') }}"><span class="nav-link-title">🌾 Pakan Ternak</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.expense-categories.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.expense-categories.index') }}"><span class="nav-link-title">💸 Kategori Pengeluaran</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.income-sources.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.income-sources.index') }}"><span class="nav-link-title">💸 Sumber Pemasukan</span></a></li>
                                </ul>
                            </div>
                        </li>

                        {{-- 3. PERTANIAN --}}
                        @php 
                            $isPertanianActive = request()->routeIs('admin.crops.*', 'admin.harvests.*', 'admin.plant-care-logs.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isPertanianActive ? 'active' : '' }}" href="#pertanian-menu" data-bs-toggle="collapse" aria-expanded="{{ $isPertanianActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon">🌱</span>
                                <span class="nav-link-title">Pertanian</span>
                                <span class="nav-link-icon ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-chevron" style="transition: transform 0.3s ease; {{ $isPertanianActive ? 'transform: rotate(180deg)' : '' }}" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </span>
                            </a>
                            <div class="collapse {{ $isPertanianActive ? 'show' : '' }}" id="pertanian-menu">
                                <ul class="navbar-nav ps-4">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.crops.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.crops.index') }}"><span class="nav-link-title">🌱 Tanaman Aktif</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.plant-care-logs.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.plant-care-logs.index') }}"><span class="nav-link-title">📋 Log Perawatan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.harvests.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.harvests.index') }}"><span class="nav-link-title">🌾 Data Panen</span></a></li>
                                </ul>
                            </div>
                        </li>

                        {{-- 4. PETERNAKAN --}}
                        @php 
                            $isPeternakanActive = request()->routeIs('admin.livestock.*', 'admin.medicine-logs.*', 'admin.feed-logs.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isPeternakanActive ? 'active' : '' }}" href="#peternakan-menu" data-bs-toggle="collapse" aria-expanded="{{ $isPeternakanActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon">🐄</span>
                                <span class="nav-link-title">Peternakan</span>
                                <span class="nav-link-icon ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-chevron" style="transition: transform 0.3s ease; {{ $isPeternakanActive ? 'transform: rotate(180deg)' : '' }}" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </span>
                            </a>
                            <div class="collapse {{ $isPeternakanActive ? 'show' : '' }}" id="peternakan-menu">
                                <ul class="navbar-nav ps-4">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.livestock.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.livestock.index') }}"><span class="nav-link-title">🐮 Ternak Aktif</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.medicine-logs.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.medicine-logs.index') }}"><span class="nav-link-title">📋 Log Obat</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.feed-logs.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.feed-logs.index') }}"><span class="nav-link-title">📋 Log Pakan</span></a></li>
                                </ul>
                            </div>
                        </li>

                        {{-- 5. PRODUK & STOK --}}
                        @php 
                            $isInventoryActive = request()->routeIs('admin.products.*', 'admin.stock.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isInventoryActive ? 'active' : '' }}" href="#inventory-menu" data-bs-toggle="collapse" aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon">📦</span>
                                <span class="nav-link-title">Inventori</span>
                                <span class="nav-link-icon ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-chevron" style="transition: transform 0.3s ease; {{ $isInventoryActive ? 'transform: rotate(180deg)' : '' }}" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </span>
                            </a>
                            <div class="collapse {{ $isInventoryActive ? 'show' : '' }}" id="inventory-menu">
                                <ul class="navbar-nav ps-4">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.products.index') }}"><span class="nav-link-title">🛍️ Produk Jualan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.stock.in.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.stock.in.index') }}"><span class="nav-link-title">⬆ Stok Masuk</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.stock.out.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.stock.out.index') }}"><span class="nav-link-title">⬇ Stok Keluar</span></a></li>
                                </ul>
                            </div>
                        </li>

                        {{-- 6. TRANSAKSI & KEUANGAN --}}
                        @php 
                            $isFinanceActive = request()->routeIs('admin.transactions.*', 'admin.finance.*');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $isFinanceActive ? 'active' : '' }}" href="#finance-menu" data-bs-toggle="collapse" aria-expanded="{{ $isFinanceActive ? 'true' : 'false' }}">
                                <span class="nav-link-icon">💰</span>
                                <span class="nav-link-title">Keuangan</span>
                                <span class="nav-link-icon ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-chevron" style="transition: transform 0.3s ease; {{ $isFinanceActive ? 'transform: rotate(180deg)' : '' }}" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 9 12 15 18 9" />
                                    </svg>
                                </span>
                            </a>
                            <div class="collapse {{ $isFinanceActive ? 'show' : '' }}" id="finance-menu">
                                <ul class="navbar-nav ps-4">
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.transactions.index') }}"><span class="nav-link-title">🧾 Transaksi / Pesanan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.finance.income.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.finance.income.index') }}"><span class="nav-link-title">💵 Pemasukan</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.finance.expense.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.finance.expense.index') }}"><span class="nav-link-title">💸 Pengeluaran</span></a></li>
                                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.finance.profit-loss') ? 'active fw-bold text-success' : '' }}" href="{{ route('admin.finance.profit-loss') }}"><span class="nav-link-title">📊 Laba Rugi</span></a></li>
                                </ul>
                            </div>
                        </li>

                    </ul>

                    {{-- Logout --}}
                    <div class="mt-auto pt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-ghost-danger w-100">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Content --}}
        <div class="page-wrapper">
            
            {{-- HEADER NAVBAR --}}
            <div class="page-header mt-3 mb-3">
                <div class="container-xl">
                    <div class="d-flex align-items-center justify-content-between">
                        
                        {{-- BAGIAN KIRI: Judul Halaman --}}
                        <div>
                            <div class="page-pretitle text-muted text-uppercase tracking-wide">FarmApp</div>
                            <h2 class="page-title fw-bold">@yield('title', 'Dashboard')</h2>
                        </div>
                        
                        {{-- BAGIAN KANAN: Jam, Notifikasi & Profil --}}
                        <div class="d-flex align-items-center gap-3">
                            
                            {{-- 1. Jam Live (Pakai border-end biar ada pemisah vertikal) --}}
                            <div class="text-end text-muted pe-3 border-end">
                                <div class="fw-bold" style="font-size: 0.85rem; color: #2d7a2d;">{{ \Carbon\Carbon::now()->format('d M Y') }}</div>
                                <div id="live-clock" style="font-size: 0.8rem; font-family: monospace;">--:--:--</div>
                            </div>
                            
                            {{-- 2. Notifikasi --}}
                            <div class="dropdown">
                                <a href="#" class="position-relative text-decoration-none" data-bs-toggle="dropdown" style="font-size: 1.2rem;">
                                    🔔
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="badge bg-danger position-absolute text-white" style="top:-5px; right:-8px; font-size:9px; padding: 3px 5px;">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm" style="width:320px; border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                        <span class="fw-bold">Notifikasi</span>
                                        <div class="d-flex gap-2">
                                            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-secondary border-0" style="font-size:11px;">Tandai dibaca</button>
                                            </form>
                                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-xs btn-outline-primary" style="font-size:11px;">Semua</a>
                                        </div>
                                    </div>
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                        <div class="dropdown-item py-2 {{ $notification->read_at ? '' : 'bg-light' }}" style="white-space:normal;">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold small">{{ $notification->data['title'] }}</span>
                                                <span class="text-muted" style="font-size:10px;">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-muted small">{{ $notification->data['message'] }}</div>
                                            @if(isset($notification->data['order_id']))
                                                <a href="{{ route('admin.transactions.show', $notification->data['order_id']) }}" class="btn btn-xs btn-success mt-1" style="font-size:10px;">Lihat Pesanan</a>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="dropdown-item text-center text-muted py-3">Tidak ada notifikasi</div>
                                    @endforelse
                                </div>
                            </div>
                            
                            {{-- 3. Profil --}}
                            <div class="d-flex align-items-center gap-2 ps-2">
                                <div class="text-end d-none d-md-block">
                                    <div class="fw-bold small mb-0" style="line-height: 1;">{{ auth()->user()->name }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Admin</small>
                                </div>
                                <a href="{{ route('profile.edit') }}">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width:38px;height:38px;object-fit:cover;border-radius:50%; border: 2px solid #2d7a2d;">
                                    @else
                                        <div class="navbar-avatar shadow-sm">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>
        </div>

    </div>

    {{-- ELEMEN PENAMPUNG DATA SESSION UNTUK SWEETALERT (Wajib ada) --}}
    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}" style="display: none;"></div>

    <script src="{{ asset('js/currentformat.js') }}"></script>
    <script>
        // Script untuk animasi chevron / panah pada menu dropdown sidebar
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(el) {
            var target = document.querySelector(el.getAttribute('href'));
            // Mencari element chevron tanpa mempedulikan nama class spesifiknya (disamakan jadi .icon-chevron)
            var icon = el.querySelector('.icon-chevron');
            if (!target || !icon) return;

            target.addEventListener('show.bs.collapse', function() {
                icon.style.transform = 'rotate(180deg)';
            });
            target.addEventListener('hide.bs.collapse', function() {
                icon.style.transform = 'rotate(0deg)';
            });
        });
        
        // Inisialisasi waktu dari server via Carbon
        let serverTime = new Date("{{ \Carbon\Carbon::now()->toIso8601String() }}");

        function updateClock() {
            // Tambahkan 1 detik setiap kali fungsi dipanggil
            serverTime.setSeconds(serverTime.getSeconds() + 1);

            const hours = String(serverTime.getHours()).padStart(2, '0');
            const minutes = String(serverTime.getMinutes()).padStart(2, '0');
            const seconds = String(serverTime.getSeconds()).padStart(2, '0');
            
            document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
        
    </script>
</body>
</html>