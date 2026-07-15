@extends('layouts.admin')
@section('title', 'Log Perawatan Tanaman')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">📋 Log Perawatan Tanaman</h3>
                    <a href="{{ route('admin.plant-care-logs.create') }}" class="btn btn-primary btn-sm">+ Catat Perawatan</a>
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
                            <th>Tanaman</th>
                            <th>Jenis Perawatan</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->cared_at)->format('d M Y') }}</td>
                            <td>{{ $log->crop->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success-lt text-success">{{ $log->plantCare->type ?? '-' }}</span>
                                {{ $log->plantCare->name ?? '-' }}
                            </td>
                            <td>{{ $log->amount ?? '-' }} {{ $log->plantCare->unit ?? '' }}</td>
                            <td>{{ $log->notes ?? '-' }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada log perawatan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection