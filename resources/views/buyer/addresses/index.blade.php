@extends('layouts.buyer')
@section('title', 'Alamat Saya')

@section('content')
<div class="container py-4" style="max-width: 760px;">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">📍 Alamat Saya</h4>
        <a href="{{ route('buyer.addresses.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
            + Tambah Alamat
        </a>
    </div>

    {{-- Flash Message Success --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4">
            <span style="font-size: 1.2rem;">✅</span>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Daftar Alamat --}}
    @forelse($addresses as $address)
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; transition: transform 0.2s ease; {{ $address->is_default ? 'border: 1px solid #4caf50 !important; background-color: rgba(76, 175, 80, 0.05);' : '' }}">
        <div class="card-body p-4">
            <div class="row align-items-center">
                
                {{-- Info Alamat (Kiri) --}}
                <div class="col-md-8 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3">{{ $address->label }}</span>
                        @if($address->is_default)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">✓ Utama</span>
                        @endif
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1">{{ $address->recipient_name }}</div>
                    <div class="text-muted small fw-medium mb-2">📞 {{ $address->phone }}</div>
                    <div class="text-secondary small" style="line-height: 1.5;">
                        {{ $address->full_address }}<br>
                        {{ $address->district }}, {{ $address->city }}, {{ $address->province }} 
                        {{ $address->postal_code ? '- '.$address->postal_code : '' }}
                    </div>
                </div>

                {{-- Action Buttons (Kanan) --}}
                <div class="col-md-4">
                    <div class="d-flex flex-md-column flex-row justify-content-md-end gap-2">
                        <a href="{{ route('buyer.addresses.edit', $address) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                            ✏️ Edit
                        </a>
                        
                        @if(!$address->is_default)
                            <form method="POST" action="{{ route('buyer.addresses.set-default', $address) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 w-100 fw-semibold">
                                    ⭐ Set Utama
                                </button>
                            </form>
                            <form method="POST" action="{{ route('buyer.addresses.destroy', $address) }}" class="m-0 form-delete">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 w-100 fw-semibold btn-delete">
                                    🗑️ Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    @empty
    {{-- State Kosong --}}
    <div class="text-center py-5 text-muted bg-white shadow-sm" style="border-radius: 16px;">
        <div style="font-size: 4rem;" class="mb-3">📭</div>
        <h5 class="fw-bold text-dark">Belum ada alamat tersimpan</h5>
        <p class="small mb-4">Tambahkan alamat pengiriman agar proses checkout lebih cepat dan mudah.</p>
        <a href="{{ route('buyer.addresses.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
            + Tambah Alamat Pertama
        </a>
    </div>
    @endforelse

</div>

{{-- Memanggil SweetAlert2 dari CDN (jika belum ada di layout utama) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Listener untuk tombol hapus
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form.form-delete');
            
            Swal.fire({
                title: 'Hapus alamat ini?',
                text: "Alamat yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                borderRadius: '12px'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection