@extends('layouts.admin')

@section('title', 'Ubah Pengeluaran')

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Pengeluaran</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.finance.expense.update', $expense) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $expense->date) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori</label>
                        <select name="expense_category_id" class="form-select @error('expense_category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($expenseCategories as $category)
                                <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $expense->description) }}" required>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah (Rp)</label>
                        
                        <!-- Ubah bagian value untuk mengambil data dari database dan memformatnya -->
                        <input type="text" inputmode="numeric" name="amount" class="form-control input-rupiah @error('amount') is-invalid @enderror" 
                            value="{{ old('amount', isset($expense) ? 'Rp. ' . number_format($expense->amount, 0, ',', '.') : '') }}" 
                            required>
                            
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Perbarui</button>
                        <a href="{{ route('admin.finance.expense.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection