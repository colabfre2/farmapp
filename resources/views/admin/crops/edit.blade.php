@extends('layouts.admin')

@section('title', 'Perbarui Tanaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Perbarui Tanaman</h2>
    <a href="{{ route('admin.crops.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.crops.update', $crop) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        {{-- KOLOM KIRI (Informasi Utama) --}}
        <div class="col-lg-8">
            
            {{-- Card Informasi Dasar --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tanaman <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $crop->name) }}" placeholder="e.g. Paddy Field A" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tanaman <span class="text-danger">*</span></label>
                        <select name="crop_type_id" class="form-select @error('crop_type_id') is-invalid @enderror" required>
                            <option value="">-- Pilih jenis --</option>
                            @foreach($cropTypes as $type)
                                <option value="{{ $type->id }}" {{ old('crop_type_id', $crop->crop_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('crop_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Tambahkan keterangan tentang proses tanam ini...">{{ old('notes', $crop->notes) }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (Jadwal & Status) --}}
        <div class="col-lg-4">
            
            {{-- Card Jadwal Tanam --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Jadwal Tanam</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Tanam <span class="text-danger">*</span></label>
                        <input type="date" name="planted_at" class="form-control @error('planted_at') is-invalid @enderror" value="{{ old('planted_at', $crop->planted_at) }}" required>
                        @error('planted_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perkiraan Panen <span class="text-danger">*</span></label>
                        <input type="date" name="expected_harvest_at" class="form-control @error('expected_harvest_at') is-invalid @enderror" value="{{ old('expected_harvest_at', $crop->expected_harvest_at) }}" required>
                        @error('expected_harvest_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Panen Aktual</label>
                        <input type="date" name="actual_harvest_at" class="form-control @error('actual_harvest_at') is-invalid @enderror" value="{{ old('actual_harvest_at', $crop->actual_harvest_at) }}">
                        @error('actual_harvest_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Card Status --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Status Pertumbuhan</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Bibit" {{ old('status', $crop->status) == 'Bibit' ? 'selected' : '' }}>🌱 Bibit</option>
                            <option value="Pertumbuhan" {{ old('status', $crop->status) == 'Pertumbuhan' ? 'selected' : '' }}>🌿 Pertumbuhan</option>
                            <option value="Dipanen" {{ old('status', $crop->status) == 'Dipanen' ? 'selected' : '' }}>🌾 Dipanen</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Aksi Submit --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-bold">Perbarui Tanaman ✓</button>
            </div>

        </div>
    </div>
</form>
@endsection