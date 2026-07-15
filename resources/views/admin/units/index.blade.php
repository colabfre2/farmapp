@extends('layouts.admin')

@section('title', 'Units')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="card-title mb-0">Units</h3>

                    <form method="GET" action="{{ route('admin.units.index') }}" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap"></label>
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search units..." value="{{ $query ?? '' }}" style="width:200px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">🔍 Search</button>
                        @if(!empty($query))
                            <a href="{{ route('admin.units.index') }}" class="btn btn-sm btn-outline-danger">✕ Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.units.create') }}" class="btn btn-primary btn-sm">+ Add Unit</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(!empty($query))
                    <p class="text-muted mb-3">Showing results for "<strong>{{ $query }}</strong>" — {{ $units->count() }} found</p>
                @endif

                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->symbol }}</td>
                            <td>
                                <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.units.destroy', $unit) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                @if(!empty($query)) No units found for "{{ $query }}" @else No units yet @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
