@extends('layouts.admin')

@section('title', 'Perbarui tumbuhan')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Perbarui tumbuhan</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.crops.update', $crop) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $crop->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenit tanaman</label>
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
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tanggal tanam</label>
                            <input type="date" name="planted_at" class="form-control @error('planted_at') is-invalid @enderror" value="{{ old('planted_at', $crop->planted_at) }}" required>
                            @error('planted_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Perkiraan panen</label>
                            <input type="date" name="expected_harvest_at" class="form-control @error('expected_harvest_at') is-invalid @enderror" value="{{ old('expected_harvest_at', $crop->expected_harvest_at) }}" required>
                            @error('expected_harvest_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Panen Aktual</label>
                        <input type="date" name="actual_harvest_at" class="form-control" value="{{ old('actual_harvest_at', $crop->actual_harvest_at) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Bibit" {{ old('status', $crop->status) == 'Bibit' ? 'selected' : '' }}>Bibit</option>
                            <option value="Pertumbuhan" {{ old('status', $crop->status) == 'Pertumbuhan' ? 'selected' : '' }}>Pertumbuhan</option>
                            <option value="Dipanen" {{ old('status', $crop->status) == 'Dipanen' ? 'selected' : '' }}>Dipanen</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $crop->notes) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('admin.crops.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection