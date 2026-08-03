@extends('layouts.buyer')
@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4">🛒 Checkout FarmApp</h4>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-4 shadow-sm border-0">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('buyer.checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="row g-4">

            {{-- ── KIRI: Alamat Pengiriman & Kurir ──────────────── --}}
            <div class="col-lg-7">

                {{-- PILIH ALAMAT TERSIMPAN --}}
                <div class="card border-0 shadow-sm rounded-3 mb-3" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">📍 Alamat Pengiriman</h6>
                            <a href="{{ route('buyer.addresses.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-3" target="_blank">+ Tambah Alamat</a>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        @if($addresses->isNotEmpty())
                            <div class="mb-3" id="savedAddressSection">
                                @foreach($addresses as $addr)
                                <div class="form-check border rounded-3 p-3 mb-2 {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'border-success bg-success-subtle bg-opacity-10' : '' }} saved-addr-card"
                                    id="savedCard_{{ $addr->id }}" style="cursor:pointer;" onclick="selectAddress({{ $addr->id }})">
                                    <input class="form-check-input" type="radio" name="address_id"
                                        id="addr_{{ $addr->id }}" value="{{ $addr->id }}"
                                        onchange="onAddressSelected({{ $addr->id }}, '{{ $addr->destination_id }}', '{{ addslashes($addr->district) }}', '{{ addslashes($addr->city) }}', '{{ addslashes($addr->province) }}')"
                                        {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="addr_{{ $addr->id }}" style="cursor:pointer;">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-secondary rounded-pill small">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="badge bg-success-subtle text-success rounded-pill small">Utama</span>
                                            @endif
                                        </div>
                                        <div class="fw-semibold">{{ $addr->recipient_name }} · {{ $addr->phone }}</div>
                                        <div class="text-muted small">{{ $addr->full_address }}</div>
                                    </label>
                                </div>
                                @endforeach

                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"
                                        onclick="toggleManualAddress()">
                                        ✏️ Atau masukkan alamat baru secara manual
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Input Alamat Manual / Autocomplete --}}
                        <div id="manualAddressSection" {{ $addresses->isNotEmpty() ? 'style=display:none;' : '' }}>
                            @if($addresses->isNotEmpty())
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold small text-muted">INPUT ALAMAT BARU</span>
                                    <button type="button" class="btn btn-sm btn-link text-success p-0 text-decoration-none fw-semibold"
                                        onclick="toggleManualAddress()">
                                        ← Kembali ke alamat tersimpan
                                    </button>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_name" id="manualName" class="form-control rounded-3 py-2 manual-input"
                                        value="{{ old('shipping_name', auth()->user()->name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
                                    <input type="tel" name="shipping_phone" id="manualPhone" class="form-control rounded-3 py-2 manual-input"
                                        value="{{ old('shipping_phone') }}">
                                </div>
                                <div class="col-12 position-relative">
                                    <label class="form-label fw-semibold">Kecamatan Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" id="destinationSearch" class="form-control rounded-3 py-2 manual-input"
                                        placeholder="Ketik minimal 3 huruf nama kecamatan (contoh: Curug, Kelapa Dua)..."
                                        autocomplete="off"
                                        value="{{ old('shipping_district') ? old('shipping_district') . ', ' . old('shipping_city') . ', ' . old('province') : '' }}">
                                    
                                    {{-- DROPDOWN HASIL PENCARIAN (Solid Background & Z-Index Tinggi) --}}
                                    <div id="destinationResults" class="list-group position-absolute w-100 shadow-lg rounded-3 border"
                                        style="z-index: 9999; display: none; max-height: 260px; overflow-y: auto; background-color: #ffffff !important;"></div>
                                    
                                    <div class="form-text">Cari dan pilih nama kecamatan dari daftar yang muncul.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat Detail <span class="text-danger">*</span></label>
                                    <textarea name="shipping_address" id="manualAddressDetail" class="form-control rounded-3 manual-input" rows="3"
                                        placeholder="Nama jalan, no. rumah, RT/RW, kelurahan...">{{ old('shipping_address') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="save_address" id="saveAddress" value="1" class="form-check-input manual-input">
                                        <label for="saveAddress" class="form-check-label">Simpan alamat ini ke daftar saya</label>
                                    </div>
                                    <div id="labelGroup" style="display:none;" class="mt-2">
                                        <select name="address_label" class="form-select form-select-sm rounded-3 w-auto manual-input">
                                            <option value="Rumah">Rumah</option>
                                            <option value="Kantor">Kantor</option>
                                            <option value="Kos">Kos</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden Fields untuk Data API Ongkir --}}
                        <input type="hidden" name="destination_id" id="hiddenDestinationId" value="{{ old('destination_id') }}">
                        <input type="hidden" name="province" id="hiddenProvince" value="{{ old('province') }}">
                        <input type="hidden" name="shipping_city" id="hiddenCity" value="{{ old('shipping_city') }}">
                        <input type="hidden" name="shipping_district" id="hiddenDistrict" value="{{ old('shipping_district') }}">

                    </div>
                </div>

                {{-- PILIH KURIR & LAYANAN --}}
                <div class="card border-0 shadow-sm rounded-3 mb-3" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 px-4">
                        <h6 class="fw-bold mb-0">🚚 Pilih Kurir</h6>
                    </div>
                    <div class="card-body p-4">

                        <div class="row g-2 mb-3">
                            @foreach(['jne' => 'JNE', 'jnt' => 'J&T Express', 'sicepat' => 'SiCepat'] as $key => $label)
                            <div class="col-4">
                                <div class="form-check border rounded-3 p-3 text-center courier-card {{ old('courier') === $key ? 'border-primary bg-primary-subtle bg-opacity-10' : '' }}"
                                    id="courierCard_{{ $key }}" style="cursor:pointer;" onclick="selectCourier('{{ $key }}')">
                                    <input type="radio" class="d-none" name="courier" value="{{ $key }}" id="courier_{{ $key }}"
                                        {{ old('courier') === $key ? 'checked' : '' }}>
                                    <div class="fw-bold">{{ $label }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div id="serviceSection" style="display:none;">
                            <div class="mb-2 fw-semibold small text-muted">Layanan Tersedia</div>
                            <div id="serviceList" class="d-flex flex-column gap-2"></div>
                            <input type="hidden" name="courier_service" id="courierServiceInput">
                            <input type="hidden" name="shipping_cost" id="shippingCostInput" value="0">
                        </div>

                        <div id="courierHint" class="text-muted small mt-2">
                            Pilih alamat tujuan terlebih dahulu untuk melihat opsi layanan kurir.
                        </div>

                    </div>
                </div>

                {{-- METODE PEMBAYARAN --}}
                <div class="card border-0 shadow-sm rounded-3" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 px-4">
                        <h6 class="fw-bold mb-0">💳 Metode Pembayaran</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-2">
                            @foreach(['transfer' => '🏦 Transfer Bank', 'cod' => '💵 COD', 'card' => '💳 Kartu'] as $val => $lbl)
                            <div class="col-4">
                                <div class="form-check border rounded-3 p-3 text-center pay-card {{ old('payment_method', 'transfer') === $val ? 'border-primary bg-primary-subtle bg-opacity-10' : '' }}"
                                    id="payCard_{{ $val }}" style="cursor:pointer;" onclick="selectPaymentMethod('{{ $val }}')">
                                    <input type="radio" name="payment_method" value="{{ $val }}" id="payInput_{{ $val }}" class="d-none"
                                        {{ old('payment_method', 'transfer') === $val ? 'checked' : '' }}>
                                    <div class="small fw-semibold">{{ $lbl }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── KANAN: Ringkasan Pesanan ───────────────────── --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 80px; border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 px-4">
                        <h6 class="fw-bold mb-0">🧾 Ringkasan Pesanan</h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Item Cart --}}
                        @foreach($cart as $item)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ $item['name'] }} <span class="text-muted">×{{ $item['quantity'] }}</span></span>
                            <span class="fw-semibold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span id="shippingCostDisplay" class="text-muted">— pilih layanan</span>
                        </div>

                        <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-3">
                            <span>Total Pembayaran</span>
                            <span id="totalDisplay" class="text-success">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill mt-4 py-2 shadow-sm">
                            Buat Pesanan ✓
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
const SUBTOTAL       = {{ $subtotal }};
const SEARCH_URL     = '{{ route("buyer.shipping.search") }}';
const ONGKIR_URL     = '{{ route("buyer.shipping.ongkir") }}';
const CSRF           = '{{ csrf_token() }}';
const DEFAULT_WEIGHT = 1000;

let selectedDestinationId = '';
let selectedCourier       = '';

function formatRupiahDisplay(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function selectAddress(id) {
    document.getElementById('addr_' + id)?.click();
}

function disableManualInputs(isDisabled) {
    document.querySelectorAll('.manual-input').forEach(input => {
        input.disabled = isDisabled;
    });
}

function onAddressSelected(addrId, destinationId, district, city, province) {
    selectedDestinationId = destinationId;

    document.getElementById('hiddenDestinationId').value = destinationId;
    document.getElementById('hiddenProvince').value      = province;
    document.getElementById('hiddenCity').value          = city;
    document.getElementById('hiddenDistrict').value      = district;

    document.querySelectorAll('.saved-addr-card').forEach(card => {
        card.classList.remove('border-success', 'bg-success-subtle', 'bg-opacity-10');
    });
    document.getElementById('savedCard_' + addrId)?.classList.add('border-success', 'bg-success-subtle', 'bg-opacity-10');

    disableManualInputs(true);
    resetServiceSection();
    if (selectedCourier) fetchOngkir(selectedCourier);
}

// Inisialisasi awal saat halaman diload
@if($defaultAddress)
onAddressSelected(
    {{ $defaultAddress->id }},
    '{{ $defaultAddress->destination_id }}',
    '{{ addslashes($defaultAddress->district) }}',
    '{{ addslashes($defaultAddress->city) }}',
    '{{ addslashes($defaultAddress->province) }}'
);
@else
    @if($addresses->isNotEmpty())
        const firstAddrId = {{ $addresses->first()->id }};
        selectAddress(firstAddrId);
    @else
        disableManualInputs(false);
    @endif
@endif

function toggleManualAddress() {
    const manual = document.getElementById('manualAddressSection');
    const saved  = document.getElementById('savedAddressSection');
    const isHidden = manual.style.display === 'none';

    manual.style.display = isHidden ? '' : 'none';
    if (saved) saved.style.display = isHidden ? 'none' : '';

    if (isHidden) {
        document.querySelectorAll('input[name="address_id"]').forEach(r => r.checked = false);
        document.querySelectorAll('.saved-addr-card').forEach(c => c.classList.remove('border-success', 'bg-success-subtle', 'bg-opacity-10'));
        
        selectedDestinationId = '';
        document.getElementById('hiddenDestinationId').value = '';
        document.getElementById('hiddenProvince').value      = '';
        document.getElementById('hiddenCity').value          = '';
        document.getElementById('hiddenDistrict').value      = '';
        
        disableManualInputs(false);
        resetServiceSection();
    }
}

// Autocomplete Pencarian Tujuan
let destSearchTimeout = null;
const destInput   = document.getElementById('destinationSearch');
const destResults = document.getElementById('destinationResults');

destInput?.addEventListener('input', function () {
    const keyword = this.value.trim();
    clearTimeout(destSearchTimeout);

    document.getElementById('hiddenDestinationId').value = '';
    selectedDestinationId = '';
    resetServiceSection();

    if (keyword.length < 3) {
        destResults.style.display = 'none';
        destResults.innerHTML = '';
        return;
    }

    destSearchTimeout = setTimeout(() => {
        fetch(`${SEARCH_URL}?q=${encodeURIComponent(keyword)}`)
            .then(r => r.json())
            .then(results => renderDestinationResults(results))
            .catch(() => {
                destResults.innerHTML = '<div class="list-group-item small text-muted bg-white">Gagal mencari lokasi.</div>';
                destResults.style.display = '';
            });
    }, 350);
});

function renderDestinationResults(results) {
    destResults.innerHTML = '';
    destResults.style.backgroundColor = '#ffffff';

    if (!results || results.length === 0) {
        destResults.innerHTML = '<div class="list-group-item small text-muted bg-white">Lokasi tidak ditemukan. Ketik nama <b>Kecamatan</b>.</div>';
        destResults.style.display = '';
        return;
    }

    results.forEach(item => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'list-group-item list-group-item-action small bg-white border-bottom';
        
        const districtName = item.district_name || item.subdistrict_name || '';
        const cityName = item.city_name || '';
        const provinceName = item.province_name || '';
        const zipCode = item.zip_code ? ` (${item.zip_code})` : ''; 
        
        btn.innerHTML = `<strong>${districtName}</strong>, ${cityName}, ${provinceName}${zipCode}`;
        btn.addEventListener('click', () => selectDestination(item));
        destResults.appendChild(btn);
    });

    destResults.style.display = '';
}

function selectDestination(item) {
    const district = item.district_name || item.subdistrict_name || '';
    const city = item.city_name || '';
    const province = item.province_name || '';
    const zipCode = item.zip_code ? ` (${item.zip_code})` : '';
    
    destInput.value = `${district}, ${city}, ${province}${zipCode}`;
    destResults.style.display = 'none';
    destResults.innerHTML = '';

    document.getElementById('hiddenDestinationId').value = item.id;
    document.getElementById('hiddenProvince').value      = province;
    document.getElementById('hiddenCity').value          = city;
    document.getElementById('hiddenDistrict').value      = district;

    selectedDestinationId = item.id;

    resetServiceSection();
    if (selectedCourier) fetchOngkir(selectedCourier);
}

document.addEventListener('click', function (e) {
    if (destInput && !destInput.contains(e.target) && !destResults.contains(e.target)) {
        destResults.style.display = 'none';
    }
});

function selectCourier(courier) {
    selectedCourier = courier;

    document.querySelectorAll('.courier-card').forEach(el => el.classList.remove('border-primary', 'bg-primary-subtle', 'bg-opacity-10'));
    document.getElementById('courierCard_' + courier)?.classList.add('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
    document.getElementById('courier_' + courier).checked = true;

    resetServiceSection();

    if (!selectedDestinationId) {
        document.getElementById('courierHint').textContent = '⚠️ Pilih alamat tujuan terlebih dahulu!';
        return;
    }

    fetchOngkir(courier);
}

function selectPaymentMethod(methodVal) {
    document.querySelectorAll('.pay-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
    });

    const radioInput = document.getElementById('payInput_' + methodVal);
    if (radioInput) radioInput.checked = true;

    const selectedCard = document.getElementById('payCard_' + methodVal);
    if (selectedCard) {
        selectedCard.classList.add('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
    }
}

function fetchOngkir(courier) {
    const hint = document.getElementById('courierHint');
    hint.textContent = 'Menghitung ongkos kirim...';
    document.getElementById('serviceSection').style.display = 'none';

    fetch(ONGKIR_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            destination_id: selectedDestinationId, 
            weight: DEFAULT_WEIGHT, 
            courier: courier 
        }),
    })
    .then(r => r.json())
    .then(costs => {
        hint.textContent = '';
        if (!costs || costs.length === 0) {
            hint.textContent = 'Layanan kurir tidak tersedia untuk tujuan ini.';
            return;
        }
        renderServices(costs);
    })
    .catch(error => { 
        console.error("Error Fetch Ongkir:", error);
        hint.textContent = 'Gagal menghitung ongkos kirim. Coba lagi.'; 
    });
}

function renderServices(costs) {
    const list = document.getElementById('serviceList');
    list.innerHTML = '';

    if (!Array.isArray(costs)) {
        document.getElementById('courierHint').textContent = 'Format data ongkir dari server tidak valid.';
        return;
    }

    costs.forEach((svc, idx) => {
        const serviceKey = `${svc.service}_${idx}`;
        const div = document.createElement('div');
        div.className = 'form-check border rounded-3 p-3';
        div.style.cursor = 'pointer';
        div.innerHTML = `
            <input type="radio" name="_service_radio" class="form-check-input" id="svc_${serviceKey}"
                onchange="onServiceSelected('${svc.service}', ${svc.cost})">
            <label class="form-check-label w-100" for="svc_${serviceKey}" style="cursor:pointer;">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">${svc.service} <small class="text-muted fw-normal">${svc.description ?? ''}</small></span>
                    <span class="fw-bold text-success">${formatRupiahDisplay(svc.cost)}</span>
                </div>
                <div class="text-muted small">Estimasi ${svc.etd || '-'} hari</div>
            </label>`;
        list.appendChild(div);
    });

    document.getElementById('serviceSection').style.display = '';
}

function onServiceSelected(service, cost) {
    document.getElementById('courierServiceInput').value = service;
    document.getElementById('shippingCostInput').value   = cost;

    document.getElementById('shippingCostDisplay').textContent = formatRupiahDisplay(cost);
    document.getElementById('shippingCostDisplay').className   = 'fw-semibold text-dark';

    const total = SUBTOTAL + cost;
    document.getElementById('totalDisplay').textContent = formatRupiahDisplay(total);
}

function resetServiceSection() {
    document.getElementById('serviceSection').style.display = 'none';
    document.getElementById('serviceList').innerHTML = '';
    document.getElementById('courierServiceInput').value = '';
    document.getElementById('shippingCostInput').value   = '0';
    document.getElementById('shippingCostDisplay').textContent = '— pilih layanan';
    document.getElementById('shippingCostDisplay').className   = 'text-muted';
    document.getElementById('totalDisplay').textContent = formatRupiahDisplay(SUBTOTAL);
    document.getElementById('courierHint').textContent = '';
}

document.getElementById('saveAddress')?.addEventListener('change', function () {
    document.getElementById('labelGroup').style.display = this.checked ? '' : 'none';
});
</script>
@endsection