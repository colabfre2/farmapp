@extends('layouts.admin')

@section('title', 'Data Panen')

@section('content')
<style>
    /* Styling Card Modern Flat */
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
        <form method="GET" action="{{ route('admin.harvests.index') }}" class="m-0">
            <div class="search-wrapper">
                <span class="icon">🔍</span>
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari hasil panen..." value="{{ $query ?? '' }}">
            </div>
        </form>

        {{-- Tombol Kanan --}}
        <div class="d-flex align-items-center gap-2">
            @if(!empty($query))
                <a href="{{ route('admin.harvests.index') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">✕ Reset</a>
            @endif
            <a href="{{ route('admin.harvests.export') }}" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 shadow-sm">
                📊 Export Excel
            </a>
            <a href="{{ route('admin.harvests.create') }}" class="btn btn-sm btn-success fw-bold rounded-pill px-4 shadow-sm">
                + Catat Panen
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
            <span class="badge bg-secondary ms-2 rounded-pill">{{ $harvests->count() }} Ditemukan</span>
        </div>
    @endif

    {{-- AREA TABEL --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="20%">Tanaman Dipanen</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Jumlah</th>
                        <th width="15%">Harga Jual</th>
                        <th width="15%">Total Nilai</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($harvests as $harvest)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">
                                {{ $harvest->crop->name ?? 'Data tanaman dihapus' }}
                            </div>
                        </td>
                        <td>
                            <span class="text-secondary fw-semibold">
                                {{ \Carbon\Carbon::parse($harvest->harvested_at)->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $harvest->quantity }}</span> 
                            <span class="text-muted small">{{ $harvest->unit->symbol ?? '' }}</span>
                        </td>
                        <td>
                            <span class="text-dark">{{ rupiah($harvest->selling_price) }}</span>
                            <span class="text-muted small">/ {{ $harvest->unit->symbol ?? '' }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ rupiah($harvest->total_value) }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.harvests.edit', $harvest) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3" title="Edit">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.harvests.destroy', $harvest) }}" class="d-inline form-delete">
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
                            <div style="font-size: 2.5rem; color: #cbd5e1;" class="mb-2">🌾</div>
                            <h6 class="fw-bold text-dark mb-1 font-quicksand">Tidak Ada Data Panen</h6>
                            <p class="text-muted small mb-0">
                                @if(!empty($query))
                                    Tidak ditemukan panen dengan kata kunci <strong>"{{ $query }}"</strong>.
                                @else
                                    Mulai catat hasil panen pertanian Anda di sini.
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
                    title: 'Hapus Data Panen?',
                    text: "Data panen yang dihapus tidak dapat dikembalikan.",
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