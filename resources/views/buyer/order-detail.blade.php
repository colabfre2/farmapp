@extends('layouts.buyer')

@section('title', 'Detail Pesanan')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<div class="container-fluid py-4" style="max-width: 1100px;">
    
    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 d-flex align-items-center rounded-3 shadow-sm">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 font-quicksand text-dark">📋 Detail Pesanan</h2>
        <a href="{{ route('buyer.orders') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">← Kembali ke Pesanan</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Order Items --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">🧾 Item Pesanan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th class="pe-4 text-end">Subtotal</th>
                                    @if($order->status === 'Completed')
                                    <th>Ulasan</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $item->product_name }}</td>
                                    <td>{{ rupiah($item->unit_price) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="pe-4 text-end fw-bold text-success">{{ rupiah($item->subtotal) }}</td>
                                    @if($order->status === 'Completed')
                                    <td>
                                        <a href="{{ route('buyer.reviews.create', [$order, $item->product_id]) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">⭐ Beri Ulasan</a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div class="card card-flat">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📦 Informasi Pengiriman</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Nama Penerima</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Nomor Telepon</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kota / Wilayah</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_city ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Alamat Lengkap</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_address }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Order Info & Payment Card --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📋 Ringkasan Pesanan</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="text-muted small">No. Pesanan</div>
                        <div class="fw-bold text-dark">{{ $order->order_number }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Tanggal Pemesanan</div>
                        <div class="fw-bold text-dark">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Metode Pembayaran</div>
                        <div class="fw-bold text-dark text-uppercase">{{ $order->payment_method }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Status Pesanan</div>
                        <span class="badge rounded-pill px-3 py-2 fw-bold
                            @if($order->status == 'Pending') bg-warning-subtle text-warning
                            @elseif($order->status == 'Processing') bg-primary-subtle text-primary
                            @elseif($order->status == 'Shipped') bg-info-subtle text-info
                            @elseif($order->status == 'Completed') bg-success-subtle text-success
                            @else bg-danger-subtle text-danger
                            @endif">
                            {{ $order->status }}
                        </span>
                    </div>

                    {{-- STATUS PEMBAYARAN & TOMBOL MIDTRANS --}}
                    @if($order->payment_method === 'midtrans')
                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Status Pembayaran Midtrans</div>
                        @if($order->payment_status == 'success' || $order->payment_status == 'settlement')
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">Lunas / Berhasil ✓</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-bold">Menunggu Pembayaran ⏳</span>
                            <div class="mt-3">
                                {{-- WAJIB PAKE type="button" BIAR GAK AUTO SUBMIT --}}
                                <button type="button" id="pay-button" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm py-2">
                                    💳 Bayar Sekarang via Midtrans
                                </button>
                            </div>
                        @else
                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">Gagal / Dibatalkan ✕</span>
                        @endif
                    </div>
                    @endif

                    <hr class="text-muted opacity-25">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span class="fw-bold text-dark">{{ rupiah($order->shipping_cost ?? 0) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center fw-bold h4 mb-0">
                        <span>Total</span>
                        <span class="text-success font-quicksand">{{ rupiah($order->total_amount) }}</span>
                    </div>

                    @if($order->status === 'Pending' && ($order->payment_status !== 'success' && $order->payment_status !== 'settlement'))
                    <form method="POST" action="{{ route('buyer.orders.cancel', $order) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger rounded-pill w-100 fw-bold" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                            ✕ Batalkan Pesanan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT MIDTRANS SNAP JS (Hanya dipanggil jika metode midtrans dan pending) --}}
@if($order->payment_method === 'midtrans' && $order->payment_status == 'pending')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button')?.addEventListener('click', function (e) {
        // Matikan fungsi bawaan browser biar nggak ke-submit otomatis
        e.preventDefault();

        fetch("{{ route('buyer.payment.snap-token', $order->id) }}")
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            alert("Pembayaran berhasil!");
                            location.reload();
                        },
                        onPending: function(result) {
                            alert("Menunggu pembayaran diselesaikan!");
                            location.reload();
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                            console.log(result);
                        },
                        onClose: function() {
                            alert('Kamu menutup popup pembayaran sebelum selesai.');
                        }
                    });
                } else {
                    // Panggil data.message biar alertnya nampilin alasan yang bener
                    alert('Gagal mendapatkan token pembayaran: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan pada server.');
            });
    });
</script>
@endif

@endsection