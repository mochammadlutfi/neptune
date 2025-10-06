<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GasCompressionDataRequest extends FormRequest
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
            'vessel_id' => 'required|exists:vessels,id',
            'equipment_id' => 'required|exists:equipment,id',
            'reading_timestamp' => 'required|date',
            'operating_status' => [
                'required',
                'string',
                Rule::in(['Running', 'Stopped', 'Standby', 'Maintenance', 'Trip'])
            ],
            'operating_mode' => [
                'nullable',
                'string',
                Rule::in(['Normal', 'Recycle', 'Load Step', 'Start Up', 'Shutdown'])
            ],
            'suction_pressure_psi' => 'nullable|numeric|min:0|max:9999.99',
            'discharge_pressure_psi' => 'nullable|numeric|min:0|max:9999.99',
            'compression_ratio' => 'nullable|numeric|min:1|max:99.99',
            'differential_pressure_psi' => 'nullable|numeric|min:0|max:9999.99',
            'throughput_mmscfd' => 'nullable|numeric|min:0|max:999.99',
            'recycle_flow_mmscfd' => 'nullable|numeric|min:0|max:999.99',
            'suction_temperature_f' => 'nullable|numeric|min:-100|max:999.99',
            'discharge_temperature_f' => 'nullable|numeric|min:-100|max:999.99',
            'speed_rpm' => 'nullable|numeric|min:0|max:99999',
            'power_consumption_kw' => 'nullable|numeric|min:0|max:99999.99',
            'efficiency_percent' => 'nullable|numeric|min:0|max:100',
            'vibration_de_mm_s' => 'nullable|numeric|min:0|max:99.99',
            'vibration_nde_mm_s' => 'nullable|numeric|min:0|max:99.99',
            'surge_control_valve_position' => 'nullable|numeric|min:0|max:100',
            'distance_to_surge_percent' => 'nullable|numeric|min:0|max:100',
            'recorded_by' => 'required|string|max:100',
            'validated_by' => 'nullable|string|max:100',
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
            'equipment_id.required' => 'Equipment wajib dipilih.',
            'equipment_id.exists' => 'Equipment yang dipilih tidak valid.',
            'reading_timestamp.required' => 'Waktu pembacaan wajib diisi.',
            'reading_timestamp.date' => 'Format waktu pembacaan tidak valid.',
            'operating_status.required' => 'Operating status wajib dipilih.',
            'operating_status.in' => 'Operating status tidak valid.',
            'operating_mode.in' => 'Operating mode tidak valid.',
            'recorded_by.required' => 'Recorded by wajib diisi.',
            'suction_pressure_psi.min' => 'Suction pressure tidak boleh negatif.',
            'discharge_pressure_psi.min' => 'Discharge pressure tidak boleh negatif.',
            'compression_ratio.min' => 'Compression ratio minimal 1.',
            'throughput_mmscfd.min' => 'Throughput tidak boleh negatif.',
            'recycle_flow_mmscfd.min' => 'Recycle flow tidak boleh negatif.',
            'speed_rpm.min' => 'Speed RPM tidak boleh negatif.',
            'power_consumption_kw.min' => 'Power consumption tidak boleh negatif.',
            'efficiency_percent.min' => 'Efficiency tidak boleh negatif.',
            'efficiency_percent.max' => 'Efficiency maksimal 100%.',
            'vibration_de_mm_s.min' => 'Vibration DE tidak boleh negatif.',
            'vibration_nde_mm_s.min' => 'Vibration NDE tidak boleh negatif.',
            'surge_control_valve_position.min' => 'Surge control valve position tidak boleh negatif.',
            'surge_control_valve_position.max' => 'Surge control valve position maksimal 100%.',
            'distance_to_surge_percent.min' => 'Distance to surge tidak boleh negatif.',
            'distance_to_surge_percent.max' => 'Distance to surge maksimal 100%.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi discharge pressure > suction pressure
            $suctionPressure = $this->input('suction_pressure_psi');
            $dischargePressure = $this->input('discharge_pressure_psi');
            
            if ($suctionPressure && $dischargePressure && $dischargePressure <= $suctionPressure) {
                $validator->errors()->add('discharge_pressure_psi', 'Discharge pressure harus lebih besar dari suction pressure.');
            }

            // Validasi compression ratio calculation
            $compressionRatio = $this->input('compression_ratio');
            
            if ($suctionPressure && $dischargePressure && $compressionRatio) {
                $calculatedRatio = $dischargePressure / $suctionPressure;
                if (abs($calculatedRatio - $compressionRatio) > 0.1) {
                    $validator->errors()->add('compression_ratio', 'Compression ratio tidak konsisten dengan pressure readings.');
                }
            }

            // Validasi discharge temperature > suction temperature untuk kompresor
            $suctionTemp = $this->input('suction_temperature_f');
            $dischargeTemp = $this->input('discharge_temperature_f');
            
            if ($suctionTemp && $dischargeTemp && $dischargeTemp <= $suctionTemp) {
                $validator->errors()->add('discharge_temperature_f', 'Discharge temperature biasanya lebih tinggi dari suction temperature untuk kompresor.');
            }

            // Validasi equipment belongs to vessel
            $vesselId = $this->input('vessel_id');
            $equipmentId = $this->input('equipment_id');
            
            if ($vesselId && $equipmentId) {
                $equipment = \App\Models\Master\Equipment::find($equipmentId);
                if ($equipment && $equipment->vessel_id != $vesselId) {
                    $validator->errors()->add('equipment_id', 'Equipment yang dipilih tidak termasuk dalam vessel tersebut.');
                }
            }

            // Validasi operating status consistency
            $operatingStatus = $this->input('operating_status');
            $throughput = $this->input('throughput_mmscfd', 0);
            $powerConsumption = $this->input('power_consumption_kw', 0);
            
            if ($operatingStatus === 'Stopped' && ($throughput > 0 || $powerConsumption > 0)) {
                $validator->errors()->add('operating_status', 'Equipment stopped tidak boleh memiliki throughput atau power consumption.');
            }

            if ($operatingStatus === 'Running' && $throughput == 0) {
                $validator->errors()->add('throughput_mmscfd', 'Equipment running harus memiliki throughput.');
            }
        });
    }
}