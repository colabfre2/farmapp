<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - FarmApp</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- AOS CSS untuk Animasi Scroll --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        html, body { margin: 0 !important; padding: 0 !important; width: 100%; overflow-x: clip; font-family: 'Nunito', sans-serif; }
        .page, .page-wrapper { margin: 0 !important; padding: 0 !important; width: 100%; max-width: 100% !important; overflow-x: clip; }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .font-quicksand { font-family: 'Quicksand', sans-serif !important; }

        .hover-elevate { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }

        .badge-soft-success { background-color: #d1fae5; color: #065f46; padding: 8px 16px; border-radius: 20px; font-weight: 700; }

        .story-image-box {
            width: 100%;
            height: 340px;
            border-radius: 24px;
            background: radial-gradient(circle at top right, #eaf9e6 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 100px;
            box-shadow: 0 10px 30px rgba(45, 122, 45, 0.08);
        }

        .footer-dark { background-color: #1e293b; color: #f8fafc; padding: 3rem 0; width: 100%; }
        .footer-link { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
        .footer-link:hover { color: #10b981; }
        .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background-color: rgba(255,255,255,0.1); color: #fff; text-decoration: none; transition: 0.2s;}
        .social-icon:hover { background-color: #10b981; }
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
                <a href="/" class="navbar-brand navbar-brand-autodark text-decoration-none d-flex align-items-center">
                    <span class="avatar bg-success text-white me-2 rounded-3 shadow-sm">🌿</span>
                    <span class="fw-bolder fs-3 text-dark tracking-tight font-quicksand">FarmApp</span>
                </a>
                <div class="navbar-nav flex-row order-md-last gap-2">
                    <div class="d-none d-md-flex align-items-center">
                        @auth
                            <a href="{{ auth()->user()->role == 'admin' ? route('admin.dashboard') : '#' }}" class="btn btn-ghost-success fw-bold">Dashboard Saya</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost-secondary fw-bold">Masuk</a>
                            <a href="{{ route('register') }}" class="btn btn-success fw-bold shadow-sm rounded-pill px-4">Daftar Akun</a>
                        @endauth
                    </div>
                </div>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="navbar-nav mx-auto fw-bold gap-3">
                        <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('buyer.marketplace') }}">Katalog Produk</a></li>
                        <li class="nav-item active"><a class="nav-link text-success" href="{{ route('about') }}">Tentang Kebun Kami</a></li>
                    </ul>
                    <div class="d-md-none mt-3 pb-2 border-top pt-3">
                        <a href="{{ route('register') }}" class="btn btn-success w-100 rounded-pill mb-2 fw-bold">Daftar Akun</a>
                        <a href="{{ route('login') }}" class="btn btn-light w-100 rounded-pill fw-bold">Masuk</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrapper">

            {{-- HERO ABOUT --}}
            <div class="w-100" style="background: radial-gradient(circle at top right, #eaf9e6 0%, #f8fafc 100%); overflow:hidden;">
                <div class="container-xl py-6 py-lg-8 text-center">
                    <div class="max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
                        <span class="badge-soft-success d-inline-block mb-4">🌾 Kisah di Balik FarmApp</span>
                        <h1 class="display-4 fw-bold mb-4 font-quicksand" style="line-height: 1.25;">
                            Menghubungkan <span class="text-success">Petani</span> dengan Dunia
                        </h1>
                        <p class="text-muted fs-3 mb-0" style="line-height: 1.6;">
                            FarmApp lahir dari keinginan sederhana: membantu petani dan peternak lokal mengelola usaha mereka dengan lebih mudah, sekaligus menjangkau lebih banyak pembeli tanpa perantara yang rumit.
                        </p>
                    </div>
                </div>
            </div>

            {{-- CERITA KAMI --}}
            <div class="py-6 bg-white w-100">
                <div class="container-xl">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-5" data-aos="fade-right" data-aos-duration="1000">
                            <div class="story-image-box">🌾</div>
                        </div>
                        <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000">
                            <div class="text-uppercase text-success fw-bold tracking-wide mb-2">Cerita Kami</div>
                            <h2 class="display-6 fw-bold text-dark mb-4 font-quicksand">Dari Ladang, untuk Semua</h2>
                            <p class="text-muted fs-3 mb-3" style="line-height: 1.7;">
                                FarmApp dimulai dari pengamatan sederhana — banyak petani dan peternak di Indonesia kesulitan mengelola hasil panen, ternak, dan penjualan mereka secara terorganisir. Pencatatan manual sering membuat data tercecer, dan menjual hasil panen langsung ke pembeli masih jadi tantangan besar.
                            </p>
                            <p class="text-muted fs-3 mb-0" style="line-height: 1.7;">
                                Dari situ, kami membangun FarmApp — sebuah platform yang menggabungkan manajemen pertanian modern dengan marketplace digital. Petani bisa mencatat tanaman, ternak, keuangan, dan stok dalam satu aplikasi, sekaligus menjual produk mereka langsung ke pembeli.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NILAI KAMI --}}
            <div class="py-6 w-100" style="background-color: #f8fafc;">
                <div class="container-xl">
                    <div class="text-center mb-6 max-w-2xl mx-auto" data-aos="zoom-in">
                        <div class="text-uppercase text-success fw-bold tracking-wide mb-2">Prinsip Kami</div>
                        <h2 class="display-5 fw-bold text-dark mb-3 font-quicksand">Nilai yang Kami Pegang</h2>
                        <p class="text-muted fs-3">Fondasi yang membentuk setiap fitur dan keputusan dalam mengembangkan FarmApp.</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="card h-100 border-0 bg-white rounded-4 hover-elevate shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-light text-success shadow-sm rounded-circle mb-4 fs-1">🌱</div>
                                    <h3 class="fw-bold font-quicksand">Pertumbuhan Berkelanjutan</h3>
                                    <p class="text-muted mb-0">Kami percaya teknologi harus membantu petani tumbuh secara berkelanjutan, bukan menambah beban kerja mereka.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="card h-100 border-0 bg-white rounded-4 hover-elevate shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-light text-success shadow-sm rounded-circle mb-4 fs-1">🤝</div>
                                    <h3 class="fw-bold font-quicksand">Transparansi</h3>
                                    <p class="text-muted mb-0">Setiap transaksi dan data tercatat dengan jelas, memberikan kepercayaan antara petani dan pembeli.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="card h-100 border-0 bg-white rounded-4 hover-elevate shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="avatar avatar-xl bg-light text-success shadow-sm rounded-circle mb-4 fs-1">💚</div>
                                    <h3 class="fw-bold font-quicksand">Aksesibilitas</h3>
                                    <p class="text-muted mb-0">Platform yang mudah digunakan oleh siapa saja, dari petani kecil hingga peternak skala besar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MISI KAMI / STATS --}}
            <div class="bg-success text-white position-relative z-2 shadow-sm w-100">
                <div class="container-xl py-6">
                    <div class="text-center mb-5 max-w-2xl mx-auto" data-aos="zoom-in">
                        <h2 class="display-5 fw-bold mb-3 font-quicksand">Misi Kami</h2>
                        <p class="fs-3 text-white-50 mb-0" style="line-height: 1.7;">
                            Memberdayakan petani dan peternak Indonesia dengan teknologi yang sederhana namun powerful, sehingga mereka bisa fokus pada apa yang mereka lakukan terbaik — bertani dan beternak dengan baik.
                        </p>
                    </div>
                    <div class="row text-center pt-3">
                        <div class="col-md-4 mb-4 mb-md-0 border-end border-light border-opacity-25">
                            <div class="display-4 fw-bold mb-1 font-quicksand">2.400+</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Petani Terdaftar</p>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0 border-end border-light border-opacity-25">
                            <div class="display-4 fw-bold mb-1 font-quicksand">18.000+</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Produk Terdaftar</p>
                        </div>
                        <div class="col-md-4">
                            <div class="display-4 fw-bold mb-1 font-quicksand">52.000+</div>
                            <p class="fs-4 text-white-50 mb-0 fw-bold">Pesanan Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTTOM CTA --}}
            <div class="py-6 bg-white w-100">
                <div class="container-xl" data-aos="zoom-in" data-aos-duration="800">
                    <div class="bg-dark text-white text-center p-5 p-lg-6 rounded-4 shadow-lg position-relative overflow-hidden">
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                        <div class="position-relative z-1 max-w-3xl mx-auto py-3">
                            <h2 class="display-5 fw-bold mb-3 font-quicksand">Bergabunglah dengan Kami</h2>
                            <p class="text-white-50 fs-3 mb-5">Jadilah bagian dari komunitas petani yang mengelola usaha mereka dengan lebih cerdas.</p>
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                <a href="{{ route('register') }}" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">Mulai Sekarang</a>
                                <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold">Jelajahi Marketplace</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAILED FOOTER --}}
            <footer class="footer-dark w-100 mt-auto">
                <div class="container-xl" data-aos="fade-up" data-aos-duration="1000">
                    <div class="row g-5 mb-5">
                        <div class="col-12 col-lg-4">
                            <a href="/" class="d-inline-flex align-items-center mb-3 text-decoration-none">
                                <span class="avatar bg-success text-white me-2 rounded-3">🌿</span>
                                <span class="fw-bold fs-2 text-white tracking-tight font-quicksand">FarmApp</span>
                            </a>
                            <p class="text-slate-400 mb-4" style="line-height: 1.6;">
                                Menghadirkan kekayaan alam Nusantara langsung ke dapur Anda. Kami berkomitmen menyediakan sayuran, buah, dan hasil ternak segar berkualitas tinggi dengan harga yang jujur.
                            </p>
                            <div class="d-flex gap-2">
                                <a href="#" class="social-icon"><span>IG</span></a>
                                <a href="#" class="social-icon"><span>FB</span></a>
                                <a href="#" class="social-icon"><span>WA</span></a>
                            </div>
                        </div>

                        <div class="col-6 col-sm-6 col-lg-2 offset-lg-1">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Tautan Cepat</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><a href="/" class="footer-link">Beranda</a></li>
                                <li><a href="{{ route('buyer.marketplace') }}" class="footer-link">Katalog Produk</a></li>
                                <li><a href="{{ route('about') }}" class="footer-link">Tentang Kebun</a></li>
                                <li><a href="{{ route('login') }}" class="footer-link">Masuk Akun</a></li>
                            </ul>
                        </div>

                        <div class="col-6 col-sm-6 col-lg-2">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Layanan</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                <li><a href="#" class="footer-link">Cara Pemesanan</a></li>
                                <li><a href="#" class="footer-link">Info Pengiriman</a></li>
                                <li><a href="#" class="footer-link">Kebijakan Privasi</a></li>
                                <li><a href="#" class="footer-link">Syarat & Ketentuan</a></li>
                            </ul>
                        </div>

                        <div class="col-12 col-sm-12 col-lg-3">
                            <h4 class="fw-bold text-white mb-3 font-quicksand">Hubungi Kami</h4>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                <li class="d-flex align-items-start">
                                    <span class="text-success me-2 mt-1">📍</span>
                                    <span class="text-slate-400">Jl. Diklat Pemda, Curug,<br>Kab. Tangerang, Banten</span>
                                </li>
                                <li class="d-flex align-items-center">
                                    <span class="text-success me-2">📞</span>
                                    <span class="text-slate-400">+62 812-XXXX-XXXX</span>
                                </li>
                                <li class="d-flex align-items-center">
                                    <span class="text-success me-2">✉️</span>
                                    <span class="text-slate-400">halo@farmapp.com</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-top border-slate-700 pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <p class="text-slate-400 mb-0 small">&copy; 2026 FarmApp. Hasil Bumi Terbaik.</p>
                        <p class="text-slate-500 mb-0 small mt-2 mt-md-0">Dibuat dengan ❤️ untuk Pertanian Indonesia</p>
                    </div>
                </div>
            </footer>

        </div>
    </div>

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