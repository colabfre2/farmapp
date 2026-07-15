@extends('layouts.admin')
@section('title', 'Log Pemberian Obat')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">📋 Log Pemberian Obat</h3>
                    <a href="{{ route('admin.medicine-logs.create') }}" class="btn btn-primary btn-sm">+ Catat Pemberian Obat</a>
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
                            <th>Obat</th>
                            <th>Ternak</th>
                            <th>Dosis</th>
                            <th>Alasan</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->given_at)->format('d M Y') }}</td>
                            <td>{{ $log->medicine->name ?? '-' }}</td>
                            <td>{{ $log->livestock->name ?? '-' }}</td>
                            <td>{{ $log->dose }} {{ $log->medicine->unit ?? '' }}</td>
                            <td>{{ $log->reason ?? '-' }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada log pemberian obat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection