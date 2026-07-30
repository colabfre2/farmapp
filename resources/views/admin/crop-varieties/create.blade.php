@extends('layouts.admin')
@section('title', 'Tambah Varian Tanaman')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🌿 Tambah Varian Tanaman</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.crop-varieties.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Tanaman</label>
                        <select name="crop_type_id" class="form-select @error('crop_type_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($cropTypes as $type)
                                <option value="{{ $type->id }}" {{ old('crop_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('crop_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Varian</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="cth: Tomat Jawa" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.crop-varieties.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection