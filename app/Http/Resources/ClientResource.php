<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'public_client' => $this->publicClient,
            'secret' => $this->secret,
            'client_authenticator_type' => $this->clientAuthenticatorType,
            'redirect_uris' => $this->redirectUris,
            'attributes' => $this->attributes,
            'base_url' => $this->baseUrl,
            'admin_url' => $this->adminUrl,
            'root_url' => $this->rootUrl,
            'web_origins' => $this->webOrigins,
            'full_scope_allowed' => $this->fullScopeAllowed,
            'default_client_scopes' => $this->defaultClientScopes,
            'optional_client_scopes' => $this->optionalClientScopes,
            'protocol' => $this->protocol,
            'service_accounts_enabled' => $this->serviceAccountsEnabled,
            'frontchannel_logout' => $this->frontchannelLogout,
        ];

        // Convertir las claves a snake_case
        /* return array_combine(
            array_map([self::class, 'camelCaseToSnakeCase'], array_keys($data)),
            array_values($data)
        ); */
    }

    public static function camelCaseToSnakeCase(string $input): string
    {
        $pattern = '/(?<=\w)([A-Z])/';
        $replacement = '_$1';
        $snake_case = strtolower(preg_replace($pattern, $replacement, $input));
        return $snake_case;
    }
}
