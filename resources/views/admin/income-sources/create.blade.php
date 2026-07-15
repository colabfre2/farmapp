@extends('layouts.admin')
@section('title', 'Tambah Sumber Pemasukan')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">💵 Tambah Sumber Pemasukan</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.income-sources.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Sumber</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="cth: Penjualan Marketplace" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.income-sources.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection