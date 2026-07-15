@extends('layouts.admin')

@section('title', 'Ternak')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Ternak</h3>

                    <form method="GET" action="{{ route('admin.livestock.index') }}" class="d-flex align-items-center gap-2">
                        <label for="livestockSearch" class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input
                            type="text"
                            id="livestockSearch"
                            name="q"
                            class="form-control form-control-sm"
                            placeholder="Cari Ternak..."
                            value="{{ $query ?? '' }}"
                            style="width:200px;"
                        >
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.livestock.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.livestock.trash') }}" class="btn btn-outline-danger btn-sm">🗑️ Sampah</a>
                        <a href="{{ route('admin.livestock.create') }}" class="btn btn-primary btn-sm">+ Tambah Ternak</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">
                        Showing results for "<strong>{{ $query }}</strong>" — {{ $livestocks->count() }} found
                    </p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Berat rata-rata</th>
                            <th>Status kesehatan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livestocks as $livestock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $livestock->name }}</td>
                            <td>{{ $livestock->livestockType->name ?? '-' }}</td>
                            <td>{{ $livestock->quantity }}</td>
                            <td>{{ $livestock->avg_weight ?? '-' }}</td>
                            <td>
                                <span class="badge
                                    @if($livestock->health_status == 'Sehat') bg-success
                                    @elseif($livestock->health_status == 'Pemantauan') bg-warning text-dark
                                    @else bg-danger
                                    @endif">
                                    {{ $livestock->health_status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.livestock.edit', $livestock) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.livestock.destroy', $livestock) }}" style="display:inline" class="form-delete">
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
                                    No livestock found for "{{ $query }}"
                                @else
                                    No livestock yet
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