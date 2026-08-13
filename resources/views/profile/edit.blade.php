@php
    $isAdmin = auth()->user()->role === 'admin';
    $layout  = $isAdmin ? 'layouts.admin' : 'layouts.buyer';
@endphp

@extends($layout)

@section('title', 'Profil Saya')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .form-control-custom {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s ease-in-out;
    }
    .form-control-custom:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .font-quicksand {
        font-family: 'Quicksand', sans-serif !important;
    }
    .avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }
    .avatar-preview-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #10b981;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
        transition: transform 0.2s;
    }
    .avatar-preview-img:hover {
        transform: scale(1.05);
    }
</style>

<div class="container-fluid py-4" style="max-width: 1100px;">
    
    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 d-flex align-items-center rounded-3 shadow-sm">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('status') === 'password-updated')
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 d-flex align-items-center rounded-3 shadow-sm">
            <span class="fs-5 me-2">🔐</span> Password berhasil diperbarui!
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: Edit Profil --}}
        <div class="col-lg-7">
            <div class="card card-flat h-100">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="d-flex flex-column h-100">
                    @csrf
                    @method('PUT')

                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 px-md-5">
                        <h4 class="card-title fw-bold font-quicksand text-dark mb-1">✏️ Informasi Profil</h4>
                        <p class="text-muted small mb-0">Kelola informasi data diri, identitas, dan akun lu.</p>
                    </div>

                    <div class="card-body p-4 p-md-5 flex-grow-1">
                        {{-- Avatar Section --}}
                        <div class="mb-4 text-center">
                            <div class="avatar-container mb-3">
                                @if(auth()->user()->avatar)
                                    <img src="{{ '/storage/' . auth()->user()->avatar }}" class="avatar-preview-img" id="avatarPreview" alt="Avatar">
                                @else
                                    <div id="avatarPreview" class="avatar-preview-img bg-success d-flex align-items-center justify-content-center text-white display-4 fw-bold font-quicksand">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="avatarInput" class="btn btn-sm btn-outline-success rounded-pill px-4 py-2 fw-bold shadow-sm" style="cursor: pointer;">
                                    📸 Ganti Foto Profil
                                </label>
                                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewAvatar(event)">
                                @error('avatar') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-custom @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-custom @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Field Nomor Telepon Atas Dihapus, Digantikan dengan input yang terintegrasi di bawah untuk Admin, atau single field untuk Buyer --}}
                            {{-- @if(!$isAdmin) --}}
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="tel" name="phone" class="form-control form-control-custom" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="cth: 081234567890">
                                </div>
                            {{-- @endif --}}

                            <div class="{{ $isAdmin ? 'col-12' : 'col-md-6' }} mb-2">
                                <label class="form-label">Kota Domisili</label>
                                <input type="text" name="city" class="form-control form-control-custom" value="{{ old('city', auth()->user()->city ?? '') }}" placeholder="cth: Tangerang Selatan">
                            </div>

                            <div class="col-12 mb-2">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="address" class="form-control form-control-custom" rows="2" placeholder="Masukkan alamat lengkap lu...">{{ old('address', auth()->user()->address ?? '') }}</textarea>
                            </div>

                            {{-- KONTEN KHUSUS ADMIN: Sosmed & Kontak Publik (Hanya muncul jika role admin) --}}
                            @if($isAdmin)
                                <div class="col-12 mt-3">
                                    <hr class="text-muted opacity-25 my-2">
                                    <h6 class="fw-bold font-quicksand text-success mb-2" style="font-size: 0.95rem;">🌐 Kontak Publik & Sosial Media (Tampil di Beranda)</h6>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Email Publik (Untuk Beranda)</label>
                                    <input type="email" name="public_email" class="form-control form-control-custom" value="{{ old('public_email', auth()->user()->public_email ?? '') }}" placeholder="cth: halo@farmapp.com">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="text" name="whatsapp" class="form-control form-control-custom" value="{{ old('whatsapp', auth()->user()->whatsapp ?? '') }}" placeholder="cth: 628123456789">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Akun Instagram</label>
                                    <input type="text" name="instagram" class="form-control form-control-custom" value="{{ old('instagram', auth()->user()->instagram ?? '') }}" placeholder="cth: @farmapp_official">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Akun Facebook</label>
                                    <input type="text" name="facebook" class="form-control form-control-custom" value="{{ old('facebook', auth()->user()->facebook ?? '') }}" placeholder="cth: FarmApp Indonesia">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 px-md-5">
                        <button type="submit" class="btn btn-success rounded-pill py-2 w-100 shadow-sm fw-bold fs-6">
                            Simpan Perubahan ✓
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: Ganti Password --}}
        <div class="col-lg-5">
            <div class="card card-flat h-100">
                <form method="POST" action="{{ route('profile.password') }}" class="d-flex flex-column h-100">
                    @csrf
                    @method('PUT')

                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 px-md-5">
                        <h4 class="card-title fw-bold font-quicksand text-dark mb-1">🔒 Keamanan Akun</h4>
                        <p class="text-muted small mb-0">Pastikan akun lu pake password yang kuat dan unik.</p>
                    </div>

                    <div class="card-body p-4 p-md-5 flex-grow-1">
                        <div class="mb-3">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control form-control-custom @error('current_password', 'updatePassword') is-invalid @enderror" placeholder="••••••••" required>
                            @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-custom @error('password', 'updatePassword') is-invalid @enderror" placeholder="••••••••" required>
                            @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 px-md-5">
                        <button type="submit" class="btn btn-warning rounded-pill py-2 w-100 shadow-sm text-dark fw-bold fs-6">
                            Perbarui Password 🔐
                        </button>
                    </div>
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