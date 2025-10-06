<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
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
        $contractId = $this->route('contract') ? $this->route('contract')->id : null;
        
        return [
            'contract_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('contracts', 'contract_number')->ignore($contractId)
            ],
            'contract_name' => 'required|string|max:200',
            'contract_type' => [
                'required',
                'string',
                Rule::in(['PSC', 'Cost Recovery', 'Gross Split', 'Service Contract', 'Joint Operation'])
            ],
            'operator_name' => 'required|string|max:100',
            'kkks_representative' => 'nullable|string|max:100',
            'partner_companies' => 'nullable|array',
            'partner_companies.*' => 'string|max:100',
            'effective_date' => 'required|date',
            'expiry_date' => 'required|date|after:effective_date',
            'extension_options' => 'nullable|string',
            'field_name' => 'nullable|string|max:100',
            'block_name' => 'nullable|string|max:100',
            'working_area_km2' => 'nullable|numeric|min:0|max:999999.99',
            'cost_recovery_limit_pct' => 'nullable|numeric|min:0|max:100',
            'ftp_share_pct' => 'nullable|numeric|min:0|max:100',
            'contractor_share_oil_pct' => 'nullable|numeric|min:0|max:100',
            'contractor_share_gas_pct' => 'nullable|numeric|min:0|max:100',
            'government_share_oil_pct' => 'nullable|numeric|min:0|max:100',
            'government_share_gas_pct' => 'nullable|numeric|min:0|max:100',
            'minimum_work_commitment' => 'nullable|string',
            'minimum_expenditure_usd' => 'nullable|numeric|min:0',
            'local_content_requirement_pct' => 'nullable|numeric|min:0|max:100',
            'performance_bond_amount_usd' => 'nullable|numeric|min:0',
            'bond_expiry_date' => 'nullable|date',
            'contract_status' => [
                'required',
                'string',
                Rule::in(['Active', 'Suspended', 'Force Majeure', 'Expired', 'Terminated'])
            ],
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'contract_number.required' => 'Nomor kontrak wajib diisi.',
            'contract_number.unique' => 'Nomor kontrak sudah digunakan.',
            'contract_name.required' => 'Nama kontrak wajib diisi.',
            'contract_type.required' => 'Tipe kontrak wajib dipilih.',
            'contract_type.in' => 'Tipe kontrak tidak valid.',
            'operator_name.required' => 'Nama operator wajib diisi.',
            'effective_date.required' => 'Tanggal efektif wajib diisi.',
            'expiry_date.required' => 'Tanggal berakhir wajib diisi.',
            'expiry_date.after' => 'Tanggal berakhir harus setelah tanggal efektif.',
            'contract_status.required' => 'Status kontrak wajib dipilih.',
            'contract_status.in' => 'Status kontrak tidak valid.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi total persentase oil share
            $contractorOil = $this->input('contractor_share_oil_pct', 0);
            $governmentOil = $this->input('government_share_oil_pct', 0);
            
            if ($contractorOil + $governmentOil != 100 && ($contractorOil > 0 || $governmentOil > 0)) {
                $validator->errors()->add('contractor_share_oil_pct', 'Total persentase oil share harus 100%.');
            }

            // Validasi total persentase gas share
            $contractorGas = $this->input('contractor_share_gas_pct', 0);
            $governmentGas = $this->input('government_share_gas_pct', 0);
            
            if ($contractorGas + $governmentGas != 100 && ($contractorGas > 0 || $governmentGas > 0)) {
                $validator->errors()->add('contractor_share_gas_pct', 'Total persentase gas share harus 100%.');
            }
        });
    }
}