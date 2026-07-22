@extends('layouts.admin')

@section('title', 'Tambah Ternak Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">🐄 Tambah Data Ternak</h2>
    <a href="{{ route('admin.livestock.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.livestock.store') }}">
    @csrf
    
    <div class="row g-4">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Informasi Kandang / Ternak</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nama Kandang / Kelompok <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3 py-2 @error('name') is-invalid @enderror" placeholder="Contoh: Kandang Ayam Sektor A" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Jenis Ternak <span class="text-danger">*</span></label>
                            <select name="livestock_type_id" class="form-select rounded-3 py-2 @error('livestock_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($livestockTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('livestock_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('livestock_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Tanggal Hewan Masuk <span class="text-danger">*</span></label>
                            {{-- Otomatis terisi tanggal hari ini sebagai default --}}
                            <input type="date" name="arrival_date" class="form-control rounded-3 py-2 @error('arrival_date') is-invalid @enderror" value="{{ old('arrival_date', date('Y-m-d')) }}" required>
                            @error('arrival_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Berat Rata-rata Awal (Kg)</label>
                            <input type="number" step="0.01" name="avg_weight" class="form-control rounded-3 py-2 @error('avg_weight') is-invalid @enderror" placeholder="Contoh: 2.5" value="{{ old('avg_weight') }}">
                            @error('avg_weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Status Kesehatan <span class="text-danger">*</span></label>
                            <select name="health_status" class="form-select rounded-3 py-2 @error('health_status') is-invalid @enderror" required>
                                <option value="Sehat" {{ old('health_status') == 'Sehat' ? 'selected' : '' }}>✅ Sehat</option>
                                <option value="Pemantauan" {{ old('health_status') == 'Pemantauan' ? 'selected' : '' }}>⚠️ Pemantauan</option>
                                <option value="Sakit" {{ old('health_status') == 'Sakit' ? 'selected' : '' }}>🤒 Sakit</option>
                            </select>
                            @error('health_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control rounded-3 py-2 @error('notes') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan khusus untuk kandang ini jika ada...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Manajemen Stok Awal) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Populasi Awal</h5>
                </div>
                <div class="card-body p-4">
                    
                    {{-- INPUT STOK AWAL BISA DIEDIT KARENA CREATE --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            Jumlah Ternak (Ekor) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="quantity" class="form-control rounded-3 py-2 fw-bold text-primary border-primary @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" min="0" placeholder="0" required>
                        </div>
                        @error('quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        
                        {{-- Notifikasi SOP Mentor --}}
                        <div class="mt-4 p-3 bg-white border rounded-3 border-info border-start border-4 shadow-sm">
                            <p class="text-muted small mb-0" style="line-height: 1.4;">
                                💡 <strong>Penting:</strong> Masukkan jumlah populasi <strong>awal</strong> saat hewan baru tiba. Setelah data ini disimpan, penambahan atau pengurangan jumlah hewan hanya bisa dilakukan melalui menu <strong>Inventori > Stok Masuk / Keluar</strong>.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2 mt-4">
                <button type="submit" id="btnSubmit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm text-white">
                    Simpan Data Ternak ✓
                </button>
            </div>
        </div>
    </div>
</form>

{{-- SCRIPT ANTI DOUBLE SUBMIT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = 'Menyimpan... ⏳';
            btn.classList.add('disabled');
        });
    });
</script>
@endsection