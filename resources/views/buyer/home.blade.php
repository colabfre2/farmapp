@extends('layouts.buyer')
@section('title', 'Beranda FarmApp')

@section('content')
<div class="container-fluid px-2 py-2">

    {{-- Greeting Banner Modern Flat Design (Responsive Padding & Text) --}}
    <div class="p-4 p-md-5 mb-4 mb-md-5 rounded-4 shadow-sm border-0 text-white position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); border-radius: 16px !important;">
        <div class="position-relative z-1 py-2 py-md-3 text-center">
            <h1 class="fw-bold mb-2 fs-3 fs-md-1">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-white-50 mb-3 mb-md-4 fs-6 fs-md-5 px-2 px-md-0">Temukan produk pertanian dan peternakan unggulan dengan rating tertinggi di FarmApp.</p>
            <a href="{{ route('buyer.marketplace') }}" class="btn btn-success btn-sm btn-md-lg rounded-pill px-4 py-2 shadow-sm fw-bold">
                🛒 Jelajahi Marketplace
            </a>
        </div>
    </div>

    {{-- Top-Rated Products Section (Responsive Flexbox) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
        <h4 class="fw-bold mb-0 text-dark fs-5 fs-md-4">⭐ Produk Paling Diunggulkan</h4>
        <a href="{{ route('buyer.marketplace') }}" class="text-success text-decoration-none fw-semibold small">
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
        {{-- Ubah Grid Mobile jadi 2 Kolom (col-6) --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card product-card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top bg-light" style="height:150px; height:md:210px; width:100%; object-fit:contain; padding:1rem;" alt="{{ $product->name }}">
                @else
                    <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height:150px; font-size:36px;">
                        🌿
                    </div>
                @endif
                <div class="card-body d-flex flex-column p-3 p-md-4">
                    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center mb-2 gap-1">
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                            {{ $product->category->name ?? '-' }}
                        </span>
                        
                        @if($product->average_rating)
                            <span class="fw-bold text-warning" style="font-size: 0.75rem;">
                                ⭐ {{ number_format($product->average_rating, 1) }}
                            </span>
                        @endif
                    </div>
                    
                    <h6 class="card-title fw-bold text-dark mb-2 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-auto pt-2 gap-2">
                        <span class="fw-bolder text-success fs-6 fs-md-5">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        
                        <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold w-100 w-md-auto">
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