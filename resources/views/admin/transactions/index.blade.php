@extends('layouts.admin')

@section('title', 'Transaksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Transaksi</h3>
                    <form method="GET" action="{{ route('admin.transactions.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari no. order / nama buyer..." value="{{ $query ?? '' }}" style="width:220px;">
                        <select name="status" class="form-select form-select-sm" style="width:140px;">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processing" {{ $status == 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Shipped" {{ $status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ $status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍</button>
                        @if(!empty($query) || !empty($status))
                            <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.transactions.export') }}" class="btn btn-success btn-sm">📊 Export Excel</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No. Order</th>
                            <th>Buyer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $order->order_number }}</td>
                            <td>{{ $order->user->name ?? '-' }}</td>
                            <td>{{ $order->items->count() }} item</td>
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
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $order) }}" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection