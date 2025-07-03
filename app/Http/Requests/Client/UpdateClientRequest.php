<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'client_id' => 'required|string', // UUID interno de Keycloak

            'clientId' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'enabled' => 'sometimes|boolean',
            'publicClient' => 'sometimes|boolean',
            'bearerOnly' => 'sometimes|boolean',
            'secret' => 'nullable|string|max:255',
            'rootUrl' => 'nullable|url',
            'baseUrl' => 'nullable|url',
            'adminUrl' => 'nullable|url',

            'redirectUris' => 'nullable|array',
            'redirectUris.*' => 'string',

            'webOrigins' => 'nullable|array',
            'webOrigins.*' => 'string',

            'protocol' => 'nullable|string|in:openid-connect,saml',
            'clientAuthenticatorType' => 'nullable|string',

            'consentRequired' => 'sometimes|boolean',
            'standardFlowEnabled' => 'sometimes|boolean',
            'implicitFlowEnabled' => 'sometimes|boolean',
            'directAccessGrantsEnabled' => 'sometimes|boolean',
            'serviceAccountsEnabled' => 'sometimes|boolean',
            'authorizationServicesEnabled' => 'sometimes|boolean',
            'frontchannelLogout' => 'sometimes|boolean',
            'fullScopeAllowed' => 'sometimes|boolean',

            'notBefore' => 'nullable|integer',
            'nodeReRegistrationTimeout' => 'nullable|integer',
            'origin' => 'nullable|string',

            'access' => 'nullable|array',
            'access.*' => 'boolean',

            'attributes' => 'nullable|array',
            'attributes.*' => 'string',

            'authenticationFlowBindingOverrides' => 'nullable|array',
            'authenticationFlowBindingOverrides.*' => 'string',

            'defaultClientScopes' => 'nullable|array',
            'defaultClientScopes.*' => 'string',

            'optionalClientScopes' => 'nullable|array',
            'optionalClientScopes.*' => 'string',

            'surrogateAuthRequired' => 'sometimes|boolean',

            // Nota: puedes extender aquí si trabajas con authorizationSettings o protocolMappers
        ];
    }
}
