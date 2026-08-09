@extends('layouts.admin')

@section('title', 'Detail Transaksi')

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
</style>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 font-quicksand text-dark">📦 Detail Transaksi & Pengiriman</h2>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">← Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mb-4 rounded-3 shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Order Items --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">🧾 Item Pesanan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th class="pe-4 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $item->product_name }}</td>
                                    <td>{{ rupiah($item->unit_price) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="pe-4 text-end fw-bold text-success">{{ rupiah($item->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Shipping Info + Kurir --}}
            <div class="card card-flat">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">🚚 Informasi Pengiriman & Kurir</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Nama Penerima</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Nomor Telepon</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kurir & Layanan</div>
                            <div class="fw-bold text-dark text-uppercase">
                                {{ $order->courier ?? '-' }} 
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1 small">{{ $order->courier_service ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Kota / Wilayah</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_city ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Alamat Lengkap</div>
                            <div class="fw-bold text-dark">{{ $order->shipping_address }}</div>
                        </div>
                        <div class="col-12">
                            <hr class="text-muted opacity-25 my-2">
                            <div class="text-muted small mb-2">Nomor Resi</div>
                            <form method="POST" action="{{ route('admin.transactions.update-tracking', $order) }}" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="Masukkan nomor resi..." class="form-control rounded-3 py-2" required>
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold text-nowrap">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Order Info --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">📋 Info Pesanan</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="text-muted small">No. Order</div>
                        <div class="fw-bold text-dark">{{ $order->order_number }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Buyer</div>
                        <div class="fw-bold text-dark">{{ $order->user->name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Tanggal Pemesanan</div>
                        <div class="fw-bold text-dark">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Metode Pembayaran</div>
                        <div class="fw-bold text-dark text-uppercase">{{ $order->payment_method }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Status Pesanan</div>
                        <span class="badge rounded-pill px-3 py-2 fw-bold
                            @if($order->status == 'Pending') bg-warning-subtle text-warning
                            @elseif($order->status == 'Processing') bg-primary-subtle text-primary
                            @elseif($order->status == 'Shipped') bg-info-subtle text-info
                            @elseif($order->status == 'Completed') bg-success-subtle text-success
                            @else bg-danger-subtle text-danger
                            @endif">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Rincian Keuangan --}}
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">💰 Rincian Keuangan</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    @php
                        $itemsSubtotal = $order->items->sum('subtotal');
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Subtotal Barang</span>
                        <span class="fw-semibold text-dark">{{ rupiah($itemsSubtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Ongkos Kirim</span>
                        <span class="fw-semibold text-dark">{{ rupiah($order->shipping_cost ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Biaya Layanan (Fee)</span>
                        <span class="fw-semibold text-dark">{{ rupiah($order->fee ?? 0) }}</span>
                    </div>
                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-between align-items-center fw-bold h4 mb-0">
                        <span class="text-dark">Total</span>
                        <span class="text-success font-quicksand">{{ rupiah($order->total_amount) }}</span>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="card card-flat">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold font-quicksand text-dark mb-0">🔄 Update Status</h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST" action="{{ route('admin.transactions.update-status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <select name="status" class="form-select rounded-3 py-2">
                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Processing" {{ $order->status == 'Processing' ? 'selected' : '' }}>Processing</option>
                                <option value="Shipped" {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection