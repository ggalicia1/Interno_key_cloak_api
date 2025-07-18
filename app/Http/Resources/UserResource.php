<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                    'username' => $this->username,
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'email' => $this->email,
                    'email_verified' => $this->emailVerified,
                    'client_roles' => $this->clientRoles,
                    'client_consents' => $this->clientConsents,
                    'realm_roles' => $this->realmRoles,
                    'enabled' => $this->enabled,
                    'credentials' => $this->credentials,
                    'disableable_credential_types' => $this->disableableCredentialTypes,
                    'access' => $this->access,
                    'attributes' => $this->attributes,
                    'groups' => $this->groups,
                    'required_actions' => $this->requiredActions,
                    'federated_identities' => $this->federatedIdentities,
                    'federation_link' => $this->federationLink,
                    //'notBefore' => $this->notBefore,
                    'origin' => $this->origin,
                    //'self' => $this->self,
                    'service_account_client_id' => $this->serviceAccountClientId,
                    'totp' => $this->totp,
                    'created_timestamp' => $this->createdTimestamp,
        ];
    }
}
