@extends('layouts.admin')

@section('title', 'Perbarui Data Ternak')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">🐄 Perbarui Data Ternak</h2>
    <a href="{{ route('admin.livestock.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

@if(session('error'))
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold rounded-3 mb-4">❌ {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold rounded-3 mb-4">
        <div class="mb-1">❌ Gagal menyimpan, periksa kembali data berikut:</div>
        <ul class="mb-0 small fw-normal">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                        <label class="form-label fw-semibold">
                            Nama Kelompok <span class="text-success fw-normal small">(Otomatis, ikut kandang)</span>
                        </label>
                        <input type="text" id="generatedNameDisplay" class="form-control bg-light rounded-3 py-2"
                            value="{{ $livestock->name }}" readonly>
                        <small class="text-muted">Nama otomatis mengikuti jenis hewan dan kandang yang dipilih.</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                Jenis Hewan
                                <span class="badge bg-secondary rounded-pill" style="font-size: 0.6rem;">🔒 Terkunci</span>
                            </label>
                            <input type="text" class="form-control rounded-3 py-2 fw-bold text-dark"
                                style="background-color: #e2e8f0; border: 1px solid #cbd5e1; cursor: not-allowed;"
                                value="{{ $livestock->livestockType->name ?? '-' }}" readonly disabled>
                            <input type="hidden" name="livestock_type_id" value="{{ $livestock->livestock_type_id }}">
                            <small class="text-muted">Jenis hewan tidak bisa diganti. Kalau salah jenis, hapus kelompok ini dan buat baru lewat menu Tambah Ternak Baru.</small>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Kandang <span class="text-danger">*</span></label>
                            <select name="kandang_id" id="kandangSelect" class="form-select rounded-3 py-2 @error('kandang_id') is-invalid @enderror" required>
                                <option value="" id="kandangPlaceholder">-- Pilih kandang --</option>
                                @foreach($kandangs as $kandang)
                                    <option value="{{ $kandang->id }}"
                                        data-current="{{ $kandang->id === $livestock->kandang_id ? '1' : '0' }}"
                                        data-capacity="{{ $kandang->capacity ?? '' }}"
                                        data-name="{{ $kandang->name }}"
                                        {{ old('kandang_id', $livestock->kandang_id) == $kandang->id ? 'selected' : '' }}>
                                        {{ $kandang->name }}
                                        @if($kandang->id === $livestock->kandang_id)
                                            (kandang saat ini)
                                        @elseif($kandang->capacity)
                                            (maks. {{ $kandang->capacity }} ekor)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div id="kandangCapacityInfo" class="small mt-1" style="display:none;"></div>
                            <small class="text-muted">Hanya kandang kosong (atau kandang saat ini) untuk jenis yang sama yang bisa dipilih sebagai tujuan pindah.</small>
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
                            <p class="text-muted small mb-2" style="line-height: 1.4;">
                                💡 <strong>Info SOP:</strong> Populasi ternak tidak dapat diedit secara manual. Penambahan atau pengurangan jumlah hewan harus melalui menu Ternak Masuk / Keluar untuk menjaga keakuratan histori data.
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.livestock-movements.in.create') }}" class="btn btn-sm btn-success rounded-pill px-3">⬆️ Ternak Masuk</a>
                                <a href="{{ route('admin.livestock-movements.out.create') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">⬇️ Ternak Keluar</a>
                            </div>
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
    const kandangSelect = document.getElementById('kandangSelect');
    const capacityInfo = document.getElementById('kandangCapacityInfo');
    const nameDisplay = document.getElementById('generatedNameDisplay');
    const jenisHewan = @json($livestock->livestockType->name ?? '');

    function updateCapacityInfo() {
        const opt = kandangSelect.options[kandangSelect.selectedIndex];

        if (!opt || !opt.value) {
            capacityInfo.style.display = 'none';
            return;
        }

        const isCurrent = opt.dataset.current === '1';
        const capacity = opt.dataset.capacity;

        capacityInfo.style.display = 'block';

        if (isCurrent) {
            capacityInfo.className = 'small mt-1 fw-semibold text-secondary';
            capacityInfo.textContent = capacity
                ? `Ini kandang saat ini (kapasitas ${capacity} ekor).`
                : 'Ini kandang saat ini (tidak dibatasi kapasitas).';
        } else if (capacity) {
            capacityInfo.className = 'small mt-1 fw-semibold text-success';
            capacityInfo.textContent = `Kapasitas kandang: ${capacity} ekor (kosong, siap diisi).`;
        } else {
            capacityInfo.className = 'small mt-1 text-muted';
            capacityInfo.textContent = 'Kandang ini tidak dibatasi kapasitasnya.';
        }
    }

    function updateNamePreview() {
        const opt = kandangSelect.options[kandangSelect.selectedIndex];
        const kandangName = (opt && opt.dataset.name) ? opt.dataset.name : '';

        nameDisplay.value = kandangName
            ? `${jenisHewan} - ${kandangName}`
            : jenisHewan;
    }

    kandangSelect.addEventListener('change', function() {
        updateCapacityInfo();
        updateNamePreview();
    });
    updateCapacityInfo();
    updateNamePreview();
});
</script>
@endsection