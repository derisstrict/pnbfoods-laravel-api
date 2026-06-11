<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_orderan' => 'required|string|max:50',
            'total_harga' => 'required|numeric|min:0',
            'tanggal_orderan' => 'required|date',
            'pelanggan_id' => 'required|exists:pelanggan,id'
        ];
    }
}
