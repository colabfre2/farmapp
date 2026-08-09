@extends('layouts.buyer')
@section('title', 'Beranda FarmApp')

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
        --color-earth-deep: #a97a4f;
        --color-ink: #26332c;
        --color-ink-muted: #6b7a71;
    }

    .home-wrap, .home-wrap * { font-family: var(--font-body); }

    /* ── Hero / greeting banner ─────────────────────── */
    .home-hero {
        background: linear-gradient(135deg, #43695a 0%, #5c8570 55%, #8ca997 100%) !important;
    }
    .home-hero-title {
        font-weight: 800;
        letter-spacing: -0.015em;
        font-size: clamp(1.4rem, 1.15rem + 1.4vw, 2.1rem);
    }
    .home-hero-sub {
        font-size: clamp(0.9rem, 0.85rem + 0.25vw, 1.05rem);
        color: rgba(255,255,255,0.78) !important;
    }
    .home-hero-cta {
        font-weight: 700;
        font-size: 0.95rem;
        background-color: var(--color-earth) !important;
        border-color: var(--color-earth) !important;
    }
    .home-hero-cta:hover {
        background-color: #b3762e !important;
        border-color: #b3762e !important;
    }

    /* ── Section header ──────────────────────────────── */
    .home-section-title {
        font-weight: 800;
        font-size: clamp(1.05rem, 0.95rem + 0.4vw, 1.3rem);
        letter-spacing: -0.01em;
        color: var(--color-ink);
    }
    .home-section-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--color-forest) !important;
    }

    /* ── Kartu produk (konsisten dengan marketplace) ──── */
    .home-product-card {
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .home-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
    }
    .home-product-img {
        width: 100%;
        object-fit: contain;
        padding: 0.9rem;
        background: #fbfbf9;
        height: 150px;
    }
    @media (min-width: 768px) {
        .home-product-img { height: 200px; }
    }
    .home-product-badge {
        font-size: 0.68rem;
        font-weight: 700;
        background-color: var(--color-forest-soft);
        color: var(--color-forest);
        padding: 0.28rem 0.6rem;
    }
    .home-rating {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--color-earth-deep);
    }
    .home-product-title {
        font-weight: 700;
        font-size: clamp(0.82rem, 0.76rem + 0.3vw, 0.98rem);
        color: var(--color-ink);
        line-height: 1.35;
    }
    .home-price {
        display: flex;
        align-items: baseline;
        gap: 2px;
        color: var(--color-forest);
    }
    .home-price-currency {
        font-weight: 700;
        font-size: 0.7rem;
        opacity: 0.75;
    }
    .home-price-amount {
        font-weight: 800;
        font-size: clamp(0.95rem, 0.85rem + 0.6vw, 1.2rem);
        letter-spacing: -0.01em;
        font-variant-numeric: tabular-nums;
    }
    .home-btn-view {
        font-weight: 700;
        font-size: 0.82rem;
        background-color: var(--color-forest);
        border-color: var(--color-forest);
    }
    .home-btn-view:hover {
        background-color: #3d7457;
        border-color: #3d7457;
    }
</style>

<div class="container-fluid px-2 py-2 home-wrap">

    {{-- Banner Slider (Dinamis dari Admin) --}}
    @php
        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
    @endphp

    @if($banners->isNotEmpty())
    <div id="bannerCarousel" class="carousel slide mb-4 mb-md-5 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="4000" style="border-radius: 16px !important;">
        @if($banners->count() > 1)
        <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif

        <div class="carousel-inner rounded-4" style="aspect-ratio: 16 / 5; background: #f1f5f9;">
            @foreach($banners as $index => $banner)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="height: 100%;">
                @if($banner->link_url)
                    <a href="{{ $banner->link_url }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="{{ $banner->title }}">
                    </a>
                @else
                    <img src="{{ asset('storage/'.$banner->image) }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="{{ $banner->title }}">
                @endif
            </div>
            @endforeach
        </div>

        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Selanjutnya</span>
        </button>
        @endif
    </div>
    @endif

    {{-- Greeting Banner --}}
    <div class="p-4 p-md-5 mb-4 mb-md-5 rounded-4 shadow-sm border-0 text-white position-relative overflow-hidden home-hero"
         style="border-radius: 16px !important;">
        <div class="position-relative z-1 py-2 py-md-3 text-center">
            <h1 class="home-hero-title mb-2">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="home-hero-sub mb-3 mb-md-4 px-2 px-md-0">Temukan produk pertanian dan peternakan unggulan dengan rating tertinggi di FarmApp.</p>
            <a href="{{ route('buyer.marketplace') }}" class="btn home-hero-cta rounded-pill px-4 py-2 shadow-sm text-white">
                🛒 Jelajahi Marketplace
            </a>
        </div>
    </div>

    {{-- Top-Rated Products Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
        <h4 class="home-section-title mb-0">⭐ Produk Paling Diunggulkan</h4>
        <a href="{{ route('buyer.marketplace') }}" class="home-section-link text-decoration-none">
            Lihat Semua Marketplace →
        </a>
    </div>

    <div class="row row-cards g-3 g-md-4">
        @php
            $topProducts = \App\Models\Product::with('category', 'unit')
                ->withAvg('reviews as average_rating', 'rating')
                ->where('is_active', true)
                ->orderByDesc('average_rating')
                ->take(20)
                ->get();
        @endphp

        @forelse($topProducts as $product)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card home-product-card h-100 border-0" style="overflow: hidden;">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top home-product-img" alt="{{ $product->name }}">
                @else
                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height:150px; font-size:36px;">
                        🌿
                    </div>
                @endif
                <div class="card-body d-flex flex-column p-3 p-md-4">
                    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center mb-2 gap-1">
                        <span class="badge home-product-badge rounded-pill">
                            {{ $product->category->name ?? '-' }}
                        </span>

                        @if($product->average_rating)
                            <span class="home-rating">
                                ⭐ {{ number_format($product->average_rating, 1) }}
                            </span>
                        @endif
                    </div>

                    <h6 class="home-product-title mb-2 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-auto pt-2 gap-2">
                        <span class="home-price">
                            <span class="home-price-currency">Rp</span>
                            <span class="home-price-amount">{{ number_format($product->price, 0, ',', '.') }}</span>
                        </span>

                        <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-sm home-btn-view rounded-pill shadow-sm text-white w-100 w-md-auto">
                            Lihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light text-center py-5 border-0 shadow-sm rounded-3 text-muted">
                <div style="font-size: 40px;" class="mb-2">🌱</div>
                <h5 class="fw-bold text-dark">Belum ada produk tersedia</h5>
                <p class="small mb-0">Produk unggulan dengan rating tertinggi akan muncul di sini.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection