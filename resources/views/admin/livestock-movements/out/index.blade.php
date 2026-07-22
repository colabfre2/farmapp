@extends('layouts.admin')
@section('title', 'Ternak Keluar')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">⬇ Ternak Keluar</h3>
                    <form method="GET" action="{{ route('admin.livestock-movements.out.index') }}" class="d-flex align-items-center gap-2">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari kandang..." value="{{ $query ?? '' }}" style="width:180px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.livestock-movements.out.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('admin.livestock-movements.out.create') }}" class="btn btn-danger btn-sm">+ Tambah Ternak Keluar</a>
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
                            <th>Kandang</th>
                            <th>Jumlah</th>
                            <th>Alasan</th>
                            <th>Catatan</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($movement->date)->format('d M Y') }}</td>
                            <td>{{ $movement->livestock->name ?? '-' }}</td>
                            <td class="fw-bold text-danger">-{{ $movement->quantity }} ekor</td>
                            <td>{{ $movement->reason ?? '-' }}</td>
                            <td>{{ $movement->notes ?? '-' }}</td>
                            <td>{{ $movement->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data ternak keluar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection