@extends('layouts.admin')

@section('title', 'Daftar Ternak')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .font-quicksand { font-family: 'Quicksand', sans-serif !important; }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }
    .table-custom tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .table-custom tbody tr:last-child td { border-bottom: none; }
    .search-wrapper { position: relative; width: 260px; }
    .search-wrapper input { padding-left: 2.5rem; border-radius: 20px; font-size: 0.875rem; }
    .search-wrapper .icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.9rem;
    }
</style>

<div class="card card-flat">
    
    <div class="card-header bg-white border-bottom p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <form method="GET" action="{{ route('admin.livestock.index') }}" class="m-0">
            <div class="search-wrapper">
                <span class="icon">🔍</span>
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari nama kandang..." value="{{ $query ?? '' }}">
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">
            @if(!empty($query))
                <a href="{{ route('admin.livestock.index') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">✕ Reset</a>
            @endif
            <a href="{{ route('admin.livestock.trash') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">
                🗑️ Sampah
            </a>
            <a href="{{ route('admin.livestock.create') }}" class="btn btn-sm btn-success fw-bold rounded-pill px-4 shadow-sm">
                + Tambah Ternak
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold m-3 d-flex align-items-center rounded-3">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif

    @if(!empty($query))
        <div class="px-4 pt-3 text-muted small fw-semibold">
            Menampilkan hasil untuk: <span class="text-dark fw-bold">"{{ $query }}"</span> 
            <span class="badge bg-secondary ms-2 rounded-pill">{{ $livestocks->count() }} Ditemukan</span>
        </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">Nama Kelompok</th>
                        
                        <th width="11%">Tgl Masuk</th>
                        <th width="8%">Populasi</th>
                        <th width="9%" class="text-center">Status</th>
                        <th width="19%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($livestocks as $livestock)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td><div class="fw-bold text-dark">{{ $livestock->name }}</div></td>
                        
                        <td>
                            @if($livestock->arrival_date)
                                {{ \Carbon\Carbon::parse($livestock->arrival_date)->format('d M Y') }}
                            @else
                                <span class="text-muted small">Belum di-set</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $livestock->quantity }}</span>
                            <span class="text-muted small">ekor</span>
                        </td>
                        
                        <td class="text-center">
                            @if($livestock->health_status == 'Sehat')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">✅ Sehat</span>
                            @elseif($livestock->health_status == 'Pemantauan')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">⚠️ Pantau</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">🤒 Sakit</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.livestock.show', $livestock) }}" class="btn btn-sm btn-outline-primary rounded-3" title="Lihat Detail">
                                    👀 Lihat
                                </a>
                                <a href="{{ route('admin.livestock.edit', $livestock) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3" title="Edit">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.livestock.destroy', $livestock) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm rounded-3 btn-delete" title="Hapus">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div style="font-size: 2.5rem; color: #cbd5e1;" class="mb-2">🐄</div>
                            <h6 class="fw-bold text-dark mb-1 font-quicksand">Tidak Ada Data Ternak</h6>
                            <p class="text-muted small mb-0">
                                @if(!empty($query))
                                    Tidak ada data yang cocok dengan pencarian <strong>"{{ $query }}"</strong>.
                                @else
                                    Mulai catat data kandang dan populasi ternak Anda.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Data Ternak?',
                    text: "Data kandang akan dipindahkan ke Trash.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'rounded-pill fw-bold px-4',
                        cancelButton: 'rounded-pill fw-bold px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endsection