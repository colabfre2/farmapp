@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div class="mb-4">
    <h2 class="fw-bold">Good morning, {{ auth()->user()->name }} 👋</h2>
    <p class="text-muted">Ini yang terjadi di farm kamu hari ini.</p>
</div>

<div class="mb-4">
    <form method="GET" action="{{ route('admin.search') }}" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" placeholder="Search dashboard..." style="max-width:300px;" value="{{ request('q') }}">
        <button type="submit" class="btn btn-outline-secondary">🔍 Search</button>
    </form>
</div>

{{-- Stat Cards --}}
<div class="row row-deck row-cards mb-4">
    <!-- Baris 1: 3 Cards -->
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-blue">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">📦</span>
                    <span class="text-primary small fw-bold">↗ Total</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ $totalProducts }}</div>
                <div class="text-muted fw-semibold">Total Produk</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-green">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">🌱</span>
                    <span class="text-success small fw-bold">↗ Aktif</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ $totalCrops }}</div>
                <div class="text-muted fw-semibold">Tanaman aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-orange">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">🐄</span>
                    <span class="text-warning small fw-bold">↗ Populasi</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ $totalLivestock }}</div>
                <div class="text-muted fw-semibold">Ternak</div>
            </div>
        </div>
    </div>

    <!-- Baris 2: 3 Cards -->
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-purple">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">🛒</span>
                    <span class="text-purple small fw-bold" style="color: #6f42c1;">↗ Transaksi</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ $totalOrders }}</div>
                <div class="text-muted fw-semibold">Pesanan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-emerald">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">💵</span>
                    <span class="text-success small fw-bold">↗ Akumulasi</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ rupiah($totalRevenue) }}</div>
                <div class="text-muted fw-semibold">Pendapatan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 mb-3">
        <div class="card h-100 card-modern bg-grad-cyan">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <span class="fs-3 me-auto">📈</span>
                    <span class="text-info small fw-bold">↗ Net Profit</span>
                </div>
                <div class="h1 mb-0 fw-bolder text-dark">{{ rupiah($netProfit) }}</div>
                <div class="text-muted fw-semibold">Laba bersih</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row row-deck row-cards mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Pendapatan & Pengeluaran</h3>
                <span class="text-muted small">{{ date('Y') }}</span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tren laba</h3>
            </div>
            <div class="card-body">
                <canvas id="profitChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders & Recent Harvests --}}
<div class="row row-deck row-cards">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Pesanan terbaru</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-success small">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentOrders as $order)
                    <div class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar bg-success-lt text-success fw-bold">
                            {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                        </span>
                        <div class="flex-fill">
                            <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                            <div class="text-muted small">{{ $order->items->first()->product_name ?? '-' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ rupiah($order->total_amount) }}</div>
                            <span class="badge 
                                @if($order->status == 'Pending') bg-warning text-dark
                                @elseif($order->status == 'Processing') bg-primary text-white
                                @elseif($order->status == 'Shipped') bg-info text-white
                                @elseif($order->status == 'Completed') bg-success text-white
                                @else bg-danger text-white
                                @endif">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-muted text-center py-4">Belum ada pesanan</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Panen terbaru</h3>
                <a href="{{ route('admin.harvests.index') }}" class="text-success small">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentHarvests as $harvest)
                    <div class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar bg-success-lt">🌾</span>
                        <div class="flex-fill">
                            <div class="fw-bold">{{ $harvest->product_name }}</div>
                            <div class="text-muted small">{{ $harvest->harvested_at }} · {{ $harvest->quantity }} {{ $harvest->unit?->symbol ?? '' }}</div>
                        </div>
                        <div class="fw-bold text-success">
                            {{ rupiah($harvest->quantity * $harvest->selling_price) }}
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-muted text-center py-4">Belum ada panen</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const revenueData  = @json($revenueData);
    const expensesData = @json($expensesData);
    const profitData   = revenueData.map((r, i) => r - expensesData[i]);

    // Helper format Rupiah di JS Tooltip
    const formatRp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    // Revenue & Expenses Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76,175,80,0.1)',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Expenses',
                    data: expensesData,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255,152,0,0.1)',
                    fill: true,
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${formatRp(ctx.raw)}`
                    }
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    ticks: { callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                } 
            }
        }
    });

    // Profit Trend Chart
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Profit',
                data: profitData,
                backgroundColor: '#4caf50',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `Profit: ${formatRp(ctx.raw)}`
                    }
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    ticks: { callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                } 
            }
        }
    });
</script>
@endsection