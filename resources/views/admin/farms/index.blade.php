@extends('layouts.admin')
@section('title', 'Data Ladang')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">🚜 Data Ladang</h3>
                    <form method="GET" action="{{ route('admin.farms.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Ladang :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari ladang..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.farms.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.farms.create') }}" class="btn btn-primary btn-sm">+ Tambah Ladang</a>
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
                            <th>Nama Ladang</th>
                            <th>Ukuran</th>
                            <th>Tanaman Aktif</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($farms as $farm)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $farm->name }}</td>
                            <td>
                                @if($farm->area_size)
                                    {{ number_format($farm->area_size, 2) }} {{ $farm->area_unit }}
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="badge bg-success-lt text-success">{{ $farm->crops_count }} tanaman</span></td>
                            <td>{{ $farm->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.farms.edit', $farm) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.farms.destroy', $farm) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus ladang ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data ladang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection