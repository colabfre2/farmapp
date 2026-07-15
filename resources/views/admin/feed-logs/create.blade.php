@extends('layouts.admin')
@section('title', 'Catat Pemberian Pakan')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">📋 Catat Pemberian Pakan</h3></div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('admin.feed-logs.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pakan</label>
                        <select name="feed_id" class="form-select @error('feed_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pakan --</option>
                            @foreach($feeds as $feed)
                                <option value="{{ $feed->id }}" {{ old('feed_id') == $feed->id ? 'selected' : '' }}>
                                    {{ $feed->name }} (Stok: {{ $feed->stock }} {{ $feed->unit }})
                                </option>
                            @endforeach
                        </select>
                        @error('feed_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ternak</label>
                        <select name="livestock_id" class="form-select @error('livestock_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Ternak --</option>
                            @foreach($livestocks as $livestock)
                                <option value="{{ $livestock->id }}" {{ old('livestock_id') == $livestock->id ? 'selected' : '' }}>
                                    {{ $livestock->name }} ({{ $livestock->quantity }} ekor)
                                </option>
                            @endforeach
                        </select>
                        @error('livestock_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Jumlah</label>
                            <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Waktu Pemberian</label>
                            <select name="time_of_day" class="form-select @error('time_of_day') is-invalid @enderror" required>
                                <option value="">-- Pilih Waktu --</option>
                                <option value="Pagi" {{ old('time_of_day') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="Siang" {{ old('time_of_day') == 'Siang' ? 'selected' : '' }}>Siang</option>
                                <option value="Sore" {{ old('time_of_day') == 'Sore' ? 'selected' : '' }}>Sore</option>
                                <option value="Malam" {{ old('time_of_day') == 'Malam' ? 'selected' : '' }}>Malam</option>
                            </select>
                            @error('time_of_day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="fed_at" class="form-control" value="{{ old('fed_at', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.feed-logs.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection