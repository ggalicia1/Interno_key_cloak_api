<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserCredentialRequest extends FormRequest
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
            'realm' => 'required|string|max:100',
            'user_id' => 'required|string|max:255', // ajusta según tu estructura++
            'credentials' => 'nullable|array', // si quieres enviar credenciales
            'credentials.*.type' => 'required_with:credentials|string', // tipo como 'password'
            'credentials.*.value' => 'required_with:credentials|string|min:8', // valor de la contraseña
            'credentials.*.temporary' => 'boolean', // valor de la contraseña

        ];
    }
}
