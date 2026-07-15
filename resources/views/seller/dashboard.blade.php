@extends('layouts.seller')

@section('title', 'Dashboard')
@section('content')
<div class="row row-deck row-cards">

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Products</div>
                <div class="h1 mb-0">{{ $totalProducts }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Orders</div>
                <div class="h1 mb-0">{{ $totalOrders }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Crops</div>
                <div class="h1 mb-0">{{ $totalCrops }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="subheader">Total Livestock</div>
                <div class="h1 mb-0">{{ $totalLivestock }}</div>
            </div>
        </div>
    </div>

</div>
@endsection