@php
    $isAdmin = auth()->user()->role === 'admin';
    $layout  = $isAdmin ? 'layouts.admin' : 'layouts.buyer';
@endphp

@extends($layout)

@section('title', 'Profil Saya')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- Edit Profil --}}
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">✏️ Edit Profil</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Avatar --}}
                    <div class="mb-4 text-center">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 style="width:100px;height:100px;object-fit:cover;border-radius:50%;" id="avatarPreview">
                        @else
                            <div id="avatarPreview" style="width:100px;height:100px;background:#2d7a2d;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;color:white;margin:0 auto;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="mt-2">
                            <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm" accept="image/*" onchange="previewAvatar(event)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="cth: 081234567890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', auth()->user()->address) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kota</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', auth()->user()->city) }}">
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔒 Ganti Password</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Lama</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Ganti Password</button>
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
                img.style = 'width:100px;height:100px;object-fit:cover;border-radius:50%;';
                img.id = 'avatarPreview';
                preview.replaceWith(img);
            }
        }
        reader.readAsDataURL(file);
    }
}
</script>

@endsection