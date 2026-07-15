@extends('layouts.admin')
@section('title', 'Obat Ternak')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">💊 Obat Ternak</h3>
                    <form method="GET" action="{{ route('admin.medicines.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Obat :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari obat..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.medicines.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.medicines.create') }}" class="btn btn-primary btn-sm">+ Tambah Obat</a>
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
                        @forelse($medicines as $medicine)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->type }}</td>
                            <td>{{ $medicine->unit }}</td>
                            <td>{{ $medicine->stock }}</td>
                            <td>{{ rupiah($medicine->price_per_unit) }}</td>
                            <td>
                                <a href="{{ route('admin.medicines.edit', $medicine) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.medicines.destroy', $medicine) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data obat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection