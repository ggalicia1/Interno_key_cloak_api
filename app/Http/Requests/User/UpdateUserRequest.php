<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            //'realm' => 'required|string|max:100',
            //'username' => 'required|string|max:255',
            'email' => 'nullable|email',
            'email_verified' => 'nullable|boolean',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'enabled' => 'nullable|boolean',
            //'credentials' => 'nullable|array',
            //'credentials.*.type' => 'required_with:credentials|string',
            //'credentials.*.value' => 'required_with:credentials|string|min:8',
            //'credentials.*.temporary' => 'boolean',
            //'requiredActions' => 'nullable|array',
            //'attributes' => 'nullable|array',
            //'groups' => 'nullable|array',
        ];
    }
}
