@extends('layouts.buyer')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="container-fluid px-2 py-2">
    <div class="row g-5">
        {{-- Product Image --}}
        <div class="col-lg-5">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid bg-light shadow-sm" style="width:100%; height:400px; object-fit:contain; padding:2rem; border-radius:12px;" alt="{{ $product->name }}">
            @else
                <div class="bg-light shadow-sm text-secondary d-flex align-items-center justify-content-center" style="height:400px; font-size:80px; border-radius:12px;">
                    🌿
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="col-lg-7">
            <div class="ps-lg-3">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-3 fw-medium">
                    {{ $product->category->name ?? '-' }}
                </span>
                <h1 class="fw-bold text-dark mb-3">{{ $product->name }}</h1>

                {{-- Rating --}}
                @if($reviewCount > 0)
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span style="color:#f59e0b; font-size:20px;">⭐ {{ $product->rating }}</span>
                    <span class="text-muted fw-medium">({{ $reviewCount }} ulasan)</span>
                </div>
                @else
                <div class="mb-3">
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill">Belum ada ulasan</span>
                </div>
                @endif

                <p class="text-muted mb-4" style="line-height: 1.6;">{{ $product->description }}</p>

                <div class="d-flex align-items-center gap-2 mb-4 p-3 bg-light rounded-3">
                    <span class="h2 fw-bold text-success mb-0">{{ rupiah($product->price) }}</span>
                    <span class="text-muted fs-5 fw-medium">/ {{ $product->unit->symbol ?? '' }}</span>
                </div>

                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small fw-medium mb-1">Stok Tersedia</div>
                            <div class="fw-bold fs-5 text-dark">{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small fw-medium mb-1">Kategori</div>
                            <div class="fw-bold fs-5 text-dark">{{ $product->category->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Jumlah Pembelian</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="number" id="qtyInput" value="1" min="1" max="{{ $product->stock }}" class="form-control rounded-3 py-2 text-center fw-bold" style="width:120px; font-size: 1.1rem;">
                        <span class="text-muted fw-medium">/ {{ $product->unit->symbol ?? '' }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 mb-4 pt-3 border-top">
                    <form method="POST" action="{{ route('buyer.cart.add', $product) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="quantity" id="qtyHidden" value="1">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold px-4 shadow-sm" onclick="document.getElementById('qtyHidden').value = document.getElementById('qtyInput').value">
                            🛒 Tambah ke Keranjang
                        </button>
                    </form>
                    <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary btn-lg rounded-pill fw-bold px-4 shadow-sm">← Kembali</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success bg-success-subtle text-success border-0 rounded-3 shadow-sm fw-bold">
                        ✓ {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger bg-danger-subtle text-danger border-0 rounded-3 shadow-sm fw-bold">
                        ✕ {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Section Bawah: Ulasan & Info Tambahan --}}
    <div class="row mt-5 g-4">
        {{-- Ulasan Pembeli --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h4 class="card-title fw-bold text-dark mb-0">💬 Ulasan Pembeli ({{ $reviewCount }})</h4>
                    @if($reviewCount > 0)
                    <div class="bg-light px-3 py-1 rounded-pill">
                        <span style="color:#f59e0b; font-weight:bold;">⭐ {{ $product->rating }}</span>
                        <span class="text-muted small fw-medium">/ 5.0</span>
                    </div>
                    @endif
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <hr class="mt-0 mb-4">
                    @forelse($reviews as $review)
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-dark">{{ $review->user->name ?? 'Pengguna' }}</div>
                                <div style="color:#f59e0b; font-size: 0.9rem;" class="mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '⭐' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <span class="badge bg-light text-muted rounded-pill">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if($review->comment)
                            <p class="text-muted mb-0 mt-2 p-3 bg-light rounded-3" style="font-style: italic;">"{{ $review->comment }}"</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div style="font-size: 50px;" class="mb-3 text-muted">💬</div>
                        <h6 class="fw-bold text-dark mb-1">Belum ada ulasan</h6>
                        <p class="text-muted small mb-0">Jadilah yang pertama memberi ulasan untuk produk ini!</p>
                    </div>
                    @endforelse
                </div>
                @if($reviews->hasPages())
                <div class="card-footer bg-white border-top-0 pb-4 px-4">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Info Tambahan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
                    <h4 class="card-title fw-bold text-dark mb-0">ℹ️ Informasi Produk</h4>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <hr class="mt-0 mb-4">
                    <div class="mb-4">
                        <div class="text-muted small fw-medium mb-1">Nama Produk</div>
                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small fw-medium mb-1">Kategori</div>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 mt-1">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted small fw-medium mb-1">Satuan</div>
                        <div class="fw-bold text-dark">{{ $product->unit->name ?? '-' }} <span class="text-muted">({{ $product->unit->symbol ?? '-' }})</span></div>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium mb-1">Stok Tersedia</div>
                        <div class="fw-bold text-success fs-5">{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection