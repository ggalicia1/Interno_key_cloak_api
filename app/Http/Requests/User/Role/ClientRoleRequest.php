<?php

namespace App\Http\Requests\User\Role;

use Illuminate\Foundation\Http\FormRequest;

class ClientRoleRequest extends FormRequest
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
            'realm' => 'required|string',
            'user_uuid' => 'required|string',
            'client_uuid' => 'required|string',
            'role_name' => 'nullable|string'
        ];
    }
}
