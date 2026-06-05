<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metode_pembayaran' => 'required|string|max:100',
            'total_pembayaran' => 'required|numeric|min:0',
            'status_pembayaran' => 'required|string|max:50',
        ];
    }
}
