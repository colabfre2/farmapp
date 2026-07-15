@extends('layouts.admin')
@section('title', 'Tambah Perawatan Tanaman')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🧪 Tambah Perawatan Tanaman</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.plant-cares.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Pupuk" {{ old('type') == 'Pupuk' ? 'selected' : '' }}>Pupuk</option>
                            <option value="Penyiraman" {{ old('type') == 'Penyiraman' ? 'selected' : '' }}>Penyiraman</option>
                            <option value="Pestisida" {{ old('type') == 'Pestisida' ? 'selected' : '' }}>Pestisida</option>
                            <option value="Pemangkasan" {{ old('type') == 'Pemangkasan' ? 'selected' : '' }}>Pemangkasan</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="cth: kg, liter, ml">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Stok Awal</label>
                            <input type="number" step="0.01" name="stock" class="form-control" value="{{ old('stock', 0) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Harga per Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="form-control" value="{{ old('price_per_unit', 0) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.plant-cares.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection