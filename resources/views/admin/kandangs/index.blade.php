@extends('layouts.admin')
@section('title', 'Data Kandang')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">🏠 Data Kandang</h3>
                    <form method="GET" action="{{ route('admin.kandangs.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Kandang :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari kandang..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.kandangs.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.kandangs.create') }}" class="btn btn-primary btn-sm">+ Tambah Kandang</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kandang</th>
                            <th>Jenis Hewan</th>
                            <th>Kapasitas</th>
                            <th>Lokasi</th>
                            <th>Kelompok Ternak Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kandangs as $kandang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $kandang->name }}</td>
                            <td><span class="badge bg-info-lt text-info">{{ $kandang->livestockType->name ?? '-' }}</span></td>
                            <td>{{ $kandang->capacity ? $kandang->capacity . ' ekor' : '-' }}</td>
                            <td>{{ $kandang->location ?? '-' }}</td>
                            <td><span class="badge bg-success-lt text-success">{{ $kandang->livestocks_count }} kelompok</span></td>
                            <td>
                                <a href="{{ route('admin.kandangs.edit', $kandang) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.kandangs.destroy', $kandang) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kandang ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data kandang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection