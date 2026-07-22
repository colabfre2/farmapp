<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'livestock_type_id' => 'required|exists:livestock_types,id',
            'arrival_date'      => 'nullable|date',
            'name'              => 'required|string|max:255',
            'quantity'          => 'required|integer|min:1',
            'avg_weight' => 'nullable|numeric',
            'health_status'     => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'             => 'nullable|string',
        ];
    }
}