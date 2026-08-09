@extends('layouts.admin')

@section('title', 'Tambah Admin')

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
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 font-quicksand text-dark">➕ Tambah Admin Baru</h2>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">← Kembali</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-flat">
                <form method="POST" action="{{ route('admin.admins.store') }}">
                    @csrf

                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 px-md-5">
                        <p class="text-muted small mb-0">Akun ini bakal punya akses penuh ke dashboard admin FarmApp. Pastiin lu percaya sama orangnya ya 🙏</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-custom @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" name="phone" class="form-control form-control-custom @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="cth: 081234567890">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-custom @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text text-muted">Minimal 8 karakter.</div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control form-control-custom" required>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 px-md-5">
                        <button type="submit" class="btn btn-success rounded-pill py-2 w-100 shadow-sm fw-bold fs-6">
                            Simpan Admin ✓
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
