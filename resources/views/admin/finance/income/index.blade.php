@extends('layouts.admin')

@section('title', 'Pemasukan')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Summary Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-lt">💵</div>
                    <div>
                        <div class="text-muted small">Total Pemasukan</div>
                        <div class="h2 mb-0 fw-bold text-success">{{ rupiah($totalIncome) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Pemasukan</h3>
                    <form method="GET" action="{{ route('admin.finance.income.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Sumber :</label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari pemasukan..." value="{{ $query ?? '' }}" style="width:200px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Cari</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.finance.income.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.finance.income.create') }}" class="btn btn-success btn-sm">+ Tambah Pemasukan</a>
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
                            <th>Sumber</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomes as $income)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($income->date)->format('d M Y') }}</td>
                            <td>{{ $income->incomeSource->name ?? '-' }}</td>
                            <td class="fw-bold text-success">{{ rupiah($income->amount) }}</td>
                            <td>{{ $income->notes ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.finance.income.edit', $income) }}" class="btn btn-sm btn-warning">Ubah</a>
                                <form method="POST" action="{{ route('admin.finance.income.destroy', $income) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                @if(!empty($query)) Pemasukan tidak ditemukan @else Belum ada pemasukan @endif
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