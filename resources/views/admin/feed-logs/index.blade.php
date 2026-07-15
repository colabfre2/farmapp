@extends('layouts.admin')
@section('title', 'Log Pemberian Pakan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">📋 Log Pemberian Pakan</h3>
                    <a href="{{ route('admin.feed-logs.create') }}" class="btn btn-primary btn-sm">+ Catat Pemberian Pakan</a>
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
                            <th>Waktu</th>
                            <th>Pakan</th>
                            <th>Ternak</th>
                            <th>Jumlah</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->fed_at)->format('d M Y') }}</td>
                            <td><span class="badge bg-info">{{ $log->time_of_day }}</span></td>
                            <td>{{ $log->feed->name ?? '-' }}</td>
                            <td>{{ $log->livestock->name ?? '-' }}</td>
                            <td>{{ $log->amount }} {{ $log->feed->unit ?? '' }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada log pemberian pakan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection