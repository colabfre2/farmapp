@extends('layouts.admin')

@section('title', 'Record Harvest')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Catat panen</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.harvests.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" required>
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal panen</label>
                        <input type="date" name="harvested_at" class="form-control @error('harvested_at') is-invalid @enderror" value="{{ old('harvested_at') }}" required>
                        @error('harvested_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" step="0.01" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga jual (per satuan)</label>
                        <input type="text" inputmode="numeric" name="selling_price" class="form-control input-rupiah @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" required>
                        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.harvests.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection