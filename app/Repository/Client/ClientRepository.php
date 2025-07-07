<?php

namespace App\Repository\Client;

use App\Contracts\Client\IClient;
use App\Http\Resources\ClientResource;
use App\Paginate\GeneratePagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
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
                $new_data [] = new ClientResource($client);
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

    public function clientById(array $data) : array
    {
        try {
            $client = KeycloakAdmin::clients()->get($data['realm'], $data['client_id']);
            if(!$client) return [false, 'No se encontraron clientes.', null, 404];
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

            $client = ClientRepresentation::from($data);

            KeycloakAdmin::clients()->update($realm, $client_uuid, $client);


            $updated = KeycloakAdmin::clients()->get($realm, $client_uuid);

            return [true, 'Cliente actualizado correctamente.', new ClientResource($updated), 200];
        } catch (\Throwable $th) {
            dd($th);
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


    public function clientData(string $realm, array $data) : array
    {
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


}
