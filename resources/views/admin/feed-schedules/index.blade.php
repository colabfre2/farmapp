@extends('layouts.admin')
@section('title', 'Jadwal Pakan')

@section('content')
<div class="row g-4">
    {{-- KIRI: Form Tambah Jadwal --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4 rounded-top-4">
                <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <span class="fs-5">⏰</span> Tambah Jadwal Pakan
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Alert Error Validasi Tetap Pakai Bootstrap agar jelas --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 bg-danger-subtle text-danger p-3 mb-4" role="alert">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.feed-schedules.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold small mb-2">Jam Pemberian Pakan <span class="text-danger">*</span></label>
                        <input type="time" name="time" class="form-control form-control-lg bg-light border-0 shadow-none @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                        @error('time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold small mb-2">Label Jadwal (Opsional)</label>
                        <input type="text" name="label" class="form-control form-control-lg bg-light border-0 shadow-none" value="{{ old('label') }}" placeholder="Contoh: Pagi, Sore, Malam">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold shadow-sm transition-all">
                        + Tambah Jadwal
                    </button>
                </form>

                <div class="alert bg-info-subtle text-info-emphasis border-0 mt-4 mb-0 small rounded-3 d-flex align-items-start gap-2 p-3">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <span>Notifikasi akan otomatis terkirim ke semua admin persis di jam yang dipilih, setiap harinya.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Tabel Jadwal --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom border-light pt-4 pb-3 px-4 rounded-top-4">
                <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <span class="fs-5">📋</span> Daftar Jadwal Aktif
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase fs-7">
                            <tr>
                                <th class="ps-4 py-3">Jam</th>
                                <th class="py-3">Label</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Terakhir Dikirim</th>
                                <th class="text-center pe-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($schedules as $schedule)
                            <tr>
                                <td class="ps-4 fw-bold text-dark fs-5">
                                    {{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}
                                </td>
                                <td>
                                    <span class="fw-medium text-secondary">{{ $schedule->label ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-medium border border-success-subtle">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1.5 fw-medium border border-secondary-subtle">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        @if($schedule->last_notified_at)
                                            <span class="fw-medium text-dark">{{ \Carbon\Carbon::parse($schedule->last_notified_at)->format('d M Y') }}</span>
                                        @else
                                            Belum pernah
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        {{-- Tombol Toggle Status --}}
                                        <form method="POST" action="{{ route('admin.feed-schedules.toggle', $schedule) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm rounded-pill px-3 fw-medium {{ $schedule->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                {{ $schedule->is_active ? 'Matikan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        
                                        {{-- Tombol Hapus (Dilengkapi Class form-delete) --}}
                                        <form method="POST" action="{{ route('admin.feed-schedules.destroy', $schedule) }}" class="form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light text-danger rounded-circle d-flex align-items-center justify-content-center btn-delete" style="width: 32px; height: 32px;" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="p-3 bg-light rounded-circle text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar-x" viewBox="0 0 16 16">
                                                <path d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z"/>
                                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                            </svg>
                                        </div>
                                        <span class="fw-medium">Belum ada jadwal pakan</span>
                                        <small class="text-muted">Tambahkan jadwal baru melalui form di sebelah kiri.</small>
                                    </div>
                                </td>
                            </tr>
                            @endempty
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CDN SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. SweetAlert untuk Konfirmasi Hapus
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Hapus Jadwal?',
                    text: "Jadwal pakan yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'rounded-pill px-4',
                        cancelButton: 'rounded-pill px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // 2. SweetAlert Toast untuk Session Success
        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif
    });
</script>
@endsection