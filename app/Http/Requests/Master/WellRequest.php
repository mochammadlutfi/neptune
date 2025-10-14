<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WellRequest extends FormRequest
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
        $wellId = $this->route('id') ?? null;
        return [
            // Basic Information

            'vessel_id' => 'required|exists:vessels,id',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('wells', 'code')->ignore($wellId)
            ],
            'name' => 'required|string|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vessel_id.required' => 'Vessel is required.',
            'vessel_id.exists' => 'Selected vessel does not exist.',
            'code.required' => 'Well code is required.',
            'code.unique' => 'Well code already exists.',
            'code.max' => 'Well code cannot exceed 20 characters.',
            'name.required' => 'Well name is required.',
            'name.max' => 'Well name cannot exceed 100 characters.',
            'type.required' => 'Well type is required.',
            'type.in' => 'Invalid well type selected.',
            'status.required' => 'Well status is required.',
            'status.in' => 'Invalid well status selected.',
        ];
    }
}