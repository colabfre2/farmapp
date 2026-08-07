@extends('layouts.admin')
@section('title', 'Log Pemberian Pakan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            {{-- Header Card --}}
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                   
                    
                    <a href="{{ route('admin.feed-logs.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                        </svg>
                        <span>Catat Pemberian Pakan</span>
                    </a>
                </div>
            </div>

            {{-- Body Card --}}
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3 rounded-3 border-0 shadow-sm" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.018-1.042z"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary text-uppercase fs-7">
                            <tr>
                                <th class="ps-4 py-3" style="width: 50px;">#</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Waktu</th>
                                <th class="py-3">Pakan</th>
                                <th class="py-3">Ternak</th>
                                <th class="py-3">Jumlah</th>
                                <th class="pe-4 py-3">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-muted fw-light">{{ $loop->iteration }}</td>
                                <td class="fw-medium text-dark">
                                    {{ \Carbon\Carbon::parse($log->fed_at)->format('d M Y') }}
                                </td>
                                <td>
                                    @php
                                        // Variasi warna soft berdasarkan waktu
                                        $timeClass = match(strtolower($log->time_of_day ?? '')) {
                                            'pagi' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                            'siang' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                            'sore' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                                            'malam' => 'bg-dark-subtle text-dark-emphasis border-dark-subtle',
                                            default => 'bg-light text-secondary border'
                                        };
                                    @endphp
                                    <span class="badge border rounded-pill px-2.5 py-1.5 fw-normal {{ $timeClass }}">
                                        {{ ucfirst($log->time_of_day) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $log->feed->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-2 px-2 py-1 fw-normal">
                                        {{ $log->livestock->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ $log->amount }}
                                    </span>
                                    <small class="text-muted fs-7">
                                        {{ $log->feed->unit->symbol ?? $log->feed->unit->name ?? '' }}
                                    </small>
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 12px;">
                                            {{ strtoupper(substr($log->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-secondary small">{{ $log->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="p-3 bg-light rounded-circle text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-inbox" viewBox="0 0 16 16">
                                                <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438zM3.809 3.563A1.5 1.5 0 0 1 4.981 3h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a1 1 0 0 1 .228.626v3.911a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 13.725V9.314a1 1 0 0 1 .228-.626z"/>
                                            </svg>
                                        </div>
                                        <span class="fw-medium">Belum ada log pemberian pakan</span>
                                        <small class="text-muted">Klik tombol di atas untuk mencatat pakan pertama.</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Pagination (Opsional jika menggunakan pagination) --}}
            @if(method_exists($logs, 'hasPages') && $logs->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection