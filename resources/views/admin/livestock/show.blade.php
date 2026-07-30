@extends('layouts.admin')

@section('title', 'Detail Ternak')

@section('content')
<style>
    .card-flat { border: none !important; border-radius: 12px !important; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
    .font-quicksand { font-family: 'Quicksand', sans-serif !important; }
    .info-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }
    .info-value { font-size: 0.95rem; font-weight: 600; color: #1e293b; }
    .summary-box { background: #f8fafc; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 12px; }
    .summary-box:last-child { margin-bottom: 0; }
    .summary-number { font-size: 1.5rem; font-weight: 800; font-family: 'Quicksand', sans-serif; }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1rem; }
    .table-custom tbody td { padding: 0.75rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; color: #334155; }
    .table-custom tbody tr:last-child td { border-bottom: none; }
</style>

{{-- Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-0 font-quicksand">🐄 {{ $livestock->name }}</h2>
        <p class="text-muted small mb-0">Detail informasi ternak</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.livestock.edit', $livestock) }}" class="btn btn-sm btn-light text-primary border shadow-sm rounded-3 fw-bold px-3">✏️ Ubah</a>
        <a href="{{ route('admin.livestock.index') }}" class="btn btn-sm btn-light text-secondary border shadow-sm rounded-3 fw-bold px-3">← Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Info Utama --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">📋 Informasi Ternak</h3>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-label">Nama Kelompok</div>
                        <div class="info-value">{{ $livestock->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Status Kesehatan</div>
                        <div>
                            @if($livestock->health_status == 'Sehat')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">✅ Sehat</span>
                            @elseif($livestock->health_status == 'Pemantauan')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">⚠️ Pemantauan</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">🤒 Sakit</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Jenis Hewan</div>
                        <div class="info-value">{{ $livestock->livestockType->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Kandang</div>
                        <div class="info-value">
                            {{ $livestock->kandang->name ?? '-' }}
                            @if($livestock->kandang && $livestock->kandang->capacity)
                                <span class="text-muted fw-normal small">(kapasitas {{ $livestock->kandang->capacity }} ekor)</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Tanggal Masuk</div>
                        <div class="info-value">
                            {{ $livestock->arrival_date ? \Carbon\Carbon::parse($livestock->arrival_date)->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Berat Rata-rata</div>
                        <div class="info-value">{{ $livestock->avg_weight ? $livestock->avg_weight . ' kg' : '-' }}</div>
                    </div>
                    @if($livestock->notes)
                    <div class="col-12">
                        <div class="info-label">Catatan</div>
                        <div class="info-value fw-normal text-muted">{{ $livestock->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat Pergerakan --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h3 class="h6 fw-bold mb-0 font-quicksand">📦 Riwayat Pergerakan</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.livestock-movements.in.create') }}" class="btn btn-sm btn-success rounded-pill px-3">+ Masuk</a>
                    <a href="{{ route('admin.livestock-movements.out.create') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">− Keluar</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Alasan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livestock->movements->sortByDesc('date') as $movement)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($movement->date)->format('d M Y') }}</td>
                                <td>
                                    @if($movement->type == 'in')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">⬆️ Masuk</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">⬇️ Keluar</span>
                                    @endif
                                </td>
                                <td><span class="fw-bold">{{ $movement->quantity }}</span> ekor</td>
                                <td>{{ $movement->reason ?? '-' }}</td>
                                <td class="text-muted">{{ $movement->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pergerakan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Pakan --}}
        <div class="card card-flat mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">🌾 Riwayat Pemberian Pakan</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pakan</th>
                                <th>Jumlah</th>
                                <th>Waktu</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livestock->feedLogs->sortByDesc('fed_at') as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->fed_at)->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $log->feed->name ?? '-' }}</td>
                                <td>{{ $log->amount }} {{ $log->feed->unit->symbol ?? '' }}</td>
                                <td><span class="badge bg-info-subtle text-info rounded-pill px-2">{{ $log->time_of_day }}</span></td>
                                <td class="text-muted">{{ $log->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pemberian pakan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Obat --}}
        <div class="card card-flat">
            <div class="card-header bg-white border-bottom p-3">
                <h3 class="h6 fw-bold mb-0 font-quicksand">💊 Riwayat Pemberian Obat</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Obat</th>
                                <th>Dosis</th>
                                <th>Alasan</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livestock->medicineLogs->sortByDesc('given_at') as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->given_at)->format('d M Y') }}</td>
                                <td class="fw-semibold">{{ $log->medicine->name ?? '-' }}</td>
                                <td>{{ $log->dose }} {{ $log->medicine->unit->symbol ?? '' }}</td>
                                <td>{{ $log->reason ?? '-' }}</td>
                                <td class="text-muted">{{ $log->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pemberian obat</td>
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
                    <div class="info-label mb-1">Populasi Saat Ini</div>
                    <div class="summary-number text-dark">{{ $livestock->quantity }} <span class="fs-6 fw-semibold text-muted">ekor</span></div>
                </div>
                <div class="summary-box">
                    <div class="info-label mb-1">Total Pergerakan</div>
                    <div class="summary-number text-dark">{{ $livestock->movements->count() }} <span class="fs-6 fw-semibold text-muted">kali</span></div>
                </div>
                <div class="summary-box">
                    <div class="info-label mb-1">Total Pemberian Pakan</div>
                    <div class="summary-number text-dark">{{ $livestock->feedLogs->count() }} <span class="fs-6 fw-semibold text-muted">kali</span></div>
                </div>
                <div class="summary-box">
                    <div class="info-label mb-1">Total Pemberian Obat</div>
                    <div class="summary-number text-dark">{{ $livestock->medicineLogs->count() }} <span class="fs-6 fw-semibold text-muted">kali</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection