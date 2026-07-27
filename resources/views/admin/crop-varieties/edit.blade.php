@extends('layouts.admin')
@section('title', 'Ubah Varian Tanaman')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🌿 Ubah Varian Tanaman</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.crop-varieties.update', $cropVariety) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Tanaman</label>
                        <select name="crop_type_id" class="form-select" required>
                            @foreach($cropTypes as $type)
                                <option value="{{ $type->id }}" {{ old('crop_type_id', $cropVariety->crop_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Varian</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $cropVariety->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $cropVariety->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.crop-varieties.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection