@extends('layouts.admin')
@section('title', 'Ubah Kandang')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">🏠 Ubah Kandang</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.kandangs.update', $kandang) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Hewan <span class="text-danger">*</span></label>
                        <select name="livestock_type_id" class="form-select @error('livestock_type_id') is-invalid @enderror" required>
                            <option value="">-- Pilih jenis hewan --</option>
                            @foreach($livestockTypes as $type)
                                <option value="{{ $type->id }}" {{ old('livestock_type_id', $kandang->livestock_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @if($kandang->livestocks()->exists())
                            <small class="text-warning d-block mt-1">⚠️ Kandang ini sudah dipakai oleh {{ $kandang->livestocks()->count() }} data ternak. Mengubah jenis hewan tidak mengubah data ternak yang sudah ada.</small>
                        @endif
                        @error('livestock_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kandang</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $kandang->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kapasitas Maksimal (ekor)</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $kandang->capacity) }}">
                        @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lokasi</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $kandang->location) }}">
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $kandang->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.kandangs.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection