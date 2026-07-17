@extends('layouts.admin')

@section('title', 'Catat Panen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Catat Panen Baru</h2>
    <a href="{{ route('admin.harvests.index') }}" class="btn btn-outline-secondary">← Kembali</a>
</div>

<form method="POST" action="{{ route('admin.harvests.store') }}">
    @csrf
    <div class="row">
        {{-- KOLOM KIRI --}}
        <div class="col-lg-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Detail Panen</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Tanaman <span class="text-danger">*</span></label>
                        <select name="crop_id" class="form-select @error('crop_id') is-invalid @enderror" required>
                            <option value="">-- Pilih tanaman yang dipanen --</option>
                            @foreach($crops as $crop)
                                <option value="{{ $crop->id }}" {{ old('crop_id') == $crop->id ? 'selected' : '' }}>
                                    {{ $crop->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('crop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Panen <span class="text-danger">*</span></label>
                        <input type="date" name="harvested_at" class="form-control @error('harvested_at') is-invalid @enderror" value="{{ old('harvested_at', date('Y-m-d')) }}" required>
                        @error('harvested_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">-- Pilih satuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->symbol }})</option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h3 class="card-title fw-bold">Finansial & Info</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga Jual (per satuan) <span class="text-danger">*</span></label>
                        <input type="text" inputmode="numeric" name="selling_price" class="form-control input-rupiah @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" required>
                        @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Opsional...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg fw-bold">Simpan Data Panen ✓</button>
            </div>
        </div>
    </div>
</form>
@endsection