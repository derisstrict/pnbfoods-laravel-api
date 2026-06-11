<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetailOrderanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
            'orderan_id' => 'required|exists:orderan,id'
        ];
    }
}
