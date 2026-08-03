@extends('layouts.buyer')
@section('title', 'Beranda FarmApp')

@section('content')
<div class="container-fluid px-2 py-2">

    {{-- Greeting Banner Modern Flat Design --}}
    <div class="p-5 mb-5 rounded-4 shadow-sm border-0 text-white position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); border-radius: 16px !important;">
        <div class="position-relative z-1 py-3 text-center">
            <h1 class="fw-bold mb-2 display-6">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-white-50 mb-4 fs-5">Temukan produk pertanian dan peternakan unggulan dengan rating tertinggi di FarmApp.</p>
            <a href="{{ route('buyer.marketplace') }}" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm fw-bold">
                🛒 Jelajahi Marketplace
            </a>
        </div>
    </div>

    {{-- Top-Rated Products Section (Menampilkan 20 Produk Rating Tertinggi) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0 text-dark">⭐ Produk Paling Diunggulkan & Rating Tertinggi</h3>
        <a href="{{ route('buyer.marketplace') }}" class="text-success text-decoration-none fw-semibold small">
            Lihat Semua Marketplace →
        </a>
    </div>

    <div class="row row-cards g-4">
        @php
            // Mengambil hingga 20 produk aktif, diurutkan berdasarkan rata-rata rating review tertinggi
            $topProducts = \App\Models\Product::with('category', 'unit')
                ->withAvg('reviews as average_rating', 'rating')
                ->where('is_active', true)
                ->orderByDesc('average_rating')
                ->take(20)
                ->get();
        @endphp

        @forelse($topProducts as $product)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="card product-card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:210px;object-fit:cover;" alt="{{ $product->name }}">
                @else
                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height:210px; font-size:48px;">
                        🌿
                    </div>
                @endif
                <div class="card-body d-flex flex-column p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-medium">
                            {{ $product->category->name ?? '-' }}
                        </span>
                        
                        {{-- Menampilkan Rata-rata Rating Bintang --}}
                        @if($product->average_rating)
                            <span class="small fw-bold text-warning">
                                ⭐ {{ number_format($product->average_rating, 1) }}
                            </span>
                        @else
                            <span class="small text-muted" style="font-size: 0.75rem;">Belum ada rating</span>
                        @endif
                    </div>
                    
                    <h5 class="card-title fw-bold text-dark mb-3 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                        <span class="fw-bolder text-success fs-5">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        
                        <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm fw-bold">
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