<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
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

            'protocol' => 'required|string|in:openid-connect',
            'client_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',

            //'public_client' => 'required|boolean',
            //'authorization_services_enabled' => 'required|boolean',
            //'service_accounts_enabled' => 'required|boolean',
            //'implicit_flow_enabled' => 'required|boolean',
            //'direct_access_grants_enabled' => 'required|boolean',
            //'standard_Flow_enabled' => 'required|boolean',
            //'frontchannel_logout' => 'required|boolean',
            //'always_display_in_console' => 'required|boolean',

            'root_url' => 'required|url',
            'base_url' => 'required|url',

            'redirect_uris' => 'required|array',
            'redirect_uris.*' => 'string|url',

            'web_origins' => 'required|array',
            'web_origins.*' => 'string|url',

            'attributes' => 'required|array',
            //'attributes.saml_idp_initiated_sso_url_name' => 'nullable|string',
            //'attributes.standard.token.exchange.enabled' => 'required|boolean',
            //'attributes.oauth2.device.authorization.grant.enabled' => 'required|boolean',
            //'attributes.oidc.ciba.grant.enabled' => 'required|boolean',
            'attributes.post_logout_redirect_uris' => 'required|string|url'








            /* 'realm' => 'required|string',
            'clientId' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'enabled' => 'boolean',
            'publicClient' => 'boolean',
            'bearerOnly' => 'boolean',
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
            'consentRequired' => 'boolean',
            'standardFlowEnabled' => 'boolean',
            'implicitFlowEnabled' => 'boolean',
            'directAccessGrantsEnabled' => 'boolean',
            'serviceAccountsEnabled' => 'boolean',
            'authorizationServicesEnabled' => 'boolean',
            'frontchannelLogout' => 'boolean',
            'fullScopeAllowed' => 'boolean',
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
            'surrogateAuthRequired' => 'boolean', */
        ];
    }
}
