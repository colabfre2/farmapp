@extends('layouts.admin')

@section('title', 'Barang Keluar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">⬇ Barang Keluar</h3>
                    <form method="GET" action="{{ route('admin.stock.out.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Produk :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.stock.out.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.stock.out.export') }}" class="btn btn-success btn-sm">📊 Export Excel</a>
                    <a href="{{ route('admin.stock.out.create') }}" class="btn btn-danger btn-sm">+ Tambah Barang Keluar</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Alasan</th>
                            <th>Catatan</th>
                            <th>Oleh</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $movement->product->name ?? '-' }}</td>
                            <td class="fw-bold text-danger">-{{ $movement->quantity }}</td>
                            <td>{{ $movement->reason ?? '-' }}</td>
                            <td>{{ $movement->notes ?? '-' }}</td>
                            <td>{{ $movement->user->name ?? '-' }}</td>
                            <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada barang keluar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection