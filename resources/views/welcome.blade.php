<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALMS - Hasil Tani Segar & Alami</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- AOS CSS untuk Animasi Scroll --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* CSS FIX UNTUK HILANGIN PUTIH DI PINGGIR LAYAR */
        html, body { margin: 0 !important; padding: 0 !important; width: 100%; overflow-x: clip; font-family: 'Nunito', sans-serif; }
        .page, .page-wrapper { margin: 0 !important; padding: 0 !important; width: 100%; max-width: 100% !important; overflow-x: clip; }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .font-quicksand { font-family: 'Quicksand', sans-serif !important; }

        .hover-elevate { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
        
        .hero-card { border-radius: 16px; border: 1px solid rgba(255,255,255,0.8); background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(45, 122, 45, 0.05); }
        .badge-soft-success { background-color: #d1fae5; color: #065f46; padding: 8px 16px; border-radius: 20px; font-weight: 700; }
        
        .product-card { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: all 0.3s ease; }
        .product-card:hover { box-shadow: 0 12px 25px rgba(0,0,0,0.1); transform: translateY(-4px); }
        
        /* Footer Full Width */
        .footer-dark { background-color: #1e293b; color: #f8fafc; padding: 3rem 0; width: 100%; }
        .footer-link { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
        .footer-link:hover { color: #10b981; }
        .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: #fff; text-decoration: none; transition: 0.2s;}
        .social-icon:hover { background-color: #10b981; transform: translateY(-2px); }
    </style>
</head>
<body class="layout-fluid text-gray-800">
    <div class="page">
        {{-- NAVBAR --}}
        <header class="navbar navbar-expand-md navbar-light bg-white d-print-none sticky-top shadow-sm w-100" style="border-bottom: none;">
            <div class="container-xl py-2">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                {{-- LOGO & BRAND ALMS --}}
                <a href="/" class="navbar-brand navbar-brand-autodark text-decoration-none d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="ALMS Logo" style="height: 42px; width: auto; object-fit: contain;">
                    <div class="d-flex flex-column">
                        <span class="fw-bolder fs-3 text-dark tracking-tight font-quicksand" style="line-height: 1.1;">ALMS</span>
                        <span class="text-muted" style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">Agriculture Livestock Management</span>
                    </div>
                </a>

                <div class="navbar-nav flex-row order-md-last gap-2">
                    <div class="d-none d-md-flex align-items-center">
                        @auth
                            <a href="{{ auth()->user()->role == 'admin' ? route('admin.dashboard') : route('buyer.home') }}" class="btn btn-ghost-success fw-bold">Dashboard Saya</a>
                        @else
                            {{-- TOMBOL MASUK (MODAL TRIGGER) --}}
                            <button type="button" class="btn btn-ghost-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Masuk
                            </button>
                            {{-- TOMBOL DAFTAR (MODAL TRIGGER) --}}
                            <button type="button" class="btn btn-success fw-bold shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#registerModal">
                                Daftar Akun
                            </button>
                        @endauth
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="navbar-nav mx-auto fw-bold gap-3">
                        <li class="nav-item active"><a class="nav-link text-success" href="/">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('buyer.marketplace') }}">Katalog Produk</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">Tentang Kebun Kami</a></li>
                    </ul>
                    <div class="d-md-none mt-3 pb-2 border-top pt-3">
                        <button type="button" class="btn btn-success w-100 rounded-pill mb-2 fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar Akun</button>
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk</button>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">
            {{-- HERO SECTION --}}
            <div class="w-100" style="background: radial-gradient(circle at top right, #eaf9e6 0%, #f8fafc 100%); overflow:hidden;">
                <div class="container-xl py-6 py-lg-8">
                    <div class="row align-items-center" style="min-height: 75vh;">
                        <div class="col-lg-6 pe-lg-5 mb-5 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                            <span class="badge-soft-success d-inline-block mb-4">✨ 100% Segar & Organik</span>
                            <h1 class="display-3 fw-bold mb-4 font-quicksand" style="line-height: 1.2;">
                                Hasil Tani Terbaik, <br><span class="text-success position-relative">Langsung dari Kebun <svg class="position-absolute w-100 text-success opacity-25" style="bottom:-10px; left:0; height:12px;" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00031 6.83785C47.8872 -0.803362 121.758 -1.78287 197.904 6.83785" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg></span>
                            </h1>
                            <p class="text-muted fs-3 mb-5" style="line-height: 1.6;">
                                Penuhi kebutuhan nutrisi harianmu dengan sayuran, buah, dan hasil peternakan segar yang kami rawat sepenuh hati setiap harinya.
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-3 mb-5">
                                <a href="{{ route('buyer.marketplace') }}" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                    🛒 Mulai Belanja
                                </a>
                                <a href="#tentang-kami" class="btn btn-white btn-lg border rounded-pill px-5 shadow-sm text-dark fw-bold hover-elevate">
                                    Kenali Kami
                                </a>
                            </div>
                            <div class="d-flex align-items-center mt-3">
                                <div class="text-warning fs-3 me-2">★★★★★</div>
                                <span class="text-muted"><strong class="text-dark">Ratusan pelanggan</strong> telah menikmati kesegaran produk kami.</span>
                            </div>
                        </div>
                        
                        {{-- HERO RIGHT WIDGETS (FOTO) --}}
                        <div class="col-lg-6 position-relative">
                            <div class="row g-3 position-relative z-1">
                                <div class="col-6 mt-lg-5" data-aos="fade-up" data-aos-delay="200">
                                    <div class="card text-center h-100 hero-card hover-elevate overflow-hidden border-0 p-1 bg-white shadow-sm">
                                        <img src="{{ asset('images/hero-sayur.jpg') }}" class="rounded-3 w-100" style="height: 140px; object-fit: cover;" alt="Sayur Segar">
                                        <div class="card-body p-3">
                                            <div class="h5 fw-bold text-dark mb-0 font-quicksand">Sayuran Segar</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6" data-aos="fade-up" data-aos-delay="400">
                                    <div class="card text-center h-100 hero-card hover-elevate overflow-hidden border-0 p-1 bg-white shadow-sm">
                                        <img src="{{ asset('images/hero-buah.jpg') }}" class="rounded-3 w-100" style="height: 140px; object-fit: cover;" alt="Buah-buahan">
                                        <div class="card-body p-3">
                                            <div class="h5 fw-bold text-dark mb-0 font-quicksand">Buah & Panen Lokal</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6" data-aos="fade-up" data-aos-delay="600">
                                    <div class="card text-center h-100 hero-card hover-elevate overflow-hidden border-0 p-1 bg-white shadow-sm">
                                        <img src="{{ asset('images/hero-ternak.jpg') }}" class="rounded-3 w-100" style="height: 140px; object-fit: cover;" alt="Hasil Ternak">
                                        <div class="card-body p-3">
                                            <div class="h5 fw-bold text-dark mb-0 font-quicksand">Hasil Peternakan</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-lg-n5" data-aos="fade-up" data-aos-delay="800">
                                    <div class="card text-center h-100 hero-card hover-elevate overflow-hidden border-0 p-1 bg-white shadow-sm">
                                        <img src="{{ asset('images/hero-packing.png') }}" class="rounded-3 w-100" style="height: 140px; object-fit: cover;" alt="Pengiriman">
                                        <div class="card-body p-3">
                                            <div class="h5 fw-bold text-success mb-0 font-quicksand">Pengiriman Cepat</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS STRIP --}}
            <div class="bg-success text-white position-relative z-2 shadow-sm w-100">
                <div class="container-xl">
                    <div class="row text-center py-5">
                        <div class="col-md-4 mb-4 mb-md-0 border-end border-light border-opacity-25">
                            <div class="display-4 fw-bold mb-1 font-quicksand">100%</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Alami & Tanpa Pengawet</p>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0 border-end border-light border-opacity-25">
                            <div class="display-4 fw-bold mb-1 font-quicksand">Setiap Hari</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Jadwal Panen Rutin</p>
                        </div>
                        <div class="col-md-4">
                            <div class="display-4 fw-bold mb-1 font-quicksand">24 Jam</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Layanan Pengiriman</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WHY CHOOSE US (TENTANG KAMI) --}}
            <div id="tentang-kami" class="py-6 bg-white w-100">
                <div class="container-xl">
                    <div class="text-center mb-6 max-w-2xl mx-auto" data-aos="zoom-in">
                        <div class="text-uppercase text-success fw-bold tracking-wide mb-2">Kenapa Memilih Kami?</div>
                        <h2 class="display-5 fw-bold text-dark mb-3 font-quicksand">Kualitas Terbaik di Setiap Gigitan</h2>
                        <p class="text-muted fs-3">Kami menjaga standar kebersihan dan nutrisi dari kebun hingga sampai ke meja makan Anda.</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                            <div class="card h-100 border-0 bg-light rounded-4 hover-elevate">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-white text-success shadow-sm rounded-circle mb-4 fs-1">🚜</div>
                                    <h3 class="fw-bold font-quicksand">Langsung dari Kebun</h3>
                                    <p class="text-muted">Tanpa perantara. Anda mendapatkan hasil panen segar langsung dari tangan kami.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                            <div class="card h-100 border-0 bg-light rounded-4 hover-elevate">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-white text-success shadow-sm rounded-circle mb-4 fs-1">🛡️</div>
                                    <h3 class="fw-bold font-quicksand">Kualitas Terjamin</h3>
                                    <p class="text-muted">Perawatan tanaman dan ternak dilakukan dengan standar tinggi demi hasil optimal.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                            <div class="card h-100 border-0 bg-light rounded-4 hover-elevate">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-white text-success shadow-sm rounded-circle mb-4 fs-1">💰</div>
                                    <h3 class="fw-bold font-quicksand">Harga Bersahabat</h3>
                                    <p class="text-muted">Dapatkan harga terbaik dan lebih murah dibandingkan pasar swalayan pada umumnya.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                            <div class="card h-100 border-0 bg-light rounded-4 hover-elevate">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-white text-success shadow-sm rounded-circle mb-4 fs-1">📦</div>
                                    <h3 class="fw-bold font-quicksand">Pengiriman Aman</h3>
                                    <p class="text-muted">Proses pengemasan yang higienis menjaga produk tetap utuh saat tiba di rumah Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KATALOG PRODUK --}}
            <div class="py-6 w-100" style="background-color: #f8fafc;">
                <div class="container-xl">
                    <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-right">
                        <div>
                            <div class="text-uppercase text-danger fw-bold tracking-wide mb-1">🔥 SEDANG PANEN</div>
                            <h2 class="display-6 fw-bold text-dark mb-0 font-quicksand">Tersedia Saat Ini</h2>
                            <p class="text-muted mt-2 mb-0 fs-4">Pesan sekarang sebelum kehabisan stok hari ini di ALMS.</p>
                        </div>
                        <div class="d-none d-sm-block">
                            <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-success rounded-pill fw-bold px-4">Lihat Etalase Lengkap →</a>
                        </div>
                    </div>

                    @php
                        $availableProducts = \App\Models\Product::with('category', 'unit')->where('is_active', true)->latest()->take(8)->get();
                    @endphp

                    <div class="row g-4">
                        @forelse($availableProducts as $product)
                        <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                            <a href="{{ route('buyer.marketplace') }}" class="card product-card h-100 text-decoration-none text-reset">
                                <div class="position-relative">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top bg-light w-100" style="height:200px; object-fit:contain; padding:1rem;" alt="{{ $product->name }}">
                                    @else
                                        <div class="card-img-top w-100 d-flex align-items-center justify-content-center bg-light" style="height:200px;font-size:48px;">🌿</div>
                                    @endif
                                </div>
                                <div class="card-body p-4">
                                    <div class="text-success small fw-bold mb-1">{{ $product->category->name ?? 'Produk Tani' }}</div>
                                    <h3 class="card-title text-dark fw-bold mb-3 fs-3 font-quicksand">{{ $product->name }}</h3>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <div class="text-muted small mb-1">Per {{ $product->unit->name ?? 'satuan' }}</div>
                                            <div class="fw-bold text-dark fs-3">{{ rupiah($product->price) }}</div>
                                        </div>
                                        <div class="text-muted small bg-light fw-bold px-2 py-1 rounded">Sisa {{ $product->stock }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted fs-3">Belum ada stok produk untuk saat ini. Silakan mampir kembali nanti!</p>
                        </div>
                        @endforelse
                    </div>
                    
                    <div class="text-center mt-5 d-sm-none">
                        <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-success w-100 rounded-pill fw-bold">Lihat Etalase Lengkap</a>
                    </div>
                </div>
            </div>

            {{-- BOTTOM CTA --}}
            <div class="py-6 bg-white w-100">
                <div class="container-xl" data-aos="zoom-in" data-aos-duration="800">
                    <div class="bg-dark text-white text-center p-5 p-lg-6 rounded-4 shadow-lg position-relative overflow-hidden">
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                        <div class="position-relative z-1 max-w-3xl mx-auto py-3">
                            <h2 class="display-5 fw-bold mb-3 font-quicksand">Mulai Belanja Kebutuhan Dapurmu?</h2>
                            <p class="text-white-50 fs-3 mb-5">Daftar sekarang untuk mempermudah proses pemesanan dan dapatkan info ketersediaan panen terbaru kami.</p>
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                <button type="button" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#registerModal">Buat Akun Pembeli</button>
                                <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold">Belanja Tanpa Akun</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAILED FOOTER (DINAMIS DARI PROFIL ADMIN) --}}
            @php
                $admin = \App\Models\User::where('role', 'admin')->first() ?? \App\Models\User::first();
            @endphp
            <footer class="footer-dark w-100 mt-auto">
                <div class="container-xl" data-aos="fade-up" data-aos-duration="1000">
                    <div class="row g-5 mb-5">
                        {{-- Kolom 1: Brand Info & Social Media Logos --}}
                        <div class="col-12 col-lg-4">
                            <a href="/" class="d-inline-flex align-items-center mb-3 text-decoration-none gap-2">
                                <img src="{{ asset('images/logo.png') }}" alt="ALMS Logo" style="height: 38px; width: auto; object-fit: contain;">
                                <span class="fw-bold fs-2 text-white tracking-tight font-quicksand">ALMS</span>
                            </a>
                            <p class="text-slate-400 mb-4" style="line-height: 1.6;">
                                Menghadirkan kekayaan alam Nusantara langsung ke dapur Anda. Kami berkomitmen menyediakan sayuran, buah, dan hasil ternak segar berkualitas tinggi dengan harga yang jujur.
                            </p>
                            {{-- LOGO SOSMED (IG, FB, WA) DINAMIS --}}
                            <div class="d-flex gap-2">
                                @if($admin && $admin->instagram)
                                    <a href="https://instagram.com/{{ ltrim($admin->instagram, '@') }}" target="_blank" class="social-icon" title="Instagram">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.198-.509.333-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.282.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.282.11-.705.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.668 2.668 0 1 1 0 5.335 2.668 2.668 0 0 1 0-5.335"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($admin && $admin->facebook)
                                    <a href="{{ $admin->facebook }}" target="_blank" class="social-icon" title="Facebook">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($admin && $admin->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $admin->whatsapp) }}" target="_blank" class="social-icon" title="WhatsApp">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.558 6.558 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.347.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Kolom 2: Tautan Cepat --}}
                        <div class="col-6 col-sm-6 col-lg-2 offset-lg-1">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Tautan Cepat</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><a href="/" class="footer-link">Beranda</a></li>
                                <li><a href="{{ route('buyer.marketplace') }}" class="footer-link">Katalog Produk</a></li>
                                <li><a href="#tentang-kami" class="footer-link">Tentang Kebun</a></li>
                                <li><a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk Akun</a></li>
                            </ul>
                        </div>
                        
                        {{-- Kolom 3: Layanan --}}
                        <div class="col-6 col-sm-6 col-lg-2">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Layanan</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><a href="#" class="footer-link">Cara Pemesanan</a></li>
                                <li><a href="#" class="footer-link">Info Pengiriman</a></li>
                                <li><a href="#" class="footer-link">Kebijakan Privasi</a></li>
                                <li><a href="#" class="footer-link">Syarat & Ketentuan</a></li>
                            </ul>
                        </div>
                        
                        {{-- Kolom 4: Kontak & Lokasi Dinamis dari Profil Admin --}}
                        <div class="col-12 col-sm-12 col-lg-3">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Hubungi Kami</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                <li class="d-flex align-items-start">
                                    <span class="text-success me-2 mt-1">📍</span>
                                    <span class="text-slate-400">
                                        {{ $admin->address ?? 'Jl. Diklat Pemda, Curug' }},<br>{{ $admin->city ?? 'Kab. Tangerang, Banten' }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center">
                                    <span class="text-success me-2">📞</span>
                                    <span class="text-slate-400">{{ $admin->whatsapp ?? ($admin->phone ?? '+62 812-XXXX-XXXX') }}</span>
                                </li>
                               <li class="d-flex align-items-center">
                                    <span class="text-success me-2">✉️</span>
                                    <span class="text-slate-400">{{ $admin->public_email ?? ($admin->email ?? 'halo@alms.com') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    {{-- Bottom Footer --}}
                    <div class="border-top border-slate-700 pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <p class="text-slate-400 mb-0 small">&copy; 2026 ALMS. Hasil Bumi Terbaik.</p>
                        <p class="text-slate-500 mb-0 small mt-2 mt-md-0">Dibuat dengan ❤️ untuk Pertanian Indonesia</p>
                    </div>
                </div>
            </footer>
            
        </div>
    </div>

    {{-- MODAL LOGIN POPUP --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold font-quicksand fs-4" id="loginModalLabel">Masuk ke ALMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="ALMS Logo" style="height: 44px; width: auto; object-fit: contain;" class="mb-2">
                        <p class="text-muted small">Kelola pertanian & peternakan jadi lebih mudah.</p>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Alamat Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success py-2 rounded-pill fw-bold shadow-sm">
                                Masuk Sekarang ✓
                            </button>
                        </div>
                        <div class="text-center text-muted small">
                            Belum punya akun? <a href="#" class="text-success fw-bold text-decoration-none" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL REGISTER POPUP --}}
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold font-quicksand fs-4" id="registerModalLabel">Buat Akun Pembeli</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="ALMS Logo" style="height: 40px; width: auto; object-fit: contain;" class="mb-1">
                        <p class="text-muted small">Daftar untuk mulai belanja hasil tani & ternak.</p>
                    </div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 py-2" value="{{ old('name') }}" placeholder="Nama Lengkap Kamu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Alamat Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success py-2 rounded-pill fw-bold shadow-sm">
                                Daftar Sekarang ✓
                            </button>
                        </div>
                        <div class="text-center text-muted small">
                            Sudah punya akun? <a href="#" class="text-success fw-bold text-decoration-none" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Masuk di sini</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- AOS Script Init --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({
                duration: 800, 
                once: true,    
                offset: 50,
            });

            setTimeout(function() {
                AOS.refresh();
            }, 300);
        });
    </script>
</body>
</html>