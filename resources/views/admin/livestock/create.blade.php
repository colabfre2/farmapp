@extends('layouts.admin')

@section('title', 'Tambah Ternak Baru')

@section('content')
<style>
    .livestock-row {
        position: relative;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        background: #fff;
    }
    .livestock-row.has-error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .livestock-row .row-number {
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
    .livestock-row.has-error .row-number { background: #ef4444; }
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
    .capacity-info { font-size: 0.8rem; margin-top: 4px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark font-quicksand mb-0">🐄 Tambah Data Ternak</h2>
    <a href="{{ route('admin.livestock.index') }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

@if(session('warning'))
    <div class="alert alert-warning bg-warning-subtle text-warning border-0 fw-bold rounded-3 mb-4">⚠️ {{ session('warning') }}</div>
@endif
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

{{-- Info kontekstual kalau ada kandang yang disembunyikan --}}
@if($kandangsTerisi > 0)
    <div class="alert alert-info bg-info-subtle text-info border-0 rounded-3 mb-4 d-flex align-items-center">
        <span class="fs-5 me-2">ℹ️</span>
        <div>
            <strong>{{ $kandangsTerisi }} kandang</strong> tidak ditampilkan karena masih aktif terisi ternak.
            Untuk menambah ternak ke kandang yang sudah ada, gunakan menu
            <a href="{{ route('admin.livestock-movements.in.create') }}" class="fw-bold">Ternak Masuk</a>.
        </div>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Form ini untuk mendaftarkan batch/kelompok ternak baru ke kandang yang kosong. Bisa input beberapa sekaligus.</p>
    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">+ Tambah Baris</button>
</div>

<form id="bulkLivestockForm" method="POST" action="{{ route('admin.livestock.store-bulk') }}">
    @csrf
    <div id="livestockRowsContainer"></div>
    <div class="d-grid gap-2 mt-4">
        <button type="submit" id="btnSubmitAll" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">Simpan Semua Data Ternak ✓</button>
    </div>
</form>

<template id="livestockRowTemplate">
    <div class="livestock-row" data-row>
        <span class="row-number" data-row-label></span>
        <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3 btn-remove-row" title="Hapus baris ini">🗑️</button>

        <div class="row g-3">
            {{-- KOLOM KIRI --}}
            <div class="col-lg-8">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nama Kelompok <span class="text-success fw-normal small">(Otomatis Dibuat)</span>
                    </label>
                    <input type="text" name="livestocks[__INDEX__][name]"
                        class="form-control generated-name-input bg-light rounded-3 py-2"
                        placeholder="Pilih jenis dan kandang di bawah..."
                        readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Ternak <span class="text-danger">*</span></label>
                        <select name="livestocks[__INDEX__][livestock_type_id]"
                            class="form-select livestock-type-select rounded-3 py-2" required>
                            <option value="">-- Pilih jenis --</option>
                            @foreach($livestockTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kandang <span class="text-danger">*</span></label>
                        <select name="livestocks[__INDEX__][kandang_id]"
                            class="form-select kandang-select rounded-3 py-2" disabled required>
                            <option value="" class="kandang-placeholder">-- Pilih jenis terlebih dahulu --</option>
                            @foreach($kandangs as $kandang)
                                <option value="{{ $kandang->id }}"
                                    data-type="{{ $kandang->livestock_type_id }}"
                                    data-name="{{ $kandang->name }}"
                                    data-capacity="{{ $kandang->capacity ?? '' }}"
                                    style="display:none;">
                                    {{ $kandang->name }}
                                    @if($kandang->capacity)
                                        (kapasitas {{ $kandang->capacity }} ekor)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="capacity-info" style="display:none;"></div>
                        <small class="text-muted">
                            Hanya kandang kosong yang tampil di sini.
                            <a href="{{ route('admin.kandangs.create') }}" target="_blank">Tambah kandang baru</a>
                        </small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="livestocks[__INDEX__][notes]" class="form-control rounded-3 py-2" rows="2" placeholder="Opsional..."></textarea>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-lg-4">
                <div class="card border-0 h-100" style="border-radius: 10px; background: #f8fafc;">
                    <div class="card-body p-3">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" name="livestocks[__INDEX__][arrival_date]"
                                class="form-control rounded-3 py-2" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah (ekor) <span class="text-danger">*</span></label>
                            <input type="number" name="livestocks[__INDEX__][quantity]"
                                class="form-control quantity-input rounded-3 py-2" min="1" required>
                            <div class="quantity-warning text-danger small mt-1 fw-semibold" style="display:none;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Berat Rata-rata (kg)</label>
                            <input type="number" step="0.01" name="livestocks[__INDEX__][avg_weight]"
                                class="form-control rounded-3 py-2" placeholder="Opsional">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Status Kesehatan</label>
                            <select name="livestocks[__INDEX__][health_status]" class="form-select rounded-3 py-2" required>
                                <option value="Sehat" selected>✅ Sehat</option>
                                <option value="Pemantauan">⚠️ Pemantauan</option>
                                <option value="Sakit">🤒 Sakit</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <ul class="row-error-list d-none"></ul>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('livestockRowsContainer');
    const template = document.getElementById('livestockRowTemplate');
    const btnAddRow = document.getElementById('btnAddRow');

    const oldFailedRows = @json(session('failedRows', []));
    const oldInput = @json(old('livestocks', []));

    let rowIndex = 0;

    function renumberRows() {
        const rows = container.querySelectorAll('[data-row]');
        rows.forEach((row, i) => {
            row.querySelector('[data-row-label]').textContent = 'Ternak #' + (i + 1);
            row.querySelector('.btn-remove-row').style.display = rows.length > 1 ? '' : 'none';
        });
    }

    function generateName(row) {
        const typeSelect = row.querySelector('.livestock-type-select');
        const kandangSelect = row.querySelector('.kandang-select');
        const nameInput = row.querySelector('.generated-name-input');

        let typeText = typeSelect.options[typeSelect.selectedIndex]?.text || '';
        if (typeText.startsWith('--')) typeText = '';

        const selectedOpt = kandangSelect.options[kandangSelect.selectedIndex];
        const kandangText = (selectedOpt && selectedOpt.dataset.name) ? selectedOpt.dataset.name : '';

        nameInput.value = typeText
            ? typeText + (kandangText ? ' - ' + kandangText : '')
            : '';
    }

    function updateCapacityInfo(row) {
        const kandangSelect = row.querySelector('.kandang-select');
        const capacityInfo = row.querySelector('.capacity-info');
        const quantityInput = row.querySelector('.quantity-input');
        const opt = kandangSelect.options[kandangSelect.selectedIndex];

        if (!opt || !opt.value) {
            capacityInfo.style.display = 'none';
            quantityInput.removeAttribute('max');
            return;
        }

        const capacity = opt.dataset.capacity;
        if (capacity) {
            // Kandang yang muncul di sini sudah pasti kosong (quantity = 0),
            // jadi sisa kapasitas = kapasitas penuh
            capacityInfo.style.display = 'block';
            capacityInfo.className = 'capacity-info fw-semibold text-success';
            capacityInfo.textContent = `Kapasitas kandang: ${capacity} ekor (kosong, siap diisi)`;
            quantityInput.setAttribute('max', parseInt(capacity));
        } else {
            capacityInfo.style.display = 'block';
            capacityInfo.className = 'capacity-info text-muted';
            capacityInfo.textContent = 'Kandang ini tidak dibatasi kapasitasnya.';
            quantityInput.removeAttribute('max');
        }

        checkQuantity(row);
    }

    function checkQuantity(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const qtyWarning = row.querySelector('.quantity-warning');
        const max = quantityInput.getAttribute('max');
        const val = parseInt(quantityInput.value || 0);

        if (max !== null && val > parseInt(max)) {
            qtyWarning.style.display = 'block';
            qtyWarning.textContent = `⚠️ Jumlah melebihi kapasitas kandang (maks. ${max} ekor)!`;
        } else {
            qtyWarning.style.display = 'none';
        }
    }

    function filterKandangByType(row, selectedType) {
        const kandangSelect = row.querySelector('.kandang-select');
        const placeholder = kandangSelect.querySelector('.kandang-placeholder');
        const options = kandangSelect.querySelectorAll('option[data-type]');

        if (!selectedType) {
            kandangSelect.setAttribute('disabled', 'disabled');
            placeholder.textContent = '-- Pilih jenis terlebih dahulu --';
            options.forEach(opt => opt.style.display = 'none');
        } else {
            kandangSelect.removeAttribute('disabled');
            placeholder.textContent = '-- Pilih kandang --';
            options.forEach(opt => {
                opt.style.display = opt.dataset.type === selectedType ? '' : 'none';
            });
        }
    }

    function attachRowEvents(row) {
        const typeSelect = row.querySelector('.livestock-type-select');
        const kandangSelect = row.querySelector('.kandang-select');
        const quantityInput = row.querySelector('.quantity-input');

        typeSelect.addEventListener('change', function() {
            filterKandangByType(row, this.value);
            kandangSelect.value = '';
            row.querySelector('.capacity-info').style.display = 'none';
            generateName(row);
        });

        kandangSelect.addEventListener('change', function() {
            generateName(row);
            updateCapacityInfo(row);
        });

        quantityInput.addEventListener('input', () => checkQuantity(row));

        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            row.remove();
            renumberRows();
        });
    }

    function addRow(prefill = null, errors = null) {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('[data-row]');

        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', rowIndex);
        });

        container.appendChild(row);
        const insertedRow = container.lastElementChild;

        if (prefill) {
            const typeSelect = insertedRow.querySelector('.livestock-type-select');
            const kandangSelect = insertedRow.querySelector('.kandang-select');

            if (prefill.livestock_type_id) {
                typeSelect.value = prefill.livestock_type_id;
                filterKandangByType(insertedRow, typeSelect.value);
            }
            if (prefill.kandang_id) {
                kandangSelect.value = prefill.kandang_id;
                generateName(insertedRow);
                updateCapacityInfo(insertedRow);
            }
            if (prefill.arrival_date) insertedRow.querySelector('input[name$="[arrival_date]"]').value = prefill.arrival_date;
            if (prefill.quantity) insertedRow.querySelector('.quantity-input').value = prefill.quantity;
            if (prefill.avg_weight) insertedRow.querySelector('input[name$="[avg_weight]"]').value = prefill.avg_weight;
            if (prefill.health_status) insertedRow.querySelector('select[name$="[health_status]"]').value = prefill.health_status;
            if (prefill.notes) insertedRow.querySelector('textarea[name$="[notes]"]').value = prefill.notes;
        }

        if (errors) {
            insertedRow.classList.add('has-error');
            const errorList = insertedRow.querySelector('.row-error-list');
            errorList.classList.remove('d-none');
            errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
        }

        attachRowEvents(insertedRow);
        rowIndex++;
        renumberRows();
        return insertedRow;
    }

    btnAddRow.addEventListener('click', () => addRow());

    if (oldInput && oldInput.length > 0) {
        oldInput.forEach((data, i) => addRow(data, oldFailedRows[i + 1] || null));
    } else {
        addRow();
    }

    document.getElementById('bulkLivestockForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitAll');
        btn.innerHTML = 'Menyimpan... ⏳';
        btn.classList.add('disabled');
    });
});
</script>
@endsection