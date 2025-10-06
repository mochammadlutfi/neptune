<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
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
        $equipmentId = $this->route('id') ?? null;
        
        return [
            'vessel_id' => 'required|exists:vessels,id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipment', 'code')->ignore($equipmentId)
            ],
            'tag' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipment', 'tag')->ignore($equipmentId)
            ],
            'name' => 'required|string|max:200',
            'type' => [
                'required',
                'string'
            ],
            'category' => [
                'required',
                'string',
            ],
            'is_critical' => 'boolean',
            'manufacturer' => 'required|string|max:200',
            'model' => 'required|string|max:200',
            'serial_number' => 'required|string|max:200',
            'installation_date' => 'required|date',
        ];
    }

}