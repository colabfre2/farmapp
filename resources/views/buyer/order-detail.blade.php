@extends('layouts.buyer')
@section('title', 'Detail Pesanan')

@section('content')
<style>
    /* Styling khusus untuk Timeline Status ala Shopee */
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 24px;
        left: 0;
        width: 100%;
        height: 4px;
        background-color: #e9ecef;
        z-index: 1;
        border-radius: 4px;
    }
    .timeline-steps .progress-bar-fill {
        position: absolute;
        top: 24px;
        left: 0;
        height: 4px;
        background-color: #198754;
        z-index: 2;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    .step-item {
        position: relative;
        z-index: 3;
        text-align: center;
        width: 25%;
    }
    .step-icon {
        width: 48px;
        height: 48px;
        background-color: #e9ecef;
        color: #adb5bd;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 8px;
        border: 4px solid #ffffff;
        transition: all 0.3s ease;
    }
    .step-item.active .step-icon, 
    .step-item.completed .step-icon {
        background-color: #198754;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.2);
    }
    .step-item.cancelled .step-icon {
        background-color: #dc3545;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2);
    }
</style>

<div class="container-fluid py-4" style="max-width: 1100px;">
    
    @if(session('success'))
        <div class="alert alert-success bg-white text-success border-0 fw-medium mb-4 d-flex align-items-center rounded-4 shadow-sm py-3 px-4">
            <span class="fs-4 me-3">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-white text-danger border-0 fw-medium mb-4 d-flex align-items-center rounded-4 shadow-sm py-3 px-4">
            <span class="fs-4 me-3">❌</span> {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">📋 Detail Pesanan</h3>
            <p class="text-secondary small mb-0">No. Invoice: <span class="fw-bold text-success">#{{ $order->order_number }}</span></p>
        </div>
        <a href="{{ route('buyer.orders') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold shadow-sm">
            ← Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4 pt-5">
            @php
                $progress = 0;
                if($order->status == 'Processing') $progress = 33.33;
                elseif($order->status == 'Shipped') $progress = 66.66;
                elseif($order->status == 'Completed') $progress = 100;
                elseif($order->status == 'Cancelled') $progress = 0;
            @endphp
            
            <div class="timeline-steps">
                @if($order->status !== 'Cancelled')
                    <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                @endif
                
                <div class="step-item {{ in_array($order->status, ['Pending', 'Processing', 'Shipped', 'Completed']) ? 'completed' : '' }}">
                    <div class="step-icon">💳</div>
                    <div class="fw-bold text-dark small mt-2">Belum Bayar</div>
                </div>
                
                <div class="step-item {{ in_array($order->status, ['Processing', 'Shipped', 'Completed']) ? 'completed' : '' }}">
                    <div class="step-icon">📦</div>
                    <div class="fw-bold text-dark small mt-2">Diproses</div>
                </div>
                
                <div class="step-item {{ in_array($order->status, ['Shipped', 'Completed']) ? 'completed' : '' }}">
                    <div class="step-icon">🚚</div>
                    <div class="fw-bold text-dark small mt-2">Dikirim</div>
                </div>
                
                @if($order->status == 'Cancelled')
                <div class="step-item cancelled">
                    <div class="step-icon">❌</div>
                    <div class="fw-bold text-danger small mt-2">Dibatalkan</div>
                </div>
                @else
                <div class="step-item {{ $order->status == 'Completed' ? 'completed' : '' }}">
                    <div class="step-icon">✨</div>
                    <div class="fw-bold text-dark small mt-2">Selesai</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">🧾 Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="white-space: nowrap;">
                            <thead class="bg-light text-secondary uppercase tracking-wider" style="font-size: 13px;">
                                <tr>
                                    <th class="py-3 ps-4 border-0 fw-semibold">Produk</th>
                                    <th class="py-3 border-0 fw-semibold">Harga</th>
                                    <th class="py-3 border-0 fw-semibold">Jumlah</th>
                                    <th class="py-3 pe-4 border-0 text-end fw-semibold">Subtotal</th>
                                    @if($order->status === 'Completed')
                                    <th class="py-3 pe-4 border-0 text-center fw-semibold">Ulasan</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="border-top-0" style="font-size: 14px;">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 py-3 fw-bold text-dark">{{ $item->product_name }}</td>
                                    <td class="py-3 text-secondary">{{ rupiah($item->unit_price) }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium">
                                            {{ $item->quantity }} x
                                        </span>
                                    </td>
                                    <td class="pe-4 py-3 text-end fw-bold text-success">{{ rupiah($item->subtotal) }}</td>
                                    @if($order->status === 'Completed')
                                    <td class="pe-4 py-3 text-center">
                                        <a href="{{ route('buyer.reviews.create', [$order, $item->product_id]) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-semibold">
                                            ⭐ Beri Ulasan
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold text-dark mb-0">📦 Informasi Pengiriman</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <span class="text-muted d-block small mb-1">Nama Penerima</span>
                            <span class="fw-bold text-dark">{{ $order->shipping_name }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block small mb-1">Nomor Telepon</span>
                            <span class="fw-bold text-dark">{{ $order->shipping_phone }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block small mb-1">Kota / Wilayah</span>
                            <span class="fw-bold text-dark text-uppercase">{{ $order->shipping_city ?? '-' }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block small mb-1">Alamat Lengkap</span>
                            <span class="fw-bold text-dark">{{ $order->shipping_address }}</span>
                        </div>
                        @if(!empty($order->tracking_number))
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-4 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="text-muted d-block small mb-1">📦 Nomor Resi</span>
                                    <span class="fw-bold text-dark">{{ $order->tracking_number }}</span>
                                </div>
                                @if($order->courier)
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-bold text-uppercase">{{ $order->courier }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">📋 Ringkasan Pesanan</h5>
                    
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Tanggal</span>
                        <span class="fw-semibold text-dark small">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Pembayaran</span>
                        <span class="fw-bold text-dark text-uppercase small">{{ $order->payment_method }}</span>
                    </div>

                    @if(strtolower($order->payment_method) === 'midtrans')
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <span class="text-muted d-block small mb-2 text-center">Status Pembayaran</span>
                        <div class="text-center">
                            @if($order->payment_status == 'success' || $order->payment_status == 'settlement')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold border border-success-subtle">Lunas / Berhasil ✓</span>
                            @elseif($order->payment_status == 'pending')
                                <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold border border-warning-subtle mb-3">Menunggu Pembayaran ⏳</span>
                                <button type="button" id="pay-button" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm py-2">
                                    💳 Bayar Sekarang
                                </button>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold border border-danger-subtle">Gagal / Dibatalkan ✕</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <hr class="text-muted opacity-25 mb-3">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span class="fw-semibold text-dark">{{ rupiah($order->shipping_cost ?? 0) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold text-dark fs-6">Total Belanja</span>
                        <span class="fw-bolder text-success fs-4">{{ rupiah($order->total_amount) }}</span>
                    </div>

                    @if($order->status === 'Pending' && ($order->payment_status !== 'success' && $order->payment_status !== 'settlement'))
                    <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" id="cancel-form">
                        @csrf
                        @method('PATCH')
                        <button type="button" id="btn-cancel-order" class="btn btn-outline-danger rounded-pill w-100 fw-bold py-2 shadow-sm">
                            ✕ Batalkan Pesanan
                        </button>
                    </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(strtolower($order->payment_method) === 'midtrans' && $order->payment_status == 'pending')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button')?.addEventListener('click', function (e) {
        e.preventDefault();
        
        fetch("{{ route('buyer.payment.snap-token', $order) }}")
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) { location.reload(); },
                        onPending: function(result) { location.reload(); },
                        onError: function(result) { alert("Pembayaran gagal!"); },
                        onClose: function() { alert('Popup ditutup sebelum pembayaran selesai.'); }
                    });
                } else {
                    alert('Gagal mendapatkan token: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
            });
    });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('btn-cancel-order')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Yakin Batalkan Pesanan?',
            text: "Pesanan yang dibatalkan tidak bisa dikembalikan, dan stok akan direstore.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup',
            customClass: {
                confirmButton: 'rounded-pill px-4 py-2',
                cancelButton: 'rounded-pill px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form').submit();
            }
        });
    });
</script>
@endpush