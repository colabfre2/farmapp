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
            <p class="text-muted">{{ $product->description }}</p>

            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="h2 fw-bold text-success mb-0">{{ rupiah($product->price) }}</span>
                <span class="text-muted">/ {{ $product->unit->symbol ?? '' }}</span>
            </div>

            <div class="mb-4">
                <form action="{{ route('buyer.cart.add', $product) }}" method="post">
                    @csrf
                    <input type="hidden" name="quantity" id="qtyHidden" value="1">
                    <button type="submit" class="btn btn-succes btn-lg" onclick="document.getElementById('qtyHidden').value = document.getElementById('qtyInput').value">
                        Tambah ke keranjang
                    </button>
                </form>
                <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary btn-lg"><= Kembali</a>
            
            </div>

                        
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>

@endsection