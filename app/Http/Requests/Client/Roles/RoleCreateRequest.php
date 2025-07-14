<?php

namespace App\Http\Requests\Client\Roles;

use Illuminate\Foundation\Http\FormRequest;

class RoleCreateRequest extends FormRequest
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
            'client_uuid' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'attributes' => 'nullable|array',
        ];
    }
}
