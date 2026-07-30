<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivestockHealthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'health_status' => 'required|in:Sehat,Pemantauan,Sakit',
            'notes'         => 'nullable|string',
        ];
    }
}