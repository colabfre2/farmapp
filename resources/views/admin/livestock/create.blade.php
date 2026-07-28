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

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Bisa input beberapa kelompok ternak sekaligus — klik "Tambah Baris" untuk menambah form baru.</p>
    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">+ Tambah Baris</button>
</div>

<form id="bulkLivestockForm" method="POST" action="{{ route('admin.livestock.store-bulk') }}">
    @csrf
    <div id="livestockRowsContainer"></div>
    <div class="d-grid gap-2 mt-4">
        <button type="submit" id="btnSubmitAll" class="btn btn-success btn-lg fw-bold rounded-pill shadow-sm">Simpan Semua Data Ternak ✓</button>
    </div>
</form>

{{-- Template baris -- dipakai JS untuk clone --}}
<template id="livestockRowTemplate">
    <div class="livestock-row" data-row>
        <span class="row-number" data-row-label></span>
        <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3 btn-remove-row" title="Hapus baris ini">🗑️</button>

        <div class="row g-3">
            {{-- KOLOM KIRI --}}
            <div class="col-lg-8">

                {{-- Auto-generated name --}}
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
                        {{-- livestock_type_id tetap dikirim sebagai referensi filter kandang di UI,
                             tapi di controller akan di-override dengan nilai dari kandang yang dipilih --}}
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
                                    data-terisi="{{ $kandang->livestocks_sum_quantity ?? 0 }}"
                                    style="display:none;">
                                    {{ $kandang->name }}
                                    @if($kandang->capacity)
                                        (maks. {{ $kandang->capacity }} ekor, terisi {{ $kandang->livestocks_sum_quantity ?? 0 }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="capacity-info" style="display:none;"></div>
                        <small class="text-muted">
                            Belum ada kandangnya? <a href="{{ route('admin.kandangs.create') }}" target="_blank">Tambah kandang baru</a>
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
                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background: #f8fafc;">
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
            const btn = row.querySelector('.btn-remove-row');
            btn.style.display = rows.length > 1 ? '' : 'none';
        });
    }

    function generateName(row) {
        const typeSelect = row.querySelector('.livestock-type-select');
        const kandangSelect = row.querySelector('.kandang-select');
        const nameInput = row.querySelector('.generated-name-input');

        let typeText = typeSelect.options[typeSelect.selectedIndex]?.text || '';
        if (typeText.startsWith('--')) typeText = '';

        let kandangText = '';
        const selectedOpt = kandangSelect.options[kandangSelect.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.name) {
            kandangText = selectedOpt.dataset.name;
        }

        const today = new Date();
        const bulanTahun = today.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });

        let name = '';
        if (typeText) {
            name = typeText;
            name += kandangText ? ' - ' + kandangText : '';
            name += ' (' + bulanTahun + ')';
        }

        nameInput.value = name;
    }

    function updateCapacityInfo(row) {
        const kandangSelect = row.querySelector('.kandang-select');
        const capacityInfo = row.querySelector('.capacity-info');
        const quantityInput = row.querySelector('.quantity-input');
        const qtyWarning = row.querySelector('.quantity-warning');
        const opt = kandangSelect.options[kandangSelect.selectedIndex];

        if (!opt || !opt.value) {
            capacityInfo.style.display = 'none';
            quantityInput.removeAttribute('max');
            return;
        }

        const capacity = opt.dataset.capacity;
        const terisi = parseInt(opt.dataset.terisi || 0);

        if (capacity) {
            const sisa = parseInt(capacity) - terisi;
            capacityInfo.style.display = 'block';
            capacityInfo.className = 'capacity-info fw-semibold ' + (sisa > 0 ? 'text-success' : 'text-danger');
            capacityInfo.textContent = `Kapasitas: ${capacity} ekor — Terisi: ${terisi} — Sisa: ${sisa} ekor`;
            quantityInput.setAttribute('max', Math.max(sisa, 0));
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
            qtyWarning.textContent = `⚠️ Jumlah melebihi sisa kapasitas kandang (maks. ${max} ekor)!`;
        } else {
            qtyWarning.style.display = 'none';
        }
    }

    function attachRowEvents(row) {
        const typeSelect = row.querySelector('.livestock-type-select');
        const kandangSelect = row.querySelector('.kandang-select');
        const placeholder = kandangSelect.querySelector('.kandang-placeholder');
        const quantityInput = row.querySelector('.quantity-input');
        const removeBtn = row.querySelector('.btn-remove-row');

        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const options = kandangSelect.querySelectorAll('option[data-type]');

            if (!selectedType) {
                kandangSelect.setAttribute('disabled', 'disabled');
                placeholder.textContent = '-- Pilih jenis terlebih dahulu --';
                options.forEach(opt => opt.style.display = 'none');
                kandangSelect.value = '';
            } else {
                kandangSelect.removeAttribute('disabled');
                placeholder.textContent = '-- Pilih kandang --';
                options.forEach(opt => {
                    opt.style.display = (opt.dataset.type === selectedType) ? '' : 'none';
                });
                kandangSelect.value = '';
            }

            row.querySelector('.capacity-info').style.display = 'none';
            generateName(row);
        });

        kandangSelect.addEventListener('change', function() {
            generateName(row);
            updateCapacityInfo(row);
        });

        quantityInput.addEventListener('input', () => checkQuantity(row));

        removeBtn.addEventListener('click', function() {
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
                typeSelect.dispatchEvent(new Event('change'));
            }

            if (prefill.kandang_id) {
                setTimeout(() => {
                    kandangSelect.value = prefill.kandang_id;
                    kandangSelect.dispatchEvent(new Event('change'));
                }, 0);
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

    // Render ulang baris gagal validasi dari session
    if (oldInput && oldInput.length > 0) {
        oldInput.forEach((data, i) => {
            const errors = oldFailedRows[i + 1] || null;
            addRow(data, errors);
        });
    } else {
        addRow(); // 1 baris kosong saat halaman baru dibuka
    }

    // Anti double submit
    document.getElementById('bulkLivestockForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitAll');
        btn.innerHTML = 'Menyimpan... ⏳';
        btn.classList.add('disabled');
    });
});
</script>
@endsection