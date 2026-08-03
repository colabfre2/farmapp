{{--
    Partial form — dipakai oleh create.blade.php dan edit.blade.php
    Variables: $address (opsional, untuk edit)
--}}
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Label Alamat <span class="text-danger">*</span></label>
        <select name="label" class="form-select rounded-3">
            @foreach(['Rumah', 'Kantor', 'Kos', 'Lainnya'] as $lbl)
                <option value="{{ $lbl }}" {{ old('label', $address->label ?? 'Rumah') === $lbl ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
        <input type="text" name="recipient_name" class="form-control rounded-3 @error('recipient_name') is-invalid @enderror"
            value="{{ old('recipient_name', $address->recipient_name ?? '') }}" required>
        @error('recipient_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
        <input type="tel" name="phone" class="form-control rounded-3 @error('phone') is-invalid @enderror"
            value="{{ old('phone', $address->phone ?? '') }}" required>
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Kode Pos</label>
        <input type="text" name="postal_code" class="form-control rounded-3"
            value="{{ old('postal_code', $address->postal_code ?? '') }}" maxlength="10">
    </div>

    <div class="col-12 position-relative">
        <label class="form-label fw-semibold">Kota / Kecamatan Tujuan <span class="text-danger">*</span></label>
        <input type="text" id="destinationSearch" class="form-control rounded-3 @error('destination_id') is-invalid @enderror"
            placeholder="Ketik minimal 3 huruf, contoh: Cengkareng, Jakarta Barat..."
            autocomplete="off"
            value="{{ old('district') ? old('district') . ', ' . old('city') . ', ' . old('province') : (isset($address) ? $address->district . ', ' . $address->city . ', ' . $address->province : '') }}">
        
        {{-- DROPDOWN HASIL PENCARIAN (Fix Background Tembus & Numpuk) --}}
        <div id="destinationResults" class="list-group position-absolute w-100 shadow-lg border rounded-3 mt-1"
            style="z-index: 9999; display:none; max-height: 260px; overflow-y:auto; background-color: #ffffff !important;"></div>
        
        <div class="form-text">Cari nama kecamatan atau kota tujuan, lalu pilih dari daftar.</div>
        @error('destination_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

        <input type="hidden" name="destination_id" id="destinationIdInput" value="{{ old('destination_id', $address->destination_id ?? '') }}">
        <input type="hidden" name="province" id="provinceNameInput" value="{{ old('province', $address->province ?? '') }}">
        <input type="hidden" name="city" id="cityNameInput" value="{{ old('city', $address->city ?? '') }}">
        <input type="hidden" name="district" id="districtNameInput" value="{{ old('district', $address->district ?? '') }}">
        <input type="hidden" name="subdistrict" id="subdistrictNameInput" value="{{ old('subdistrict', $address->subdistrict ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Alamat Detail <span class="text-danger">*</span></label>
        <textarea name="address_detail" class="form-control rounded-3 @error('address_detail') is-invalid @enderror"
            rows="3" placeholder="Nama jalan, no. rumah, RT/RW, kelurahan..." required>{{ old('address_detail', $address->address_detail ?? '') }}</textarea>
        @error('address_detail') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_default" id="isDefault" value="1" class="form-check-input"
                {{ old('is_default', ($address->is_default ?? false) ? '1' : '') ? 'checked' : '' }}>
            <label for="isDefault" class="form-check-label fw-semibold">Jadikan alamat utama</label>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const SEARCH_URL = '{{ route('buyer.shipping.search') }}';
    let searchTimeout = null;

    const input   = document.getElementById('destinationSearch');
    const results = document.getElementById('destinationResults');

    input.addEventListener('input', function () {
        const keyword = this.value.trim();
        clearTimeout(searchTimeout);

        // Reset pilihan tiap kali user ngetik ulang, biar nggak submit data lama yang udah nggak sesuai teks
        document.getElementById('destinationIdInput').value = '';

        if (keyword.length < 3) {
            results.style.display = 'none';
            results.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`${SEARCH_URL}?q=${encodeURIComponent(keyword)}`)
                .then(r => r.json())
                .then(data => renderResults(data))
                .catch(() => {
                    results.innerHTML = '<div class="list-group-item small text-muted bg-white">Gagal mencari lokasi.</div>';
                    results.style.display = '';
                });
        }, 350);
    });

    function renderResults(data) {
        results.innerHTML = '';

        if (!data || data.length === 0) {
            results.innerHTML = '<div class="list-group-item small text-muted bg-white">Lokasi tidak ditemukan.</div>';
            results.style.display = '';
            return;
        }

        data.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            // Paksa pakai background putih dan border bottom biar makin solid
            btn.className = 'list-group-item list-group-item-action small bg-white border-bottom';
            btn.textContent = item.label || `${item.district_name}, ${item.city_name}, ${item.province_name}`;
            btn.addEventListener('click', () => selectDestination(item));
            results.appendChild(btn);
        });

        results.style.display = '';
    }

    function selectDestination(item) {
        const label = item.label || `${item.district_name}, ${item.city_name}, ${item.province_name}`;

        input.value = label;
        results.style.display = 'none';
        results.innerHTML = '';

        document.getElementById('destinationIdInput').value = item.id;
        document.getElementById('provinceNameInput').value   = item.province_name || '';
        document.getElementById('cityNameInput').value       = item.city_name || '';
        document.getElementById('districtNameInput').value   = item.district_name || '';
        document.getElementById('subdistrictNameInput').value = item.subdistrict_name || '';
    }

    // Tutup dropdown hasil pencarian kalau klik di luar
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.style.display = 'none';
        }
    });
});
</script>