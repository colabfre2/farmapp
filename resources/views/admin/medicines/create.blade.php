@extends('layouts.admin')
@section('title', 'Tambah Obat Ternak')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">💊 Tambah Obat Ternak</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.medicines.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Obat</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis</label>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}" placeholder="cth: Antibiotik, Vitamin, Vaksin">
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" placeholder="cth: ml, tablet, cc">
                            @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Stok Awal</label>
                            <input type="number" step="0.01" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga per Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', 0) }}">
                        @error('price_per_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.medicines.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection