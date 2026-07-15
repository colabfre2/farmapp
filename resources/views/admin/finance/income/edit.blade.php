@extends('layouts.admin')

@section('title', 'Ubah Pemasukan')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Pemasukan</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.finance.income.update', $income) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $income->date) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sumber Pemasukan</label>
                        <select name="income_source_id" class="form-select @error('income_source_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Sumber --</option>
                            @foreach($incomeSources as $source)
                                <option value="{{ $source->id }}" {{ old('income_source_id', $income->income_source_id) == $source->id ? 'selected' : '' }}>
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('income_source_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah (Rp)</label>
                        <input type="text" inputmode="numeric" name="amount" class="form-control input-rupiah @error('amount') is-invalid @enderror" value="{{ old('amount', isset($income) ? 'Rp. ' . number_format($income->amount, 0, ',', '.') : '') }}" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $income->notes) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Perbarui</button>
                        <a href="{{ route('admin.finance.income.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection