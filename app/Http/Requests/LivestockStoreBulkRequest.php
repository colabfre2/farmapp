<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LivestockStoreBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'livestocks'                    => ['required', 'array', 'min:1'],
            'livestocks.*.name'             => ['required', 'string', 'max:255'],
            // kandang_id WAJIB — livestock_type_id di-derive dari kandang di controller,
            // tidak perlu divalidasi/dikirim dari form (mencegah data inkonsisten)
            'livestocks.*.kandang_id'       => ['required', 'exists:kandangs,id'],
            'livestocks.*.arrival_date'     => ['required', 'date'],
            'livestocks.*.avg_weight'       => ['nullable', 'numeric', 'min:0'],
            'livestocks.*.quantity'         => ['required', 'integer', 'min:1'],
            'livestocks.*.health_status'    => ['required', 'in:Sehat,Pemantauan,Sakit'],
            'livestocks.*.notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'livestocks.*.kandang_id.required'  => 'Kandang wajib dipilih untuk setiap baris.',
            'livestocks.*.kandang_id.exists'     => 'Kandang yang dipilih tidak valid.',
            'livestocks.*.quantity.required'     => 'Jumlah ternak wajib diisi.',
            'livestocks.*.quantity.min'          => 'Jumlah ternak minimal 1 ekor per baris.',
            'livestocks.*.health_status.required'=> 'Status kesehatan wajib dipilih.',
        ];
    }
}