<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SequenceFormRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendapatkan validation rules yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prefix' => 'nullable|string|max:10',
            'date_format' => [
                'nullable',
                'string',
                Rule::in(['', 'Y', 'Ym', 'Ymd'])
            ],
            'padding' => 'required|integer|min:1|max:10',
            'reset_period' => [
                'required',
                'string',
                Rule::in(['never', 'daily', 'monthly', 'yearly'])
            ]
        ];
    }

    /**
     * Mendapatkan pesan error kustom untuk validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prefix.max' => 'Prefix tidak boleh lebih dari 10 karakter.',
            'date_format.in' => 'Format tanggal harus salah satu dari: kosong, Y, Ym, atau Ymd.',
            'padding.required' => 'Padding wajib diisi.',
            'padding.integer' => 'Padding harus berupa angka.',
            'padding.min' => 'Padding minimal 1.',
            'padding.max' => 'Padding maksimal 10.',
            'reset_period.required' => 'Periode reset wajib diisi.',
            'reset_period.in' => 'Periode reset harus salah satu dari: never, daily, monthly, atau yearly.'
        ];
    }

    /**
     * Mendapatkan nama atribut kustom untuk validation error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'prefix' => 'prefix',
            'date_format' => 'format tanggal',
            'padding' => 'padding',
            'reset_period' => 'periode reset'
        ];
    }
}