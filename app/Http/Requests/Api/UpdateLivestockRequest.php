<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'arrival_date'      => 'nullable|date',
            'name'              => 'required|string|max:255',
            'avg_weight'        => 'nullable|numeric',
            'health_status'     => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'             => 'nullable|string',
        ];
    }
}