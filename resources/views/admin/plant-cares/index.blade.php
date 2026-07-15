@extends('layouts.admin')
@section('title', 'Master Perawatan Tanaman')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">🧪 Master Perawatan Tanaman</h3>
                    <form method="GET" action="{{ route('admin.plant-cares.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Perawatan :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.plant-cares.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.plant-cares.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
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
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Harga/Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plantCares as $plantCare)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $plantCare->name }}</td>
                            <td><span class="badge bg-success-lt text-success">{{ $plantCare->type }}</span></td>
                            <td>{{ $plantCare->unit ?? '-' }}</td>
                            <td>{{ $plantCare->stock }}</td>
                            <td>{{ rupiah($plantCare->price_per_unit) }}</td>
                            <td>
                                <a href="{{ route('admin.plant-cares.edit', $plantCare) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.plant-cares.destroy', $plantCare) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data perawatan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection