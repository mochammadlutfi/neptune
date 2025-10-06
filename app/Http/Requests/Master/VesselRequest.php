<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VesselRequest extends FormRequest
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
        $vesselId = $this->route('id') ?? null;
        
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vessels', 'code')->ignore($vesselId)
            ],
            'name' => 'required|string|max:200',
            'type' => [
                'required',
                'string'
            ],
            'oim_id' => 'required|integer|exists:users,id',
            'client_name' => 'nullable|string|max:100',
            'client_oim' => 'nullable|string|max:100',
            'is_active' => 'required'
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode vessel wajib diisi.',
            'code.unique' => 'Kode vessel sudah digunakan.',
            'name.required' => 'Nama vessel wajib diisi.',
            'type.required' => 'Tipe vessel wajib dipilih.',
            'type.in' => 'Tipe vessel tidak valid.',
            'operator.required' => 'Nama operator wajib diisi.',
            'contract_id.exists' => 'Kontrak yang dipilih tidak valid.',
            'coordinates_lat.between' => 'Latitude harus antara -90 hingga 90.',
            'coordinates_lng.between' => 'Longitude harus antara -180 hingga 180.',
            'fabrication_year.min' => 'Tahun fabrikasi tidak valid.',
            'fabrication_year.max' => 'Tahun fabrikasi tidak valid.',
            'commissioning_date.after_or_equal' => 'Tanggal commissioning harus setelah atau sama dengan tanggal instalasi.',
            'status.required' => 'Status vessel wajib dipilih.',
            'status.in' => 'Status vessel tidak valid.',
        ];
    }

    /**
     * Validasi tambahan setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi koordinat - jika salah satu diisi, keduanya harus diisi
            $lat = $this->input('coordinates_lat');
            $lng = $this->input('coordinates_lng');
            
            if (($lat && !$lng) || (!$lat && $lng)) {
                $validator->errors()->add('coordinates_lat', 'Koordinat latitude dan longitude harus diisi keduanya atau tidak sama sekali.');
            }

            // Validasi tanggal instalasi dan commissioning
            $installationDate = $this->input('installation_date');
            $commissioningDate = $this->input('commissioning_date');
            
            if ($installationDate && $commissioningDate) {
                if (strtotime($commissioningDate) < strtotime($installationDate)) {
                    $validator->errors()->add('commissioning_date', 'Tanggal commissioning harus setelah atau sama dengan tanggal instalasi.');
                }
            }
        });
    }
}