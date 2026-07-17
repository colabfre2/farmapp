@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    /* Styling khusus ala Flat/Modern Dashboard */
    .bg-dashboard {
        background-color: #f0f3f8;
        min-height: 100vh;
    }
    .card-flat {
        border: none !important;
        border-radius: 16px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-flat:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    }
    .card-dark-stat {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        border-radius: 16px;
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
    }
    /* Mini Calendar Grid */
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        text-align: center;
        font-size: 13px;
    }
    .cal-day-head {
        font-weight: 700;
        color: #94a3b8;
        padding-bottom: 4px;
    }
    .cal-date {
        padding: 6px 0;
        border-radius: 8px;
        color: #334155;
        font-weight: 600;
    }
    .cal-date.active {
        background-color: #10b981;
        color: #ffffff;
    }
    .cal-date.highlight {
        background-color: #f1f5f9;
    }
</style>

<div class="container-fluid py-2">
    
    {{-- Top Bar: Greeting & Search Floating --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            
            <p class="text-muted mb-0">Welcome back, <strong class="text-dark">{{ auth()->user()->name }}</strong>! Ini rangkuman farm kamu hari ini.</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.search') }}" class="d-flex align-items-center bg-white p-1 rounded-pill shadow-sm border" style="max-width: 320px;">
                <input type="text" name="q" class="form-control border-0 bg-transparent shadow-none ps-3" placeholder="Search dashboard..." value="{{ request('q') }}">
                <button type="submit" class="btn btn-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    🔍
                </button>
            </form>
        </div>
    </div>

    {{-- STAT CARDS (ROW 1: 6 STATS COMPACT) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Produk --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-primary-subtle text-primary">📦</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small">Total</span>
                </div>
                <div class="h2 fw-bold text-dark mb-0 font-quicksand">{{ $totalProducts }}</div>
                <div class="text-muted small fw-semibold">Total Produk</div>
            </div>
        </div>

        {{-- Card 2: Tanaman Aktif --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-success-subtle text-success">🌱</span>
                    <span class="badge bg-success-subtle text-success rounded-pill small">Aktif</span>
                </div>
                <div class="h2 fw-bold text-dark mb-0 font-quicksand">{{ $totalCrops }}</div>
                <div class="text-muted small fw-semibold">Tanaman Aktif</div>
            </div>
        </div>

        {{-- Card 3: Ternak --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-warning-subtle text-warning">🐄</span>
                    <span class="badge bg-warning-subtle text-warning rounded-pill small">Populasi</span>
                </div>
                <div class="h2 fw-bold text-dark mb-0 font-quicksand">{{ $totalLivestock }}</div>
                <div class="text-muted small fw-semibold">Ternak</div>
            </div>
        </div>

        {{-- Card 4: Pesanan --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-purple-subtle" style="background:#f3e8ff; color:#a855f7;">🛒</span>
                    <span class="badge rounded-pill small" style="background:#f3e8ff; color:#a855f7;">Order</span>
                </div>
                <div class="h2 fw-bold text-dark mb-0 font-quicksand">{{ $totalOrders }}</div>
                <div class="text-muted small fw-semibold">Pesanan</div>
            </div>
        </div>

        {{-- Card 5: Pendapatan --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-emerald-subtle" style="background:#d1fae5; color:#059669;">💵</span>
                    <span class="badge rounded-pill small" style="background:#d1fae5; color:#059669;">Bruto</span>
                </div>
                <div class="h4 fw-bold text-dark mb-0 font-quicksand">{{ rupiah($totalRevenue) }}</div>
                <div class="text-muted small fw-semibold">Pendapatan</div>
            </div>
        </div>

        {{-- Card 6: Laba Bersih --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-flat p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-icon-box bg-info-subtle text-info">📈</span>
                    <span class="badge bg-info-subtle text-info rounded-pill small">Net</span>
                </div>
                <div class="h4 fw-bold text-dark mb-0 font-quicksand">{{ rupiah($netProfit) }}</div>
                <div class="text-muted small fw-semibold">Laba Bersih</div>
            </div>
        </div>
    </div>

    {{-- MIDDLE SECTION: CHARTS & SIDE WIDGET (CALENDAR / SUMMARY) --}}
    <div class="row g-4 mb-4">
        
        {{-- Left: Revenue vs Expenses Chart (Sesuai Layout Mockup) --}}
        <div class="col-lg-8">
            <div class="card card-flat p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold text-dark font-quicksand mb-0">Pendapatan & Pengeluaran</h4>
                        <span class="text-muted small">Perbandingan performa keuangan {{ date('Y') }}</span>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-quicksand">{{ date('Y') }}</span>
                </div>
                <div class="mt-2" style="position: relative; height: 280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Right Column: Highlight Income Box + Mini Calendar Widget --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                
                {{-- Dark Highlight Income Box --}}
                <div class="card card-dark-stat p-3 shadow-sm">
                    <div class="text-white-50 small font-quicksand text-uppercase tracking-wider">Laba Bersih Terbaru</div>
                    <div class="display-6 fw-bold text-success my-1 font-quicksand">{{ rupiah($netProfit) }}</div>
                    <div class="text-slate-300 small">Akumulasi laba bersih yang tercatat sejauh ini.</div>
                </div>

                {{-- Calendar Widget (Gaya Mockup July 2018) --}}
                <div class="card card-flat p-3 flex-fill">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark font-quicksand mb-0">📅 {{ strtoupper(date('F Y')) }}</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary border-0 py-0">&lt;</button>
                            <button class="btn btn-outline-secondary border-0 py-0">&gt;</button>
                        </div>
                    </div>
                    <div class="cal-grid">
                        <div class="cal-day-head">S</div>
                        <div class="cal-day-head">M</div>
                        <div class="cal-day-head">T</div>
                        <div class="cal-day-head">W</div>
                        <div class="cal-day-head">T</div>
                        <div class="cal-day-head">F</div>
                        <div class="cal-day-head">S</div>

                        {{-- Dummy Date Grid for Visual Effect --}}
                        <div class="cal-date text-muted">29</div>
                        <div class="cal-date text-muted">30</div>
                        <div class="cal-date">1</div>
                        <div class="cal-date">2</div>
                        <div class="cal-date">3</div>
                        <div class="cal-date">4</div>
                        <div class="cal-date">5</div>
                        <div class="cal-date">6</div>
                        <div class="cal-date highlight">7</div>
                        <div class="cal-date">8</div>
                        <div class="cal-date">9</div>
                        <div class="cal-date">10</div>
                        <div class="cal-date">11</div>
                        <div class="cal-date">12</div>
                        <div class="cal-date">13</div>
                        <div class="cal-date">14</div>
                        <div class="cal-date active">15</div>
                        <div class="cal-date">16</div>
                        <div class="cal-date">17</div>
                        <div class="cal-date">18</div>
                        <div class="cal-date">19</div>
                        <div class="cal-date">20</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- BARIS 3: PROFIT TREND CHART & TABLES --}}
    <div class="row g-4 mb-4">
        
        {{-- Profit Bar Chart --}}
        <div class="col-lg-4">
            <div class="card card-flat p-4 h-100">
                <div class="mb-3">
                    <h4 class="fw-bold text-dark font-quicksand mb-0">Tren Laba</h4>
                    <span class="text-muted small">Grafik keuntungan bulanan</span>
                </div>
                <div style="position: relative; height: 230px;">
                    <canvas id="profitChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-lg-4">
            <div class="card card-flat h-100 overflow-hidden">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h5 class="fw-bold text-dark font-quicksand mb-0">🛒 Pesanan Terbaru</h5>
                    <a href="{{ route('admin.transactions.index') }}" class="text-success small fw-bold text-decoration-none">Lihat Semua →</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                        <div class="list-group-item d-flex align-items-center gap-3 py-3 px-3 border-0 border-bottom">
                            <div class="avatar bg-success-subtle text-success fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="flex-fill overflow-hidden">
                                <div class="fw-bold text-dark text-truncate">{{ $order->user->name ?? 'Guest' }}</div>
                                <div class="text-muted small text-truncate">{{ $order->items->first()->product_name ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark small">{{ rupiah($order->total_amount) }}</div>
                                <span class="badge rounded-pill 
                                    @if($order->status == 'Pending') bg-warning-subtle text-warning
                                    @elseif($order->status == 'Processing') bg-primary-subtle text-primary
                                    @elseif($order->status == 'Shipped') bg-info-subtle text-info
                                    @elseif($order->status == 'Completed') bg-success-subtle text-success
                                    @else bg-danger-subtle text-danger
                                    @endif" style="font-size: 10px;">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted small">Belum ada pesanan masuk</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Harvests --}}
        <div class="col-lg-4">
            <div class="card card-flat h-100 overflow-hidden">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h5 class="fw-bold text-dark font-quicksand mb-0">🌾 Panen Terbaru</h5>
                    <a href="{{ route('admin.harvests.index') }}" class="text-success small fw-bold text-decoration-none">Lihat Semua →</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentHarvests as $harvest)
                        <div class="list-group-item d-flex align-items-center gap-3 py-3 px-3 border-0 border-bottom">
                            <div class="avatar bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                🌾
                            </div>
                            <div class="flex-fill overflow-hidden">
                                <div class="fw-bold text-dark text-truncate">{{ $harvest->product_name }}</div>
                                <div class="text-muted small">{{ $harvest->harvested_at }} · {{ $harvest->quantity }} {{ $harvest->unit?->symbol ?? '' }}</div>
                            </div>
                            <div class="fw-bold text-success small">
                                {{ rupiah($harvest->quantity * $harvest->selling_price) }}
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted small">Belum ada catatan panen</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const revenueData  = @json($revenueData);
    const expensesData = @json($expensesData);
    const profitData   = revenueData.map((r, i) => r - expensesData[i]);

    const formatRp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    // 1. Revenue & Expenses Line Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981'
                },
                {
                    label: 'Expenses',
                    data: expensesData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.08)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'top', labels: { usePointStyle: true, font: { family: 'Quicksand', weight: 'bold' } } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${formatRp(ctx.raw)}`
                    }
                }
            },
            scales: { 
                x: { grid: { display: false } },
                y: { 
                    beginAtZero: true,
                    grid: { borderDash: [4, 4] },
                    ticks: { callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                } 
            }
        }
    });

    // 2. Profit Trend Bar Chart (Sesuai gaya bar warna-warni/modern)
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Profit',
                data: profitData,
                backgroundColor: '#3b82f6',
                borderRadius: 8,
                hoverBackgroundColor: '#2563eb'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `Profit: ${formatRp(ctx.raw)}`
                    }
                }
            },
            scales: { 
                x: { grid: { display: false } },
                y: { 
                    beginAtZero: true,
                    grid: { borderDash: [4, 4] },
                    ticks: { callback: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
                } 
            }
        }
    });
</script>
@endsection