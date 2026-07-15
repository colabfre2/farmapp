@extends('layouts.buyer')

@section('content')

<h2 class="fw-bold mb-4">Checkout</h2>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('buyer.checkout.store') }}" id="checkoutForm">
    @csrf
    <div class="row">
        {{-- Shipping Info --}}
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">📦 Informasi pengiriman</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama lengkap</label>
                        <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror" value="{{ old('shipping_name', auth()->user()->name) }}" required>
                        @error('shipping_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Telepon</label>
                        <input type="text" name="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror" value="{{ old('shipping_phone', auth()->user()->phone) }}" required>
                        @error('shipping_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="2" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Provinsi</label>
                            <select name="province_id" id="provinceSelect" class="form-select" required>
                                <option value="">-- Pilih provinsi --</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Kota / Kabupaten</label>
                            <select name="shipping_city" id="citySelect" class="form-select" required>
                                <option value="">-- Pilih kota / Kabupaten --</option>
                            </select>
                            <input type="hidden" name="city_id" id="cityId">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shipping Cost --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">🚚 Ongkos kirim</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Kurir</label>
                            <select id="courierSelect" class="form-select">
                                <option value="jne">JNE</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="tiki">TIKI</option>
                            </select>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <button type="button" id="checkOngkirBtn" class="btn btn-outline-success w-100">
                                🔍 Cek ongkir
                            </button>
                        </div>
                    </div>

                    <div id="ongkirResult" style="display:none;">
                        <label class="form-label fw-bold">Pilih layanan</label>
                        <div id="ongkirOptions"></div>
                        <input type="hidden" name="shipping_cost" id="shippingCost" value="0">
                        <input type="hidden" name="courier" id="courierHidden">
                        <input type="hidden" name="courier_service" id="courierService">
                    </div>

                    <div id="ongkirLoading" style="display:none;" class="text-center py-3">
                        <div class="spinner-border text-success" role="status"></div>
                        <div class="text-muted mt-2">Mengecek ongkir...</div>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">💳 Metode pembayaran</h3>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                        <label class="form-check-label" for="cod">Bayar di tempat (COD)</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="transfer">
                        <label class="form-check-label" for="transfer">Transfer Bank</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="card" id="card">
                        <label class="form-check-label" for="card">Kartu kredit/Debit</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🧾 Ringkasan pesanan</h3>
                </div>
                <div class="card-body">
                    @foreach($cart as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                        <span>{{ rupiah($item['price'] * $item['quantity']) }}</span>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>{{ rupiah($total) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkir</span>
                        <span id="shippingDisplay">-</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h4">
                        <span>Total</span>
                        <span class="text-success" id="grandTotal">{{ rupiah($total) }}</span>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-3 btn-lg">
                        Buat pesanan ✓
                    </button>
                    <a href="{{ route('buyer.cart') }}" class="btn btn-outline-secondary w-100 mt-2">
                        ← Kembali ke keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    window.checkoutData = {
        subtotal: {{ $total }},
        provincesUrl: '{{ route('buyer.shipping.provinces') }}',
        citiesUrl: '{{ url('buyer/shipping/cities') }}',
        ongkirUrl: '{{ route('buyer.shipping.ongkir') }}',
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('js/checkout.js') }}"></script>

@endsection