@extends('layouts.admin')

@section('title', 'Hasil Pencarian')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .font-quicksand {
        font-family: 'Quicksand', sans-serif !important;
    }
    .list-group-item-custom {
        border-left: none;
        border-right: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 1.25rem;
        color: #334155;
        transition: all 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .list-group-item-custom:last-child {
        border-bottom: none;
    }
    .list-group-item-custom:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
        color: #10b981;
    }
    .icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background-color: #f1f5f9;
        margin-right: 12px;
        font-size: 1.2rem;
    }
</style>

<div class="container-fluid py-3">
    
    {{-- Header & Tombol Kembali --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark font-quicksand mb-1">🔍 Hasil Pencarian</h2>
            <p class="text-muted small mb-0">Menampilkan data untuk kata kunci: <strong class="text-success fw-bold">"{{ $query }}"</strong></p>
        </div>
        <div>
            {{-- Tombol kembali pakai fungsi bawaan Laravel (kembali ke page sebelumnya) --}}
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill fw-bold shadow-sm px-4">
                ← Kembali
            </a>
        </div>
    </div>

    @php
    // Bikin array mapping yang rapi (Gua translate labelnya ke Bahasa Indonesia biar seragam)
    $sections = [
        'Produk Jualan' => ['data' => $products, 'route' => 'admin.products.edit', 'icon' => '📦', 'color' => 'text-primary'],
        'Tanaman' => ['data' => $crops, 'route' => 'admin.crops.edit', 'icon' => '🌱', 'color' => 'text-success'],
        'Hewan Ternak' => ['data' => $livestocks, 'route' => 'admin.livestock.edit', 'icon' => '🐄', 'color' => 'text-warning'],
        'Riwayat Panen' => ['data' => $harvests, 'route' => 'admin.harvests.edit', 'icon' => '🌾', 'field' => 'product_name', 'color' => 'text-success'],
        'Kategori Produk' => ['data' => $categories, 'route' => 'admin.categories.edit', 'icon' => '📂', 'color' => 'text-secondary'],
        'Satuan (Unit)' => ['data' => $units, 'route' => 'admin.units.edit', 'icon' => '📏', 'color' => 'text-info'],
        'Varietas Tanaman' => ['data' => $cropTypes, 'route' => 'admin.crop-types.edit', 'icon' => '🌿', 'color' => 'text-success'],
        'Jenis Ternak' => ['data' => $livestockTypes, 'route' => 'admin.livestock-types.edit', 'icon' => '🐃', 'color' => 'text-warning'],
        'Pengeluaran' => ['data' => $expenseCategories, 'route' => 'admin.expense-categories.edit', 'icon' => '💸', 'color' => 'text-danger'],
    ];
    
    $totalResults = collect($sections)->sum(function ($section) {
        return $section['data']->count();
    });
    @endphp

    @if($totalResults === 0)
        {{-- Tampilan Kalau Data Gak Ketemu --}}
        <div class="card card-flat">
            <div class="card-body text-center py-6">
                <div style="font-size: 3.5rem; color: #cbd5e1;" class="mb-3">👻</div>
                <h4 class="fw-bold text-dark font-quicksand">Ups, Data Tidak Ditemukan</h4>
                <p class="text-muted">Kami tidak bisa menemukan data apapun di seluruh sistem yang cocok dengan <strong>"{{ $query }}"</strong>.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-success rounded-pill px-4 mt-2 shadow-sm fw-bold">Kembali ke Dashboard</a>
            </div>
        </div>
    @else
        {{-- Tampilan Kalau Data Ketemu (Pake Grid 3 Kolom) --}}
        <div class="row g-4">
            @foreach($sections as $label => $section)
                @if($section['data']->count() > 0)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-flat h-100 border border-light">
                            <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold font-quicksand mb-0 d-flex align-items-center">
                                    <span class="icon-box {{ $section['color'] }}">{{ $section['icon'] }}</span>
                                    {{ $label }} 
                                </h5>
                                <span class="badge bg-light text-dark border rounded-pill">{{ $section['data']->count() }}</span>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($section['data'] as $item)
                                    <a href="{{ route($section['route'], $item) }}" class="list-group-item list-group-item-action list-group-item-custom text-decoration-none" title="Klik untuk mengedit">
                                        <span class="fw-semibold text-truncate pe-2">{{ $item[$section['field'] ?? 'name'] }}</span>
                                        <span class="text-muted small">→</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection