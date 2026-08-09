@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }
    .table-custom tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    .stat-card {
        border: none;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 1.25rem;
        height: 100%;
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .review-item {
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 0;
    }
    .review-item:last-child { border-bottom: none; }
    .star-rating { color: #f59e0b; letter-spacing: 1px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">📦 Detail Produk</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-4 shadow-sm">✏️ Edit Produk</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-4 shadow-sm">← Kembali</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 d-flex align-items-center rounded-3 shadow-sm">
        <span class="fs-5 me-2">✅</span> {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    {{-- KIRI: Info Produk --}}
    <div class="col-lg-4">
        <div class="card card-flat">
            <div class="card-body p-4 text-center">
                @if($product->image)
                    <img src="{{ '/storage/' . $product->image }}" alt="{{ $product->name }}" class="rounded-3 mb-3 shadow-sm" style="width: 100%; height: 220px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center text-muted rounded-3 mb-3" style="width: 100%; height: 220px; font-size: 3rem;">
                        📸
                    </div>
                @endif

                <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
                <div class="text-muted small mb-3">{{ $product->category->name ?? 'Tanpa Kategori' }}</div>

                <div class="d-flex justify-content-center mb-3">
                    @if($product->is_active)
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Aktif</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">Nonaktif</span>
                    @endif
                </div>

                <hr class="text-muted opacity-25">

                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Harga</span>
                        <span class="fw-bold text-success">{{ rupiah($product->price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Stok Saat Ini</span>
                        <span class="fw-bold {{ $product->stock < 10 ? 'text-danger' : 'text-dark' }}">
                            {{ $product->stock }} {{ $product->unit->symbol ?? '' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Satuan</span>
                        <span class="fw-bold text-dark">{{ $product->unit->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Ditambahkan Oleh</span>
                        <span class="fw-bold text-dark">{{ $product->user->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Tanggal Dibuat</span>
                        <span class="fw-bold text-dark">{{ $product->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                @if($product->description)
                    <hr class="text-muted opacity-25">
                    <div class="text-start">
                        <div class="text-muted small mb-1">Deskripsi</div>
                        <p class="text-dark small mb-0" style="line-height: 1.6;">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- KANAN: Statistik & Riwayat --}}
    <div class="col-lg-8">
        {{-- Kartu Statistik --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary mb-2">🛒</div>
                    <div class="fw-bold fs-4 text-dark">{{ $totalSold }}</div>
                    <div class="text-muted small">Terjual</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success mb-2">💰</div>
                    <div class="fw-bold fs-5 text-dark">{{ rupiah($totalOmzet) }}</div>
                    <div class="text-muted small">Total Omzet</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning mb-2">⭐</div>
                    <div class="fw-bold fs-4 text-dark">{{ $avgRating ?? '-' }}</div>
                    <div class="text-muted small">Rating Rata-rata</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle text-info mb-2">💬</div>
                    <div class="fw-bold fs-4 text-dark">{{ $totalReview }}</div>
                    <div class="text-muted small">Ulasan</div>
                </div>
            </div>
        </div>

        {{-- Riwayat Pesanan Terbaru --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <h5 class="fw-bold text-dark mb-0">🧾 Pesanan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pembeli</th>
                                <th>Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrderItems as $item)
                            <tr>
                                <td class="fw-bold text-dark">
                                    @if($item->order)
                                        <a href="{{ route('admin.transactions.show', $item->order) }}" class="text-decoration-none">
                                            #{{ $item->order->order_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $item->order->user->name ?? '-' }}</td>
                                <td>{{ $item->quantity }} {{ $product->unit->symbol ?? '' }}</td>
                                <td class="text-end fw-bold text-success">{{ rupiah($item->subtotal) }}</td>
                                <td class="text-muted small">{{ $item->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan untuk produk ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Ulasan Produk --}}
        <div class="card card-flat">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <h5 class="fw-bold text-dark mb-0">⭐ Ulasan Pembeli</h5>
            </div>
            <div class="card-body p-4">
                @forelse($product->reviews as $review)
                    <div class="review-item">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-dark small">{{ $review->user->name ?? 'Pengguna' }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="star-rating mb-1">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </div>
                        @if($review->comment)
                            <p class="text-secondary small mb-0" style="line-height: 1.5;">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada ulasan untuk produk ini.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection