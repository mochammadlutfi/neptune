<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SparePartsInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spare_part_id' => 'required|exists:spare_parts,id',
            'inventory_date' => 'required|date',
            'quantity_onboard' => 'required|integer|min:0|max:99999',
            'min_stock_level' => 'nullable|integer|min:0|max:9999',
            'reorder_point' => 'nullable|integer|min:0|max:9999',
            'reorder_quantity' => 'nullable|integer|min:0|max:9999',
            'last_reorder_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:100',
            'condition_status' => [
                'nullable',
                'string',
                Rule::in(['New', 'Used', 'Repair Required', 'Obsolete'])
            ],
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'spare_part_id.required' => 'Spare part wajib dipilih.',
            'spare_part_id.exists' => 'Spare part yang dipilih tidak valid.',
            'inventory_date.required' => 'Tanggal inventory wajib diisi.',
            'inventory_date.date' => 'Format tanggal inventory tidak valid.',
            'quantity_onboard.required' => 'Quantity onboard wajib diisi.',
            'quantity_onboard.min' => 'Quantity onboard tidak boleh negatif.',
            'quantity_onboard.max' => 'Quantity onboard terlalu besar.',
            'min_stock_level.min' => 'Min stock level tidak boleh negatif.',
            'min_stock_level.max' => 'Min stock level terlalu besar.',
            'reorder_point.min' => 'Reorder point tidak boleh negatif.',
            'reorder_point.max' => 'Reorder point terlalu besar.',
            'reorder_quantity.min' => 'Reorder quantity tidak boleh negatif.',
            'reorder_quantity.max' => 'Reorder quantity terlalu besar.',
            'last_reorder_date.date' => 'Format tanggal reorder tidak valid.',
            'storage_location.max' => 'Storage location maksimal 100 karakter.',
            'condition_status.in' => 'Condition status tidak valid.',
            'remarks.max' => 'Remarks maksimal 500 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi reorder point tidak boleh lebih besar dari min stock level
            $minStockLevel = $this->input('min_stock_level');
            $reorderPoint = $this->input('reorder_point');

            if ($minStockLevel && $reorderPoint && $reorderPoint > $minStockLevel) {
                $validator->errors()->add('reorder_point', 'Reorder point tidak boleh lebih besar dari min stock level.');
            }

            // Validasi last reorder date tidak boleh di masa depan
            $lastReorderDate = $this->input('last_reorder_date');
            if ($lastReorderDate && $lastReorderDate > now()->format('Y-m-d')) {
                $validator->errors()->add('last_reorder_date', 'Tanggal reorder tidak boleh di masa depan.');
            }

            // Validasi inventory date tidak boleh di masa depan
            $inventoryDate = $this->input('inventory_date');
            if ($inventoryDate && $inventoryDate > now()->format('Y-m-d')) {
                $validator->errors()->add('inventory_date', 'Tanggal inventory tidak boleh di masa depan.');
            }
        });
    }
}