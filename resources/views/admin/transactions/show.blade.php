@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    
    <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

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
        <div class="card mb-4">
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
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">📋 Info Pesanan</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">No. Order</div>
                    <div class="fw-bold">{{ $order->order_number }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Buyer</div>
                    <div class="fw-bold">{{ $order->user->name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-bold">{{ $order->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Metode Pembayaran</div>
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
                    <span class="text-success">{{ rupiah($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🔄 Update Status</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.transactions.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processing" {{ $order->status == 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Shipped" {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection