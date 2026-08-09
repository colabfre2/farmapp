@extends('layouts.buyer')
@section('title', 'Checkout')

@section('content')
<style>
    /* Styling tambahan untuk kesan clean & natural */
    .checkout-container { max-width: 1140px; }
    .form-control, .form-select {
        background-color: #f8f9fa;
        border: 1px solid transparent;
        transition: all 0.2s ease-in-out;
    }
    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
    }
    .selection-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e9ecef;
    }
    .selection-card:hover {
        border-color: #198754;
        background-color: #f8fff9;
    }
    .active-card {
        border-color: #198754 !important;
        background-color: rgba(25, 135, 84, 0.08) !important;
    }
    .section-title-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        background: #e8f5e9; color: #198754;
        border-radius: 8px; margin-right: 12px;
    }
    .sticky-summary { top: 90px; z-index: 10; }
</style>

<div class="container-fluid py-5 checkout-container">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <h2 class="fw-bold mb-0 text-dark font-quicksand">🛒 Checkout Pesanan</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger bg-danger-subtle text-danger rounded-4 mb-4 shadow-sm border-0 d-flex align-items-start gap-3 p-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li class="mb-1">{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('buyer.checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="row g-4 g-lg-5">

            {{-- ── KIRI: Informasi Pengiriman, Kurir & Pembayaran ──────────────── --}}
            <div class="col-lg-7">

                {{-- 1. ALAMAT PENGIRIMAN --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-semibold mb-0 text-dark d-flex align-items-center">
                                <span class="section-title-icon fs-5">📍</span>
                                Alamat Pengiriman
                            </h5>
                            <a href="{{ route('buyer.addresses.create') }}" class="btn btn-sm btn-light text-success fw-medium rounded-pill px-3" target="_blank">
                                + Tambah Baru
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        @if($addresses->isNotEmpty())
                            <div id="savedAddressSection">
                                <div class="d-flex flex-column gap-3">
                                    @foreach($addresses as $addr)
                                    <div class="selection-card rounded-4 p-3 saved-addr-card {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'active-card' : '' }}"
                                        id="savedCard_{{ $addr->id }}" style="cursor:pointer;" onclick="selectAddress({{ $addr->id }})">
                                        
                                        <div class="d-flex gap-3">
                                            <div class="pt-1">
                                                <input class="form-check-input mt-0" type="radio" name="address_id"
                                                    id="addr_{{ $addr->id }}" value="{{ $addr->id }}"
                                                    onchange="onAddressSelected({{ $addr->id }}, '{{ $addr->destination_id }}', '{{ addslashes($addr->district) }}', '{{ addslashes($addr->city) }}', '{{ addslashes($addr->province) }}')"
                                                    {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'checked' : '' }}>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-light text-dark border">{{ $addr->label }}</span>
                                                    @if($addr->is_default)
                                                        <span class="badge bg-success text-white">Utama</span>
                                                    @endif
                                                </div>
                                                <div class="fw-bold text-dark">{{ $addr->recipient_name }} <span class="fw-normal text-muted px-1">|</span> {{ $addr->phone }}</div>
                                                <div class="text-secondary mt-1 small" style="line-height: 1.5;">{{ $addr->full_address }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="button" class="btn btn-link text-decoration-none text-muted fw-medium small" onclick="toggleManualAddress()">
                                        Tidak menemukan alamat? <span class="text-success">Input manual disini</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Input Alamat Manual --}}
                        <div id="manualAddressSection" class="bg-light p-4 rounded-4 mt-2" {{ $addresses->isNotEmpty() ? 'style=display:none;' : '' }}>
                            @if($addresses->isNotEmpty())
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                    <span class="fw-bold text-dark fs-6">Input Alamat Baru</span>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="toggleManualAddress()"></button>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small fw-semibold mb-1">Nama Penerima <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_name" id="manualName" class="form-control rounded-3 py-2.5 manual-input" value="{{ old('shipping_name', auth()->user()->name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small fw-semibold mb-1">No. HP <span class="text-danger">*</span></label>
                                    <input type="tel" name="shipping_phone" id="manualPhone" class="form-control rounded-3 py-2.5 manual-input" value="{{ old('shipping_phone') }}">
                                </div>
                                <div class="col-12 position-relative">
                                    <label class="form-label text-secondary small fw-semibold mb-1">Kecamatan / Kota <span class="text-danger">*</span></label>
                                    <input type="text" id="destinationSearch" class="form-control rounded-3 py-2.5 manual-input"
                                        placeholder="Ketik min. 3 huruf nama kecamatan..." autocomplete="off"
                                        value="{{ old('shipping_district') ? old('shipping_district') . ', ' . old('shipping_city') . ', ' . old('province') : '' }}">
                                    
                                    <div id="destinationResults" class="list-group position-absolute w-100 shadow rounded-3 border-0 mt-1"
                                        style="z-index: 9999; display: none; max-height: 250px; overflow-y: auto;"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-secondary small fw-semibold mb-1">Detail Alamat <span class="text-danger">*</span></label>
                                    <textarea name="shipping_address" id="manualAddressDetail" class="form-control rounded-3 py-2.5 manual-input" rows="3"
                                        placeholder="Contoh: Jl. Merdeka No.1, RT 01/02, Patokan warna cat rumah hijau.">{{ old('shipping_address') }}</textarea>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input manual-input" type="checkbox" role="switch" name="save_address" id="saveAddress" value="1">
                                        <label class="form-check-label text-dark" for="saveAddress">Simpan alamat untuk belanja berikutnya</label>
                                    </div>
                                    <div id="labelGroup" style="display:none;" class="mt-3 bg-white p-3 border rounded-3">
                                        <label class="form-label text-secondary small fw-semibold mb-1">Beri Label Alamat</label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach(['Rumah', 'Kantor', 'Kos', 'Lainnya'] as $lbl)
                                                <input type="radio" class="btn-check manual-input" name="address_label" id="lbl_{{ $lbl }}" value="{{ $lbl }}" {{ $loop->first ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm rounded-pill px-3" for="lbl_{{ $lbl }}">{{ $lbl }}</label>
                                            @endforeach
                                        </div>
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

                {{-- 2. PILIHAN KURIR --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-semibold mb-0 text-dark d-flex align-items-center">
                            <span class="section-title-icon fs-5">🚚</span>
                            Jasa Pengiriman
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            @foreach(['jne' => 'JNE', 'jnt' => 'J&T Express', 'sicepat' => 'SiCepat'] as $key => $label)
                            <div class="col-4">
                                <div class="selection-card h-100 rounded-3 p-3 text-center courier-card {{ old('courier') === $key ? 'active-card' : '' }}"
                                    id="courierCard_{{ $key }}" style="cursor:pointer;" onclick="selectCourier('{{ $key }}')">
                                    <input type="radio" class="d-none" name="courier" value="{{ $key }}" id="courier_{{ $key }}" {{ old('courier') === $key ? 'checked' : '' }}>
                                    <div class="fw-bold text-dark fs-6">{{ $label }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div id="serviceSection" class="p-3 bg-light rounded-4" style="display:none;">
                            <div class="fw-semibold text-dark mb-3 small text-uppercase">Pilih Layanan</div>
                            <div id="serviceList" class="d-flex flex-column gap-2"></div>
                            <input type="hidden" name="courier_service" id="courierServiceInput">
                            <input type="hidden" name="shipping_cost" id="shippingCostInput" value="0">
                        </div>

                        <div id="courierHint" class="text-secondary bg-light p-3 rounded-3 text-center small mt-2">
                            Pilih alamat tujuan terlebih dahulu untuk melihat ongkos kirim.
                        </div>
                    </div>
                </div>

                {{-- 3. METODE PEMBAYARAN --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-semibold mb-0 text-dark d-flex align-items-center">
                            <span class="section-title-icon fs-5">💳</span>
                            Metode Pembayaran
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @foreach([
                                'midtrans' => ['label' => 'Bayar Otomatis', 'icon' => '💳', 'desc' => 'QRIS, Transfer VA, E-Wallet'],
                                'transfer' => ['label' => 'Transfer Manual', 'icon' => '🏦', 'desc' => 'Verifikasi bukti manual'],
                                'cod'      => ['label' => 'Bayar di Tempat', 'icon' => '💵', 'desc' => 'Bayar ke kurir (COD)']
                            ] as $val => $info)
                            <div class="col-md-4">
                                <div class="selection-card rounded-4 p-3 h-100 text-center pay-card {{ old('payment_method', 'midtrans') === $val ? 'active-card' : '' }}"
                                    id="payCard_{{ $val }}" style="cursor:pointer;" onclick="selectPaymentMethod('{{ $val }}')">
                                    <input type="radio" name="payment_method" value="{{ $val }}" id="payInput_{{ $val }}" class="d-none"
                                        {{ old('payment_method', 'midtrans') === $val ? 'checked' : '' }}>
                                    <div class="fs-4 mb-2">{{ $info['icon'] }}</div>
                                    <div class="fw-bold text-dark small mb-1">{{ $info['label'] }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem; line-height: 1.3;">{{ $info['desc'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── KANAN: Ringkasan Pesanan ───────────────────── --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top sticky-summary">
                    <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                        <h5 class="fw-bold mb-0 font-quicksand text-dark">🧾 Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body p-4">

                        <div class="d-flex flex-column gap-3 mb-4 max-h-300 overflow-auto">
                            @foreach($cart as $item)
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-medium text-dark">{{ $item['name'] }}</div>
                                    <div class="text-secondary small">{{ $item['quantity'] }} Barang</div>
                                </div>
                                <div class="fw-semibold text-dark text-end">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="bg-light p-3 rounded-4 mb-4">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-secondary">Subtotal Barang</span>
                                <span class="text-dark fw-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-secondary">Ongkos Kirim</span>
                                <span id="shippingCostDisplay" class="text-secondary">—</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-dark fs-5">Total Bayar</span>
                            <span id="totalDisplay" class="fs-4 fw-bold text-success font-quicksand">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill py-3 px-4 shadow-sm fs-5 d-flex justify-content-center align-items-center gap-2">
                            Buat Pesanan & Bayar ✓
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // Konfigurasi Variabel
    const SUBTOTAL       = {{ $subtotal }};
    const SEARCH_URL     = '{{ route("buyer.shipping.search") }}';
    const ONGKIR_URL     = '{{ route("buyer.shipping.ongkir") }}';
    const CSRF           = '{{ csrf_token() }}';
    const DEFAULT_WEIGHT = 1000;
    
    let selectedDestinationId = '';
    let selectedCourier       = '';
    
    // Utilities
    function formatRupiahDisplay(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }
    
    // Address Management
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
            card.classList.remove('active-card');
        });
        document.getElementById('savedCard_' + addrId)?.classList.add('active-card');
    
        disableManualInputs(true);
        resetServiceSection();
        if (selectedCourier) fetchOngkir(selectedCourier);
    }
    
    // Initialize Default Address
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
    
        manual.style.display = isHidden ? 'block' : 'none';
        if (saved) saved.style.display = isHidden ? 'none' : 'block';
    
        if (isHidden) {
            document.querySelectorAll('input[name="address_id"]').forEach(r => r.checked = false);
            document.querySelectorAll('.saved-addr-card').forEach(c => c.classList.remove('active-card'));
            
            selectedDestinationId = '';
            document.getElementById('hiddenDestinationId').value = '';
            document.getElementById('hiddenProvince').value      = '';
            document.getElementById('hiddenCity').value          = '';
            document.getElementById('hiddenDistrict').value      = '';
            
            disableManualInputs(false);
            resetServiceSection();
        }
    }
    
    // Location Autocomplete
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
                    destResults.innerHTML = '<div class="list-group-item small text-muted bg-white p-3">Gagal mencari lokasi.</div>';
                    destResults.style.display = 'block';
                });
        }, 350);
    });
    
    function renderDestinationResults(results) {
        destResults.innerHTML = '';
    
        if (!results || results.length === 0) {
            destResults.innerHTML = '<div class="list-group-item small text-muted bg-white p-3">Lokasi tidak ditemukan. Coba gunakan nama <b>Kecamatan</b>.</div>';
            destResults.style.display = 'block';
            return;
        }
    
        results.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action bg-white border-bottom p-3';
            
            const districtName = item.district_name || item.subdistrict_name || '';
            const cityName = item.city_name || '';
            const provinceName = item.province_name || '';
            const zipCode = item.zip_code ? ` (${item.zip_code})` : ''; 
            
            btn.innerHTML = `<div class="fw-semibold text-dark">${districtName}</div><div class="text-secondary small mt-1">${cityName}, ${provinceName}${zipCode}</div>`;
            btn.addEventListener('click', () => selectDestination(item));
            destResults.appendChild(btn);
        });
    
        destResults.style.display = 'block';
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
    
    // Courier Management
    function selectCourier(courier) {
        selectedCourier = courier;
    
        document.querySelectorAll('.courier-card').forEach(el => el.classList.remove('active-card'));
        document.getElementById('courierCard_' + courier)?.classList.add('active-card');
        document.getElementById('courier_' + courier).checked = true;
    
        resetServiceSection();
    
        if (!selectedDestinationId) {
            document.getElementById('courierHint').style.display = 'block';
            document.getElementById('courierHint').innerHTML = '⚠️ Pilih alamat tujuan terlebih dahulu!';
            return;
        }
    
        fetchOngkir(courier);
    }
    
    function fetchOngkir(courier) {
        const hint = document.getElementById('courierHint');
        hint.style.display = 'block';
        hint.innerHTML = '<span class="spinner-border spinner-border-sm text-success me-2" role="status" aria-hidden="true"></span> Mencari layanan kurir...';
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
            hint.style.display = 'none';
            if (!costs || costs.length === 0) {
                hint.style.display = 'block';
                hint.textContent = 'Layanan kurir tidak tersedia untuk tujuan ini.';
                return;
            }
            renderServices(costs);
        })
        .catch(error => { 
            console.error("Error Fetch Ongkir:", error);
            hint.style.display = 'block';
            hint.textContent = 'Gagal mengambil data ongkos kirim. Coba lagi.'; 
        });
    }
    
    function renderServices(costs) {
        const list = document.getElementById('serviceList');
        list.innerHTML = '';
    
        if (!Array.isArray(costs)) {
            document.getElementById('courierHint').style.display = 'block';
            document.getElementById('courierHint').textContent = 'Format data ongkir dari server tidak valid.';
            return;
        }
    
        costs.forEach((svc, idx) => {
            const serviceKey = `${svc.service}_${idx}`;
            const div = document.createElement('div');
            div.className = 'selection-card bg-white rounded-3 p-3';
            div.style.cursor = 'pointer';
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <input type="radio" name="_service_radio" class="form-check-input mt-0 me-3" id="svc_${serviceKey}"
                        onchange="onServiceSelected('${svc.service}', ${svc.cost})">
                    <label class="form-check-label w-100" for="svc_${serviceKey}" style="cursor:pointer;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">${svc.service} <small class="text-secondary fw-normal px-1">${svc.description ?? ''}</small></span>
                            <span class="fw-bold text-success">${formatRupiahDisplay(svc.cost)}</span>
                        </div>
                        <div class="text-secondary small">Estimasi sampai ${svc.etd || '-'} hari</div>
                    </label>
                </div>`;
            
            // Add click listener to the whole div to trigger radio
            div.addEventListener('click', () => {
                const radio = document.getElementById(`svc_${serviceKey}`);
                if(radio && !radio.checked) {
                    radio.checked = true;
                    onServiceSelected(svc.service, svc.cost);
                    
                    // Style update for active service
                    document.querySelectorAll('#serviceList .selection-card').forEach(el => el.classList.remove('active-card'));
                    div.classList.add('active-card');
                }
            });
            list.appendChild(div);
        });
    
        document.getElementById('serviceSection').style.display = 'block';
    }
    
    function onServiceSelected(service, cost) {
        document.getElementById('courierServiceInput').value = service;
        document.getElementById('shippingCostInput').value   = cost;
    
        document.getElementById('shippingCostDisplay').textContent = formatRupiahDisplay(cost);
        document.getElementById('shippingCostDisplay').className   = 'text-dark fw-bold';
    
        const total = SUBTOTAL + cost;
        document.getElementById('totalDisplay').textContent = formatRupiahDisplay(total);
    }
    
    function resetServiceSection() {
        document.getElementById('serviceSection').style.display = 'none';
        document.getElementById('serviceList').innerHTML = '';
        document.getElementById('courierServiceInput').value = '';
        document.getElementById('shippingCostInput').value   = '0';
        
        document.getElementById('shippingCostDisplay').textContent = '—';
        document.getElementById('shippingCostDisplay').className   = 'text-secondary';
        
        document.getElementById('totalDisplay').textContent = formatRupiahDisplay(SUBTOTAL);
        document.getElementById('courierHint').style.display = 'block';
        document.getElementById('courierHint').textContent = 'Pilih layanan kurir di atas.';
    } 
    
    // Payment Management
    function selectPaymentMethod(methodVal) {
        document.querySelectorAll('.pay-card').forEach(card => {
            card.classList.remove('active-card');
        });
    
        const radioInput = document.getElementById('payInput_' + methodVal);
        if (radioInput) radioInput.checked = true;
    
        const selectedCard = document.getElementById('payCard_' + methodVal);
        if (selectedCard) {
            selectedCard.classList.add('active-card');
        }
    }
    
    // Save Address Toggle
    document.getElementById('saveAddress')?.addEventListener('change', function () {
        document.getElementById('labelGroup').style.display = this.checked ? 'block' : 'none';
    });
</script>
@endsection