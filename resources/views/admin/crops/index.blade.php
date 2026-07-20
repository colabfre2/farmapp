@extends('layouts.admin')

@section('title', 'Daftar Tanaman')

@section('content')
<style>
    /* Styling Card Modern */
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .font-quicksand {
        font-family: 'Quicksand', sans-serif !important;
    }
    
    /* Styling Tabel Seamless */
    .table-custom {
        margin-bottom: 0;
    }
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
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    /* Input Search Modern */
    .search-wrapper {
        position: relative;
        width: 260px;
    }
    .search-wrapper input {
        padding-left: 2.5rem;
        border-radius: 20px;
        font-size: 0.875rem;
    }
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
    
    {{-- TOOLBAR: Search di Kiri, Tombol Aksi di Kanan --}}
    <div class="card-header bg-white border-bottom p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        
        {{-- Form Search --}}
        <form method="GET" action="{{ route('admin.crops.index') }}" class="m-0">
            <div class="search-wrapper">
                <span class="icon">🔍</span>
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari nama tanaman..." value="{{ $query ?? '' }}">
            </div>
        </form>

        {{-- Tombol Kanan --}}
        <div class="d-flex align-items-center gap-2">
            @if(!empty($query))
                <a href="{{ route('admin.crops.index') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">✕ Reset</a>
            @endif
            <a href="{{ route('admin.crops.trash') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">
                🗑️ Sampah
            </a>
            <a href="{{ route('admin.crops.create') }}" class="btn btn-sm btn-success fw-bold rounded-pill px-4 shadow-sm">
                + Tambah Tanaman
            </a>
        </div>
        
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold m-3 d-flex align-items-center rounded-3">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif

    {{-- Info Hasil Pencarian --}}
    @if(!empty($query))
        <div class="px-4 pt-3 text-muted small fw-semibold">
            Menampilkan hasil untuk: <span class="text-dark fw-bold">"{{ $query }}"</span> 
            <span class="badge bg-secondary ms-2 rounded-pill">{{ $crops->count() }} Ditemukan</span>
        </div>
    @endif

    {{-- AREA TABEL --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="25%">Nama Tanaman</th>
                        <th width="15%">Jenis</th>
                        <th width="15%">Tanggal Tanam</th>
                        <th width="15%">Perkiraan Panen</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($crops as $crop)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $crop->name }}</div>
                        </td>
                        <td>
                            <span class="text-muted fw-semibold">{{ $crop->cropType->name ?? '-' }}</span>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($crop->planted_at)->format('d M Y') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($crop->expected_harvest_at)->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            @if($crop->status == 'Bibit')
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">🌱 Bibit</span>
                            @elseif($crop->status == 'Pertumbuhan')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">🌿 Pertumbuhan</span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">🌾 Dipanen</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.crops.edit', $crop) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3" title="Edit">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.crops.destroy', $crop) }}" class="d-inline">
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
                        <td colspan="7" class="text-center py-5">
                            <div style="font-size: 2.5rem; color: #cbd5e1;" class="mb-2">🌱</div>
                            <h6 class="fw-bold text-dark mb-1 font-quicksand">Tidak Ada Data Tanaman</h6>
                            <p class="text-muted small mb-0">
                                @if(!empty($query))
                                    Tidak ada tanaman yang cocok dengan pencarian <strong>"{{ $query }}"</strong>.
                                @else
                                    Mulai catat aktivitas pertanian Anda di sini.
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

{{-- Script SweetAlert2 Konfirmasi Hapus --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Tanaman?',
                    text: "Data tanaman akan dipindahkan ke Trash.",
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