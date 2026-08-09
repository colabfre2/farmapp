@extends('layouts.buyer')
@section('title', 'Marketplace ALMS')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --font-body: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        --color-forest: #5c8570;
        --color-forest-soft: #eef2ee;
        --color-earth: #c1946a;
        --color-earth-soft: #f7efe7;
        --color-ink: #26332c;
        --color-ink-muted: #6b7a71;
        --card-radius: 14px;
    }

    .mp-wrap, .mp-wrap * { font-family: var(--font-body); }

    .mp-header-title {
        font-weight: 800;
        font-size: clamp(1.35rem, 1.1rem + 1vw, 1.75rem);
        letter-spacing: -0.01em;
        color: var(--color-ink);
    }
    .mp-header-sub {
        font-size: clamp(0.85rem, 0.8rem + 0.15vw, 0.95rem);
        color: var(--color-ink-muted);
        max-width: 640px;
    }

    /* ── Sidebar kategori ───────────────────────────── */
    .mp-cat-card { border-radius: 16px; }
    .mp-cat-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--color-ink);
        letter-spacing: -0.01em;
    }
    .mp-cat-item {
        font-size: 0.88rem;
        font-weight: 600;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .mp-cat-item:hover { transform: translateX(2px); }
    .mp-cat-item.active-cat {
        background-color: var(--color-forest) !important;
        color: #fff !important;
        border-color: var(--color-forest) !important;
    }
    .mp-cat-item.active-cat .mp-cat-badge {
        background-color: #fff !important;
        color: var(--color-forest) !important;
    }
    .mp-cat-badge {
        font-size: 0.72rem;
        font-weight: 700;
        background-color: var(--color-forest-soft);
        color: var(--color-forest);
    }

    /* ── Kartu produk ───────────────────────────────── */
    .mp-product-card {
        border-radius: var(--card-radius);
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .mp-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
    }
    .mp-product-img {
        height: 200px;
        width: 100%;
        object-fit: contain;
        padding: 1.1rem;
        background: #fbfbf9;
    }
    .mp-product-badge {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        background-color: var(--color-forest-soft);
        color: var(--color-forest);
        padding: 0.32rem 0.7rem;
    }
    .mp-product-title {
        font-weight: 700;
        font-size: clamp(0.95rem, 0.88rem + 0.25vw, 1.08rem);
        color: var(--color-ink);
        line-height: 1.35;
        letter-spacing: -0.005em;
    }
    .mp-price {
        display: flex;
        align-items: baseline;
        gap: 3px;
        color: var(--color-forest);
    }
    .mp-price-currency {
        font-weight: 700;
        font-size: 0.78rem;
        opacity: 0.75;
    }
    .mp-price-amount {
        font-weight: 800;
        font-size: clamp(1.15rem, 1rem + 0.9vw, 1.5rem);
        letter-spacing: -0.015em;
        font-variant-numeric: tabular-nums;
    }
    .mp-btn-view {
        font-weight: 700;
        font-size: 0.85rem;
        background-color: var(--color-forest);
        border-color: var(--color-forest);
        padding: 0.5rem 1.15rem;
    }
    .mp-btn-view:hover {
        background-color: #3d7457;
        border-color: #3d7457;
    }
</style>

<div class="container-fluid px-2 py-2 mp-wrap">

    {{-- Header Halaman --}}
    <div class="mb-4">
        <h3 class="mp-header-title mb-1">🛒 ALMS Marketplace</h3>
        <p class="mp-header-sub mb-0">Temukan produk pertanian dan peternakan berkualitas langsung dari sumbernya.</p>
        @if(request('search'))
            <p class="small mt-2 fw-semibold" style="color: var(--color-forest);">Menampilkan hasil pencarian untuk: "{{ request('search') }}"</p>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 rounded-3 shadow-sm fw-bold mb-4">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-danger-subtle text-danger border-0 rounded-3 shadow-sm fw-bold mb-4">
            ✕ {{ session('error') }}
        </div>
    @endif

    {{-- Layout Utama: Sidebar Kiri Mentok & Produk Kanan --}}
    <div class="row g-4">

        {{-- SIDEBAR FILTER KATEGORI (MENTOK KIRI) --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 sticky-top mp-cat-card" style="top: 90px;">
                <h5 class="mp-cat-title mb-3">📂 Kategori Produk</h5>

                <div class="list-group list-group-flush gap-2">
                    {{-- Tombol Semua Produk (Menampilkan total hasil pencarian dinamis) --}}
                    <a href="{{ route('buyer.marketplace', ['search' => request('search') ?? request('q')]) }}"
                       class="list-group-item list-group-item-action rounded-3 py-3 px-3 mp-cat-item d-flex justify-content-between align-items-center {{ !request('category') ? 'active-cat' : 'text-dark bg-transparent' }}">
                        <span>Semua Produk</span>
                        <span class="badge mp-cat-badge rounded-pill px-3 py-2">
                            {{ $totalSearchCount ?? 0 }}
                        </span>
                    </a>

                    {{-- List Kategori dari Database dengan Hitungan Dinamis Berdasarkan Search --}}
                    @foreach($categories as $cat)
                        @php
                            $productCount = $categoryCounts[$cat->id] ?? 0;
                        @endphp
                        <a href="{{ route('buyer.marketplace', ['category' => $cat->id, 'search' => request('search') ?? request('q')]) }}"
                           class="list-group-item list-group-item-action rounded-3 py-3 px-3 mp-cat-item d-flex justify-content-between align-items-center {{ request('category') == $cat->id ? 'active-cat' : 'text-dark bg-transparent' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="badge mp-cat-badge rounded-pill px-3 py-2">
                                {{ $productCount }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Tombol Reset Filter --}}
                @if(request('category') || request('search') || request('q'))
                    <div class="mt-4 pt-3 border-top">
                        <a href="{{ route('buyer.marketplace') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-pill py-2 fw-semibold">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- GRID PRODUK (KANAN) --}}
        <div class="col-lg-9">
            <div class="row row-cards g-4">
                @forelse($products as $product)
                <div class="col-sm-6 col-md-6 col-xl-4">
                    <div class="card mp-product-card h-100 border-0" style="overflow: hidden;">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top mp-product-img" alt="{{ $product->name }}">
                        @else
                            <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height:200px; font-size:44px;">
                                🌿
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column p-4">
                            <div class="mb-2">
                                <span class="badge mp-product-badge rounded-pill">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </div>

                            <h5 class="mp-product-title mb-3 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <span class="mp-price">
                                    <span class="mp-price-currency">Rp</span>
                                    <span class="mp-price-amount">{{ number_format($product->price, 0, ',', '.') }}</span>
                                </span>

                                <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-sm mp-btn-view rounded-pill shadow-sm text-white">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-light text-center py-5 border-0 shadow-sm rounded-3 text-muted">
                        <div style="font-size: 40px;" class="mb-2">🔍</div>
                        <h5 class="fw-bold text-dark">Produk tidak ditemukan</h5>
                        <p class="small mb-3">Tidak ada produk yang tersedia untuk kata kunci "{{ request('search') }}" pada kategori ini.</p>
                        <a href="{{ route('buyer.marketplace') }}" class="btn btn-sm btn-outline-success rounded-pill px-4">Reset Filter</a>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>

    </div>

</div>
@endsection