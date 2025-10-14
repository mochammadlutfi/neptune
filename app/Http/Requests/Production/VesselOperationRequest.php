<?php
namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VesselOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $operationId = $this->route('vessel_operation');
        
        return [
            'vessel_id' => ['required', 'exists:vessels,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('vessel_operations')
                    ->where('vessel_id', $this->vessel_id)
                    ->ignore($operationId)
            ],
            
            // Gas Operations
            'inlet_gas_mmscfd' => ['nullable', 'numeric', 'min:0'],
            'total_sales_gas_mmscfd' => ['nullable', 'numeric', 'min:0'],
            'fuel_gas_mmscfd' => ['nullable', 'numeric', 'min:0'],
            'flare_hp_mmscfd' => ['nullable', 'numeric', 'min:0'],
            'flare_lp_mmscfd' => ['nullable', 'numeric', 'min:0'],
            'gas_export_uptime' => ['nullable', 'date_format:H:i:s'],
            'inlet_pressure_psi' => ['nullable', 'numeric'],
            'inlet_temp_f' => ['nullable', 'numeric'],
            'outlet_pressure_psi' => ['nullable', 'numeric'],
            'outlet_temp_f' => ['nullable', 'numeric'],
            
            // Condensate Operations
            'condensate_produced_lts' => ['nullable', 'numeric', 'min:0'],
            'condensate_produced_bbls' => ['nullable', 'numeric', 'min:0'],
            'condensate_skimmed_bbls' => ['nullable', 'numeric', 'min:0'],
            'condensate_stock_bbls' => ['nullable', 'numeric', 'min:0'],
            'condensate_consumed_gtg_bbls' => ['nullable', 'numeric', 'min:0'],
            'condensate_temp_f' => ['nullable', 'numeric'],
            'condensate_uptime' => ['nullable', 'date_format:H:i:s'],
            
            // Diesel Fuel
            'diesel_fuel_mopu_ltr' => ['nullable', 'numeric', 'min:0'],
            'diesel_fuel_hcml_ltr' => ['nullable', 'numeric', 'min:0'],
            
            // Water Operations
            'produced_water_total_bbls' => ['nullable', 'numeric', 'min:0'],
            'produced_water_offspec_bbls' => ['nullable', 'numeric', 'min:0'],
            'produced_water_overboard_bbls' => ['nullable', 'numeric', 'min:0'],
            'water_oiw_content_ppm' => ['nullable', 'numeric', 'min:0'],
            
            // Well Flows (dynamic)
            'well_flows' => ['nullable', 'array'],
            'well_flows.*.well_id' => ['required_with:well_flows', 'exists:wells,id'],
            'well_flows.*.gas_flow_rate_mmscfd' => ['required_with:well_flows', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'vessel_id.required' => 'Vessel wajib dipilih.',
            'vessel_id.exists' => 'Vessel tidak valid.',
            'operation_date.required' => 'Tanggal operasi wajib diisi.',
            'operation_date.unique' => 'Data operasi untuk vessel dan tanggal ini sudah ada.',
            'gas_export_uptime.date_format' => 'Format uptime harus HH:MM:SS (contoh: 10:00:00)',
            'condensate_uptime.date_format' => 'Format uptime harus HH:MM:SS (contoh: 08:30:00)',
            'well_flows.*.well_id.exists' => 'Well tidak valid.',
            'well_flows.*.gas_flow_rate_mmscfd.required_with' => 'Flow rate well wajib diisi.',
        ];
    }
}