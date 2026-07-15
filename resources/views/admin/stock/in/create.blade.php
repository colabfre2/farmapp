@extends('layouts.admin')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">⬆ Tambah Barang Masuk</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.stock.in.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product</label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" min="1" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason</label>
                        <select name="reason" class="form-select">
                            <option value="">-- Select Reason --</option>
                            <option value="Restock" {{ old('reason') == 'Restock' ? 'selected' : '' }}>Restock</option>
                            <option value="Return dari buyer" {{ old('reason') == 'Return dari buyer' ? 'selected' : '' }}>Return dari buyer</option>
                            <option value="Koreksi stok" {{ old('reason') == 'Koreksi stok' ? 'selected' : '' }}>Koreksi stok</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ route('admin.stock.in.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection