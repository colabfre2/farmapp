@extends('layouts.admin')
@section('title', 'Ubah Obat Ternak')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">💊 Ubah Obat Ternak</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.medicines.update', $medicine) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Obat</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $medicine->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type', $medicine->type) }}">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $medicine->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" step="0.01" name="stock" class="form-control" value="{{ old('stock', $medicine->stock) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga per Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" value="{{ old('price_per_unit', $medicine->price_per_unit) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $medicine->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.medicines.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection