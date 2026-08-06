@extends('layouts.buyer')
@section('title', 'Pesanan Saya')

@section('content')
<div class="container-fluid px-2 py-4" style="max-width: 1200px;">

    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">📋 Pesanan Saya</h2>
            <p class="text-secondary small mb-0">Lacak status pesanan produk pertanian & peternakanmu di sini.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success bg-white text-success border-0 fw-medium mb-4 d-flex align-items-center rounded-4 shadow-sm py-3 px-4">
        <span class="fs-4 me-3">✅</span> 
        <div>{{ session('success') }}</div>
    </div>
    @endif

    {{-- TAB FILTER MENU ALA E-COMMERCE (MODERN FLAT DESIGN) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-1 p-1 small">
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == '' || request('status') == 'all' ? 'active bg-success text-white shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'all']) }}">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == 'Pending' ? 'active bg-warning text-dark shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'Pending']) }}">⏳ Belum Bayar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == 'Processing' ? 'active bg-primary text-white shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'Processing']) }}">📦 Diproses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == 'Shipped' ? 'active bg-info text-white shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'Shipped']) }}">🚚 Dikirim</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == 'Completed' ? 'active bg-success text-white shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'Completed']) }}">✅ Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill fw-semibold py-2 px-3 {{ request('status') == 'Cancelled' ? 'active bg-danger text-white shadow-sm' : 'text-secondary' }}" 
                       href="{{ route('buyer.orders', ['status' => 'Cancelled']) }}">❌ Dibatalkan</a>
                </li>
            </ul>
        </div>
    </div>

    @if($orders->isEmpty())
        {{-- EMPTY STATE YANG SOFT & CLEAN --}}
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body text-center py-5 px-4">
                <div class="mb-3" style="font-size: 64px; filter: grayscale(20%);">🛒</div>
                <h4 class="fw-bold text-dark mb-2">
                    @if(request('status') == 'Pending') Belum ada tagihan pembayaran nih.
                    @elseif(request('status') == 'Processing') Belum ada pesanan yang sedang diproses.
                    @elseif(request('status') == 'Shipped') Belum ada pesanan dalam perjalanan.
                    @elseif(request('status') == 'Completed') Belum ada pesanan yang selesai.
                    @elseif(request('status') == 'Cancelled') Tidak ada pesanan yang dibatalkan.
                    @else Belum ada riwayat pesanan sama sekali nih.
                    @endif
                </h4>
                <p class="text-secondary mb-4 mx-auto" style="max-width: 400px;">Yuk, penuhi kebutuhan hasil bumi dan ternak berkualitas langsung dari petani lokal!</p>
                <a href="{{ route('buyer.marketplace') }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                    Jelajahi Marketplace 🌾
                </a>
            </div>
        </div>
    @else
        {{-- TABEL SEAMLESS MODERN FLAT DESIGN --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="white-space: nowrap;">
                        <thead class="bg-light text-secondary uppercase tracking-wider" style="font-size: 13px;">
                            <tr>
                                <th class="py-3 px-4 border-0 fw-semibold">No. Pesanan</th>
                                <th class="py-3 border-0 fw-semibold">Tanggal</th>
                                <th class="py-3 border-0 fw-semibold">Jumlah Item</th>
                                <th class="py-3 border-0 fw-semibold">Total Belanja</th>
                                <th class="py-3 border-0 fw-semibold">Pembayaran</th>
                                <th class="py-3 border-0 fw-semibold">Status Pesanan</th>
                                <th class="py-3 px-4 border-0 text-center fw-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0" style="font-size: 14px;">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-4 py-3 fw-bold text-dark">
                                    <span class="text-success font-monospace">#</span>{{ $order->order_number }}
                                </td>
                                <td class="py-3 text-secondary">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="py-3 text-secondary">
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium">
                                        📦 {{ $order->items->count() }} produk
                                    </span>
                                </td>
                                <td class="py-3 fw-bold text-success">{{ rupiah($order->total_amount) }}</td>
                                <td class="py-3 text-secondary">
                                    <span class="text-uppercase small fw-semibold text-muted bg-light px-2 py-1 rounded-3">
                                        {{ $order->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    {{-- BADGE STATUS DENGAN SOFT PASTEL & ICON --}}
                                    <span class="badge rounded-pill px-3 py-2 fw-medium d-inline-flex align-items-center gap-1
                                        @if($order->status == 'Pending') bg-warning-subtle text-warning-emphasis border border-warning-subtle
                                        @elseif($order->status == 'Processing') bg-primary-subtle text-primary-emphasis border border-primary-subtle
                                        @elseif($order->status == 'Shipped') bg-info-subtle text-info-emphasis border border-info-subtle
                                        @elseif($order->status == 'Completed') bg-success-subtle text-success-emphasis border border-success-subtle
                                        @else bg-danger-subtle text-danger-emphasis border border-danger-subtle
                                        @endif">
                                        @if($order->status == 'Pending') ⏳
                                        @elseif($order->status == 'Processing') 🔄
                                        @elseif($order->status == 'Shipped') 🚚
                                        @elseif($order->status == 'Completed') ✨
                                        @else ❌
                                        @endif
                                        <span>{{ $order->status }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Tombol Lihat Detail (ROUTE AMAN TETAP BAWAAN LU) --}}
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm">
                                            Lihat
                                        </a>
                                        
                                        {{-- Tombol Batal (ROUTE AMAN TETAP BAWAAN LU + SWEETALERT) --}}
                                        @if($order->status === 'Pending')
                                        <form method="POST" action="{{ route('buyer.orders.cancel', $order) }}" id="cancel-form-{{ $order->id }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" onclick="confirmCancel({{ $order->id }})" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm">
                                                Batal
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Aksi Gagal',
                text: "{!! addslashes(session('error')) !!}",
                confirmButtonColor: '#2d7a2d',
                confirmButtonText: 'Mengerti',
                customClass: { confirmButton: 'rounded-pill px-4 py-2' }
            });
        @endif
    });

    function confirmCancel(orderId) {
        Swal.fire({
            title: 'Batalkan Pesanan Ini?',
            text: "Stok produk akan otomatis dikembalikan ke sistem dan pesanan tidak dapat dilanjutkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan Saja',
            cancelButtonText: 'Kembali',
            customClass: {
                confirmButton: 'rounded-pill px-4 py-2',
                cancelButton: 'rounded-pill px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + orderId).submit();
            }
        });
    }
</script>
@endpush