@extends('layouts.buyer')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">🛒 Marketplace</h2>
    <span class="text-muted">{{ $products->count() }} Produk tersedia</span>
</div>

{{-- Search & Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('buyer.marketplace') }}" class="d-flex gap-3 flex-wrap">
            <input type="text" name="q" class="form-control" placeholder="Cari produk..." value="{{ $query ?? '' }}" style="max-width:300px;">
            <select name="category" class="form-select" style="max-width:200px;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-success">🔍 Cari</button>
            @if(!empty($query) || !empty($categoryId))
                <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary">✕ Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- Products Grid --}}
@if($products->isEmpty())
    <div class="text-center py-5 text-muted">
        <div style="font-size:48px">🌾</div>
        <h4>Produk tidak ditemukan</h4>
        <p>Coba Ganti kata kunci atau kategori lain
</p>
    </div>
@else
    <div class="row row-cards">
        @foreach($products as $product)
        <div class="col-sm-6 col-lg-3">
            <div class="card product-card h-100">
                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:200px;object-fit:cover;" alt="{{ $product->name }}">
                @else
                    <div style="height:200px;background:#f4f6f8;display:flex;align-items:center;justify-content:center;font-size:48px;">
                        🌿
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="mb-1">
                        <span class="badge bg-success-lt text-success">{{ $product->category->name ?? '-' }}</span>
                    </div>
                    <h4 class="card-title mb-1">{{ $product->name }}</h4>
                    <p class="text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h3 mb-0 fw-bold text-success">{{ rupiah($product->price) }}</span>
                            <span class="text-muted small">/ {{ $product->unit->symbol ?? '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Stok: {{ $product->stock }}</span>
                        </div>
                        <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-success w-100">Lihat detail</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection