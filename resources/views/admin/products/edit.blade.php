@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah produk</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama produk</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga </label>
                        <input type="text" inputmode="numeric" name="price" class="form-control input-rupiah @error('price') is-invalid @enderror" value="{{ old('price', isset($product) ? 'Rp. ' . number_format($product->price, 0, ',', '.') : '') }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @if($product->image)
                    <div class="mb-3">
                        <label class="form-label">Foto saat ini</label><br>
                        <img src="{{ asset('storage/'.$product->image) }}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Ubah foto produk</label>
                        <div class="mt-2">
                            <img id="imagePreview" src="" style="display:none; width:150px; height:150px; object-fit:cover; border-radius:8px;">
                        </div>
                        <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Active (visible in marketplace)</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection