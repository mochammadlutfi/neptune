<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonnelPositionRequest extends FormRequest
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
        $personnelPositionId = $this->route('personnel_position') ? $this->route('personnel_position') : null;
        
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('personnel_positions', 'code')->ignore($personnelPositionId)
            ],
            'name' => 'required|string|max:200',
            'department' => [
                'required',
                'string',
                Rule::in(['Operations', 'Maintenance', 'HSE', 'Marine', 'Engineering', 'Administration', 'QA/QC'])
            ],
            'level' => [
                'required',
                'string',
                Rule::in(['Director', 'Manager', 'Supervisor', 'Staff', 'Operator', 'Technician'])
            ],
            'is_essential' => 'boolean',
            'is_minimum_manning' => 'boolean',
            'min_experience_years' => 'nullable|integer|min:0|max:50',
            'required_certificates' => 'nullable|array',
            'required_certificates.*' => 'string|max:100',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode posisi wajib diisi.',
            'code.unique' => 'Kode posisi sudah digunakan.',
            'name.required' => 'Nama posisi wajib diisi.',
            'department.required' => 'Departemen wajib dipilih.',
            'department.in' => 'Departemen tidak valid.',
            'level.required' => 'Level posisi wajib dipilih.',
            'level.in' => 'Level posisi tidak valid.',
            'min_experience_years.min' => 'Pengalaman minimum tidak boleh kurang dari 0.',
            'min_experience_years.max' => 'Pengalaman minimum tidak boleh lebih dari 50 tahun.',
            'required_certificates.array' => 'Sertifikat yang diperlukan harus berupa array.',
            'required_certificates.*.string' => 'Setiap sertifikat harus berupa teks.',
            'required_certificates.*.max' => 'Nama sertifikat tidak boleh lebih dari 100 karakter.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi jika is_minimum_manning true, maka is_essential juga harus true
            $isEssential = $this->input('is_essential', false);
            $isMinimumManning = $this->input('is_minimum_manning', false);
            
            if ($isMinimumManning && !$isEssential) {
                $validator->errors()->add('is_minimum_manning', 'Posisi minimum manning harus merupakan posisi essential.');
            }

            // Validasi required_certificates tidak kosong jika ada
            $requiredCertificates = $this->input('required_certificates', []);
            if (is_array($requiredCertificates)) {
                $filteredCertificates = array_filter($requiredCertificates);
                if (count($requiredCertificates) !== count($filteredCertificates)) {
                    $validator->errors()->add('required_certificates', 'Sertifikat tidak boleh kosong.');
                }
            }
        });
    }
}