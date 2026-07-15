@extends('layouts.buyer')

@section('content')

{{-- Greeting --}}
<div class="mb-5 text-center">
    <h1 class="fw-bold">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
    <p class="text-muted">Temukan produk pertanian segar langsung dari sumbernya</p>
    <a href="{{ route('buyer.marketplace') }}" class="btn btn-success btn-lg mt-2">
        🛒 Jelajahi Marketplace
    </a>
</div>

{{-- Featured Products --}}
<h3 class="fw-bold mb-3">Produk Unggulan</h3>
<div class="row row-cards">
    @foreach(\App\Models\Product::with('category', 'unit')->where('is_active', true)->latest()->take(4)->get() as $product)
    <div class="col-sm-6 col-lg-3">
        <div class="card product-card h-100">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:180px;object-fit:cover;" alt="{{ $product->name }}">
            @else
                <div style="height:180px;background:#f4f6f8;display:flex;align-items:center;justify-content:center;font-size:48px;">
                    🌿
                </div>
            @endif
            <div class="card-body">
                <span class="badge bg-success-lt text-success mb-1">{{ $product->category->name ?? '-' }}</span>
                <h4 class="card-title">{{ $product->name }}</h4>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="fw-bold text-success">${{ number_format($product->price, 2) }}</span>
                    <a href="{{ route('buyer.marketplace.show', $product) }}" class="btn btn-sm btn-success">Lihat</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection