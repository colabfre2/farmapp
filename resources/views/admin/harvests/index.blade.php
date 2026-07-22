@extends('layouts.admin')

@section('title', 'Panen')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Panen</h3>

                    <form method="GET" action="{{ route('admin.harvests.index') }}" class="d-flex align-items-center gap-2">
                        <label for="harvestSearch" class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input
                            type="text"
                            id="harvestSearch"
                            name="q"
                            class="form-control form-control-sm"
                            placeholder="Cari panen..."
                            value="{{ $query ?? '' }}"
                            style="width:200px;"
                        >
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.harvests.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.harvests.export') }}" class="btn btn-success btn-sm">📊 Export Excel</a>
                        <a href="{{ route('admin.harvests.create') }}" class="btn btn-primary btn-sm">+ Catat panen</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">
                        Menampilkan hasil untuk "<strong>{{ $query }}</strong>" — {{ $harvests->count() }} found
                    </p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanaman yang di panen</th>
                            <th>Tanggal panen</th>
                            <th>Jumlah</th>
                            <th>Harga jual</th>
                            <th>Total nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($harvests as $harvest)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $harvest->crop->name ?? "Data tanaman di hapus" }}</td>
                            <td>{{ \Carbon\Carbon::parse($harvest->harvested_at)->format('d M Y') }}</td>
                            <!-- Menggunakan ->symbol agar yang tampil adalah "lsn" atau "g" -->
                            <td>{{ $harvest->quantity }} {{ $harvest->unit->symbol }}</td>
                            <td>{{ rupiah($harvest->selling_price) }} / {{ $harvest->unit->symbol }}</td>

                            <!-- ATAU jika ingin menampilkan nama lengkapnya (Lusin / Gram) -->
                            <!-- <td>{{ $harvest->quantity }} {{ $harvest->unit->name }}</td> -->
                            <td class="fw-bold text-success">{{ rupiah($harvest->total_value) }}</td>
                            <td>
                                <a href="{{ route('admin.harvests.edit', $harvest) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.harvests.destroy', $harvest) }}" style="display:inline" class="form-delete">
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
                                    Tidak di temukan panen dari kata : "{{ $query }}"
                                @else
                                    Tidak ada panen
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
