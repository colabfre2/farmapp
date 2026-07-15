@extends('layouts.buyer')

@section('content')

<h2 class="fw-bold mb-4">📋 Pesanan Saya
</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($orders->isEmpty())
    <div class="text-center py-5 text-muted">
        <div style="font-size:64px">📋</div>
        <h4>Belum ada pesanan</h4>
        <a href="{{ route('buyer.marketplace') }}" class="btn btn-success mt-3">Jelajahi Marketplace</a>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-vcenter mb-0">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->items->count() }} items</td>
                        <td class="fw-bold text-success">{{ rupiah($order->total_amount) }}</td>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                        <td>
                            <span class="badge
                                @if($order->status == 'Pending') bg-warning text-dark
                                @elseif($order->status == 'Processing') bg-primary
                                @elseif($order->status == 'Shipped') bg-info
                                @elseif($order->status == 'Completed') bg-success
                                @else bg-danger
                                @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection