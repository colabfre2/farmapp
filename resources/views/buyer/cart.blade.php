@extends('layouts.buyer')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        🛒 Keranjang saya
    </h2>
    @if(!empty($cart))
    <form action="{{ route('buyer.cart.clear') }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Kosongkan semua item?')">🗑️ Kosongkan Keranjang</button>
    </form>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success')}}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    ✕ {{ session('error')}}
</div>
@endif

@if(empty($cart))
    <div class="text-center py-5 text-muted">
        <div class="font-size:64px">🛒</div>
        <h4>Keranjang kamu kosong</h4>
        <a href="{{ route('buyer.marketplace') }}" class="btn btn-success mt-3">Jelajahi marketplace</a>
    
    </div>
    @else
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/'.$item['image']) }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                        @else
                                            <div style="width:50px;height:50px;background:#f4f6f8;border-radius:8px;display:flex;align-items:center;justify-content:center;">🌿</div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <div class="text-muted small">{{ rupiah($item['price']) }} / {{ $item['unit'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ rupiah($item['price']) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('buyer.cart.update', $id) }}" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="form-control form-control-sm" style="width:70px;" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="fw-bold text-success">{{ rupiah($item['price'] * $item['quantity']) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('buyer.cart.remove', $id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">✕</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ringkasan Pesanan</h3>
                </div>
                <div class="card-body">
                    @foreach($cart as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                        <span>{{ rupiah($item['price'] * $item['quantity']) }}</span>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h4">
                        <span>Total</span>
                        <span class="text-success">{{ rupiah($total) }}</span>
                    </div>
                    <a href="{{ route('buyer.checkout')}}" class="btn btn-success w-100 mt-3 btn-lg">
                        Checkout →
                    </a>
                    <a href="{{ route('buyer.marketplace') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Lanjut belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection