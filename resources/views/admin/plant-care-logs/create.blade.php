@extends('layouts.admin')
@section('title', 'Catat Perawatan Tanaman')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">📋 Catat Perawatan Tanaman</h3></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.plant-care-logs.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Perawatan</label>
                        <select name="plant_care_id" class="form-select @error('plant_care_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Perawatan --</option>
                            @foreach($plantCares as $plantCare)
                                <option value="{{ $plantCare->id }}" {{ old('plant_care_id') == $plantCare->id ? 'selected' : '' }}>
                                    {{ $plantCare->name }} ({{ $plantCare->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('plant_care_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanaman</label>
                        <select name="crop_id" class="form-select @error('crop_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Tanaman --</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->name }} ({{ $crop->status }})
                                </option>
                            @endforeach
                        </select>
                        @error('crop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah (opsional)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="Kosongkan jika tidak ada takaran">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Perawatan</label>
                        <input type="date" name="cared_at" class="form-control" value="{{ old('cared_at', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.plant-care-logs.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection