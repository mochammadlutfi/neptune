<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentStatusRequest extends FormRequest
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
        return [
            'equipment_id' => 'required|exists:equipment,id',
            'reading_time' => 'required|date',
            'shift' => [
                'required',
                'string',
                Rule::in(['Day', 'Night'])
            ],
            'operational_status' => [
                'required',
                'string',
                Rule::in(['Running', 'Standby', 'Shutdown'])
            ],
            'running_hours' => 'nullable|numeric|min:0|max:99999.99',
            'parameters' => 'nullable|json',
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'equipment_id.required' => 'Equipment wajib dipilih.',
            'equipment_id.exists' => 'Equipment yang dipilih tidak valid.',
            'reading_time.required' => 'Waktu pembacaan wajib diisi.',
            'reading_time.date' => 'Format waktu pembacaan tidak valid.',
            'shift.required' => 'Shift wajib dipilih.',
            'shift.in' => 'Shift tidak valid.',
            'operational_status.required' => 'Status operasional wajib dipilih.',
            'operational_status.in' => 'Status operasional tidak valid.',
            'running_hours.min' => 'Running hours tidak boleh negatif.',
            'running_hours.max' => 'Running hours terlalu besar.',
            'parameters.json' => 'Format parameters tidak valid (harus JSON).',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi equipment belongs to user's vessel
            $user = $this->user();
            $equipmentId = $this->input('equipment_id');

            if ($user && $equipmentId) {
                $equipment = \App\Models\Master\Equipment::find($equipmentId);
                if ($equipment && $equipment->vessel_id != $user->vessel_id) {
                    $validator->errors()->add('equipment_id', 'Equipment yang dipilih tidak termasuk dalam vessel Anda.');
                }
            }

            // Validasi running hours hanya boleh diisi jika status Running
            $operationalStatus = $this->input('operational_status');
            $runningHours = $this->input('running_hours');

            if ($operationalStatus !== 'Running' && $runningHours > 0) {
                $validator->errors()->add('running_hours', 'Running hours hanya boleh diisi jika status operasional adalah Running.');
            }
        });
    }
}