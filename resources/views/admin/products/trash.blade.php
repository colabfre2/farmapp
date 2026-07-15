@extends('layouts.admin')

@section('title', 'Products Trash')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">🗑️ Sampah produk</h3>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">← Kembali ke produk</a>
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
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Dihapus pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->deleted_at->format('d M Y H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">♻️ Pulihkan</button>
                                </form>
                                <form method="POST" action="{{ route('admin.products.force-delete', $product->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus Permanen ? Tidak bisa dikembalikan')">Hapus Permanen</button>
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