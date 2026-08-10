@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')
@php
    // Fallback defensif — biar halaman gak error walau controller kadang gak kirim $month/$year/dst
    $month = $month ?? request('month');
    $year  = $year ?? request('year', date('Y'));
@endphp
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }
    .stat-icon-flat {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid py-2">

    {{-- Filter Periode --}}
    <div class="card card-flat mb-4">
        <div class="card-body px-4 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📊 Laporan Laba Rugi</h3>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('admin.finance.profit-loss') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <select name="month" class="form-select form-select-sm rounded-pill px-3" style="width:160px;">
                            <option value="">📅 Semua Bulan (Tahunan)</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (string) $month === (string) $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                        <select name="year" class="form-select form-select-sm rounded-pill px-3" style="width:110px;">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">🔍 Tampilkan</button>
                        @if($month)
                            <a href="{{ route('admin.finance.profit-loss', ['year' => $year]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">✕ Reset ke Tahunan</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.finance.profit-loss.export-pdf', ['year' => $year, 'month' => $month]) }}" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm">📄 Export PDF</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-flat h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-flat bg-success-subtle text-success">💵</div>
                        <div>
                            <div class="text-muted small mb-1">Total Pemasukan</div>
                            <div class="text-muted mb-1" style="font-size:0.75rem;">{{ $periodLabel }}</div>
                            <div class="h4 mb-0 fw-bold text-success font-quicksand">{{ rupiah($totalIncome) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flat h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-flat bg-danger-subtle text-danger">💸</div>
                        <div>
                            <div class="text-muted small mb-1">Total Pengeluaran</div>
                            <div class="text-muted mb-1" style="font-size:0.75rem;">{{ $periodLabel }}</div>
                            <div class="h4 mb-0 fw-bold text-danger font-quicksand">{{ rupiah($totalExpense) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-flat h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-flat {{ $netProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">📈</div>
                        <div>
                            <div class="text-muted small mb-1">Laba Bersih</div>
                            <div class="text-muted mb-1" style="font-size:0.75rem;">{{ $periodLabel }}</div>
                            <div class="h4 mb-0 fw-bold font-quicksand {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ rupiah($netProfit) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Bulanan / Harian --}}
    <div class="card card-flat">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <h3 class="card-title fw-bold font-quicksand text-dark mb-0">
                Rincian {{ $month ? 'Harian' : 'per Bulan' }} — {{ $periodLabel }}
            </h3>
        </div>
        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-custom table-vcenter mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ $month ? 'Tanggal' : 'Bulan' }}</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th class="pe-4">Laba/Rugi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($months as $row)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $row['month'] }}</td>
                            <td class="text-success fw-semibold">{{ rupiah($row['income']) }}</td>
                            <td class="text-danger fw-semibold">{{ rupiah($row['expense']) }}</td>
                            <td class="pe-4 fw-bold {{ $row['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $row['profit'] >= 0 ? '+' : '' }}{{ rupiah($row['profit']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background:#f8fafc;">
                            <td class="ps-4 py-3">Total</td>
                            <td class="text-success py-3">{{ rupiah($totalIncome) }}</td>
                            <td class="text-danger py-3">{{ rupiah($totalExpense) }}</td>
                            <td class="pe-4 py-3 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $netProfit >= 0 ? '+' : '' }}{{ rupiah($netProfit) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
