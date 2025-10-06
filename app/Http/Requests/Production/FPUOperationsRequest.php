<?php

namespace App\Http\Requests\Production;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FPUOperationsRequest extends FormRequest
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
            'reading_date' => 'required|date',
            'reading_hour' => 'required|integer|min:0|max:23',
            'inlet_pressure_psi' => 'nullable|numeric|min:0|max:10000',
            'inlet_temp_f' => 'nullable|numeric|min:-50|max:500',
            'outlet_pressure_psi' => 'nullable|numeric|min:0|max:10000',
            'outlet_temp_f' => 'nullable|numeric|min:-50|max:500',
            'total_gas_rate_mmscfd' => 'nullable|numeric|min:0|max:1000',
            'fuel_gas_rate_mmscfd' => 'nullable|numeric|min:0|max:100',
            'flare_hp_rate_mmscfd' => 'nullable|numeric|min:0|max:100',
            'flare_lp_rate_mmscfd' => 'nullable|numeric|min:0|max:100',
            'process_data' => 'nullable|json',
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
            'reading_date.required' => 'Tanggal pembacaan wajib diisi.',
            'reading_date.date' => 'Format tanggal pembacaan tidak valid.',
            'reading_hour.required' => 'Jam pembacaan wajib diisi.',
            'reading_hour.integer' => 'Jam pembacaan harus berupa angka.',
            'reading_hour.min' => 'Jam pembacaan minimal 0.',
            'reading_hour.max' => 'Jam pembacaan maksimal 23.',
            'inlet_pressure_psi.numeric' => 'Tekanan inlet harus berupa angka.',
            'inlet_pressure_psi.min' => 'Tekanan inlet tidak boleh negatif.',
            'inlet_pressure_psi.max' => 'Tekanan inlet maksimal 10,000 PSI.',
            'inlet_temp_f.numeric' => 'Temperatur inlet harus berupa angka.',
            'inlet_temp_f.min' => 'Temperatur inlet minimal -50°F.',
            'inlet_temp_f.max' => 'Temperatur inlet maksimal 500°F.',
            'outlet_pressure_psi.numeric' => 'Tekanan outlet harus berupa angka.',
            'outlet_pressure_psi.min' => 'Tekanan outlet tidak boleh negatif.',
            'outlet_pressure_psi.max' => 'Tekanan outlet maksimal 10,000 PSI.',
            'outlet_temp_f.numeric' => 'Temperatur outlet harus berupa angka.',
            'outlet_temp_f.min' => 'Temperatur outlet minimal -50°F.',
            'outlet_temp_f.max' => 'Temperatur outlet maksimal 500°F.',
            'total_gas_rate_mmscfd.numeric' => 'Total gas rate harus berupa angka.',
            'total_gas_rate_mmscfd.min' => 'Total gas rate tidak boleh negatif.',
            'total_gas_rate_mmscfd.max' => 'Total gas rate maksimal 1,000 MMSCFD.',
            'fuel_gas_rate_mmscfd.numeric' => 'Fuel gas rate harus berupa angka.',
            'fuel_gas_rate_mmscfd.min' => 'Fuel gas rate tidak boleh negatif.',
            'fuel_gas_rate_mmscfd.max' => 'Fuel gas rate maksimal 100 MMSCFD.',
            'flare_hp_rate_mmscfd.numeric' => 'Flare HP rate harus berupa angka.',
            'flare_hp_rate_mmscfd.min' => 'Flare HP rate tidak boleh negatif.',
            'flare_hp_rate_mmscfd.max' => 'Flare HP rate maksimal 100 MMSCFD.',
            'flare_lp_rate_mmscfd.numeric' => 'Flare LP rate harus berupa angka.',
            'flare_lp_rate_mmscfd.min' => 'Flare LP rate tidak boleh negatif.',
            'flare_lp_rate_mmscfd.max' => 'Flare LP rate maksimal 100 MMSCFD.',
            'process_data.json' => 'Format process data tidak valid (harus JSON).',
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
            // Validasi bahwa total gas rate tidak boleh lebih kecil dari fuel + flare
            $totalGasRate = $this->input('total_gas_rate_mmscfd', 0);
            $fuelGasRate = $this->input('fuel_gas_rate_mmscfd', 0);
            $flareHpRate = $this->input('flare_hp_rate_mmscfd', 0);
            $flareLpRate = $this->input('flare_lp_rate_mmscfd', 0);
            
            $consumedGas = $fuelGasRate + $flareHpRate + $flareLpRate;
            
            if ($totalGasRate > 0 && $consumedGas > $totalGasRate) {
                $validator->errors()->add(
                    'total_gas_rate_mmscfd',
                    'Total gas rate tidak boleh lebih kecil dari jumlah fuel gas dan flare gas.'
                );
            }

            // Validasi bahwa outlet pressure tidak boleh lebih besar dari inlet pressure
            $inletPressure = $this->input('inlet_pressure_psi');
            $outletPressure = $this->input('outlet_pressure_psi');
            
            if ($inletPressure && $outletPressure && $outletPressure > $inletPressure) {
                $validator->errors()->add(
                    'outlet_pressure_psi',
                    'Tekanan outlet tidak boleh lebih besar dari tekanan inlet.'
                );
            }

            // Validasi duplikasi data untuk tanggal dan jam yang sama
            if ($this->input('reading_date') && $this->input('reading_hour') !== null) {
                $user = $this->user();
                if ($user && $user->vessel_id) {
                    $existingQuery = \App\Models\Production\FPUOperations::where('vessel_id', $user->vessel_id)
                        ->whereDate('reading_date', $this->input('reading_date'))
                        ->where('reading_hour', $this->input('reading_hour'));
                    
                    // Jika ini adalah update, exclude record yang sedang diupdate
                    if ($this->route('id')) {
                        $existingQuery->where('id', '!=', $this->route('id'));
                    }
                    
                    if ($existingQuery->exists()) {
                        $validator->errors()->add(
                            'reading_hour',
                            'Data FPU operations untuk tanggal dan jam ini sudah ada.'
                        );
                    }
                }
            }
        });
    }
}