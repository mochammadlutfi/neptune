<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChemicalRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $chemicalId = $this->route('id') ? $this->route('id') : null;
        
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('chemicals', 'code')->ignore($chemicalId)
            ],
            'name' => 'required|string|max:200',
            'type' => [
                'required',
            ],
            'unit' => [
                'required',
                'string'
            ],
            'spesific_gravity' => [
                'nullable'
            ]
        ];
    }
}