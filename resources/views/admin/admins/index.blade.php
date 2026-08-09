@extends('layouts.admin')

@section('title', 'Kelola Admin')

@section('content')
<style>
    .card-flat {
        border: none !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card card-flat mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h3 class="card-title fw-bold font-quicksand text-dark mb-0">🛡️ Kelola Admin</h3>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <form method="GET" action="{{ route('admin.admins.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                                <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Cari nama / email admin..." value="{{ $query ?? '' }}" style="width:220px;">
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">🔍</button>
                                @if(!empty($query))
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">✕ Reset</a>
                                @endif
                            </form>
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm">+ Tambah Admin</a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0 pb-0">
                    @if(session('success'))
                        <div class="alert alert-success bg-success-subtle text-success border-0 fw-bold mx-4 mb-3 rounded-3 shadow-sm">
                            ✅ {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger bg-danger-subtle text-danger border-0 fw-bold mx-4 mb-3 rounded-3 shadow-sm">
                            ❌ {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-custom table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No. Telepon</th>
                                    <th>Terdaftar</th>
                                    <th class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ $admin->name }}
                                        @if($admin->id === auth()->id())
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small ms-1">Kamu</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $admin->email }}</td>
                                    <td class="text-muted">{{ $admin->phone ?? '-' }}</td>
                                    <td class="text-muted">{{ $admin->created_at->format('d M Y') }}</td>
                                    <td class="pe-4 text-end">
                                        @if($admin->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="d-inline delete-admin-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold btn-delete-admin">🗑️ Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">Belum ada data admin.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3">
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-delete-admin').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = this.closest('.delete-admin-form');
            Swal.fire({
                title: 'Hapus akun admin ini?',
                text: "Akun yang dihapus gak bisa dipulihkan lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'rounded-pill px-4 py-2',
                    cancelButton: 'rounded-pill px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
