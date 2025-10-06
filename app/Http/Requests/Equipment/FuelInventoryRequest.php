<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class FuelInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tank_id' => 'required|exists:fuel_tanks,id',
            'inventory_date' => 'required|date',
            'opening_volume_liters' => 'nullable|numeric|min:0|max:999999999.99',
            'received_volume_liters' => 'nullable|numeric|min:0|max:999999999.99',
            'consumed_volume_liters' => 'nullable|numeric|min:0|max:999999999.99',
            'rob_volume_liters' => 'required|numeric|min:0|max:999999999.99',
            'ullage_level' => 'nullable|numeric|min:0|max:99999.99',
            'temperature' => 'nullable|numeric|min:-50|max:200',
            'density' => 'nullable|numeric|min:0|max:9999.99',
            'water_content' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'tank_id.required' => 'Tank wajib dipilih.',
            'tank_id.exists' => 'Tank yang dipilih tidak valid.',
            'inventory_date.required' => 'Tanggal inventory wajib diisi.',
            'inventory_date.date' => 'Format tanggal inventory tidak valid.',
            'opening_volume_liters.min' => 'Opening volume tidak boleh negatif.',
            'opening_volume_liters.max' => 'Opening volume terlalu besar.',
            'received_volume_liters.min' => 'Received volume tidak boleh negatif.',
            'received_volume_liters.max' => 'Received volume terlalu besar.',
            'consumed_volume_liters.min' => 'Consumed volume tidak boleh negatif.',
            'consumed_volume_liters.max' => 'Consumed volume terlalu besar.',
            'rob_volume_liters.required' => 'ROB volume wajib diisi.',
            'rob_volume_liters.min' => 'ROB volume tidak boleh negatif.',
            'rob_volume_liters.max' => 'ROB volume terlalu besar.',
            'ullage_level.min' => 'Ullage level tidak boleh negatif.',
            'ullage_level.max' => 'Ullage level terlalu besar.',
            'temperature.min' => 'Temperature terlalu rendah.',
            'temperature.max' => 'Temperature terlalu tinggi.',
            'density.min' => 'Density tidak boleh negatif.',
            'density.max' => 'Density terlalu besar.',
            'water_content.min' => 'Water content tidak boleh negatif.',
            'water_content.max' => 'Water content maksimal 100%.',
            'remarks.max' => 'Remarks maksimal 500 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi inventory date tidak boleh di masa depan
            $inventoryDate = $this->input('inventory_date');
            if ($inventoryDate && $inventoryDate > now()->format('Y-m-d')) {
                $validator->errors()->add('inventory_date', 'Tanggal inventory tidak boleh di masa depan.');
            }

            // Validasi material balance jika semua data tersedia
            $openingVolume = $this->input('opening_volume_liters', 0);
            $receivedVolume = $this->input('received_volume_liters', 0);
            $consumedVolume = $this->input('consumed_volume_liters', 0);
            $robVolume = $this->input('rob_volume_liters', 0);

            $calculatedROB = $openingVolume + $receivedVolume - $consumedVolume;

            if (abs($calculatedROB - $robVolume) > ($robVolume * 0.05)) { // 5% tolerance
                $validator->errors()->add('rob_volume_liters', 'ROB volume tidak sesuai dengan material balance (toleransi 5%).');
            }

            // Validasi ROB tidak melebihi kapasitas tank
            $tankId = $this->input('tank_id');
            if ($tankId && $robVolume > 0) {
                $tank = \App\Models\Equipment\FuelTanks::find($tankId);
                if ($tank && $robVolume > $tank->capacity_liters) {
                    $validator->errors()->add('rob_volume_liters', 'ROB volume tidak boleh melebihi kapasitas tank.');
                }
            }
        });
    }
}