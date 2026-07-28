@extends('layouts.admin')

@section('title', 'Ubah Jenis Tanaman')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Jenis Tanaman</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.crop-types.update', $cropType) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $cropType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Panen <span class="text-danger">*</span></label>
                        <select name="harvest_type" class="form-select @error('harvest_type') is-invalid @enderror" required>
                            <option value="">-- Pilih tipe panen --</option>
                            <option value="Sekali Panen" {{ old('harvest_type', $cropType->harvest_type) == 'Sekali Panen' ? 'selected' : '' }}>
                                🔒 Sekali Panen (contoh: Padi, Jagung — langsung selesai setelah dipanen)
                            </option>
                            <option value="Panen Berkelanjutan" {{ old('harvest_type', $cropType->harvest_type) == 'Panen Berkelanjutan' ? 'selected' : '' }}>
                                🔁 Panen Berkelanjutan (contoh: Cabai, Tomat — bisa dipanen berkali-kali)
                            </option>
                        </select>
                        <small class="text-muted">Menentukan apakah status tanaman otomatis terkunci setelah panen pertama.</small>
                        @if($cropType->crops()->exists())
                            <small class="text-warning d-block mt-1"><i class="bi bi-exclamation-triangle me-1"></i>Jenis tanaman ini sudah dipakai di {{ $cropType->crops()->count() }} data tanaman. Mengubah tipe panen tidak mengubah status tanaman yang sudah ada.</small>
                        @endif
                        @error('harvest_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $cropType->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.crop-types.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection