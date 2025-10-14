<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TankRequest extends FormRequest
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
        $vesselId = $this->input('vessel_id');
        
        return [
            'vessel_id' => 'required|integer|exists:vessels,id',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('tanks', 'code')
                    ->where('vessel_id', $vesselId)
                    ->ignore($id)
            ],
            'name' => 'required|string|max:100',
            'type' => [
                'required',
                Rule::in(['Condensate', 'Produced Water', 'Ballast', 'Fuel'])
            ],
            'is_multiphase' => 'nullable|boolean',
            'product_type' => [
                'nullable',
                Rule::in(['Oil', 'Condensate', 'Gas', 'None'])
            ],
            'capacity_bbls' => 'required|numeric|min:0|max:99999999.99',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'vessel_id.required' => 'Vessel harus dipilih.',
            'vessel_id.exists' => 'Vessel yang dipilih tidak valid.',
            'code.required' => 'Kode tank harus diisi.',
            'code.unique' => 'Kode tank sudah digunakan untuk vessel ini.',
            'code.max' => 'Kode tank maksimal 10 karakter.',
            'name.required' => 'Nama tank harus diisi.',
            'name.max' => 'Nama tank maksimal 100 karakter.',
            'type.required' => 'Tipe tank harus dipilih.',
            'type.in' => 'Tipe tank tidak valid.',
            'product_type.in' => 'Tipe produk tidak valid.',
            'capacity_bbls.required' => 'Kapasitas tank harus diisi.',
            'capacity_bbls.numeric' => 'Kapasitas tank harus berupa angka.',
            'capacity_bbls.min' => 'Kapasitas tank tidak boleh negatif.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi product_type untuk tank multiphase
            if ($this->is_multiphase && $this->product_type === 'None') {
                $validator->errors()->add(
                    'product_type', 
                    'Tank multiphase harus memiliki product type yang valid.'
                );
            }
        });
    }
}