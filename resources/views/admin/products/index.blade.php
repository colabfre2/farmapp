@extends('layouts.admin')

@section('title', 'Daftar Produk')

@section('content')
<style>
    /* Styling Card Modern */
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden; /* Biar ujung tabel ikut melengkung ngikutin card */
    }
    
    /* Styling Tabel Seamless (Menyatu) */
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
        border-bottom: none; /* Hilangin garis di baris paling bawah */
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

{{-- HAPUS JUDUL DOBEL DI SINI, LANGSUNG MASUK KE CARD --}}
<div class="card card-flat">
    
    {{-- TOOLBAR: Search di Kiri, Tombol Aksi di Kanan --}}
    <div class="card-header bg-white border-bottom p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        
        {{-- Form Search --}}
        <form method="GET" action="{{ route('admin.products.index') }}" class="m-0">
            <div class="search-wrapper">
                <span class="icon">🔍</span>
                <input type="text" name="q" class="form-control bg-light border-0" placeholder="Cari nama produk..." value="{{ $query ?? '' }}">
            </div>
        </form>

        {{-- Tombol Kanan --}}
        <div class="d-flex align-items-center gap-2">
            @if(!empty($query))
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">✕ Reset</a>
            @endif
            <a href="{{ route('admin.products.trash') }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 shadow-sm border">
                🗑️ Tempat Sampah
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-success fw-bold rounded-pill px-4 shadow-sm">
                + Tambah Produk
            </a>
        </div>
        
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold m-3 d-flex align-items-center rounded-3">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif

    {{-- AREA TABEL (Padding 0 biar mepet ujung card) --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="10%">Foto</th>
                        <th width="25%">Info Produk</th>
                        <th width="15%">Harga</th>
                        <th width="15%">Stok</th>
                        <th width="15%" class="text-center">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:45px; height:45px; object-fit:cover; border-radius:8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted border" style="width:45px; height:45px; border-radius:8px;">
                                    📸
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $product->name }}</div>
                            <div class="small text-muted">{{ $product->category->name ?? 'Tanpa Kategori' }}</div>
                        </td>
                        <td class="fw-bold text-dark">
                            {{ rupiah($product->price) }}
                        </td>
                        <td>
                            <span class="fw-bold {{ $product->stock < 10 ? 'text-danger' : 'text-dark' }}">{{ $product->stock }}</span> 
                            <span class="small text-muted">{{ $product->unit->symbol ?? '' }}</span>
                        </td>
                        <td class="text-center">
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-light text-success border shadow-sm rounded-3" title="Lihat Detail">
                                    👁️ Detail
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3" title="Edit">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline form-delete">
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
                            <div style="font-size: 2.5rem; color: #cbd5e1;" class="mb-2">🛒</div>
                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Produk</h6>
                            <p class="text-muted small mb-0">
                                @if(!empty($query))
                                    Tidak ada hasil untuk "<strong>{{ $query }}</strong>".
                                @else
                                    Mulai tambahkan produk jualan Anda.
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

{{-- Script SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Produk?',
                    text: "Produk akan dipindahkan ke Trash.",
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