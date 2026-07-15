@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Produk</h3>

                    <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex align-items-center gap-2">
                        <label for="productSearch" class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input
                            type="text"
                            id="productSearch"
                            name="q"
                            class="form-control form-control-sm"
                            placeholder="Cari produk..."
                            value="{{ $query ?? '' }}"
                            style="width:200px;"
                        >
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-danger btn-sm">🗑️ Trash</a>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">
                        Menampilkan hasil dari : "<strong>{{ $query }}</strong>" — {{ $products->count() }} Ditemukan
                    </p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                @else
                                    <span class="text-muted">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ rupiah($product->price) }}</td>
                            <td>{{ $product->stock }} {{ $product->unit->symbol ?? '' }}</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Tidak aktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                @if(!empty($query))
                                    Produk tidak di temukan dari kata kunci : "{{ $query }}"
                                @else
                                    Belum ada produk
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