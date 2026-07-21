<?php

namespace App\Http\Requests;

use App\DTOs\ExpenseDTO;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date'                => 'required|date',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ];
    }

    public function toDTO(): ExpenseDTO
    {
        $data = $this->validated();
        if ($this->isMethod('post')) {
            $data['user_id'] = auth()->id();
        }

        return ExpenseDTO::fromRequest($data);
    }
}