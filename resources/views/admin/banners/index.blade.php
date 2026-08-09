@extends('layouts.admin')
@section('title', 'Kelola Banner')
@section('content')
<div class="card card-flat" style="border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden;">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">🖼️ Kelola Banner Beranda</h3>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-success fw-bold rounded-pill px-4 shadow-sm">+ Tambah Banner</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold m-3 rounded-3">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter mb-0">
                <thead>
                    <tr>
                        <th width="10%" class="text-center">Urutan</th>
                        <th width="20%">Preview</th>
                        <th>Judul</th>
                        <th>Link Tujuan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $banner->order }}</td>
                        <td>
                            <img src="{{ '/storage/' . $banner->image }}" alt="{{ $banner->title }}" style="width:120px; height:60px; object-fit:cover; border-radius:8px;">
                        </td>
                        <td class="fw-bold text-dark">{{ $banner->title }}</td>
                        <td class="text-muted small">{{ $banner->link_url ?? '-' }}</td>
                        <td class="text-center">
                            @if($banner->is_active)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-light border shadow-sm rounded-3">
                                        {{ $banner->is_active ? '🚫' : '✅' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Hapus banner ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div style="font-size: 2.5rem;" class="mb-2">🖼️</div>
                            Belum ada banner. Klik "Tambah Banner" untuk membuat yang pertama.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection