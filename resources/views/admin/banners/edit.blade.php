@extends('layouts.admin')
@section('title', 'Edit Banner')
@section('content')
<style>
    .upload-area {
        border: 2px dashed #cbd5e1;
        cursor: pointer;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #2d7a2d;
        background-color: #f0fdf4;
    }
    .upload-area.has-image #drop-text { display: none; }
    .upload-area.has-image #imagePreview { display: block; }
    #imagePreview {
        display: none;
        max-height: 180px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 8px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Edit Banner</h2>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Banner</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Banner <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link Tujuan (opsional)</label>
                        <input type="url" name="link_url" class="form-control @error('link_url') is-invalid @enderror" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://...">
                        @error('link_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $banner->order) }}" min="0">
                        @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">Tampilkan di beranda</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Gambar Banner</h3>
                </div>
                <div class="card-body">
                    <div id="drop-zone" class="upload-area p-3 text-center rounded-3 has-image">
                        <input type="file" name="image" id="imageInput" class="d-none @error('image') is-invalid @enderror" accept="image/*">
                        <div id="drop-text">
                            <div style="font-size: 2.5rem; color: #94a3b8;">🖼️</div>
                            <h5 class="mt-2 text-dark fw-semibold">Ubah Gambar</h5>
                            <p class="text-muted small mb-0">Tarik & lepas atau klik untuk mengganti.</p>
                        </div>
                        <img id="imagePreview" src="{{ asset('storage/'.$banner->image) }}" class="img-fluid mx-auto" alt="Preview">
                    </div>
                    @error('image') <div class="text-danger small mt-2 text-center">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg fw-bold">Perbarui Banner ✓</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropZone = document.getElementById('drop-zone');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        window.addEventListener(eventName, e => { e.preventDefault(); e.stopPropagation(); }, false);
    });
    ['dragenter', 'dragover'].forEach(eventName => dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false));
    ['dragleave', 'drop'].forEach(eventName => dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false));

    dropZone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files.length > 0) {
            imageInput.files = e.dataTransfer.files;
            handlePreview(e.dataTransfer.files[0]);
        }
    });

    dropZone.addEventListener('click', () => imageInput.click());
    imageInput.addEventListener('change', function() {
        if (this.files.length > 0) handlePreview(this.files[0]);
    });

    function handlePreview(file) {
        if (!file.type.startsWith('image/')) return alert('Harus berupa file gambar!');
        const reader = new FileReader();
        reader.onload = e => {
            imagePreview.src = e.target.result;
            dropZone.classList.add('has-image');
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection