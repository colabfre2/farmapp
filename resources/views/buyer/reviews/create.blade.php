@extends('layouts.buyer')
@section('title', 'Beri Ulasan')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            {{-- Header --}}
            <div class="card-header bg-white py-3 border-bottom border-light rounded-top-4">
                <h4 class="card-title fw-semibold text-dark mb-0">Beri Ulasan Produk</h4>
            </div>

            <div class="card-body p-4">
                {{-- Product Info --}}
                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="shadow-sm rounded-3" style="width:64px; height:64px; object-fit:cover;">
                    @else
                        <div class="bg-secondary rounded-3 d-flex align-items-center justify-content-center text-white" style="width:64px; height:64px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $product->name }}</div>
                        <div class="text-muted small">No. Pesanan: <span class="fw-medium text-dark">#{{ $order->order_number }}</span></div>
                    </div>
                </div>

                <form method="POST" action="{{ route('buyer.reviews.store', [$order, $product->id]) }}">
                    @csrf
                    
                    {{-- Star Rating --}}
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block mb-3">Bagaimana kualitas produk ini?</label>
                        
                        <div class="d-flex justify-content-center gap-2 mb-2" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="star-label m-0" style="cursor:pointer; transition: transform 0.2s;" data-value="{{ $i }}">
                                <input type="radio" name="rating" value="{{ $i }}" style="display:none;"
                                    {{ old('rating', $existingReview->rating ?? 0) == $i ? 'checked' : '' }}
                                    onchange="setRating({{ $i }})">
                                
                                {{-- Menggunakan SVG Icon agar warna bisa dikontrol sempurna --}}
                                <svg class="star-icon" width="40" height="40" fill="currentColor" viewBox="0 0 16 16" style="color: #e4e5e9; transition: color 0.2s;">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                </svg>
                            </label>
                            @endfor
                        </div>
                        
                        {{-- Indikator Teks --}}
                        <div id="rating-text" style="height: 24px;"></div>
                        
                        @error('rating') 
                            <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Komentar --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Ceritakan pengalamanmu (Opsional)</label>
                        <textarea name="comment" class="form-control bg-light border-0" rows="4" style="resize: none;" placeholder="Bagaimana kualitas, bahan, atau rasa dari produk ini?">{{ old('comment', $existingReview->comment ?? '') }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-medium shadow-sm">Kirim Ulasan</button>
                        <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-light w-100 py-2 rounded-3 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Efek membesar sedikit saat bintang di-hover */
    .star-label:hover {
        transform: scale(1.15);
    }
</style>

<script>
    const stars = document.querySelectorAll('.star-icon');
    const labels = document.querySelectorAll('.star-label');
    const ratingText = document.getElementById('rating-text');
    
    // Teks indikator berdasarkan jumlah bintang
    const statusTexts = {
        1: '<span class="text-danger fw-semibold">Sangat Buruk 😞</span>',
        2: '<span class="text-warning fw-semibold">Kurang 😕</span>',
        3: '<span class="text-secondary fw-semibold">Cukup 😐</span>',
        4: '<span class="text-info fw-semibold">Bagus 🙂</span>',
        5: '<span class="text-success fw-semibold">Sangat Bagus 🤩</span>'
    };

    // Fungsi untuk mewarnai bintang (warna emas atau abu-abu)
    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffc107'; // Warna emas Bootstrap
            } else {
                star.style.color = '#e4e5e9'; // Warna abu-abu
            }
        });
    }

    // Fungsi saat bintang di-klik (disimpan)
    function setRating(rating) {
        highlightStars(rating);
        if(rating > 0) {
            ratingText.innerHTML = statusTexts[rating];
        }
    }

    // Event Listeners untuk Hover (Menyala saat dilewati kursor)
    labels.forEach((label, index) => {
        // Saat mouse masuk ke area bintang
        label.addEventListener('mouseenter', () => {
            highlightStars(index + 1);
        });

        // Saat mouse keluar dari area bintang, kembalikan ke rating yang di-klik
        label.addEventListener('mouseleave', () => {
            const checked = document.querySelector('input[name="rating"]:checked');
            const currentVal = checked ? checked.value : 0;
            highlightStars(currentVal);
        });
    });

    // Inisialisasi warna saat halaman pertama kali di-load (jika ada error validasi / form edit)
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="rating"]:checked');
        if (checked) setRating(checked.value);
    });
</script>
@endsection