<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GasSalesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gas_buyer_id' => 'required|exists:gas_buyers,id',
            'sales_date' => 'required|date',
            'export_pressure_psi' => 'nullable|numeric|min:0|max:5000',
            'export_temp_f' => 'nullable|numeric|min:-50|max:300',
            'actual_delivery_mmscf' => 'nullable|numeric|min:0|max:1000',
            'nomination_mmscf' => 'nullable|numeric|min:0|max:1000',
            'heating_value_btu' => 'nullable|numeric|min:800|max:1200',
            'specific_gravity' => 'nullable|numeric|min:0.5|max:1.0',
            'h2s_content_ppm' => 'nullable|numeric|min:0|max:10000',
            'co2_content_pct' => 'nullable|numeric|min:0|max:50',
            'moisture_content_ppm' => 'nullable|numeric|min:0|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gas_buyer_id.required' => 'Gas buyer wajib dipilih.',
            'gas_buyer_id.exists' => 'Gas buyer yang dipilih tidak valid.',
            'sales_date.required' => 'Tanggal penjualan wajib diisi.',
            'sales_date.date' => 'Format tanggal penjualan tidak valid.',
            'export_pressure_psi.numeric' => 'Tekanan export harus berupa angka.',
            'export_pressure_psi.min' => 'Tekanan export tidak boleh negatif.',
            'export_pressure_psi.max' => 'Tekanan export maksimal 5,000 PSI.',
            'export_temp_f.numeric' => 'Temperatur export harus berupa angka.',
            'export_temp_f.min' => 'Temperatur export minimal -50°F.',
            'export_temp_f.max' => 'Temperatur export maksimal 300°F.',
            'actual_delivery_mmscf.numeric' => 'Actual delivery harus berupa angka.',
            'actual_delivery_mmscf.min' => 'Actual delivery tidak boleh negatif.',
            'actual_delivery_mmscf.max' => 'Actual delivery maksimal 1,000 MMSCF.',
            'nomination_mmscf.numeric' => 'Nomination harus berupa angka.',
            'nomination_mmscf.min' => 'Nomination tidak boleh negatif.',
            'nomination_mmscf.max' => 'Nomination maksimal 1,000 MMSCF.',
            'heating_value_btu.numeric' => 'Heating value harus berupa angka.',
            'heating_value_btu.min' => 'Heating value minimal 800 BTU.',
            'heating_value_btu.max' => 'Heating value maksimal 1,200 BTU.',
            'specific_gravity.numeric' => 'Specific gravity harus berupa angka.',
            'specific_gravity.min' => 'Specific gravity minimal 0.5.',
            'specific_gravity.max' => 'Specific gravity maksimal 1.0.',
            'h2s_content_ppm.numeric' => 'H2S content harus berupa angka.',
            'h2s_content_ppm.min' => 'H2S content tidak boleh negatif.',
            'h2s_content_ppm.max' => 'H2S content maksimal 10,000 PPM.',
            'co2_content_pct.numeric' => 'CO2 content harus berupa angka.',
            'co2_content_pct.min' => 'CO2 content tidak boleh negatif.',
            'co2_content_pct.max' => 'CO2 content maksimal 50%.',
            'moisture_content_ppm.numeric' => 'Moisture content harus berupa angka.',
            'moisture_content_ppm.min' => 'Moisture content tidak boleh negatif.',
            'moisture_content_ppm.max' => 'Moisture content maksimal 1,000 PPM.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validasi bahwa actual delivery tidak boleh melebihi nomination secara signifikan
            $actualDelivery = $this->input('actual_delivery_mmscf');
            $nomination = $this->input('nomination_mmscf');
            
            if ($actualDelivery && $nomination) {
                $variance = abs($actualDelivery - $nomination) / $nomination * 100;
                
                if ($variance > 20) { // Variance lebih dari 20%
                    $validator->errors()->add(
                        'actual_delivery_mmscf',
                        'Actual delivery terlalu jauh berbeda dari nomination (variance > 20%).'
                    );
                }
            }

            // Validasi heating value dalam range normal untuk gas alam
            $heatingValue = $this->input('heating_value_btu');
            if ($heatingValue && ($heatingValue < 950 || $heatingValue > 1050)) {
                $validator->errors()->add(
                    'heating_value_btu',
                    'Heating value di luar range normal untuk gas alam (950-1050 BTU).'
                );
            }

            // Validasi H2S content tidak boleh terlalu tinggi (safety concern)
            $h2sContent = $this->input('h2s_content_ppm');
            if ($h2sContent && $h2sContent > 4) {
                $validator->errors()->add(
                    'h2s_content_ppm',
                    'H2S content terlalu tinggi (> 4 PPM), berpotensi berbahaya.'
                );
            }

            // Validasi duplikasi data untuk gas buyer dan tanggal yang sama
            if ($this->input('gas_buyer_id') && $this->input('sales_date')) {
                $user = $this->user();
                if ($user && $user->vessel_id) {
                    $existingQuery = \App\Models\Production\GasSales::where('vessel_id', $user->vessel_id)
                        ->where('gas_buyer_id', $this->input('gas_buyer_id'))
                        ->whereDate('sales_date', $this->input('sales_date'));
                    
                    // Jika ini adalah update, exclude record yang sedang diupdate
                    if ($this->route('id')) {
                        $existingQuery->where('id', '!=', $this->route('id'));
                    }
                    
                    if ($existingQuery->exists()) {
                        $validator->errors()->add(
                            'sales_date',
                            'Data gas sales untuk buyer dan tanggal ini sudah ada.'
                        );
                    }
                }
            }
        });
    }
}