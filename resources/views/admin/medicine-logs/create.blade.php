@extends('layouts.admin')
@section('title', 'Catat Pemberian Obat')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">📋 Catat Pemberian Obat</h3></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.medicine-logs.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Obat</label>
                        <select name="medicine_id" class="form-select @error('medicine_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Obat --</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                    {{ $medicine->name }} (Stok: {{ $medicine->stock }} {{ $medicine->unit }})
                                </option>
                            @endforeach
                        </select>
                        @error('medicine_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ternak</label>
                        <select name="livestock_id" class="form-select @error('livestock_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Ternak --</option>
                            @foreach($livestocks as $livestock)
                                <option value="{{ $livestock->id }}" {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>
                                    {{ $livestock->name }} ({{ $livestock->quantity }} ekor)
                                </option>
                            @endforeach
                        </select>
                        @error('livestock_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dosis</label>
                        <input type="number" step="0.01" name="dose" class="form-control @error('dose') is-invalid @enderror" value="{{ old('dose') }}" required>
                        @error('dose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Pemberian</label>
                        <input type="date" name="given_at" class="form-control @error('given_at') is-invalid @enderror" value="{{ old('given_at', date('Y-m-d')) }}" required>
                        @error('given_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="cth: Sakit, Pencegahan, Vaksinasi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.medicine-logs.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection