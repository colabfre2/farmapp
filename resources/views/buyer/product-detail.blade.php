@extends('layouts.buyer')

@section('content')

<div class="row">
    {{-- Product Image --}}
    <div class="col-lg-5">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid rounded" style="width:100%;height:400px;object-fit:cover;" alt="{{ $product->name }}">
        @else
            <div style="height:400px;background:#f4f6f8;display:flex;align-items:center;justify-content:center;font-size:80px;border-radius:8px;">
                🌿
            </div>
        @endif
    </div>

    {{-- Product Info --}}
    <div class="col-lg-7">
        <div class="ps-lg-4">
            <span class="badge bg-success-lt text-success mb-2">{{ $product->category->name ?? '-' }}</span>
            <h1 class="fw-bold">{{ $product->name }}</h1>

            
            {{-- Rating --}}
            @if($reviewCount > 0)
            <div class="d-flex align-items-center gap-2 mb-3">
                <span style="color:#f59e0b; font-size:18px;">⭐ {{ $product->rating }}</span>
                <span class="text-muted">({{ $reviewCount }} ulasan)</span>
            </div>
            @else
            <div class="mb-3">
                <span class="text-muted">Belum ada ulasan</span>
            </div>
            @endif

            <p class="text-muted">{{ $product->description }}</p>

            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="h2 fw-bold text-success mb-0">{{ rupiah($product->price) }}</span>
                <span class="text-muted">/ {{ $product->unit->symbol ?? '' }}</span>
            </div>

            <div class="mb-4">
                <div class="row">
                    <div class="col-6">
                        <div class="text-muted small">Stok Tersedia</div>
                        <div class="fw-bold">{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Kategori</div>
                        <div class="fw-bold">{{ $product->category->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Jumlah</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" id="qtyInput" value="1" min="1" max="{{ $product->stock }}" class="form-control" style="width:100px;">
                    <span class="text-muted">/ {{ $product->unit->symbol ?? '' }}</span>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <form method="POST" action="{{ route('buyer.cart.add', $product) }}">
                    @csrf
                    <input type="hidden" name="quantity" id="qtyHidden" value="1">
                    <button type="submit" class="btn btn-success btn-lg" onclick="document.getElementById('qtyHidden').value = document.getElementById('qtyInput').value">
                        🛒 Tambah ke Keranjang
                    </button>
                </form>
                <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary btn-lg">← Kembali</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>
{{-- Ulasan Pembeli --}}
<div class="row mt-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">💬 Ulasan Pembeli ({{ $reviewCount }})</h3>
                @if($reviewCount > 0)
                <div>
                    <span style="color:#f59e0b; font-weight:bold;">⭐ {{ $product->rating }}</span>
                    <span class="text-muted">/ 5.0</span>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($reviews as $review)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold">{{ $review->user->name ?? 'Pengguna' }}</div>
                            <div style="color:#f59e0b;">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '⭐' : '☆' }}
                                @endfor
                            </div>
                        </div>
                        <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->comment)
                        <p class="text-muted mb-0">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    Belum ada ulasan untuk produk ini. Jadilah yang pertama memberi ulasan!
                </div>
                @endforelse
            </div>
            @if($reviews->hasPages())
            <div class="card-footer">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Info Tambahan --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">ℹ️ Informasi Produk</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Nama Produk</div>
                    <div class="fw-bold">{{ $product->name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Kategori</div>
                    <div class="fw-bold">{{ $product->category->name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Satuan</div>
                    <div class="fw-bold">{{ $product->unit->name ?? '-' }} ({{ $product->unit->symbol ?? '-' }})</div>
                </div>
                <div>
                    <div class="text-muted small">Stok Tersedia</div>
                    <div class="fw-bold">{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection