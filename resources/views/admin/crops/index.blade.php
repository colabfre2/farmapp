@extends('layouts.admin')

@section('title', 'Tanaman')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Tanaman</h3>

                    <form method="GET" action="{{ route('admin.crops.index') }}" class="d-flex align-items-center gap-2">
                        <label for="cropSearch" class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input
                            type="text"
                            id="cropSearch"
                            name="q"
                            class="form-control form-control-sm"
                            placeholder="Cari Tanaman..."
                            value="{{ $query ?? '' }}"
                            style="width:200px;"
                        >
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.crops.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.crops.trash') }}" class="btn btn-outline-danger btn-sm">🗑️ Sampah</a>
                        <a href="{{ route('admin.crops.create') }}" class="btn btn-primary btn-sm">+ Tambah Tanaman</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">
                        Menampilkan hasil untuk "<strong>{{ $query }}</strong>" — {{ $crops->count() }} found
                    </p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Tanggal tanam</th>
                            <th>Perkiraan panen</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($crops as $crop)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $crop->name }}</td>
                            <td>{{ $crop->cropType->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($crop->planted_at)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($crop->expected_harvest_at)->format('d M Y') }}</td>
                            <td>
                                <span class="badge
                                    @if($crop->status == 'Bibit') bg-info
                                    @elseif($crop->status == 'Pertumbuhan') bg-warning text-dark
                                    @else bg-success
                                    @endif">
                                    {{ $crop->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.crops.edit', $crop) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.crops.destroy', $crop) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                @if(!empty($query))
                                    Tidak ada tanaman dari pencarian : "{{ $query }}"
                                @else
                                    Belum ada tanaman
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection