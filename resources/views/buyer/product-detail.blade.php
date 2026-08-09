@extends('layouts.buyer')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<style>
    .pd-breadcrumb a { color: #64748b; text-decoration: none; }
    .pd-breadcrumb a:hover { color: #16a34a; }
    .pd-image-wrap {
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        position: sticky;
        top: 90px;
    }
    .pd-badge-category {
        background: #ecfdf5;
        color: #16a34a;
        border: 1px solid #d1fae5;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 6px 16px;
        border-radius: 999px;
        display: inline-block;
    }
    .pd-title { font-size: 1.9rem; font-weight: 800; color: #1e293b; letter-spacing: -0.02em; }
    .pd-price-box {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #d1fae5;
        border-radius: 14px;
        padding: 20px 22px;
    }
    .pd-price { font-size: 2rem; font-weight: 800; color: #16a34a; letter-spacing: -0.02em; }
    .pd-unit-tag { color: #64748b; font-size: 0.95rem; font-weight: 500; }
    .pd-stat-card {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 14px 16px;
    }
    .pd-stat-label { color: #94a3b8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
    .pd-stat-value { color: #1e293b; font-weight: 700; font-size: 1.05rem; margin-top: 2px; }
    .pd-qty-stepper {
        display: inline-flex;
        align-items: stretch;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }
    .pd-qty-btn {
        background: #f8fafc;
        border: none;
        width: 44px;
        font-size: 1.2rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: background 0.15s;
    }
    .pd-qty-btn:hover { background: #ecfdf5; color: #16a34a; }
    .pd-qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .pd-qty-input {
        border: none;
        border-left: 1.5px solid #e2e8f0;
        border-right: 1.5px solid #e2e8f0;
        width: 70px;
        text-align: center;
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
    }
    .pd-qty-input:focus { outline: none; box-shadow: none; }
    .pd-btn-primary {
        background: #16a34a;
        border: none;
        color: #fff;
        font-weight: 700;
        border-radius: 999px;
        padding: 14px 32px;
        transition: all 0.2s;
        box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);
    }
    .pd-btn-primary:hover { background: #15803d; color: #fff; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22, 163, 74, 0.3); }
    .pd-btn-outline {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        font-weight: 700;
        border-radius: 999px;
        padding: 14px 28px;
        transition: all 0.2s;
    }
    .pd-btn-outline:hover { border-color: #cbd5e1; background: #f8fafc; color: #1e293b; }
    .pd-section-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .pd-section-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 1.15rem;
        letter-spacing: -0.01em;
    }
    .pd-desc-text { color: #475569; line-height: 1.8; font-size: 0.98rem; white-space: pre-line; }
    .pd-rating-chip {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 999px;
        padding: 5px 14px;
        font-weight: 700;
        color: #b45309;
        font-size: 0.9rem;
    }
    .pd-review-item { padding: 20px 0; border-bottom: 1px solid #f1f5f9; }
    .pd-review-item:last-child { border-bottom: none; }
    .pd-review-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #ecfdf5; color: #16a34a; font-weight: 700;
        display: flex; align-items: center; justify-content: center; font-size: 1rem;
    }
    .pd-alert-success, .pd-alert-error {
        border: none; border-radius: 12px; font-weight: 600;
        padding: 14px 18px; display: flex; align-items: center; gap: 10px;
    }
    .pd-alert-success { background: #f0fdf4; color: #15803d; }
    .pd-alert-error { background: #fef2f2; color: #dc2626; }
</style>

<div class="container-fluid px-2 py-2">

    {{-- Breadcrumb --}}
    <div class="pd-breadcrumb small mb-4">
        <a href="{{ route('buyer.marketplace') }}">Marketplace</a>
        <span class="text-muted mx-1">/</span>
        <a href="{{ route('buyer.marketplace', ['category' => $product->category_id ?? '']) }}">{{ $product->category->name ?? 'Produk' }}</a>
        <span class="text-muted mx-1">/</span>
        <span class="text-dark fw-semibold">{{ $product->name }}</span>
    </div>

    <div class="row g-5">
        {{-- Product Image --}}
        <div class="col-lg-5">
            <div class="pd-image-wrap">
                @if($product->image)
                    <img src="{{ '/storage/' . $product->image }}" class="img-fluid w-100" style="height:420px; object-fit:contain; padding:2rem;" alt="{{ $product->name }}">
                @else
                    <div class="text-secondary d-flex align-items-center justify-content-center" style="height:420px; font-size:90px;">
                        🌿
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-7">
            <span class="pd-badge-category mb-3">{{ $product->category->name ?? '-' }}</span>
            <h1 class="pd-title mt-3 mb-2">{{ $product->name }}</h1>

            {{-- Rating --}}
            @if($reviewCount > 0)
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="pd-rating-chip">⭐ {{ $product->rating }}</span>
                <span class="text-muted small fw-medium">{{ $reviewCount }} ulasan pembeli</span>
            </div>
            @else
            <div class="mb-4">
                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-medium">Belum ada ulasan</span>
            </div>
            @endif

            {{-- Harga --}}
            <div class="pd-price-box d-flex align-items-baseline gap-2 mb-4">
                <span class="pd-price">{{ rupiah($product->price) }}</span>
                <span class="pd-unit-tag">/ {{ $product->unit->symbol ?? '' }}</span>
            </div>

            {{-- Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="pd-stat-card">
                        <div class="pd-stat-label">Stok Tersedia</div>
                        <div class="pd-stat-value {{ $product->stock <= 0 ? 'text-danger' : '' }}">
                            {{ $product->stock }} {{ $product->unit->symbol ?? '' }}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="pd-stat-card">
                        <div class="pd-stat-label">Satuan</div>
                        <div class="pd-stat-value">{{ $product->unit->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Quantity --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing:0.4px;">Jumlah Pembelian</label>
                <div class="d-flex align-items-center gap-3">
                    <div class="pd-qty-stepper">
                        <button type="button" id="qtyMinus" class="pd-qty-btn">−</button>
                        <input type="number" id="qtyInput" value="1" min="1" max="{{ $product->stock }}" class="pd-qty-input">
                        <button type="button" id="qtyPlus" class="pd-qty-btn">+</button>
                    </div>
                    <span class="text-muted fw-medium">{{ $product->unit->symbol ?? '' }} tersedia: {{ $product->stock }}</span>
                </div>
            </div>

            {{-- CTA --}}
            <div class="d-flex flex-wrap gap-3 mb-4 pt-3 border-top">
                <form method="POST" action="{{ route('buyer.cart.add', $product) }}" class="m-0">
                    @csrf
                    <input type="hidden" name="quantity" id="qtyHidden" value="1">
                    <button type="submit" class="pd-btn-primary" {{ $product->stock <= 0 ? 'disabled' : '' }} onclick="document.getElementById('qtyHidden').value = document.getElementById('qtyInput').value">
                        🛒 {{ $product->stock <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                    </button>
                </form>
                <a href="{{ route('buyer.marketplace') }}" class="pd-btn-outline">← Kembali</a>
            </div>

            @if(session('success'))
                <div class="pd-alert-success mb-3">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="pd-alert-error mb-3">✕ {{ session('error') }}</div>
            @endif

            {{-- Deskripsi Produk --}}
            <div class="pd-section-card p-4 mt-2">
                <h3 class="pd-section-title mb-3">📝 Deskripsi Produk</h3>
                @if($product->description)
                    <p class="pd-desc-text mb-0">{{ $product->description }}</p>
                @else
                    <p class="text-muted fst-italic mb-0">Belum ada deskripsi untuk produk ini.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Section Bawah: Ulasan & Info Tambahan --}}
    <div class="row mt-4 g-4">
        {{-- Ulasan Pembeli --}}
        <div class="col-lg-8">
            <div class="pd-section-card h-100">
                <div class="d-flex justify-content-between align-items-center p-4 pb-3">
                    <h3 class="pd-section-title mb-0">💬 Ulasan Pembeli ({{ $reviewCount }})</h3>
                    @if($reviewCount > 0)
                    <span class="pd-rating-chip">⭐ {{ $product->rating }} / 5.0</span>
                    @endif
                </div>
                <div class="px-4 pb-4">
                    <hr class="mt-0 mb-2" style="border-color:#f1f5f9;">
                    @forelse($reviews as $review)
                    <div class="pd-review-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center gap-3">
                                <div class="pd-review-avatar">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $review->user->name ?? 'Pengguna' }}</div>
                                    <div style="color:#f59e0b; font-size: 0.85rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '⭐' : '☆' }}
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-light text-muted rounded-pill fw-normal">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if($review->comment)
                            <p class="text-muted mb-0 mt-3 p-3 rounded-3" style="background:#f8fafc; font-style: italic;">"{{ $review->comment }}"</p>
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
                <div class="px-4 pb-4">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Info Tambahan --}}
        <div class="col-lg-4">
            <div class="pd-section-card">
                <div class="p-4 pb-3">
                    <h3 class="pd-section-title mb-0">ℹ️ Informasi Produk</h3>
                </div>
                <div class="px-4 pb-4">
                    <hr class="mt-0 mb-3" style="border-color:#f1f5f9;">
                    <div class="mb-3 pb-3" style="border-bottom:1px solid #f8fafc;">
                        <div class="pd-stat-label mb-1">Nama Produk</div>
                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                    </div>
                    <div class="mb-3 pb-3" style="border-bottom:1px solid #f8fafc;">
                        <div class="pd-stat-label mb-1">Kategori</div>
                        <span class="pd-badge-category mt-1">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <div class="mb-3 pb-3" style="border-bottom:1px solid #f8fafc;">
                        <div class="pd-stat-label mb-1">Satuan</div>
                        <div class="fw-bold text-dark">{{ $product->unit->name ?? '-' }} <span class="text-muted fw-normal">({{ $product->unit->symbol ?? '-' }})</span></div>
                    </div>
                    <div>
                        <div class="pd-stat-label mb-1">Stok Tersedia</div>
                        <div class="fw-bold fs-5" style="color:#16a34a;">{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus  = document.getElementById('qtyPlus');
    const maxStock = {{ (int) $product->stock }};

    function clampQty() {
        let val = parseInt(qtyInput.value || 1);
        if (isNaN(val) || val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        qtyInput.value = val;
    }

    qtyMinus?.addEventListener('click', () => {
        qtyInput.value = Math.max(1, (parseInt(qtyInput.value || 1) - 1));
    });

    qtyPlus?.addEventListener('click', () => {
        qtyInput.value = Math.min(maxStock, (parseInt(qtyInput.value || 1) + 1));
    });

    qtyInput?.addEventListener('input', clampQty);
    qtyInput?.addEventListener('blur', clampQty);
});
</script>
@endsection