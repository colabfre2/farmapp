@extends('layouts.admin')
@section('title', 'Notifikasi')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">🔔 Semua Notifikasi</h3>
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Tandai semua dibaca</button>
                </form>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                    <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-fill">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if($notification->data['type'] == 'order')
                                        <span>🛒</span>
                                    @else
                                        <span>⏰</span>
                                    @endif
                                    <span class="fw-bold">{{ $notification->data['title'] }}</span>
                                    @if(!$notification->read_at)
                                        <span class="badge bg-danger" style="font-size:10px;">Baru</span>
                                    @endif
                                </div>
                                <div class="text-muted small mb-1">{{ $notification->data['message'] }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $notification->created_at->format('d M Y H:i') }} · {{ $notification->created_at->diffForHumans() }}</div>
                                @if(isset($notification->data['order_id']))
                                    <a href="{{ route('admin.transactions.show', $notification->data['order_id']) }}" class="btn btn-sm btn-outline-primary mt-2">Lihat Pesanan</a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">✕</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">Tidak ada notifikasi</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="mt-3">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection