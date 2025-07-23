<?php

namespace App\Repository\Client;

use App\Contracts\Client\IClient;
use App\Enum\UnusedCustomers;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientRolesResource;
use App\Http\Resources\ClientsTypeCatalog;
use App\Paginate\GeneratePagination;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;
use Overtrue\Keycloak\Representation\Client as ClientRepresentation;

class ClientRepository implements IClient
{

    public function clients(array $data) : array
    {
        try {

            $realm = $data['realm'];
            unset($data['realm']);
            $pagination = GeneratePagination::pagination($data, null);

            $clients = KeycloakAdmin::clients()->all($realm, [
                'max' => $pagination->page_size,
                'first' => $pagination->page_index
            ]);
            if(count($clients) == 0) return [false, 'No se encontraron clientes.', null, 404];

            $new_data = [];
            foreach ($clients as $client) {
                 if(!UnusedCustomers::unusedCustomers($client->clientId)){
                    foreach ($client->attributes as $key => $value) {
                        if($key == 'post.logout.redirect.uris'){
                            $post_logout_redirect_uris = $value;
                        }
                    }
                    $client = $client->withAttributes([
                                                        'post_logout_redirect_uris' => $post_logout_redirect_uris
                                                    ]);

                    $new_data [] = new ClientResource($client);
                }
            }
            $pagination->data = $new_data;
            return [true, 'Operación exitosa', $pagination, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
                return [false, 'Error al obtener lista de clientes.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
            return [false, 'Error en el servidor no se pudo realizar la consulta.', null, $status_code];
        }
    }
    public function clientsTypeCatalog(string $realm): array
    {
        try {
            $clients = KeycloakAdmin::clients()->all($realm);
            if(count($clients) == 0) return [false, 'No se encontraron clientes.', null, 404];

            $new_data = [];
            foreach ($clients as $client) {
                if(!UnusedCustomers::unusedCustomers($client->clientId)){
                    $new_data [] = new ClientsTypeCatalog($client);
                }
            }
            return [true, 'Operación exitosa', $new_data, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
                return [false, 'Error al obtener lista de clientes.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
            return [false, 'Error en el servidor no se pudo realizar la consulta.', null, $status_code];
        }
    }

    public function clientById(array $data) : array
    {
        try {
            $client = KeycloakAdmin::clients()->get($data['realm'], $data['client_uuid']);
            if(!$client) return [false, 'No se encontraron clientes.', null, 404];
            $post_logout_redirect_uris = null;
            foreach ($client->attributes as $key => $value) {
                if($key == 'post.logout.redirect.uris'){
                    $post_logout_redirect_uris = $value;
                }
            }
            $client = $client->withAttributes([
                                                    'post_logout_redirect_uris' => $post_logout_redirect_uris
                                                ]);
            return [true, 'Operación exitosa', new ClientResource($client), 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
                return [false, 'Error al obtener lista de clientes.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
            return [false, 'Error en el servidor no se pudo realizar la consulta.', null, $status_code];
        }
    }

    public function createClient(array $data): array
    {
        try {
            $realm = $data['realm'];
            $client_data = $this->clientData($realm, $data);
            $clientRepresentation = ClientRepresentation::from($client_data);
            $client = KeycloakAdmin::clients()->import($realm, $clientRepresentation);
            return [true, 'Cliente creado exitosamente.', new ClientResource($client), 201];

        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            $message = 'Error inesperado al crear cliente.';
            $responseBody = null;

            if (method_exists($th, 'getResponse') && $th->getResponse()) {
                $response = $th->getResponse();
                $responseBody = json_decode($response->getBody()->getContents(), true);

                switch ($status_code) {
                    case 400:
                        $message = 'Datos inválidos enviados a Keycloak.';
                        break;
                    case 401:
                        $message = 'No autorizado. Verifica las credenciales de Keycloak.';
                        break;
                    case 403:
                        $message = 'Prohibido. No tienes permisos para crear clientes.';
                        break;
                    case 409:
                        $message = 'Conflicto. Ya existe un cliente con ese clientId.';
                        break;
                    case 404:
                        $message = 'Cliente no encontrado.';
                        break;
                }
            }

            Log::error("Error al crear cliente: {$th->getMessage()} - Código: {$status_code}");

            return [
                false,
                $message,
                $responseBody,
                ($status_code >= 400 && $status_code < 600) ? $status_code : 500
            ];
        }
    }



    public function updateClient(array $data): array
    {

        try {
            $realm = $data['realm'];
            $client_uuid = $data['client_uuid'];

            unset($data['realm'], $data['client_uuid']);

            $client = KeycloakAdmin::clients()->get($realm, $client_uuid);

            $client = $this->setClientData($data, $client);
            KeycloakAdmin::clients()->update($realm, $client_uuid, $client);
            //$updated = KeycloakAdmin::clients()->get($realm, $client_uuid);

            return [true, 'Cliente actualizado correctamente.', null, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error al actualizar cliente: ' . $th->getMessage());
                return [false, 'Error al actualizar cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al actualizar cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor al actualizar cliente.', null, $status_code];
        }
    }

    public function deleteClient(array $data) : array
    {
        try {
            KeycloakAdmin::clients()->delete($data['realm'], $data['client_uuid']);
            return [true, 'Operación exitosa', null, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al eliminar el cliente: ' . $th->getMessage());
                return [false, 'Error al eliminar el cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en el servidor al eliminar el cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor al eliminar el cliente.', null, $status_code];


        }
    }
    public function clientData(string $realm, array $data) : array
    {
        //cadena.replace("_", ".")
        if(isset($data['attributes']['post_logout_redirect_uris']))
        {
            foreach ($data['attributes'] as $key => $value){
                $new_key = str_replace("_", ".", $key);
                $data['attributes'][$new_key] = $value;
                unset($data['attributes'][$key]);
            }
        }
       return [
            'protocol' => $data['protocol'],
            'clientId' => $data['client_id'],
            'name' => $data['name'],
            'description' => $data['description'],

            'publicClient' => false,//$data['public_client'],
            'authorizationServicesEnabled' => false,//$data['authorization_services_enabled'],
            'serviceAccountsEnabled' => true, //$data['service_accounts_enabled'],
            'implicitFlowEnabled' => false,// $data['implicit_flow_enabled'],
            'directAccessGrantsEnabled' => false, //$data['direct_access_grants_enabled'],
            'standardFlowEnabled' => true,//$data['standard_Flow_enabled'],
            'frontchannelLogout' => true,//$data['frontchannel_logout']
            'alwaysDisplayInConsole' => false,//$data['always_display_in_console'],

            'rootUrl' => $data['root_url'] ?? null,
            'baseUrl' => $data['base_url'] ?? null,

            'redirectUris' => $data['redirect_uris'] ?? null,
            'webOrigins' => $data['web_origins'] ?? null,
            'attributes' => $data['attributes'] ?? null,
        ];
    }

    public function setClientData($data, ClientRepresentation $client) : ClientRepresentation
    {

        if(isset($data['attributes']['post_logout_redirect_uris']))
        {
            foreach ($data['attributes'] as $key => $value){
                $new_key = str_replace("_", ".", $key);
                $data['attributes'][$new_key] = $value;
                unset($data['attributes'][$key]);
            }
        }

        foreach($data as $key => $value){
            switch ($key) {
                case 'client_id':
                    $client = $client->withClientId($data['client_id']);
                    break;
                case 'name':
                    $client = $client->withName($data['name']);
                    break;
                case 'description':
                    $client = $client->withDescription($data['description']);
                    break;
                case 'protocol':
                    $client = $client->withProtocol($data['protocol']);
                    break;
                case 'public_client':
                    $client = $client->withPublicClient($data['public_client']);
                    break;
                case 'authorization_services_enabled':
                    $client = $client->withAuthorizationServicesEnabled($data['authorization_services_enabled']);
                    break;
                case 'service_accounts_enabled':
                    $client = $client->withServiceAccountsEnabled($data['service_accounts_enabled']);
                    break;
                case 'implicit_flow_enabled':
                    $client = $client->withImplicitFlowEnabled($data['implicit_flow_enabled']);
                    break;
                case 'direct_access_grants_enabled':
                    $client = $client->withDirectAccessGrantsEnabled($data['direct_access_grants_enabled']);
                    break;
                case 'standard_flow_enabled':
                    $client = $client->withStandardFlowEnabled($data['standard_flow_enabled']);
                    break;
                case 'frontchannel_logout':
                    $client = $client->withFrontchannelLogout($data['frontchannel_logout']);
                    break;
                case 'always_display_in_console':
                    $client = $client->withAlwaysDisplayInConsole($data['always_display_in_console']);
                    break;
                case 'enabled':
                    $client = $client->withEnabled($data['enabled']);
                    break;
                case 'consent_required':
                    $client = $client->withConsentRequired($data['consent_required']);
                    break;
                case 'full_scope_allowed':
                    $client = $client->withFullScopeAllowed($data['full_scope_allowed']);
                    break;
                case 'client_authenticator_type':
                    $client = $client->withClientAuthenticatorType($data['client_authenticator_type']);
                    break;
                case 'root_url':
                    $client = $client->withRootUrl($data['root_url']);
                    break;
                case 'base_url':
                    $client = $client->withBaseUrl($data['base_url']);
                    break;
                case 'admin_url':
                    $client = $client->withAdminUrl($data['admin_url']);
                    break;
                case 'origin':
                    $client = $client->withAdminUrl($data['origin']);
                    break;
                case 'redirect_uris':
                    $client = $client->withRedirectUris($data['redirect_uris']);
                    break;
                case 'web_origins':
                    $client = $client->withWebOrigins($data['web_origins']);
                    break;
                case 'attributes':
                    $client = $client->withAttributes($data['attributes']);
                    break;
                default:
                    break;
            }
        }
        return $client;
    }


}
