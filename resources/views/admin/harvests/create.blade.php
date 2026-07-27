@extends('layouts.admin')

@section('title', 'Catat Panen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Catat Panen Baru</h2>
    <a href="{{ route('admin.harvests.index') }}" class="btn btn-outline-secondary rounded-pill px-4">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.harvests.store') }}">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI --}}
        <div class="col-lg-8">
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Detail Panen</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Tanaman <span class="text-danger">*</span></label>
                        <select name="crop_id" class="form-select rounded-3 py-2 @error('crop_id') is-invalid @enderror" required>
                            <option value="">-- Pilih tanaman yang dipanen --</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->name }} 
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Hanya tanaman yang belum berstatus "Dipanen" yang muncul di sini.</small>
                        @error('crop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Panen <span class="text-danger">*</span></label>
                        <input type="date" name="harvested_at" class="form-control rounded-3 py-2 @error('harvested_at') is-invalid @enderror" value="{{ old('harvested_at', date('Y-m-d')) }}" required>
                        @error('harvested_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="quantity" class="form-control rounded-3 py-2 @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select rounded-3 py-2 @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-4">
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Finansial & Info</h3>
                </div>
                <div class="card-body">
                    {{-- Form Dual-Input Rupiah --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga Jual (per satuan) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3 border-end-0 bg-light">Rp</span>
                            <input type="text" id="display_selling_price" class="form-control rounded-end-3 py-2 border-start-0 @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" placeholder="0" required>
                        </div>
                        <!-- Input Hidden untuk dilempar ke database -->
                        <input type="hidden" name="selling_price" id="hidden_selling_price" value="{{ old('selling_price') }}">
                        @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control rounded-3 py-2" rows="3" placeholder="Opsional...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" id="btnSubmitHarvest" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">Simpan Data Panen ✓</button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- SCRIPT DUAL-INPUT FORMAT RUPIAH ---
    const displayInput = document.getElementById('display_selling_price');
    const hiddenInput = document.getElementById('hidden_selling_price');

    // Fungsi format angka ke Rupiah
    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split    = number_string.split(','),
            sisa     = split[0].length % 3,
            rupiah   = split[0].substr(0, sisa),
            ribuan   = split[0].substr(sisa).match(/\d{3}/gi);
            
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    // Format nilai awal jika ada (old input)
    if(hiddenInput.value) {
        displayInput.value = formatRupiah(hiddenInput.value);
    }

    displayInput.addEventListener('keyup', function(e) {
        // Update display dengan titik
        this.value = formatRupiah(this.value);
        // Simpan versi integer (tanpa titik) ke input hidden
        hiddenInput.value = this.value.replace(/\./g, '');
    });

    // --- SCRIPT ANTI DOUBLE SUBMIT ---
    const form = displayInput.closest('form');
    form.addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitHarvest');
        btn.innerHTML = 'Menyimpan... ⏳';
        btn.classList.add('disabled');
    });
});
</script>
@endsection