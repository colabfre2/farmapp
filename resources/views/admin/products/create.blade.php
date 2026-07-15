@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Produk Baru</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            
            {{-- Card Informasi Dasar --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Tomat Organik Segar" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan keunggulan produk Anda di sini...">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Satuan Penjualan <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Harga & Stok --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Harga & Manajemen Stok</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                            
                            {{-- HAPUS <div class="input-group"> DAN <span>Rp</span> DI SINI --}}
                            <input type="text" inputmode="numeric" name="price" class="form-control input-rupiah @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0" required>
                            
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (Media & Status) --}}
        <div class="col-lg-4">
            
            {{-- Card Media --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Media Produk</h3>
                </div>
                <div class="card-body">
                    <div id="drop-zone" class="p-4 text-center rounded-3" style="border: 2px dashed #cbd5e1; cursor: pointer; background-color: #f8fafc; transition: all 0.3s ease;">
                        <input type="file" name="image" id="imageInput" class="d-none @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                        
                        <div id="drop-text">
                            <div style="font-size: 2.5rem; color: #94a3b8;">📸</div>
                            <h5 class="mt-2 text-dark fw-semibold">Upload Foto</h5>
                            <p class="text-muted small mb-0">Tarik & lepas file di sini<br>atau klik untuk menelusuri.</p>
                        </div>
                        
                        <img id="imagePreview" src="" class="img-fluid mx-auto" style="display:none; max-height: 200px; width: 100%; object-fit: contain; border-radius: 8px;">
                    </div>
                    @error('image')
                        <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Card Status --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Visibilitas</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 mb-3">
                        <div class="form-check form-switch mb-0 w-100 d-flex justify-content-between align-items-center">
                            <label class="form-check-label fw-semibold mb-0" for="isActive">Tampilkan di Marketplace</label>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" name="is_active" id="isActive" checked style="width: 2.5em; height: 1.25em;">
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Jika dinonaktifkan, pembeli tidak akan bisa melihat atau membeli produk ini.</p>
                </div>
            </div>

            {{-- Aksi Submit --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg fw-bold">Simpan Produk ✓</button>
            </div>

        </div>
    </div>
</form>

{{-- SCRIPT UNTUK DRAG AND DROP --}}
<script>
    const dropZone = document.getElementById('drop-zone');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const dropText = document.getElementById('drop-text');

    dropZone.addEventListener('click', () => imageInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#2d7a2d';
        dropZone.style.backgroundColor = '#f0fdf4';
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#cbd5e1';
        dropZone.style.backgroundColor = '#f8fafc';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#cbd5e1';
        dropZone.style.backgroundColor = '#f8fafc';
        
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            imageInput.files = e.dataTransfer.files;
            handlePreview(imageInput.files[0]);
        }
    });

    function previewImage(event) {
        if (event.target.files && event.target.files.length > 0) {
            handlePreview(event.target.files[0]);
        }
    }

    function handlePreview(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                dropText.style.display = 'none';
                imagePreview.style.display = 'block';
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection