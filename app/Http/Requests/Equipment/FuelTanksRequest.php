<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FuelTanksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tankId = $this->route('id');

        return [
            'tank_name' => 'required|string|max:50',
            'tank_number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('fuel_tanks', 'tank_number')
                    ->ignore($tankId)
                    ->where('vessel_id', auth()->user()->vessel_id ?? null)
            ],
            'capacity_liters' => 'required|numeric|min:1|max:999999999.99',
            'fuel_type' => [
                'nullable',
                'string',
                Rule::in(['Diesel', 'MGO', 'HFO', 'Lube Oil'])
            ],
            'tank_location' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'tank_name.required' => 'Nama tank wajib diisi.',
            'tank_name.max' => 'Nama tank maksimal 50 karakter.',
            'tank_number.required' => 'Nomor tank wajib diisi.',
            'tank_number.unique' => 'Nomor tank sudah ada di vessel ini.',
            'tank_number.max' => 'Nomor tank maksimal 10 karakter.',
            'capacity_liters.required' => 'Kapasitas tank wajib diisi.',
            'capacity_liters.min' => 'Kapasitas tank minimal 1 liter.',
            'capacity_liters.max' => 'Kapasitas tank terlalu besar.',
            'fuel_type.in' => 'Jenis fuel tidak valid.',
            'tank_location.max' => 'Lokasi tank maksimal 100 karakter.',
        ];
    }
}