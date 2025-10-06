<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
        $userId = $this->route('id'); // Get user ID from route parameters

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,id',
            'password' => $userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'password_confirmation' => $userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'vessel_id' => 'nullable',
            'vessel_list' => 'nullable',
        ];
    }
}
