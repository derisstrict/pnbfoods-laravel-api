<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_orderan' => 'sometimes|string|max:50',
            'total_harga' => 'sometimes|numeric|min:0',
            'tanggal_orderan' => 'sometimes|date',
        ];
    }
}
