<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metode_pembayaran' => 'sometimes|string|max:100',
            'total_pembayaran' => 'sometimes|numeric|min:0',
            'status_pembayaran' => 'sometimes|string|max:50',
        ];
    }
}
