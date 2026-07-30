@extends('layouts.admin')

@section('title', 'Perbarui Tanaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Perbarui Tanaman</h2>
    <a href="{{ route('admin.crops.index') }}" class="btn btn-outline-secondary rounded-pill px-4">← Kembali</a>
</div>

<form id="cropForm" method="POST" action="{{ route('admin.crops.update', $crop) }}">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    {{-- Form Nama Tanaman (Live Readonly) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Label / Siklus Tanam <span class="text-success fw-normal fs-7">(Auto-Generated)</span>
                        </label>
                        <input type="text" name="name" id="generatedNameInput" class="form-control bg-light rounded-3 py-2 @error('name') is-invalid @enderror" value="{{ old('name', $crop->name) }}" readonly>
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Sistem merakit nama ini secara otomatis berdasarkan pilihan Anda.</small>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jenis Tanaman <span class="text-danger">*</span></label>
                            <select name="crop_type_id" id="cropTypeSelect" class="form-select rounded-3 py-2 @error('crop_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih jenis --</option>
                                @foreach($cropTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('crop_type_id', $crop->crop_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('crop_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Varian Tanaman</label>
                            <select name="crop_variety_id" id="cropVarietySelect" class="form-select rounded-3 py-2 @error('crop_variety_id') is-invalid @enderror"
                                {{ old('crop_type_id', $crop->crop_type_id) ? '' : 'disabled' }}>
                                <option value="" id="varietyPlaceholder">
                                    {{ old('crop_type_id', $crop->crop_type_id) ? '-- Pilih varian (opsional) --' : '-- Pilih jenis tanaman terlebih dahulu --' }}
                                </option>
                                @foreach($cropVarieties as $variety)
                                    <option value="{{ $variety->id }}" data-type="{{ $variety->crop_type_id }}"
                                        {{ old('crop_variety_id', $crop->crop_variety_id) == $variety->id ? 'selected' : '' }}
                                        style="{{ old('crop_type_id', $crop->crop_type_id) != $variety->crop_type_id ? 'display:none;' : '' }}">
                                        {{ $variety->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('crop_variety_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ladang</label>
                        <select name="farm_id" id="farmSelect" class="form-select rounded-3 py-2 @error('farm_id') is-invalid @enderror">
                            <option value="">-- Pilih ladang (opsional) --</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}"
                                    {{ old('farm_id', $crop->farm_id) == $farm->id ? 'selected' : '' }}>
                                    {{ $farm->name }}
                                    @if($farm->area_size)
                                        ({{ number_format($farm->area_size, 2) }} {{ $farm->area_unit }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('farm_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">
                            Belum ada ladangnya? <a href="{{ route('admin.farms.create') }}" target="_blank">Tambah ladang baru</a>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control rounded-3 py-2" rows="4" placeholder="Tambahkan keterangan tentang proses tanam ini...">{{ old('notes', $crop->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Jadwal & Status) --}}
        <div class="col-lg-4">
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Jadwal Tanam</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Tanam <span class="text-danger">*</span></label>
                        <input type="date" id="planted_at" name="planted_at" class="form-control rounded-3 py-2 @error('planted_at') is-invalid @enderror" value="{{ old('planted_at', $crop->planted_at) }}" required {{ $crop->status == 'Dipanen' ? 'readonly' : '' }}>
                        @error('planted_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perkiraan Panen <span class="text-danger">*</span></label>
                        <input type="date" id="expected_harvest_at" name="expected_harvest_at" class="form-control rounded-3 py-2 @error('expected_harvest_at') is-invalid @enderror" value="{{ old('expected_harvest_at', $crop->expected_harvest_at) }}" required {{ $crop->status == 'Dipanen' ? 'readonly' : '' }}>
                        @error('expected_harvest_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- DIBUAT READONLY KARENA DIISI DARI MODUL PANEN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Panen Aktual</label>
                        <input type="date" id="actual_harvest_at" name="actual_harvest_at" class="form-control bg-light rounded-3 py-2" value="{{ $crop->actual_harvest_at }}" readonly>
                        <small class="text-primary mt-1 d-block"><i class="bi bi-lock-fill me-1"></i>Otomatis terisi dari form Catat Panen.</small>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Status Pertumbuhan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        {{-- JIKA SUDAH DIPANEN, STATUS DIKUNCI --}}
                        @if($crop->status == 'Dipanen')
                            <input type="text" class="form-control bg-light rounded-3 py-2 text-success fw-bold" value="🌾 Dipanen" readonly>
                            <input type="hidden" name="status" value="Dipanen">
                            <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Siklus tanam sudah ditandai selesai, data terkunci.</small>
                        @else
                            <select name="status" class="form-select rounded-3 py-2 @error('status') is-invalid @enderror" required>
                                <option value="Bibit" {{ old('status', $crop->status) == 'Bibit' ? 'selected' : '' }}>🌱 Bibit</option>
                                <option value="Pertumbuhan" {{ old('status', $crop->status) == 'Pertumbuhan' ? 'selected' : '' }}>🌿 Pertumbuhan</option>
                                <option value="Dipanen" {{ old('status', $crop->status) == 'Dipanen' ? 'selected' : '' }}>🌾 Dipanen (Selesaikan Siklus)</option>
                            </select>
                            @if($crop->harvests()->exists())
                                <small class="text-success mt-1 d-block"><i class="bi bi-check-circle me-1"></i>Tanaman ini sudah dipanen {{ $crop->harvests()->count() }}x. Masih bisa dipanen lagi selama status belum "Dipanen".</small>
                            @endif
                            <small class="text-warning mt-1 d-block"><i class="bi bi-exclamation-triangle me-1"></i>Pilih "Dipanen" hanya jika siklus tanam ini benar-benar selesai (tidak akan dipanen lagi).</small>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" id="btnSubmitCrop" class="btn btn-warning text-white btn-lg fw-bold rounded-pill shadow-sm" {{ $crop->status == 'Dipanen' ? 'disabled' : '' }}>
                    {{ $crop->status == 'Dipanen' ? 'Data Terkunci 🔒' : 'Perbarui Tanaman ✓' }}
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- 1. SCRIPT AUTO-GENERATE NAMA (Live Preview) ---
        function generateLiveName() {
            const typeSelect = document.getElementById('cropTypeSelect');
            const varietySelect = document.getElementById('cropVarietySelect');
            const farmSelect = document.getElementById('farmSelect');
            const nameInput = document.getElementById('generatedNameInput');

            let typeText = typeSelect.options[typeSelect.selectedIndex]?.text || '';
            if (typeText.startsWith('--')) typeText = '';

            let varietyText = varietySelect.options[varietySelect.selectedIndex]?.text || '';
            if (varietyText.startsWith('--')) varietyText = '';

            let farmOptionText = farmSelect.options[farmSelect.selectedIndex]?.text || '';
            let farmText = farmOptionText.startsWith('--') ? '' : farmOptionText.split(' (')[0].trim();

            let baseName = varietyText ? varietyText : typeText;
            let finalName = '';

            if (baseName) {
                finalName = baseName;
                finalName += farmText ? ' - ' + farmText : ' - Lahan Utama';
            }

            nameInput.value = finalName;
        }

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

        document.getElementById('cropVarietySelect').addEventListener('change', generateLiveName);
        document.getElementById('farmSelect').addEventListener('change', generateLiveName);


        // --- 2. SCRIPT LOGIKA TANGGAL (Mencegah Time-Travel) ---
        const plantedInput = document.getElementById('planted_at');
        const expectedInput = document.getElementById('expected_harvest_at');
        const actualInput = document.getElementById('actual_harvest_at');

        function enforceDateLogic() {
            if (plantedInput.value) {
                expectedInput.min = plantedInput.value;
                actualInput.min = plantedInput.value;
            }
        }

        enforceDateLogic();

        plantedInput.addEventListener('change', function() {
            enforceDateLogic();
            if(expectedInput.value && expectedInput.value < this.value) expectedInput.value = this.value;
            if(actualInput.value && actualInput.value < this.value) actualInput.value = this.value;
        });


        // --- 3. SCRIPT ANTI DOUBLE SUBMIT ---
        document.getElementById('cropForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitCrop');
            btn.innerHTML = 'Memperbarui... ⏳';
            btn.classList.add('disabled');
        });
    });
</script>
@endsection