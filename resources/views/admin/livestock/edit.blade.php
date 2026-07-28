@extends('layouts.admin')

@section('title', 'Perbarui Data Ternak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">🐄 Perbarui Data Ternak</h2>
    <a href="{{ route('admin.livestock.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.livestock.update', $livestock) }}">
    @csrf
    @method('PUT')
    
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
                        <input type="text" name="name" class="form-control rounded-3 py-2 @error('name') is-invalid @enderror" value="{{ old('name', $livestock->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Jenis Hewan <span class="text-danger">*</span></label>
                            <select name="livestock_type_id" id="livestockTypeSelect" class="form-select rounded-3 py-2 @error('livestock_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($livestockTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('livestock_type_id', $livestock->livestock_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Otomatis ikut jenis hewan dari kandang yang dipilih.</small>
                            @error('livestock_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Kandang <span class="text-danger">*</span></label>
                            <select name="kandang_id" id="kandangSelect" class="form-select rounded-3 py-2 @error('kandang_id') is-invalid @enderror" required>
                                <option value="" id="kandangPlaceholder">-- Pilih kandang --</option>
                                @foreach($kandangs as $kandang)
                                    <option value="{{ $kandang->id }}" data-type="{{ $kandang->livestock_type_id }}"
                                        {{ old('kandang_id', $livestock->kandang_id) == $kandang->id ? 'selected' : '' }}
                                        style="{{ old('livestock_type_id', $livestock->livestock_type_id) != $kandang->livestock_type_id ? 'display:none;' : '' }}">
                                        {{ $kandang->name }}
                                        @if($kandang->capacity)
                                            (maks. {{ $kandang->capacity }} ekor)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('kandang_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Tanggal Hewan Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="arrival_date" class="form-control rounded-3 py-2 @error('arrival_date') is-invalid @enderror" 
                                   value="{{ old('arrival_date', $livestock->arrival_date ? \Carbon\Carbon::parse($livestock->arrival_date)->format('Y-m-d') : '') }}" required>
                            @error('arrival_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Berat Rata-rata (Kg)</label>
                            <input type="number" step="0.01" name="avg_weight" class="form-control rounded-3 py-2 @error('avg_weight') is-invalid @enderror" value="{{ old('avg_weight', $livestock->avg_weight) }}">
                            @error('avg_weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status Kesehatan <span class="text-danger">*</span></label>
                        <select name="health_status" class="form-select rounded-3 py-2 @error('health_status') is-invalid @enderror" required>
                            <option value="Sehat" {{ old('health_status', $livestock->health_status) == 'Sehat' ? 'selected' : '' }}>✅ Sehat</option>
                            <option value="Pemantauan" {{ old('health_status', $livestock->health_status) == 'Pemantauan' ? 'selected' : '' }}>⚠️ Pemantauan</option>
                            <option value="Sakit" {{ old('health_status', $livestock->health_status) == 'Sakit' ? 'selected' : '' }}>🤒 Sakit</option>
                        </select>
                        @error('health_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control rounded-3 py-2 @error('notes') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan khusus untuk kandang ini jika ada...">{{ old('notes', $livestock->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Manajemen Stok Terkunci) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark">Manajemen Populasi</h5>
                </div>
                <div class="card-body p-4">
                    
                    {{-- INPUT STOK DIBUAT READONLY DAN DISABLED --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                            Jumlah Ternak (Ekor) 
                            <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem;">🔒 Terkunci</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control rounded-3 py-2 fw-bold text-dark" style="background-color: #e2e8f0; border: 1px solid #cbd5e1; cursor: not-allowed;" value="{{ $livestock->quantity }}" readonly disabled>
                        </div>
                        
                        {{-- Notifikasi SOP Mentor --}}
                        <div class="mt-4 p-3 bg-white border rounded-3 border-warning border-start border-4 shadow-sm">
                            <p class="text-muted small mb-0" style="line-height: 1.4;">
                                💡 <strong>Info SOP:</strong> Populasi ternak tidak dapat diedit secara manual. Penambahan atau pengurangan jumlah hewan harus melalui menu <strong>Inventori > Stok Masuk / Keluar</strong> untuk menjaga keakuratan histori data.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm text-dark">
                    Perbarui Data ✓
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const typeSelect = document.getElementById('livestockTypeSelect');
    const kandangSelect = document.getElementById('kandangSelect');
    const placeholder = document.getElementById('kandangPlaceholder');

    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        const options = kandangSelect.querySelectorAll('option[data-type]');

        placeholder.textContent = selectedType ? '-- Pilih kandang --' : '-- Pilih jenis hewan terlebih dahulu --';
        options.forEach(opt => {
            opt.style.display = (!selectedType || opt.dataset.type === selectedType) ? '' : 'none';
        });

        // Kalau kandang yang sedang dipilih tidak cocok lagi dengan jenis hewan baru, reset pilihan
        const currentSelected = kandangSelect.querySelector('option:checked');
        if (currentSelected && currentSelected.dataset.type && currentSelected.dataset.type !== selectedType) {
            kandangSelect.value = '';
        }
    });
});
</script>
@endsection