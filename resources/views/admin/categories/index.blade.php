@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Categories</h3>

                    <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex align-items-center gap-2" >
                        <label class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search categories..." value="{{ $query ?? '' }}" style="width:200px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Search</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-danger ">✕ Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">+ Add Category</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">Showing results for "<strong>{{ $query }}</strong>" — {{ $categories->count() }} found</p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete" >Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                @if(!empty($query)) No categories found for "{{ $query }}" @else No categories yet @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    const searchInput = document.querySelector('input[name="q"]');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            searchInput.closest('form').submit();
        }, 500); // delay 500ms setelah berhenti mengetik
    });
</script>
@endsection
