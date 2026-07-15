@extends('layouts.admin')
@section('title', 'Ubah Perawatan Tanaman')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🧪 Ubah Perawatan Tanaman</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.plant-cares.update', $plantCare) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $plantCare->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis</label>
                        <select name="type" class="form-select" required>
                            <option value="Pupuk" {{ old('type', $plantCare->type) == 'Pupuk' ? 'selected' : '' }}>Pupuk</option>
                            <option value="Penyiraman" {{ old('type', $plantCare->type) == 'Penyiraman' ? 'selected' : '' }}>Penyiraman</option>
                            <option value="Pestisida" {{ old('type', $plantCare->type) == 'Pestisida' ? 'selected' : '' }}>Pestisida</option>
                            <option value="Pemangkasan" {{ old('type', $plantCare->type) == 'Pemangkasan' ? 'selected' : '' }}>Pemangkasan</option>
                            <option value="Lainnya" {{ old('type', $plantCare->type) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $plantCare->unit) }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" step="0.01" name="stock" class="form-control" value="{{ old('stock', $plantCare->stock) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga per Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" value="{{ old('price_per_unit', $plantCare->price_per_unit) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $plantCare->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.plant-cares.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection