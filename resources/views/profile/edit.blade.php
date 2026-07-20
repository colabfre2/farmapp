@php
    $isAdmin = auth()->user()->role === 'admin';
    $layout  = $isAdmin ? 'layouts.admin' : 'layouts.buyer';
@endphp

@extends($layout)

@section('title', 'Profil Saya')

@section('content')
<style>
    /* Styling Card Modern Flat */
    .card-flat {
        border: none !important;
        border-radius: 16px !important;
        background: #ffffff;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .btn-modern {
        border-radius: 10px;
        font-weight: 700;
        padding: 0.7rem 1.8rem;
        transition: all 0.2s;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
    }
    .font-quicksand {
        font-family: 'Quicksand', sans-serif !important;
    }
    .avatar-container {
        position: relative;
        width: 110px;
        height: 110px;
        margin: 0 auto;
    }
    .avatar-preview-img {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
</style>

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 d-flex align-items-center rounded-3">
        <span class="fs-5 me-2">✅</span> {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    {{-- KOLOM KIRI: Edit Profil --}}
    <div class="col-lg-7">
        <div class="card card-flat h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="card-title fw-bold font-quicksand text-dark mb-1">✏️ Informasi Profil</h3>
                <p class="text-muted small mb-0">Kelola informasi data diri dan identitas akun Anda.</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Avatar Section --}}
                    <div class="mb-4 text-center">
                        <div class="avatar-container mb-3">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="avatar-preview-img" id="avatarPreview" alt="Avatar">
                            @else
                                <div id="avatarPreview" class="avatar-preview-img bg-success d-flex align-items-center justify-content-center text-white fs-2 fw-bold font-quicksand">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="avatarInput" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" style="cursor: pointer;">
                                📁 Ganti Foto Profil
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewAvatar(event)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="cth: 081234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kota Domisili</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', auth()->user()->city) }}" placeholder="cth: Jakarta">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Masukkan alamat lengkap Anda...">{{ old('address', auth()->user()->address) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-modern w-100 shadow-sm">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Ganti Password --}}
    <div class="col-lg-5">
        <div class="card card-flat h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="card-title fw-bold font-quicksand text-dark mb-1">🔒 Keamanan Akun</h3>
                <p class="text-muted small mb-0">Pastikan akun Anda menggunakan password yang aman.</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-warning btn-modern w-100 shadow-sm text-dark fw-bold">
                        Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-preview-img';
                img.id = 'avatarPreview';
                preview.replaceWith(img);
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection