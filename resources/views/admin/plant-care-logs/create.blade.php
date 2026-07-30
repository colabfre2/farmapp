@extends('layouts.admin')

@section('title', 'Catat Perawatan Tanaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">📋 Catat Perawatan Tanaman</h2>
    <a href="{{ route('admin.plant-care-logs.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

@if($errors->any())
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold mb-4 rounded-3 d-flex align-items-center shadow-sm">
        <span class="fs-5 me-2">⚠️</span> Terdapat kesalahan input, mohon periksa kembali form di bawah.
    </div>
@endif

<form method="POST" action="{{ route('admin.plant-care-logs.store') }}">
    @csrf
    
    <div class="row g-4">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Detail Perawatan</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Lahan / Tanaman <span class="text-danger">*</span></label>
                        <select name="crop_id" class="form-select rounded-3 py-2 @error('crop_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tanaman yang Dirawat --</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                    🌱 {{ $crop->name }} (Status: {{ $crop->status }})
                                </option>
                            @endforeach
                        </select>
                        @error('crop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jenis Perawatan (Master) <span class="text-danger">*</span></label>
                        <select name="plant_care_id" class="form-select rounded-3 py-2 @error('plant_care_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Perawatan yang Diberikan --</option>
                            @foreach($plantCares as $plantCare)
                                <option value="{{ $plantCare->id }}" {{ old('plant_care_id') == $plantCare->id ? 'selected' : '' }}>
                                    🧪 {{ $plantCare->name }} ({{ $plantCare->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('plant_care_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Keterangan Eksekusi</label>
                        <textarea name="notes" class="form-control rounded-3 py-2 @error('notes') is-invalid @enderror" rows="4" placeholder="Contoh: Disiram merata ke seluruh bedengan, atau ada hama kutu daun ditemukan...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Waktu & Takaran) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Pelaksanaan</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tanggal Perawatan <span class="text-danger">*</span></label>
                        <input type="date" name="cared_at" class="form-control rounded-3 py-2 @error('cared_at') is-invalid @enderror" value="{{ old('cared_at', date('Y-m-d')) }}" required>
                        @error('cared_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah / Takaran (Opsional)</label>
                        <input type="number" step="0.01" name="amount" class="form-control rounded-3 py-2 @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Contoh: 1.5">
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        
                        <div class="mt-4 p-3 bg-white border rounded-3 border-info border-start border-4 shadow-sm">
                            <p class="text-muted small mb-0" style="line-height: 1.4;">
                                💡 <strong>Info:</strong> Kosongkan kolom <strong>Jumlah</strong> jika perawatan tidak memiliki takaran khusus (misalnya: Pemangkasan daun kering atau pengecekan rutin).
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2 mt-4">
                <button type="submit" id="btnSubmit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm text-white">
                    Simpan Catatan ✓
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