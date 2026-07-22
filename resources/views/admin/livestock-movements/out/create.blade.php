@extends('layouts.admin')
@section('title', 'Tambah Ternak Keluar')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">⬇ Tambah Ternak Keluar</h3></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.livestock-movements.out.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kandang</label>
                        <select name="livestock_id" class="form-select @error('livestock_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kandang --</option>
                            @foreach($livestocks as $livestock)
                                <option value="{{ $livestock->id }}" {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>
                                    {{ $livestock->name }} (Jumlah saat ini: {{ $livestock->quantity }} ekor)
                                </option>
                            @endforeach
                        </select>
                        @error('livestock_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah (ekor)</label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" min="1" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan</label>
                        <select name="reason" class="form-select">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Dijual" {{ old('reason') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
                            <option value="Mati" {{ old('reason') == 'Mati' ? 'selected' : '' }}>Mati</option>
                            <option value="Dipindah ke Kandang Lain" {{ old('reason') == 'Dipindah ke Kandang Lain' ? 'selected' : '' }}>Dipindah ke Kandang Lain</option>
                            <option value="Koreksi Data" {{ old('reason') == 'Koreksi Data' ? 'selected' : '' }}>Koreksi Data</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Simpan</button>
                        <a href="{{ route('admin.livestock-movements.out.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection