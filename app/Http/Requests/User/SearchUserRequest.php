<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
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
            'limit' => 'required|integer',
            'offset' => 'required|integer',
            'search' => 'required|string',
        ];
    }
}
