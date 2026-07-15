@extends('layouts.buyer')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Detail pesanan</h2>
    <a href="{{ route('buyer.orders') }}" class="btn btn-outline-secondary">← Kembali ke pesanan</a>
</div>

<div class="row">
    <div class="col-lg-8">
        {{-- Order Items --}}
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">🧾 Item Pesanan</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-vcenter mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->product_name }}</td>
                            <td>{{ rupiah($item->unit_price) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-bold text-success">{{ rupiah($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📦 Informasi Pengiriman</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Nama</div>
                        <div class="fw-bold">{{ $order->shipping_name }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Telepon</div>
                        <div class="fw-bold">{{ $order->shipping_phone }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Kota</div>
                        <div class="fw-bold">{{ $order->shipping_city ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Alamat</div>
                        <div class="fw-bold">{{ $order->shipping_address }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Order Info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📋 Info pesanan</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">No. Pesanan</div>
                    <div class="fw-bold">{{ $order->order_number }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-bold">{{ $order->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Metode pembayaran</div>
                    <div class="fw-bold">{{ ucfirst($order->payment_method) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Status</div>
                    <span class="badge
                        @if($order->status == 'Pending') bg-warning text-dark
                        @elseif($order->status == 'Processing') bg-primary
                        @elseif($order->status == 'Shipped') bg-info
                        @elseif($order->status == 'Completed') bg-success
                        @else bg-danger
                        @endif">
                        {{ $order->status }}
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold h4">
                    <span>Total</span>
                    <span class="text-success">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection