<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SparePartsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sparePartId = $this->route('id');

        return [
            'part_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('spare_parts', 'part_number')->ignore($sparePartId)
            ],
            'part_name' => 'required|string|max:200',
            'part_description' => 'nullable|string|max:1000',
            'equipment_id' => 'nullable|exists:equipment,id',
            'part_category' => [
                'nullable',
                'string',
                Rule::in(['Engine', 'Electrical', 'Mechanical', 'Instrumentation', 'Safety'])
            ],
            'manufacturer' => 'nullable|string|max:100',
            'supplier' => 'nullable|string|max:100',
            'unit_cost' => 'nullable|numeric|min:0|max:999999.9999',
            'currency' => 'nullable|string|max:3|in:USD,IDR,EUR,SGD',
            'unit_measure' => 'nullable|string|max:20',
            'min_stock_level' => 'nullable|integer|min:0|max:9999',
            'reorder_point' => 'nullable|integer|min:0|max:9999',
        ];
    }

    public function messages(): array
    {
        return [
            'part_number.required' => 'Part number wajib diisi.',
            'part_number.unique' => 'Part number sudah ada.',
            'part_number.max' => 'Part number maksimal 50 karakter.',
            'part_name.required' => 'Nama part wajib diisi.',
            'part_name.max' => 'Nama part maksimal 200 karakter.',
            'part_description.max' => 'Deskripsi part maksimal 1000 karakter.',
            'equipment_id.exists' => 'Equipment yang dipilih tidak valid.',
            'part_category.in' => 'Kategori part tidak valid.',
            'manufacturer.max' => 'Manufacturer maksimal 100 karakter.',
            'supplier.max' => 'Supplier maksimal 100 karakter.',
            'unit_cost.min' => 'Unit cost tidak boleh negatif.',
            'unit_cost.max' => 'Unit cost terlalu besar.',
            'currency.in' => 'Currency tidak valid.',
            'unit_measure.max' => 'Unit measure maksimal 20 karakter.',
            'min_stock_level.min' => 'Min stock level tidak boleh negatif.',
            'min_stock_level.max' => 'Min stock level terlalu besar.',
            'reorder_point.min' => 'Reorder point tidak boleh negatif.',
            'reorder_point.max' => 'Reorder point terlalu besar.',
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
        });
    }
}