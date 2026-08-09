@extends('layouts.admin')

@section('title', 'Perbarui Produk')

@section('content')

<style>
    .upload-area {
        border: 2px dashed #cbd5e1;
        cursor: pointer;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden; 
        min-height: 240px;
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
        max-height: 220px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 8px;
        z-index: 2;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Perbarui Produk</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form id="productForm" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            {{-- Card Informasi Dasar --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Satuan Penjualan <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="text" inputmode="numeric" id="priceInput" class="form-control border-start-0 ps-0 @error('price') is-invalid @enderror" placeholder="0" required>
                                {{-- HIDDEN INPUT BUAT DIKIRIM KE DATABASE — SELALU INTEGER MURNI, TANPA DESIMAL --}}
                                <input type="hidden" name="price" id="priceActual" value="{{ (int) old('price', $product->price) }}">
                            </div>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stok Saat Ini</label>
                            <input type="number" class="form-control bg-light" value="{{ $product->stock }}" disabled>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle"></i> Stok tidak bisa diubah dari sini. Gunakan menu
                                <a href="{{ route('admin.stock.in.create') }}" class="text-decoration-none fw-semibold">Stok Masuk</a> atau
                                <a href="{{ route('admin.stock.out.create') }}" class="text-decoration-none fw-semibold">Stok Keluar</a>
                                untuk mencatat perubahan stok.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Card Media --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Media Produk</h3>
                </div>
                <div class="card-body">
                    {{-- Kalau ada gambar bawaan, otomatis tambahkan class 'has-image' --}}
                    <div id="drop-zone" class="upload-area p-3 text-center rounded-3 {{ $product->image ? 'has-image' : '' }}">
                        <input type="file" name="image" id="imageInput" class="d-none @error('image') is-invalid @enderror" accept="image/*">
                        
                        <div id="drop-text">
                            <div style="font-size: 2.5rem; color: #94a3b8;">📸</div>
                            <h5 class="mt-2 text-dark fw-semibold">Ubah Foto</h5>
                            <p class="text-muted small mb-0">Tarik & lepas file di sini<br>atau klik untuk menelusuri.</p>
                        </div>
                        
                        {{-- Set source gambar lama jika ada --}}
                        <img id="imagePreview" src="{{ $product->image ? '/storage/' . $product->image : '' }}" class="img-fluid mx-auto" alt="Preview">
                    </div>
                    @error('image') <div class="text-danger small mt-2 text-center">{{ $message }}</div> @enderror
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
                            <input class="form-check-input ms-0" type="checkbox" role="switch" name="is_active" id="isActive" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg fw-bold">Perbarui Produk ✓</button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. SCRIPT DRAG & DROP FOTO (Anti-Bocor) ---
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

        // --- 2. SCRIPT RUPIAH FORMATTER (Fixed: anti dobel-parse & anti desimal nyasar) ---
        const priceInput = document.getElementById('priceInput');
        const priceActual = document.getElementById('priceActual');

        // Fungsi format titik ribuan — HANYA menerima angka murni (integer), tidak pernah string berformat
        function formatRupiah(angkaMurni) {
            return new Intl.NumberFormat('id-ID').format(angkaMurni);
        }

        // Format saat halaman pertama dimuat.
        // priceActual.value SUDAH DIJAMIN integer murni oleh PHP (int) di atas — aman diformat langsung.
        if (priceActual.value) {
            priceInput.value = formatRupiah(parseInt(priceActual.value, 10));
        }

        // Format saat user mengetik.
        // Setiap kali user ngetik, kita SELALU parse ulang dari input yang terlihat (this.value),
        // bukan dari priceActual — supaya tidak pernah terjadi "format di atas format".
        priceInput.addEventListener('input', function(e) {
            const rawValue = this.value.replace(/[^0-9]/g, ''); // Buang semua selain digit 0-9 (termasuk titik/koma lama)
            priceActual.value = rawValue || '0';                // Simpan nilai murni sebagai integer string
            this.value = rawValue ? formatRupiah(parseInt(rawValue, 10)) : ''; // Tampilkan ulang hasil format bersih
        });

        // --- 3. SCRIPT ANTI DOUBLE SUBMIT ---
        document.getElementById('productForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = 'Memperbarui... ⏳';
            btn.classList.add('disabled');
        });
    });
</script>
@endsection