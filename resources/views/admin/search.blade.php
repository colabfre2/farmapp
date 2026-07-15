@extends('layouts.admin')

@section('title', 'Search Results')

@section('content')
<div class="mb-4">
    <h3>Search results for: "<span class="text-success">{{ $query }}</span>"</h3>
</div>

@php
$sections = [
    'Products' => ['data' => $products, 'route' => 'admin.products.edit', 'icon' => '📦'],
    'Crops' => ['data' => $crops, 'route' => 'admin.crops.edit', 'icon' => '🌱'],
    'Livestock' => ['data' => $livestocks, 'route' => 'admin.livestock.edit', 'icon' => '🐄'],
    'Harvests' => ['data' => $harvests, 'route' => 'admin.harvests.edit', 'icon' => '🌾', 'field' => 'product_name'],
    'Categories' => ['data' => $categories, 'route' => 'admin.categories.edit', 'icon' => '📂'],
    'Units' => ['data' => $units, 'route' => 'admin.units.edit', 'icon' => '📏'],
    'Crop Types' => ['data' => $cropTypes, 'route' => 'admin.crop-types.edit', 'icon' => '🌱'],
    'Livestock Types' => ['data' => $livestockTypes, 'route' => 'admin.livestock-types.edit', 'icon' => '🐄'],
    'Expense Categories' => ['data' => $expenseCategories, 'route' => 'admin.expense-categories.edit', 'icon' => '💸'],
];
$totalResults = $products->count() + $crops->count() + $livestocks->count() + $harvests->count() + $categories->count() + $units->count() + $cropTypes->count() + $livestockTypes->count() + $expenseCategories->count();
@endphp

@if($totalResults === 0)
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            No results found for "{{ $query }}"
        </div>
    </div>
@else
    @foreach($sections as $label => $section)
        @if($section['data']->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ $section['icon'] }} {{ $label }} ({{ $section['data']->count() }})</h3>
            </div>
            <div class="list-group list-group-flush">
                @foreach($section['data'] as $item)
                <a href="{{ route($section['route'], $item) }}" class="list-group-item list-group-item-action">
                    {{ $item[$section['field'] ?? 'name'] }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@endif
@endsection