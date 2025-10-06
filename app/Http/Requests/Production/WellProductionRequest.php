<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WellProductionRequest extends FormRequest
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
            'well_id' => 'required|exists:wells,id',
            'shift' => 'nullable|string',
            'oil_rate_bph' => 'nullable|numeric|min:0|max:99999.99',
            'gas_rate_mscfh' => 'nullable|numeric|min:0|max:99999.99',
            'water_rate_bph' => 'nullable|numeric|min:0|max:99999.99',
            'wellhead_pressure_psi' => 'nullable|numeric|min:0|max:9999.99',
            'wellhead_temperature_f' => 'nullable|numeric|min:0|max:999.99',
            'choke_size_64th' => 'nullable|numeric|min:0|max:64',
            'flow_hours' => 'nullable|numeric|min:0|max:24',
            'api_gravity' => 'nullable|numeric|min:0|max:99.99',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'vessel_id.required' => 'Vessel wajib dipilih.',
            'vessel_id.exists' => 'Vessel yang dipilih tidak valid.',
            'well_id.required' => 'Well wajib dipilih.',
            'well_id.exists' => 'Well yang dipilih tidak valid.',
            'reading_timestamp.required' => 'Waktu pembacaan wajib diisi.',
            'reading_timestamp.date' => 'Format waktu pembacaan tidak valid.',
            'shift.required' => 'Shift wajib dipilih.',
            'shift.in' => 'Shift tidak valid.',
            'oil_rate_bph.min' => 'Oil rate tidak boleh negatif.',
            'gas_rate_mscfh.min' => 'Gas rate tidak boleh negatif.',
            'water_rate_bph.min' => 'Water rate tidak boleh negatif.',
            'wellhead_pressure_psi.min' => 'Wellhead pressure tidak boleh negatif.',
            'wellhead_temperature_f.min' => 'Wellhead temperature tidak boleh negatif.',
            'choke_opening_pct.min' => 'Choke opening tidak boleh negatif.',
            'choke_opening_pct.max' => 'Choke opening maksimal 100%.',
            'flow_hours.min' => 'Flow hours tidak boleh negatif.',
            'flow_hours.max' => 'Flow hours maksimal 24 jam.',
            'downtime_hours.min' => 'Downtime hours tidak boleh negatif.',
            'downtime_hours.max' => 'Downtime hours maksimal 24 jam.',
            'test_duration_hours.min' => 'Test duration tidak boleh negatif.',
            'test_duration_hours.max' => 'Test duration maksimal 24 jam.',
            'api_gravity.min' => 'API gravity tidak boleh negatif.',
            'bs_w_percent.min' => 'BS&W tidak boleh negatif.',
            'bs_w_percent.max' => 'BS&W maksimal 100%.',
            'data_quality.in' => 'Data quality tidak valid.',
            'recorded_by.required' => 'Recorded by wajib diisi.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi total flow hours + downtime hours tidak melebihi 24
            $flowHours = $this->input('flow_hours', 0);
            $downtimeHours = $this->input('downtime_hours', 0);
            
            if (($flowHours + $downtimeHours) > 24) {
                $validator->errors()->add('downtime_hours', 'Total flow hours dan downtime hours tidak boleh melebihi 24 jam.');
            }

            // Validasi jika is_well_test true, test_duration_hours harus diisi
            $isWellTest = $this->input('is_well_test', false);
            $testDuration = $this->input('test_duration_hours');
            
            if ($isWellTest && !$testDuration) {
                $validator->errors()->add('test_duration_hours', 'Test duration wajib diisi untuk well test.');
            }

            // Validasi jika ada downtime_hours, downtime_reason harus diisi
            $downtimeReason = $this->input('downtime_reason');
            
            if ($downtimeHours > 0 && !$downtimeReason) {
                $validator->errors()->add('downtime_reason', 'Downtime reason wajib diisi jika ada downtime hours.');
            }

            // Validasi well belongs to vessel
            $vesselId = $this->input('vessel_id');
            $wellId = $this->input('well_id');
            
            if ($vesselId && $wellId) {
                $well = \App\Models\Master\Well::find($wellId);
                if ($well && $well->vessel_id != $vesselId) {
                    $validator->errors()->add('well_id', 'Well yang dipilih tidak termasuk dalam vessel tersebut.');
                }
            }
        });
    }
}