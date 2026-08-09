@extends('layouts.admin')

@section('title', 'Data Buyer')

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
                        <h3 class="card-title fw-bold font-quicksand text-dark mb-0">👥 Data Buyer</h3>

                        <form method="GET" action="{{ route('admin.buyers.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                            <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari nama / email buyer..." value="{{ $query ?? '' }}" style="width:240px;">
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">🔍</button>
                            @if(!empty($query))
                                <a href="{{ route('admin.buyers.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">✕ Reset</a>
                            @endif
                        </form>
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
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Kota</th>
                                    <th class="text-center">Jumlah Transaksi</th>
                                    <th>Total Belanja</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buyers as $buyer)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">{{ $buyer->name }}</td>
                                    <td class="text-muted">{{ $buyer->email }}</td>
                                    <td>{{ $buyer->city ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                                            {{ $buyer->orders_count }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">{{ rupiah($buyer->total_belanja ?? 0) }}</td>
                                    <td>
                                        @if($buyer->last_order_at)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">Aktif</span>
                                            <div class="text-muted small mt-1">Order terakhir: {{ \Carbon\Carbon::parse($buyer->last_order_at)->format('d M Y') }}</div>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-bold">Belum Pernah Order</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.buyers.show', $buyer) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">Belum ada data buyer.</td>
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
