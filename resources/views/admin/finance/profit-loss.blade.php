@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')

{{-- Filter Tahun --}}<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">📊 Laporan Laba Rugi</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.finance.profit-loss.export-pdf', ['year' => $year]) }}" class="btn btn-danger">
            📄 Export PDF
        </a>
        <form method="GET" action="{{ route('admin.finance.profit-loss') }}" class="d-flex gap-2">
            <select name="year" class="form-select" style="width:120px;">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-lt">💵</div>
                    <div>
                        <div class="text-muted small">Total Pemasukan {{ $year }}</div>
                        <div class="h3 mb-0 fw-bold text-success">{{ rupiah($totalIncome) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger-lt">💸</div>
                    <div>
                        <div class="text-muted small">Total Pengeluaran {{ $year }}</div>
                        <div class="h3 mb-0 fw-bold text-danger">{{ rupiah($totalExpense) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon {{ $netProfit >= 0 ? 'bg-success-lt' : 'bg-danger-lt' }}">📈</div>
                    <div>
                        <div class="text-muted small">Laba Bersih {{ $year }}</div>
                        <div class="h3 mb-0 fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ rupiah($netProfit) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Bulanan --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rincian per Bulan — {{ $year }}</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-vcenter mb-0">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th>Laba/Rugi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $month)
                <tr>
                    <td class="fw-bold">{{ $month['month'] }}</td>
                    <td class="text-success">{{ rupiah($month['income']) }}</td>
                    <td class="text-danger">{{ rupiah($month['expense']) }}</td>
                    <td class="fw-bold {{ $month['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $month['profit'] >= 0 ? '+' : '' }}{{ rupiah($month['profit']) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold" style="background:#f4f6f8">
                    <td>Total</td>
                    <td class="text-success">{{ rupiah($totalIncome) }}</td>
                    <td class="text-danger">{{ rupiah($totalExpense) }}</td>
                    <td class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $netProfit >= 0 ? '+' : '' }}{{ rupiah($netProfit) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection