@extends('layouts.admin')

@section('title', 'Transaksi')

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
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📊 Daftar Transaksi & Pesanan</h3>
                        
                        <form method="GET" action="{{ route('admin.transactions.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari no. order / nama buyer..." value="{{ $query ?? '' }}" style="width:220px;">
                            <select name="status" class="form-select form-select-sm rounded-pill px-3" style="width:140px;">
                                <option value="">Semua Status</option>
                                <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Processing" {{ $status == 'Processing' ? 'selected' : '' }}>Processing</option>
                                <option value="Shipped" {{ $status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ $status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">🔍</button>
                            @if(!empty($query) || !empty($status))
                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">✕ Reset</a>
                            @endif
                        </form>

                        <a href="{{ route('admin.transactions.export') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">📊 Export Excel</a>
                    </div>
                </div>

                <div class="card-body px-0 pb-0">
                    @if(session('success'))
                        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mx-4 mb-3 rounded-3 shadow-sm">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-custom table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>No. Order</th>
                                    <th>Buyer</th>
                                    <th>Kurir & Layanan</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                                    <td>{{ $order->user->name ?? '-' }}</td>
                                    <td>
                                        <div class="fw-bold text-dark text-uppercase">{{ $order->courier ?? '-' }}</div>
                                        <div class="text-muted small">{{ $order->courier_service ?? '-' }}</div>
                                    </td>
                                    <td class="fw-bold text-success">{{ rupiah($order->total_amount) }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-uppercase small">
                                            {{ $order->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 fw-bold
                                            @if($order->status == 'Pending') bg-warning-subtle text-warning
                                            @elseif($order->status == 'Processing') bg-primary-subtle text-primary
                                            @elseif($order->status == 'Shipped') bg-info-subtle text-info
                                            @elseif($order->status == 'Completed') bg-success-subtle text-success
                                            @else bg-danger-subtle text-danger
                                            @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.transactions.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">Belum ada data transaksi yang masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection