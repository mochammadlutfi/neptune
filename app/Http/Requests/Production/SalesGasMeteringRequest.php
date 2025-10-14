<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesGasMeteringRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'pressure_psig' => 'required|numeric',
            'temperature_f' => 'required|numeric',
            'h2o_lb_mmscf' => 'required|numeric',
            'hcdp' => 'required',
            'co2_mol_pct' => 'required|numeric',
            'ghv' => 'required|numeric',
            'specific_gravity' => 'required|numeric',
            'ejgp_pressure_psig' => 'nullable|numeric',
            'remarks' => 'nullable',
            'total_flow_rate' => 'required|numeric',
            'flowrates.*.buyer_id' => 'required|exists:gas_buyers,id',
            'flowrates.*.primary_stream' => 'required|numeric',
            'flowrates.*.backup_stream' => 'nullable|numeric',
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
            'reading_time.required' => 'Waktu pembacaan wajib diisi.',
            'reading_time.date' => 'Format waktu pembacaan tidak valid.',
            'export_pressure_psi.min' => 'Export pressure tidak boleh negatif.',
            'export_temperature_f.min' => 'Export temperature tidak boleh negatif.',
            'export_flowrate_mmscfd.min' => 'Export flowrate tidak boleh negatif.',
            'heating_value_btu_scf.min' => 'Heating value tidak boleh negatif.',
            'actual_delivery_mmscf.min' => 'Actual delivery tidak boleh negatif.',
            'variance_percent.min' => 'Variance percent minimal -100%.',
            'variance_percent.max' => 'Variance percent maksimal 100%.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi variance percent consistency dengan actual delivery
            $actualDelivery = $this->input('actual_delivery_mmscf');
            $variancePercent = $this->input('variance_percent');
            
            if ($actualDelivery && $variancePercent !== null) {
                // Validasi bahwa variance percent masuk akal
                if (abs($variancePercent) > 50) {
                    $validator->errors()->add('variance_percent', 'Variance percent terlalu besar, periksa kembali perhitungan.');
                }
            }
        });
    }
}