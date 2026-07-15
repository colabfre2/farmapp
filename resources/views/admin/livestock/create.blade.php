@extends('layouts.admin')

@section('title', 'Tambah Ternak')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Ternak</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.livestock.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Chicken Coop #1" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis ternak</label>
                        <select name="livestock_type_id" class="form-select @error('livestock_type_id') is-invalid @enderror" required>
                            <option value="">-- Pilih jenis --</option>
                            @foreach($livestockTypes as $type)
                                <option value="{{ $type->id }}" {{ old('livestock_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('livestock_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Rata-rata berat</label>
                            <input type="text" name="avg_weight" class="form-control" placeholder="e.g. 2.1 kg avg" value="{{ old('avg_weight') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status kesehatan</label>
                        <select name="health_status" class="form-select @error('health_status') is-invalid @enderror" required>
                            <option value="Sehat" {{ old('health_status') == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                            <option value="Pemantauan" {{ old('health_status') == 'Pemantauan' ? 'selected' : '' }}>Pemantauan</option>
                            <option value="Sakit" {{ old('health_status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        </select>
                        @error('health_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.livestock.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection