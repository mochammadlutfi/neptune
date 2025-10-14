<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesGasAllocationRequest extends FormRequest
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
            'vessel_id' => 'required|exists:vessels,id',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'remarks' => 'nullable|string',
            'lines' => 'required|array',
            'lines.*.buyer_id' => 'required|exists:gas_buyers,id',
            'lines.*.allocation' => 'required|numeric',
        ];
    }
}