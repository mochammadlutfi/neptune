<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesGasNominationRequest extends FormRequest
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
            // 'total_nomination' => 'required|numeric',
            // 'total_confirmed' => 'required|numeric',
            'lines' => 'required|array',
            'lines.*.gas_buyer_id' => 'required|exists:gas_buyers,id',
            'lines.*.nomination' => 'required|numeric',
            'lines.*.confirmed' => 'required|numeric',
        ];
    }
}