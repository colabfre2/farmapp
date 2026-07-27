@extends('layouts.buyer')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">⭐ Beri Ulasan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                    @endif
                    <div>
                        <div class="fw-bold">{{ $product->name }}</div>
                        <div class="text-muted small">Order #{{ $order->order_number }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('buyer.reviews.store', [$order, $product->id]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rating</label>
                        <div class="d-flex gap-2" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                            <label style="cursor:pointer;">
                                <input type="radio" name="rating" value="{{ $i }}" style="display:none;"
                                    {{ old('rating', $existingReview->rating ?? 0) == $i ? 'checked' : '' }}
                                    onchange="updateStars({{ $i }})">
                                <span class="star" data-value="{{ $i }}" style="font-size:32px; color:#ddd;">⭐</span>
                            </label>
                            @endfor
                        </div>
                        @error('rating') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Komentar (opsional)</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Ceritakan pengalaman kamu dengan produk ini...">{{ old('comment', $existingReview->comment ?? '') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Kirim Ulasan</button>
                        <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateStars(rating) {
    document.querySelectorAll('.star').forEach(star => {
        star.style.color = star.dataset.value <= rating ? '#f59e0b' : '#ddd';
    });
}
// Inisialisasi warna bintang saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="rating"]:checked');
    if (checked) updateStars(checked.value);
});
</script>
@endsection