@extends('layouts.admin')
@section('title', 'Tambah Ternak Masuk')
@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">⬆️ Tambah Ternak Masuk</h3></div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.livestock-movements.in.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kandang / Kelompok Ternak <span class="text-danger">*</span></label>
                        <select name="livestock_id" id="livestockSelect" class="form-select @error('livestock_id') is-invalid @enderror" required>
                            <option value="">-- Pilih kandang --</option>
                            @foreach($livestocks as $livestock)
                                <option value="{{ $livestock->id }}"
                                    data-kandang="{{ $livestock->kandang->name ?? '-' }}"
                                    data-quantity="{{ $livestock->quantity }}"
                                    data-capacity="{{ $livestock->kandang->capacity ?? '' }}"
                                    {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>
                                    {{ $livestock->name }} — {{ $livestock->quantity }} ekor saat ini
                                </option>
                            @endforeach
                        </select>
                        <div id="livestockInfo" class="small mt-1 text-muted" style="display:none;"></div>
                        @error('livestock_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Masuk (ekor) <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantityInput" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" min="1" required>
                        <div id="capacityWarning" class="text-danger small mt-1 fw-semibold" style="display:none;"></div>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan</label>
                        <select name="reason" class="form-select">
                            <option value="">-- Pilih alasan --</option>
                            <option value="Bibit Baru" {{ old('reason') == 'Bibit Baru' ? 'selected' : '' }}>Bibit Baru</option>
                            <option value="Kelahiran" {{ old('reason') == 'Kelahiran' ? 'selected' : '' }}>Kelahiran</option>
                            <option value="Koreksi Data" {{ old('reason') == 'Koreksi Data' ? 'selected' : '' }}>Koreksi Data</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success fw-bold px-4">Simpan ✓</button>
                        <a href="{{ route('admin.livestock-movements.in.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const select = document.getElementById('livestockSelect');
    const info = document.getElementById('livestockInfo');
    const quantityInput = document.getElementById('quantityInput');
    const capacityWarning = document.getElementById('capacityWarning');

    function updateInfo() {
        const opt = select.options[select.selectedIndex];
        if (!opt.value) { info.style.display = 'none'; return; }

        const kandang = opt.dataset.kandang;
        const qty = parseInt(opt.dataset.quantity || 0);
        const cap = opt.dataset.capacity;

        let text = `Kandang: <strong>${kandang}</strong> — Populasi saat ini: <strong>${qty} ekor</strong>`;
        if (cap) {
            const sisa = parseInt(cap) - qty;
            text += ` — Sisa kapasitas: <strong class="${sisa > 0 ? 'text-success' : 'text-danger'}">${sisa} ekor</strong>`;
            quantityInput.setAttribute('max', Math.max(sisa, 0));
        } else {
            quantityInput.removeAttribute('max');
        }

        info.innerHTML = text;
        info.style.display = 'block';
        checkCapacity();
    }

    function checkCapacity() {
        const max = quantityInput.getAttribute('max');
        const val = parseInt(quantityInput.value || 0);
        if (max !== null && val > parseInt(max)) {
            capacityWarning.style.display = 'block';
            capacityWarning.textContent = `⚠️ Jumlah melebihi sisa kapasitas kandang (maks. ${max} ekor)!`;
        } else {
            capacityWarning.style.display = 'none';
        }
    }

    select.addEventListener('change', updateInfo);
    quantityInput.addEventListener('input', checkCapacity);
    if (select.value) updateInfo();
});
</script>
@endsection