@extends('layouts.buyer')
@section('title', 'Marketplace ALMS')

@section('content')
<div class="container-fluid px-2 py-2">

    {{-- Header Halaman --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1 text-dark">🛒 ALMS Marketplace</h3>
        <p class="text-muted small mb-0">Temukan produk pertanian dan peternakan berkualitas langsung dari sumbernya.</p>
        @if(request('search'))
            <p class="small text-success mt-2 fw-semibold">Menampilkan hasil pencarian untuk: "{{ request('search') }}"</p>
        @endif
    </div>

    {{-- Layout Utama: Sidebar Kiri Mentok & Produk Kanan --}}
    <div class="row g-4">
        
        {{-- SIDEBAR FILTER KATEGORI (MENTOK KIRI) --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="border-radius: 12px; top: 90px;">
                <h5 class="fw-bold text-dark mb-3">📂 Kategori Produk</h5>
                
                <div class="list-group list-group-flush gap-2">
                    {{-- Tombol Semua Produk (Menampilkan total hasil pencarian dinamis) --}}
                    <a href="{{ route('buyer.marketplace', ['search' => request('search') ?? request('q')]) }}" 
                       class="list-group-item list-group-item-action rounded-3 py-3 px-3 fw-semibold d-flex justify-content-between align-items-center {{ !request('category') ? 'bg-success text-white' : 'text-dark bg-transparent' }}">
                        <span>Semua Produk</span>
                        <span class="badge {{ !request('category') ? 'bg-white text-success' : 'bg-light text-dark' }} rounded-pill px-3 py-2">
                            {{ $totalSearchCount ?? 0 }}
                        </span>
                    </a>

                    {{-- List Kategori dari Database dengan Hitungan Dinamis Berdasarkan Search --}}
                    @foreach($categories as $cat)
                        @php
                            $productCount = $categoryCounts[$cat->id] ?? 0;
                        @endphp
                        <a href="{{ route('buyer.marketplace', ['category' => $cat->id, 'search' => request('search') ?? request('q')]) }}" 
                           class="list-group-item list-group-item-action rounded-3 py-3 px-3 fw-semibold d-flex justify-content-between align-items-center {{ request('category') == $cat->id ? 'bg-success text-white' : 'text-dark bg-transparent' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="badge {{ request('category') == $cat->id ? 'bg-white text-success' : 'bg-light text-dark' }} rounded-pill px-3 py-2">
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
                    <div class="card product-card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:210px;object-fit:cover;" alt="{{ $product->name }}">
                        @else
                            <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="height:210px; font-size:48px;">
                                🌿
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column p-4">
                            <div class="mb-2">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 small fw-medium">
                                    {{ $product->category->name ?? '-' }}
                                </span>
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