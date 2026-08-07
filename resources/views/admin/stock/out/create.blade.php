@extends('layouts.admin')

@section('title', 'Tambah Barang Keluar Borongan')

@section('content')
<style>
    .movement-row {
        position: relative;
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }
    .movement-row:hover {
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }
    .movement-row .row-number {
        position: absolute;
        top: -12px;
        left: 16px;
        background: #dc3545;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(220, 53, 69, 0.2);
    }
    .btn-remove-row {
        position: absolute;
        top: 12px;
        right: 12px;
        transition: transform 0.2s;
    }
    .btn-remove-row:hover {
        transform: scale(1.1);
    }
    .stock-info { font-size: 0.8rem; margin-top: 4px; }
    .font-quicksand { font-family: 'Quicksand', sans-serif !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark font-quicksand mb-0">📤 Catat Barang Keluar Borongan</h3>
    <a href="{{ route('admin.stock.out.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold shadow-sm px-4">← Kembali</a>
</div>

@if(session('error'))
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold rounded-3 shadow-sm mb-4">❌ {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold rounded-3 shadow-sm mb-4">
        <div class="mb-1">❌ Gagal memproses data:</div>
        <ul class="mb-0 small fw-normal">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Form ini untuk mencatat pengurangan stok produk secara borongan.</p>
    <button type="button" id="btnAddRow" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-sm">+ Tambah Baris</button>
</div>

<form id="bulkMovementForm" method="POST" action="{{ route('admin.stock.out.store-bulk') }}">
    @csrf
    <div id="movementRowsContainer"></div>
    <div class="d-grid gap-2 mt-4 pt-3 border-top">
        <button type="submit" id="btnSubmitAll" class="btn btn-danger fw-bold rounded-pill shadow-sm py-2">
            Simpan Semua Barang Keluar ✓
        </button>
    </div>
</form>

<template id="movementRowTemplate">
    <div class="movement-row" data-row>
        <span class="row-number" data-row-label></span>
        <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle btn-remove-row" title="Hapus baris ini">🗑️</button>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Produk <span class="text-danger">*</span></label>
                    <select name="movements[__INDEX__][product_id]" class="form-select product-select rounded-3 py-2" required>
                        <option value="">-- Pilih produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                {{ $product->name }} (Stok saat ini: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    <div class="stock-info text-muted" style="display:none;"></div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Alasan Keluar</label>
                        <select name="movements[__INDEX__][reason]" class="form-select rounded-3 py-2">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Terjual">🛒 Terjual</option>
                            <option value="Pindah">🔄 Pindah</option>
                            <option value="Rusak">💥 Rusak</option>
                            <option value="Expired">⌛ Expired</option>
                            <option value="Koreksi stok">🛠️ Koreksi stok</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Catatan</label>
                        <input type="text" name="movements[__INDEX__][notes]" class="form-control rounded-3 py-2" placeholder="Opsional...">
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 h-100 shadow-none" style="border-radius: 12px; background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                    <div class="card-body p-3 p-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="number" name="movements[__INDEX__][quantity]" class="form-control quantity-input rounded-3 py-2" min="1" required>
                            <div class="stock-warning text-danger small mt-1 fw-semibold" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('movementRowsContainer');
    const template = document.getElementById('movementRowTemplate');
    const btnAddRow = document.getElementById('btnAddRow');

    let rowIndex = 0;

    function renumberRows() {
        const rows = container.querySelectorAll('[data-row]');
        rows.forEach((row, i) => {
            row.querySelector('[data-row-label]').textContent = 'Item #' + (i + 1);
            row.querySelector('.btn-remove-row').style.display = rows.length > 1 ? '' : 'none';
        });
    }

    function checkStock(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.quantity-input');
        const infoDiv = row.querySelector('.stock-info');
        const warning = row.querySelector('.stock-warning');

        const selectedOpt = select.options[select.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            infoDiv.style.display = 'none';
            warning.style.display = 'none';
            return;
        }

        const currentStock = parseInt(selectedOpt.dataset.stock || 0);
        infoDiv.style.display = 'block';
        infoDiv.textContent = `Stok saat ini: ${currentStock} unit.`;

        const inputVal = parseInt(qtyInput.value || 0);
        if (inputVal > currentStock) {
            warning.style.display = 'block';
            warning.textContent = `⚠️ Melebihi stok yang tersedia (maks. ${currentStock} unit)!`;
        } else {
            warning.style.display = 'none';
        }
    }

    function attachRowEvents(row) {
        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.quantity-input');

        select.addEventListener('change', () => checkStock(row));
        qtyInput.addEventListener('input', () => checkStock(row));

        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            row.remove();
            renumberRows();
        });
    }

    function addRow() {
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('[data-row]');

        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', rowIndex);
        });

        container.appendChild(row);
        const insertedRow = container.lastElementChild;

        attachRowEvents(insertedRow);
        rowIndex++;
        renumberRows();
    }

    btnAddRow.addEventListener('click', () => addRow());
    addRow();

    document.getElementById('bulkMovementForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitAll');
        btn.innerHTML = 'Memproses... ⏳';
        btn.classList.add('disabled');
    });
});
</script>
@endsection