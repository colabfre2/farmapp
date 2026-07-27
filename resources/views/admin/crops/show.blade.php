@extends('layouts.admin')

@section('title', 'Detail Tanaman')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .font-quicksand { font-family: 'Quicksand', sans-serif !important; }

    .table-custom { margin-bottom: 0; }
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
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
    }
    .summary-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 12px;
    }
    .summary-box:last-child { margin-bottom: 0; }
    .summary-number {
        font-size: 1.5rem;
        font-weight: 800;
        font-family: 'Quicksand', sans-serif;
    }
</style>

{{-- Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-0 font-quicksand">🌱 {{ $crop->name }}</h2>
        <p class="text-muted small mb-0">Detail informasi tanaman</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.crops.edit', $crop) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3 fw-bold px-3">
            ✏️ Ubah
        </a>
        <a href="{{ route('admin.crops.index') }}" class="btn btn-sm btn-light text-secondary border shadow-sm rounded-3 fw-bold px-3">
            ← Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Info Utama --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">📋 Informasi Tanaman</h3>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-label">Nama Tanaman</div>
                        <div class="info-value">{{ $crop->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Status</div>
                        <div>
                            @if($crop->status == 'Bibit')
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">🌱 Bibit</span>
                            @elseif($crop->status == 'Pertumbuhan')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">🌿 Pertumbuhan</span>
                            @else
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">🌾 Dipanen</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Jenis Tanaman</div>
                        <div class="info-value">{{ $crop->cropType->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Varian</div>
                        <div class="info-value">{{ $crop->cropVariety->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
    <div class="info-label">Ladang</div>
    <div class="info-value">{{ $crop->farm->name ?? '-' }}</div>
</div>
<div class="col-md-6">
    <div class="info-label">Ukuran Ladang</div>
    <div class="info-value">
        @if($crop->farm && $crop->farm->area_size)
            {{ number_format($crop->farm->area_size, 2) }} {{ $crop->farm->area_unit }}
        @else
            -
        @endif
    </div>
</div>
                    <div class="col-md-6">
                        <div class="info-label">Tanggal Tanam</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($crop->planted_at)->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Perkiraan Panen</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($crop->expected_harvest_at)->format('d M Y') }}</div>
                    </div>
                    @if($crop->actual_harvest_at)
                    <div class="col-md-6">
                        <div class="info-label">Tanggal Panen Aktual</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($crop->actual_harvest_at)->format('d M Y') }}</div>
                    </div>
                    @endif
                    @if($crop->notes)
                    <div class="col-12">
                        <div class="info-label">Catatan</div>
                        <div class="info-value fw-normal text-muted">{{ $crop->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat Perawatan --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">🧪 Riwayat Perawatan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($crop->plantCareLogs as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->cared_at)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">{{ $log->plantCare->type ?? '-' }}</span>
                                    <span class="fw-semibold ms-1">{{ $log->plantCare->name ?? '-' }}</span>
                                </td>
                                <td>{{ $log->amount ?? '-' }} {{ $log->plantCare->unit->name ?? '' }}</td>
                                <td class="text-muted">{{ $log->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div style="font-size: 2rem; color: #cbd5e1;" class="mb-2">🧪</div>
                                    <p class="text-muted small mb-0">Belum ada riwayat perawatan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Panen --}}
        <div class="card card-flat">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">🌾 Riwayat Panen</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kuantitas</th>
                                <th>Harga Jual</th>
                                <th>Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($crop->harvests as $harvest)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($harvest->harvested_at)->format('d M Y') }}</td>
                                <td>{{ $harvest->quantity }} {{ $harvest->unit->symbol ?? '' }}</td>
                                <td>{{ rupiah($harvest->selling_price) }}</td>
                                <td class="fw-bold text-success">{{ rupiah($harvest->total_value) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div style="font-size: 2rem; color: #cbd5e1;" class="mb-2">🌾</div>
                                    <p class="text-muted small mb-0">Belum ada riwayat panen</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-4">
        <div class="card card-flat">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">📌 Ringkasan</h3>
            </div>
            <div class="card-body p-3">
                <div class="summary-box">
                    <div class="info-label mb-1">Total Panen</div>
                    <div class="summary-number text-dark">{{ $crop->harvests->count() }} <span class="fs-6 fw-semibold text-muted">kali</span></div>
                </div>
                <div class="summary-box">
                    <div class="info-label mb-1">Total Nilai Panen</div>
                    <div class="summary-number text-success">{{ rupiah($crop->harvests->sum('total_value')) }}</div>
                </div>
                <div class="summary-box">
                    <div class="info-label mb-1">Total Perawatan</div>
                    <div class="summary-number text-dark">{{ $crop->plantCareLogs->count() }} <span class="fs-6 fw-semibold text-muted">kali</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection