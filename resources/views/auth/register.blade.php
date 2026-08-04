<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>ALMS - Daftar Akun Baru</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/fav.ico') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8fafc; }
        h1, h2, h3, h4, h5, h6, .font-quicksand { font-family: 'Quicksand', sans-serif !important; }
        .card { border: none; border-radius: 16px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
        /* Tambahan CSS helper biar container register punya jarak aman atas bawah */
        .register-wrapper { padding-top: 4rem; padding-bottom: 4rem; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; }
    </style>
</head>
<body class="antialiased">
    <div class="page page-center">
        <div class="container container-tight register-wrapper" style="max-width: 440px;">
            
            {{-- LOGO DI LUAR CARD DENGAN JARAK AMAN --}}
            <div class="text-center mb-4">
                <a href="/" class="navbar-brand navbar-brand-autodark text-decoration-none d-inline-flex align-items-center justify-content-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="ALMS Logo" style="height: 52px; width: auto; object-fit: contain;">
                    <div class="text-start">
                        <span class="fw-bolder fs-2 text-dark tracking-tight font-quicksand d-block" style="line-height: 1.1;">ALMS</span>
                        <span class="text-muted" style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">Agriculture Livestock Management</span>
                    </div>
                </a>
            </div>

            {{-- REGISTER CARD UTAMA --}}
            <div class="card card-md p-4 bg-white">
                <div class="card-body">
                    
                    <h2 class="h3 text-center fw-bold text-dark mb-4 font-quicksand">Buat Akun Pembeli</h2>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        {{-- Nama Lengkap --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 py-2 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama Lengkap Kamu" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Alamat Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Password</label>
                            <input type="password" name="password" class="form-control rounded-3 py-2 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3 py-2" placeholder="••••••••" required>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="mb-3">
                            <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">
                                Daftar Sekarang ✓
                            </button>
                        </div>

                        {{-- Link Kembali ke Login / Home --}}
                        <div class="text-center text-muted mt-4 small">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Masuk di sini</a>
                            <span class="mx-1">|</span> 
                            <a href="/" class="text-secondary text-decoration-none">Beranda</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>