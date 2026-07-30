@extends('layouts.admin')
@section('title', 'Varian Tanaman')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">🌿 Varian Tanaman</h3>
                    <form method="GET" action="{{ route('admin.crop-varieties.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Varian :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari varian..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.crop-varieties.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.crop-varieties.create') }}" class="btn btn-primary btn-sm">+ Tambah Varian</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Varian</th>
                            <th>Jenis Tanaman</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cropVarieties as $variety)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $variety->name }}</td>
                            <td>{{ $variety->cropType->name ?? '-' }}</td>
                            <td>{{ $variety->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.crop-varieties.edit', $variety) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.crop-varieties.destroy', $variety) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus varian ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada varian tanaman</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection