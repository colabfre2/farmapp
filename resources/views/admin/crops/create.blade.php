@extends('layouts.admin')

@section('title', 'Tambah Tanaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Tanaman</h2>
    <a href="{{ route('admin.crops.index') }}" class="btn btn-outline-secondary rounded-pill px-4">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.crops.store') }}">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">

            {{-- Card Informasi Dasar --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    {{-- Form Nama Tanaman (Live Readonly) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Ladang <span class="text-success fw-normal fs-7">(Auto-Generated)</span>
                        </label>
                        <input type="text" name="name" id="generatedNameInput" class="form-control bg-light rounded-3 py-2 @error('name') is-invalid @enderror" placeholder="Pilih jenis tanaman di bawah..." value="{{ old('name') }}" readonly>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Sistem merakit nama ini secara otomatis berdasarkan pilihan Anda.</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Tanaman <span class="text-danger">*</span></label>
                            <select name="crop_type_id" id="cropTypeSelect" class="form-select rounded-3 py-2 @error('crop_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih jenis --</option>
                                @foreach($cropTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('crop_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('crop_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Varian Tanaman</label>
                            <select name="crop_variety_id" id="cropVarietySelect" class="form-select rounded-3 py-2 @error('crop_variety_id') is-invalid @enderror"
                                {{ old('crop_type_id') ? '' : 'disabled' }}>
                                <option value="" id="varietyPlaceholder">
                                    {{ old('crop_type_id') ? '-- Pilih varian (opsional) --' : '-- Pilih jenis tanaman terlebih dahulu --' }}
                                </option>
                                @foreach($cropVarieties as $variety)
                                    <option value="{{ $variety->id }}" data-type="{{ $variety->crop_type_id }}"
                                        {{ old('crop_variety_id') == $variety->id ? 'selected' : '' }}
                                        style="{{ old('crop_type_id') && old('crop_type_id') != $variety->crop_type_id ? 'display:none;' : '' }}">
                                        {{ $variety->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('crop_variety_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ladang</label>
                        <select name="farm_id" id="farmSelect" class="form-select rounded-3 py-2 @error('farm_id') is-invalid @enderror">
                            <option value="">-- Pilih ladang (opsional) --</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}"
                                    {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                    {{ $farm->name }}
                                    @if($farm->area_size)
                                        ({{ number_format($farm->area_size, 2) }} {{ $farm->area_unit }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('farm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Belum ada ladangnya? <a href="{{ route('admin.farms.create') }}" target="_blank">Tambah ladang baru</a>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control rounded-3 py-2" rows="4" placeholder="Tambahkan keterangan tentang proses tanam ini...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (Jadwal & Status) --}}
        <div class="col-lg-4">

            {{-- Card Jadwal Tanam --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Jadwal Tanam</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Tanam <span class="text-danger">*</span></label>
                        <input type="date" name="planted_at" class="form-control rounded-3 py-2 @error('planted_at') is-invalid @enderror" value="{{ old('planted_at') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perkiraan Panen <span class="text-danger">*</span></label>
                        <input type="date" name="expected_harvest_at" class="form-control rounded-3 py-2 @error('expected_harvest_at') is-invalid @enderror" value="{{ old('expected_harvest_at') }}" required>
                    </div>
                </div>
            </div>

            {{-- Card Status --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Status Pertumbuhan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <select name="status" class="form-select rounded-3 py-2 @error('status') is-invalid @enderror" required>
                            <option value="Bibit" {{ old('status') == 'Bibit' ? 'selected' : '' }}>🌱 Bibit</option>
                            <option value="Pertumbuhan" {{ old('status') == 'Pertumbuhan' ? 'selected' : '' }}>🌿 Pertumbuhan</option>
                            <option value="Dipanen" {{ old('status') == 'Dipanen' ? 'selected' : '' }}>🌾 Dipanen</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Aksi Submit --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">Simpan Tanaman ✓</button>
            </div>

        </div>
    </div>
</form>

<script>
    // Fungsi untuk merakit nama otomatis (Live Preview)
    function generateLiveName() {
        const typeSelect = document.getElementById('cropTypeSelect');
        const varietySelect = document.getElementById('cropVarietySelect');
        const farmSelect = document.getElementById('farmSelect');
        const nameInput = document.getElementById('generatedNameInput');

        let typeText = typeSelect.options[typeSelect.selectedIndex]?.text || '';
        if (typeText.startsWith('--')) typeText = '';

        let varietyText = varietySelect.options[varietySelect.selectedIndex]?.text || '';
        if (varietyText.startsWith('--')) varietyText = '';

        // Ambil nama ladang saja (tanpa ukuran yang ada di dalam kurung)
        let farmOptionText = farmSelect.options[farmSelect.selectedIndex]?.text || '';
        let farmText = farmOptionText.startsWith('--') ? '' : farmOptionText.split(' (')[0].trim();

        // Prioritaskan Varian. Kalau gak ada varian, baru pakai Jenis.
        let baseName = varietyText ? varietyText : typeText;

        let finalName = '';
        if (baseName) {
            finalName = baseName;
            finalName += farmText ? ' - ' + farmText : ' - Lahan Utama';
        }

        nameInput.value = finalName;
    }

    // Filter varian tanaman sesuai jenis yang dipilih
    document.getElementById('cropTypeSelect').addEventListener('change', function() {
        const selectedType = this.value;
        const varietySelect = document.getElementById('cropVarietySelect');
        const placeholder = document.getElementById('varietyPlaceholder');
        const options = varietySelect.querySelectorAll('option[data-type]');

        if (!selectedType) {
            varietySelect.setAttribute('disabled', 'disabled');
            placeholder.textContent = '-- Pilih jenis tanaman terlebih dahulu --';
            options.forEach(opt => opt.style.display = 'none');
            varietySelect.value = '';
        } else {
            varietySelect.removeAttribute('disabled');
            placeholder.textContent = '-- Pilih varian (opsional) --';
            options.forEach(opt => {
                opt.style.display = (opt.dataset.type === selectedType) ? '' : 'none';
            });
            varietySelect.value = '';
        }

        generateLiveName();
    });

    // Panggil fungsi auto-generate saat ganti varian atau ganti ladang
    document.getElementById('cropVarietySelect').addEventListener('change', generateLiveName);
    document.getElementById('farmSelect').addEventListener('change', generateLiveName);
</script>
@endsection