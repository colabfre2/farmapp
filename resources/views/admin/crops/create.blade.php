@extends('layouts.admin')

@section('title', 'Tambah Tanaman')

@section('content')
<style>
    .crop-row {
        position: relative;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        background: #fff;
    }
    .crop-row.has-error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .crop-row .row-number {
        position: absolute;
        top: -12px;
        left: 16px;
        background: #198754;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 2px 12px;
        border-radius: 20px;
    }
    .crop-row.has-error .row-number {
        background: #ef4444;
    }
    .btn-remove-row {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .row-error-list {
        font-size: 0.8rem;
        color: #ef4444;
        margin-top: 0.5rem;
        margin-bottom: 0;
        padding-left: 1.1rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tambah Tanaman</h2>
    <a href="{{ route('admin.crops.index') }}" class="btn btn-outline-secondary rounded-pill px-4">← Kembali</a>
</div>

@if(session('warning'))
    <div class="alert alert-warning bg-warning-subtle text-warning border-0 fw-bold rounded-3 mb-4">
        ⚠️ {{ session('warning') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold rounded-3 mb-4">
        ❌ {{ session('error') }}
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Bisa input beberapa tanaman sekaligus — klik "Tambah Baris" untuk menambah form baru.</p>
    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">+ Tambah Baris</button>
</div>

<form id="bulkCropForm" method="POST" action="{{ route('admin.crops.store-bulk') }}">
    @csrf

    <div id="cropRowsContainer">
        {{-- Baris-baris akan di-render di sini, minimal 1 baris saat halaman dibuka --}}
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" id="btnSubmitAll" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">Simpan Semua Tanaman ✓</button>
    </div>
</form>

{{-- Template 1 baris form, dipakai JS untuk clone. Ditulis pakai <template> supaya tidak dianggap input aktif oleh browser --}}
<template id="cropRowTemplate">
    <div class="crop-row" data-row>
        <span class="row-number" data-row-label></span>
        <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3 btn-remove-row" title="Hapus baris ini">🗑️</button>

        <div class="row">
            <div class="col-lg-8">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nama Ladang <span class="text-success fw-normal fs-7">(Otomatis Dibuat)</span>
                    </label>
                    <input type="text" name="crops[__INDEX__][name]" class="form-control generated-name-input bg-light rounded-3 py-2" placeholder="Pilih jenis tanaman di bawah..." readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Tanaman <span class="text-danger">*</span></label>
                        <select name="crops[__INDEX__][crop_type_id]" class="form-select crop-type-select rounded-3 py-2" required>
                            <option value="">-- Pilih jenis --</option>
                            @foreach($cropTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Varian Tanaman</label>
                        <select name="crops[__INDEX__][crop_variety_id]" class="form-select crop-variety-select rounded-3 py-2" disabled>
                            <option value="" class="variety-placeholder">-- Pilih jenis tanaman terlebih dahulu --</option>
                            @foreach($cropVarieties as $variety)
                                <option value="{{ $variety->id }}" data-type="{{ $variety->crop_type_id }}" style="display:none;">
                                    {{ $variety->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ladang</label>
                    <select name="crops[__INDEX__][farm_id]" class="form-select farm-select rounded-3 py-2">
                        <option value="">-- Pilih ladang (opsional) --</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}">
                                {{ $farm->name }}
                                @if($farm->area_size)
                                    ({{ number_format($farm->area_size, 2) }} {{ $farm->area_unit }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="crops[__INDEX__][notes]" class="form-control rounded-3 py-2" rows="2" placeholder="Opsional..."></textarea>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Tanam <span class="text-danger">*</span></label>
                    <input type="date" name="crops[__INDEX__][planted_at]" class="form-control planted-at-input rounded-3 py-2" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Perkiraan Panen <span class="text-danger">*</span></label>
                    <input type="date" name="crops[__INDEX__][expected_harvest_at]" class="form-control rounded-3 py-2" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status Awal</label>
                    <select name="crops[__INDEX__][status]" class="form-select rounded-3 py-2" required>
                        <option value="Bibit" selected>🌱 Bibit</option>
                        <option value="Pertumbuhan">🌿 Pertumbuhan</option>
                    </select>
                </div>
            </div>
        </div>

        <ul class="row-error-list d-none"></ul>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('cropRowsContainer');
    const template = document.getElementById('cropRowTemplate');
    const btnAddRow = document.getElementById('btnAddRow');

    // Data baris yang gagal validasi dari request sebelumnya (kalau ada), dikirim dari controller.
    const oldFailedRows = @json(session('failedRows', []));
    const oldInput = @json(old('crops', []));

    let rowIndex = 0;

    function renumberRows() {
        const rows = container.querySelectorAll('[data-row]');
        rows.forEach((row, i) => {
            row.querySelector('[data-row-label]').textContent = 'Tanaman #' + (i + 1);
        });
        // Tombol hapus disembunyikan kalau cuma tersisa 1 baris
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove-row');
            btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    function generateLiveName(row) {
        const typeSelect = row.querySelector('.crop-type-select');
        const varietySelect = row.querySelector('.crop-variety-select');
        const farmSelect = row.querySelector('.farm-select');
        const nameInput = row.querySelector('.generated-name-input');

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

    function attachRowEvents(row) {
        const typeSelect = row.querySelector('.crop-type-select');
        const varietySelect = row.querySelector('.crop-variety-select');
        const farmSelect = row.querySelector('.farm-select');
        const removeBtn = row.querySelector('.btn-remove-row');

        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const placeholder = varietySelect.querySelector('.variety-placeholder');
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
            generateLiveName(row);
        });

        varietySelect.addEventListener('change', () => generateLiveName(row));
        farmSelect.addEventListener('change', () => generateLiveName(row));

        removeBtn.addEventListener('click', function() {
            row.remove();
            renumberRows();
        });
    }

    function addRow(prefill = null) {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('[data-row]');

        // Ganti __INDEX__ di semua atribut name dengan index unik
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', rowIndex);
        });

        container.appendChild(row);
        const insertedRow = container.lastElementChild;

        // Isi ulang data lama kalau baris ini sebelumnya gagal validasi
        if (prefill) {
            const typeSelect = insertedRow.querySelector('.crop-type-select');
            const farmSelect = insertedRow.querySelector('.farm-select');

            if (prefill.crop_type_id) typeSelect.value = prefill.crop_type_id;
            if (prefill.farm_id) farmSelect.value = prefill.farm_id;
            if (prefill.planted_at) insertedRow.querySelector('input[name$="[planted_at]"]').value = prefill.planted_at;
            if (prefill.expected_harvest_at) insertedRow.querySelector('input[name$="[expected_harvest_at]"]').value = prefill.expected_harvest_at;
            if (prefill.notes) insertedRow.querySelector('textarea[name$="[notes]"]').value = prefill.notes;
            if (prefill.status) insertedRow.querySelector('select[name$="[status]"]').value = prefill.status;

            // Trigger change biar varian ter-filter sesuai jenis yang dipilih
            if (prefill.crop_type_id) {
                typeSelect.dispatchEvent(new Event('change'));
                if (prefill.crop_variety_id) {
                    setTimeout(() => {
                        insertedRow.querySelector('.crop-variety-select').value = prefill.crop_variety_id;
                    }, 0);
                }
            }
        }

        attachRowEvents(insertedRow);
        rowIndex++;
        renumberRows();
        return insertedRow;
    }

    btnAddRow.addEventListener('click', () => addRow());

    // --- Render ulang baris yang gagal validasi (kalau ada), lengkap dengan pesan errornya ---
    if (oldInput && oldInput.length > 0) {
        oldInput.forEach((data, i) => {
            const row = addRow(data);
            const errors = oldFailedRows[i + 1]; // key di failedRows itu 1-based
            if (errors) {
                row.classList.add('has-error');
                const errorList = row.querySelector('.row-error-list');
                errorList.classList.remove('d-none');
                errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            }
        });
    } else {
        // Halaman baru dibuka tanpa error sebelumnya → mulai dengan 1 baris kosong
        addRow();
    }

    // --- Anti double submit ---
    document.getElementById('bulkCropForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitAll');
        btn.innerHTML = 'Menyimpan... ⏳';
        btn.classList.add('disabled');
    });
});
</script>
@endsection