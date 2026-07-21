<?php

namespace App\Http\Requests;

use App\DTOs\IncomeDTO;
use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'   => 'required|date',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes'  => 'nullable|string',
        ];
    }

    public function toDTO(): IncomeDTO
    {
        $data = $this->validated();
        if ($this->isMethod('post')) {
            $data['user_id'] = auth()->id();
        }

        return IncomeDTO::fromRequest($data);
    }
}