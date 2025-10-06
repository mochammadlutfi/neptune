<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyFormRequest extends FormRequest
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
        $companyId = $this->route('company'); // Get company ID from route parameters

        return [
            'name' => 'required|string|max:191',
            'code' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('res_companies')->ignore($companyId),
            ],
            'email' => 'required|string|email|max:191',
            'phone' => 'required|string|max:191',
            'address' => 'required|string',
        ];
    }
}
