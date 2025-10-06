<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GasBuyerRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendapatkan aturan validasi yang diterapkan pada request.
     */
    public function rules(): array
    {
        $id = $this->route('id') ? $this->route('id') : null;
        
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('gas_buyers', 'code')->ignore($id)
            ],
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Gas Buyer code wajib diisi.',
            'code.unique' => 'Gas Buyer code sudah digunakan.',
            'name.required' => 'Gas Buyer name wajib diisi.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional custom validation logic can be added here
        });
    }
}