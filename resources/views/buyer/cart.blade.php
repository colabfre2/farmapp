@extends('layouts.buyer')
@section('content')
<style>
    .cart-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .cart-row.item-disabled { opacity: 0.45; }
    .cart-row.item-disabled .qty-input { pointer-events: none; background-color: #f1f1f1; }
    /* 🚀 FIX: pakai !important biar bisa ngalahin .d-flex !important dari Bootstrap
       (kalau cuma inline style="display:none" doang, KALAH sama .d-flex !important) */
    .summary-item.d-none-force { display: none !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        🛒 Keranjang saya
    </h2>
    @if(!empty($cart))
    <form action="{{ route('buyer.cart.clear') }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Kosongkan semua item?')">🗑️ Kosongkan Keranjang</button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success')}}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    ✕ {{ session('error')}}
</div>
@endif

@if(empty($cart))
    <div class="text-center py-5 text-muted">
        <div class="font-size:64px">🛒</div>
        <h4>Keranjang kamu kosong</h4>
        <a href="{{ route('buyer.marketplace') }}" class="btn btn-success mt-3">Jelajahi marketplace</a>

    </div>
    @else

    {{-- Form ini membungkus SELURUH halaman biar checkbox terpilih bisa ikut ke-submit ke checkout --}}
    <form id="checkoutForm" method="POST" action="{{ route('buyer.checkout') }}">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll" class="cart-checkbox">
                                    </th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                <tr class="cart-row" data-id="{{ $id }}" data-price="{{ $item['price'] }}" data-qty="{{ $item['quantity'] }}" data-name="{{ $item['name'] }}">
                                    <td>
                                        <input type="checkbox"
                                               name="selected_ids[]"
                                               value="{{ $id }}"
                                               class="cart-checkbox item-checkbox"
                                               checked>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item['image'])
                                                <img src="{{ '/storage/' . $item['image'] }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                            @else
                                                <div style="width:50px;height:50px;background:#f4f6f8;border-radius:8px;display:flex;align-items:center;justify-content:center;">🌿</div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item['name'] }}</div>
                                                <div class="text-muted small">{{ rupiah($item['price']) }} / {{ $item['unit'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ rupiah($item['price']) }}</td>
                                    <td>
                                        <input type="number"
                                               name="quantity_{{ $id }}"
                                               value="{{ $item['quantity'] }}"
                                               min="1"
                                               max="{{ $item['stock'] }}"
                                               class="form-control form-control-sm qty-input"
                                               style="width:70px;"
                                               data-id="{{ $id }}"
                                               data-update-url="{{ route('buyer.cart.update', $id) }}">
                                    </td>
                                    <td class="fw-bold text-success item-subtotal">{{ rupiah($item['price'] * $item['quantity']) }}</td>
                                    <td>
                                        <button type="submit"
                                                form="removeForm-{{ $id }}"
                                                class="btn btn-sm btn-outline-danger">✕</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan Pesanan</h3>
                    </div>
                    <div class="card-body">
                        <div id="summaryItems">
                            @foreach($cart as $id => $item)
                            <div class="d-flex justify-content-between mb-2 summary-item" data-id="{{ $id }}">
                                <span class="text-muted">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                <span>{{ rupiah($item['price'] * $item['quantity']) }}</span>
                            </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold h4">
                            <span>Total (<span id="selectedCount">{{ count($cart) }}</span> item)</span>
                            <span class="text-success" id="totalDisplay">{{ rupiah($total) }}</span>
                        </div>
                        <button type="submit" id="btnCheckout" class="btn btn-success w-100 mt-3 btn-lg">
                            Checkout →
                        </button>
                        <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Lanjut belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Form terpisah per item khusus hapus item (tidak ikut form checkout) --}}
    @foreach($cart as $id => $item)
    <form id="removeForm-{{ $id }}" method="POST" action="{{ route('buyer.cart.remove', $id) }}" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    @endforeach

@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll        = document.getElementById('selectAll');
    const itemCheckboxes   = document.querySelectorAll('.item-checkbox');
    const totalDisplay     = document.getElementById('totalDisplay');
    const selectedCountEl  = document.getElementById('selectedCount');
    const btnCheckout      = document.getElementById('btnCheckout');
    const summaryItemsWrap = document.getElementById('summaryItems');
    const summaryItemEls   = document.querySelectorAll('.summary-item');

    // Kalau keranjang kosong, elemen-elemen di atas ga ada -> hentikan script di sini
    if (!itemCheckboxes.length || !summaryItemsWrap) return;

    function formatRupiah(angka) {
        return 'Rp' + Math.round(angka).toLocaleString('id-ID');
    }

    // 🚀 FIX UTAMA: recalc() sekarang jadi satu-satunya sumber kebenaran untuk
    // tampilan ringkasan. Setiap kali dipanggil, SEMUA item (checked & unchecked)
    // dievaluasi ulang dari nol, jadi ga akan ada state "nyangkut" di ringkasan.
    //
    // Catatan: sebelumnya kita cari summary-item pasangannya pakai selector
    // `[data-id="..."]`, tapi ternyata di beberapa kasus ga ketemu (mismatch).
    // Sekarang kita pasangkan berdasarkan INDEX/URUTAN aja — karena baris produk
    // (.cart-row) dan baris ringkasan (.summary-item) sama-sama di-generate dari
    // loop foreach cart yang PERSIS sama urutannya, jadi index ke-0 pasti pasangan
    // index ke-0, dst. Ini jauh lebih aman daripada cocokin string id.
    function recalc() {
        let total = 0;
        let count = 0;

        itemCheckboxes.forEach((cb, index) => {
            const row = cb.closest('.cart-row');
            if (!row) return;

            const summaryItem = summaryItemEls[index] || null;
            const qtyInput = row.querySelector('.qty-input');

            if (cb.checked) {
                row.classList.remove('item-disabled');
                if (qtyInput) qtyInput.disabled = false;

                const price = parseFloat(row.dataset.price) || 0;
                const qty   = parseInt(qtyInput?.value || row.dataset.qty, 10) || 0;
                const subtotal = price * qty;

                total += subtotal;
                count++;

                const subtotalCell = row.querySelector('.item-subtotal');
                if (subtotalCell) subtotalCell.textContent = formatRupiah(subtotal);

                if (summaryItem) {
                    summaryItem.classList.remove('d-none-force');
                    const nameSpan = summaryItem.querySelector('span:first-child');
                    const priceSpan = summaryItem.querySelector('span:last-child');
                    if (nameSpan) nameSpan.textContent = row.dataset.name + ' x' + qty;
                    if (priceSpan) priceSpan.textContent = formatRupiah(subtotal);
                }
            } else {
                // Item di-uncheck -> disable input qty-nya & sembunyikan dari ringkasan
                row.classList.add('item-disabled');
                if (qtyInput) qtyInput.disabled = true;
                if (summaryItem) summaryItem.classList.add('d-none-force');
            }
        });

        totalDisplay.textContent = formatRupiah(total);
        selectedCountEl.textContent = count;
        btnCheckout.disabled = count === 0;
        btnCheckout.classList.toggle('disabled', count === 0);
    }

    // Update quantity via AJAX (fetch), TANPA reload halaman —
    // supaya checkbox yang sudah dicentang buyer tidak ter-reset.
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

    document.querySelectorAll('.qty-input').forEach(input => {
        let debounceTimer = null;
        let previousValue = input.value;

        input.addEventListener('change', function () {
            const row = input.closest('.cart-row');
            const newQty = parseInt(input.value || 1, 10);
            const url = input.dataset.updateUrl;

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ quantity: newQty }),
                })
                .then(async (response) => {
                    const data = await response.json().catch(() => null);

                    if (!response.ok) {
                        alert(data?.message || 'Gagal mengubah jumlah. Silakan coba lagi.');
                        input.value = previousValue; // rollback tampilan ke nilai sebelumnya
                        recalc();
                        return;
                    }

                    row.dataset.qty = data.quantity;
                    previousValue = String(data.quantity);
                    recalc();
                })
                .catch(() => {
                    alert('Gagal terhubung ke server. Silakan coba lagi.');
                    input.value = previousValue;
                    recalc();
                });
            }, 400); // debounce dikit biar gak nembak request tiap ketikan
        });

        input.addEventListener('input', recalc); // update tampilan lokal instan, tanpa nunggu server
    });

    selectAll?.addEventListener('change', function () {
        itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        recalc();
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!cb.checked) {
                selectAll.checked = false;
            } else if ([...itemCheckboxes].every(c => c.checked)) {
                selectAll.checked = true;
            }
            recalc(); // 🚀 dipanggil tiap kali checkbox berubah -> ringkasan langsung sinkron
        });
    });

    document.getElementById('checkoutForm')?.addEventListener('submit', function (e) {
        const anyChecked = [...itemCheckboxes].some(cb => cb.checked);
        if (!anyChecked) {
            e.preventDefault();
            alert('Pilih minimal 1 item untuk checkout.');
        }
    });

    // 🚀 SAFETY NET: seharusnya semua item sudah tercentang secara default dari HTML
    // (atribut "checked"). Tapi kalau karena suatu sebab tidak ada satupun yang
    // tercentang saat halaman pertama dimuat, paksa centang semua di sini.
    const anyCheckedOnLoad = [...itemCheckboxes].some(cb => cb.checked);
    if (!anyCheckedOnLoad && itemCheckboxes.length > 0) {
        itemCheckboxes.forEach(cb => cb.checked = true);
        if (selectAll) selectAll.checked = true;
    }

    recalc(); // hitung ulang begitu halaman dimuat, biar ringkasan selalu sesuai state checkbox
});
</script>
@endpush