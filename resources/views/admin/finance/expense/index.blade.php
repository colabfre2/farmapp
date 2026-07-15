@extends('layouts.admin')

@section('title', 'Pengeluaran')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Summary Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger-lt">💸</div>
                    <div>
                        <div class="text-muted small">Total Pengeluaran</div>
                        <div class="h2 mb-0 fw-bold text-danger">{{ rupiah($totalExpense) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Pengeluaran</h3>
                    <form method="GET" action="{{ route('admin.finance.expense.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Deskripsi :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari pengeluaran..." value="{{ $query ?? '' }}" style="width:200px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.finance.expense.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.finance.expense.create') }}" class="btn btn-danger btn-sm">+ Tambah Pengeluaran</a>
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
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                            <td>{{ $expense->expenseCategory->name ?? '-' }}</td>
                            <td>{{ $expense->description }}</td>
                            <td class="fw-bold text-danger">{{ rupiah($expense->amount) }}</td>
                            <td>{{ $expense->notes ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.finance.expense.edit', $expense) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.finance.expense.destroy', $expense) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete" >Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                @if(!empty($query)) Pengeluaran tidak ditemukan @else Belum ada pengeluaran @endif
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