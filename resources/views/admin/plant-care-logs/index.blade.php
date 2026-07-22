@extends('layouts.admin')

@section('title', 'Log Perawatan Tanaman')

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
</style>

<div class="card card-flat">
    
    {{-- TOOLBAR: Judul & Tombol Tambah --}}
    <div class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h4 class="fw-bold text-dark font-quicksand mb-0">📋 Log Perawatan Tanaman</h4>
            <p class="text-muted small mb-0 mt-1">Riwayat aktivitas pemeliharaan dan perawatan lahan.</p>
        </div>
        <div>
            <a href="{{ route('admin.plant-care-logs.create') }}" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
                + Catat Perawatan
            </a>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold m-3 d-flex align-items-center rounded-3">
            <span class="fs-5 me-2">✅</span> {{ session('success') }}
        </div>
    @endif

    {{-- AREA TABEL --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Tanaman</th>
                        <th width="20%">Perawatan</th>
                        <th width="10%">Dosis/Jumlah</th>
                        <th width="20%">Catatan</th>
                        <th width="15%">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($log->cared_at)->format('d M Y') }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $log->crop->name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-bold mb-1 d-inline-block">
                                {{ $log->plantCare->type ?? '-' }}
                            </span>
                            <div class="small text-secondary fw-semibold mt-1">
                                {{ $log->plantCare->name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $log->amount ?? '-' }}</span>
                            <span class="text-muted small">{{ $log->plantCare->unit->symbol ?? $log->plantCare->unit->name ?? '' }}</span>
                        </td>
                        <td>
                            <span class="text-secondary small" style="line-height: 1.4; display: block;">
                                {{ $log->notes ?: '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($log->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark small">{{ $log->user->name ?? '-' }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div style="font-size: 2.5rem; color: #cbd5e1;" class="mb-2">📋</div>
                            <h6 class="fw-bold text-dark mb-1 font-quicksand">Belum Ada Log Perawatan</h6>
                            <p class="text-muted small mb-0">Catatan aktivitas pemeliharaan tanaman akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection