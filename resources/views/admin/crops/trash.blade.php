@extends('layouts.admin')

@section('title', 'Sampah Tanaman')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">🗑️ Sampah Tanaman</h3>
                <a href="{{ route('admin.crops.index') }}" class="btn btn-secondary btn-sm">← Kembali ke Tanaman</a>
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
                            <th>Status</th>
                            <th>Dihapus Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($crops as $crop)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $crop->name }}</td>
                            <td>{{ $crop->cropType->name ?? '-' }}</td>
                            <td>{{ $crop->status }}</td>
                            <td>{{ $crop->deleted_at->format('d M Y H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.crops.restore', $crop->id) }}" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">♻️ Pulihkan</button>
                                </form>
                                <form method="POST" action="{{ route('admin.crops.force-delete', $crop->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus permanen? Tindakan ini tidak dapat dibatalkan!')">Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Sampah kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection