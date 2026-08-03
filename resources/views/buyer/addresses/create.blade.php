@extends('layouts.buyer')
@section('title', 'Tambah Alamat')

@section('content')
<div class="container py-4" style="max-width: 760px;">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">➕ Tambah Alamat Baru</h4>
        <a href="{{ route('buyer.addresses.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-semibold shadow-sm">
            ← Kembali
        </a>
    </div>

    {{-- Alert Validasi Error --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                <span>⚠️</span> Ada kesalahan pada input Anda:
            </div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('buyer.addresses.store') }}">
                @csrf
                
                {{-- Memanggil partial form --}}
                @include('buyer.addresses._form')
                
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-5 py-2 shadow-sm w-100 mt-2">
                        Simpan Alamat Baru ✓
                    </button>
                </div>
                
            </form>
        </div>
    </div>
    
</div>
@endsection