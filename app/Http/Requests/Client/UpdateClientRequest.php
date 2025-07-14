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
            'client_uuid' => 'required|string', // UUID interno de Keycloak

            'client_id' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'enabled' => 'sometimes|boolean',
            'public_client' => 'sometimes|boolean',
            'bearer_only' => 'sometimes|boolean',
            'secret' => 'nullable|string|max:255',
            'root_url' => 'nullable|url',
            'base_url' => 'nullable|url',
            'admin_url' => 'nullable|url',

            'redirect_uris' => 'nullable|array',
            'redirect_uris.*' => 'string',

            'web_origins' => 'nullable|array',
            'web_origins.*' => 'string',

            'protocol' => 'nullable|string|in:openid-connect,saml',
            'client_authenticator_type' => 'nullable|string',

            'consent_required' => 'sometimes|boolean',
            'standard_flow_enabled' => 'sometimes|boolean',
            'implicit_flow_enabled' => 'sometimes|boolean',
            'direct_access_grants_enabled' => 'sometimes|boolean',
            'service_accounts_enabled' => 'sometimes|boolean',
            'authorization_services_enabled' => 'sometimes|boolean',
            'frontchannel_logout' => 'sometimes|boolean',
            'full_scope_allowed' => 'sometimes|boolean',

            'not_before' => 'nullable|integer',
            'node_re_registration_timeout' => 'nullable|integer',
            'origin' => 'nullable|string',

            'access' => 'nullable|array',
            'access.*' => 'boolean',

            'attributes' => 'nullable|array',
            /* 'attributes.*.saml_idp_initiated_sso_url_name' => 'string',
            'attributes.*.standard_token_exchange_enabled' => 'string',
            'attributes.*.oauth2_device_authorization_grant_enabled' => 'string',
            'attributes.*.oidc_ciba_grant_enabled' => 'string',
            'attributes.*.post_logout_redirect_uris' => 'string', */

            'authentication_flow_binding_overrides' => 'nullable|array',
            'authentication_flow_binding_overrides.*' => 'string',

            'default_client_scopes' => 'nullable|array',
            'default_client_scopes.*' => 'string',

            'optional_client_scopes' => 'nullable|array',
            'optional_client_scopes.*' => 'string',

            'surrogate_auth_aequired' => 'sometimes|boolean',

            // Nota: puedes extender aquí si trabajas con authorizationSettings o protocolMappers
        ];
    }
}
