@extends('layouts.admin')
@section('title', 'Ubah Ladang')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🚜 Ubah Ladang</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.farms.update', $farm) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Ladang</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $farm->name) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Ukuran</label>
                            <input type="number" step="0.01" name="area_size" class="form-control" value="{{ old('area_size', $farm->area_size) }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <select name="area_unit" class="form-select">
                                <option value="m²" {{ old('area_unit', $farm->area_unit) == 'm²' ? 'selected' : '' }}>m²</option>
                                <option value="hektar" {{ old('area_unit', $farm->area_unit) == 'hektar' ? 'selected' : '' }}>hektar</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $farm->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.farms.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection