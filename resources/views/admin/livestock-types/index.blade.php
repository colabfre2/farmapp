@extends('layouts.admin')

@section('title', 'Livestock Types')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Livestock Types</h3>
                <a href="{{ route('admin.livestock-types.create') }}" class="btn btn-primary btn-sm">+ Add Livestock Type</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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
                        @foreach($livestockTypes as $livestockType)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $livestockType->name }}</td>
                            <td>{{ $livestockType->description ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.livestock-types.edit', $livestockType) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.livestock-types.destroy', $livestockType) }}" style="display:inline" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete" >Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection