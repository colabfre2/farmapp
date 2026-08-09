@extends('layouts.admin')

@section('title', 'Detail Buyer')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .stat-box {
        border-radius: 12px;
        padding: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        height: 100%;
    }
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 font-quicksand text-dark">👤 Detail Buyer</h2>
        <a href="{{ route('admin.buyers.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">← Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            {{-- Profil Buyer --}}
            <div class="card card-flat">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📋 Profil</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="text-muted small">Nama</div>
                        <div class="fw-bold text-dark">{{ $buyer->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-bold text-dark">{{ $buyer->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">No. Telepon</div>
                        <div class="fw-bold text-dark">{{ $buyer->phone ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Kota</div>
                        <div class="fw-bold text-dark">{{ $buyer->city ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Alamat</div>
                        <div class="fw-bold text-dark">{{ $buyer->address ?? '-' }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted small">Bergabung Sejak</div>
                        <div class="fw-bold text-dark">{{ $buyer->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Ringkasan Transaksi --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📊 Ringkasan Transaksi</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="stat-box text-center">
                                <div class="text-muted small mb-1">Total Order</div>
                                <div class="fw-bold text-dark fs-3">{{ $summary['total_order'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box text-center">
                                <div class="text-muted small mb-1">Total Belanja</div>
                                <div class="fw-bold text-success fs-5">{{ rupiah($summary['total_belanja']) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box text-center">
                                <div class="text-muted small mb-1">Order Terakhir</div>
                                <div class="fw-bold text-dark">
                                    {{ $summary['last_order_at'] ? \Carbon\Carbon::parse($summary['last_order_at'])->format('d M Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-muted small mb-2 fw-bold">Rincian Berdasarkan Status</div>
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                        @php
                            $statuses = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
                            $badgeColors = [
                                'Pending' => 'warning',
                                'Processing' => 'primary',
                                'Shipped' => 'info',
                                'Completed' => 'success',
                                'Cancelled' => 'danger',
                            ];
                        @endphp
                        @foreach($statuses as $status)
                        <div class="col">
                            <div class="stat-box text-center">
                                <span class="badge bg-{{ $badgeColors[$status] }}-subtle text-{{ $badgeColors[$status] }} rounded-pill px-3 py-2 fw-bold mb-2 d-inline-block">
                                    {{ $status }}
                                </span>
                                <div class="fw-bold text-dark fs-4">{{ $summary['per_status'][$status] ?? 0 }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
