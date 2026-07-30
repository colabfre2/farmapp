@extends('layouts.admin')

@section('title', 'Tambah Perawatan Tanaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">🧪 Tambah Perawatan Tanaman</h2>
    <a href="{{ route('admin.plant-cares.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.plant-cares.store') }}">
    @csrf
    
    <div class="row g-4">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Informasi Perawatan</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Perawatan / Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3 py-2 @error('name') is-invalid @enderror" placeholder="Contoh: Pupuk Kompos Organik" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jenis Perawatan <span class="text-danger">*</span></label>
                        <select name="type" class="form-select rounded-3 py-2 @error('type') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Pupuk" {{ old('type') == 'Pupuk' ? 'selected' : '' }}>🌱 Pupuk</option>
                            <option value="Penyiraman" {{ old('type') == 'Penyiraman' ? 'selected' : '' }}>💧 Penyiraman</option>
                            <option value="Pestisida" {{ old('type') == 'Pestisida' ? 'selected' : '' }}>🛡️ Pestisida</option>
                            <option value="Pemangkasan" {{ old('type') == 'Pemangkasan' ? 'selected' : '' }}>✂️ Pemangkasan</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>📌 Lainnya</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi & Catatan Penggunaan</label>
                        <textarea name="description" class="form-control rounded-3 py-2 @error('description') is-invalid @enderror" rows="4" placeholder="Tuliskan keterangan, dosis, atau aturan pakai di sini...">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Inventori & Harga) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Inventori & Biaya</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Satuan Ukur <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select rounded-3 py-2 @error('unit_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Stok Awal</label>
                        <input type="number" step="0.01" name="stock" class="form-control rounded-3 py-2 @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" placeholder="0">
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga per Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-muted rounded-start-3">Rp</span>
                            
                            {{-- Input Text untuk nampilin format titik (contoh: 1.000.000) --}}
                            <input type="text" id="price_display" class="form-control rounded-end-3 py-2 border-start-0 ps-0 @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', '') }}" placeholder="0">
                            
                            {{-- Input Hidden buat dikirim ke Laravel (angka bersih) --}}
                            <input type="hidden" name="price_per_unit" id="price_actual" value="{{ old('price_per_unit', 0) }}">
                        </div>
                        @error('price_per_unit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2 mt-4">
                <button type="submit" id="btnSubmit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm text-white">
                    Simpan Data ✓
                </button>
            </div>
        </div>
    </div>
</form>

{{-- SCRIPT FORMAT RUPIAH & ANTI DOUBLE SUBMIT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. FORMAT RUPIAH ---
        const priceDisplay = document.getElementById('price_display');
        const priceActual = document.getElementById('price_actual');

        // Fungsi format angka ke string rupiah
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa  = split[0].length % 3,
                rupiah  = split[0].substr(0, sisa),
                ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        // Format saat ada value bawaan (old value dari validasi error)
        if(priceDisplay.value) {
            priceDisplay.value = formatRupiah(priceDisplay.value);
        }

        // Event listener pas ngetik
        priceDisplay.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
            // Hapus titiknya buat dimasukin ke input hidden
            priceActual.value = this.value.replace(/\./g, '');
        });

        // --- 2. ANTI DOUBLE SUBMIT ---
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = 'Menyimpan... ⏳';
            btn.classList.add('disabled');
        });
    });
</script>
@endsection