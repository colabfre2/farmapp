@extends('layouts.admin')

@section('title', 'Transaksi')

@section('content')
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
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .status-pill {
        border: 1px solid transparent;
        border-radius: 50px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        transition: all .15s ease;
        display: inline-block;
    }
    .status-pill.pill-all {
        color: #475569;
        background: #f1f5f9;
        border-color: #e2e8f0;
    }
    .status-pill.pill-all.active {
        color: #fff;
        background: #475569;
    }
    .status-pill.pill-warning { color: #b45309; background: #fef3c7; }
    .status-pill.pill-warning.active { color: #fff; background: #f59e0b; }
    .status-pill.pill-primary { color: #1d4ed8; background: #dbeafe; }
    .status-pill.pill-primary.active { color: #fff; background: #2563eb; }
    .status-pill.pill-info { color: #0e7490; background: #cffafe; }
    .status-pill.pill-info.active { color: #fff; background: #06b6d4; }
    .status-pill.pill-success { color: #15803d; background: #dcfce7; }
    .status-pill.pill-success.active { color: #fff; background: #16a34a; }
    .status-pill.pill-danger { color: #b91c1c; background: #fee2e2; }
    .status-pill.pill-danger.active { color: #fff; background: #dc2626; }
    .aksi-dropdown-btn {
        min-width: 110px;
        text-align: center;
    }
</style>

<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📊 Daftar Transaksi & Pesanan</h3>
                        <a href="{{ route('admin.transactions.export') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">📊 Export Excel</a>
                    </div>

                    {{-- Filter --}}
                    <form method="GET" action="{{ route('admin.transactions.index') }}">
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">Cari</label>
                                <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="No. order / nama buyer..." value="{{ $query ?? '' }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control form-control-sm rounded-pill px-3" value="{{ $dateFrom ?? '' }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control form-control-sm rounded-pill px-3" value="{{ $dateTo ?? '' }}">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 flex-fill">🔍 Terapkan</button>
                            </div>
                        </div>

                        {{-- Filter Status: pill --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small fw-bold me-1">Status:</span>

                            <a href="{{ route('admin.transactions.index', array_filter(['q' => $query, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                               class="status-pill pill-all {{ empty($status) ? 'active' : '' }}">Semua</a>

                            @php
                                $statusPills = [
                                    'Pending'    => 'warning',
                                    'Processing' => 'primary',
                                    'Shipped'    => 'info',
                                    'Completed'  => 'success',
                                    'Cancelled'  => 'danger',
                                ];
                            @endphp

                            @foreach($statusPills as $statusValue => $color)
                                <a href="{{ route('admin.transactions.index', array_filter(['q' => $query, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'status' => $statusValue])) }}"
                                   class="status-pill pill-{{ $color }} {{ $status === $statusValue ? 'active' : '' }}">{{ $statusValue }}</a>
                            @endforeach

                            @if(!empty($query) || !empty($status) || !empty($dateFrom) || !empty($dateTo))
                                <a href="{{ route('admin.transactions.index') }}" class="status-pill pill-all ms-1">✕ Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pb-0">
                    @if(session('success'))
                        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mx-4 mb-3 rounded-3 shadow-sm">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-custom table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>No. Order</th>
                                    <th>Buyer</th>
                                    <th>Kurir & Layanan</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Alur maju status: status sekarang => [status berikutnya, label tombol, warna tombol]
                                    $nextStatusMap = [
                                        'Pending'    => ['Processing', 'Proses Pesanan', 'primary'],
                                        'Processing' => ['Shipped', 'Kirim Pesanan', 'info'],
                                        'Shipped'    => ['Completed', 'Selesaikan Pesanan', 'success'],
                                    ];
                                @endphp
                                @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                                    <td>{{ $order->user->name ?? '-' }}</td>
                                    <td>
                                        <div class="fw-bold text-dark text-uppercase">{{ $order->courier ?? '-' }}</div>
                                        <div class="text-muted small">{{ $order->courier_service ?? '-' }}</div>
                                    </td>
                                    <td class="fw-bold text-success">{{ rupiah($order->total_amount) }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 text-uppercase small">
                                            {{ $order->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 fw-bold
                                            @if($order->status == 'Pending') bg-warning-subtle text-warning
                                            @elseif($order->status == 'Processing') bg-primary-subtle text-primary
                                            @elseif($order->status == 'Shipped') bg-info-subtle text-info
                                            @elseif($order->status == 'Completed') bg-success-subtle text-success
                                            @else bg-danger-subtle text-danger
                                            @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold dropdown-toggle aksi-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                ⚙️ Aksi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="border-radius:14px; min-width:200px;">
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold" href="{{ route('admin.transactions.show', $order) }}">
                                                        🔍 Lihat Detail
                                                    </a>
                                                </li>

                                                @if(isset($nextStatusMap[$order->status]))
                                                    @php [$nextStatus, $btnLabel, $btnColor] = $nextStatusMap[$order->status]; @endphp
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.transactions.update-status', $order) }}" id="status-form-{{ $order->id }}" class="d-none">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                        </form>
                                                        <button type="button"
                                                                class="dropdown-item py-2 fw-bold text-{{ $btnColor }} btn-next-status"
                                                                data-form-target="status-form-{{ $order->id }}"
                                                                data-order-number="{{ $order->order_number }}"
                                                                data-next-status="{{ $nextStatus }}">
                                                            ▶️ {{ $btnLabel }}
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">Tidak ada data transaksi yang cocok dengan filter.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="px-4 py-3">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CDN SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-next-status').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById(this.dataset.formTarget);
            const orderNumber = this.dataset.orderNumber;
            const nextStatus = this.dataset.nextStatus;

            Swal.fire({
                title: 'Ubah Status Pesanan?',
                html: `Pesanan <b>${orderNumber}</b> akan diubah menjadi <b>${nextStatus}</b>.<br>Pastikan sudah benar sebelum melanjutkan.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Ubah Status!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'rounded-pill px-4 py-2',
                    cancelButton: 'rounded-pill px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
