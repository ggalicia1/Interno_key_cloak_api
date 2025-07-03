<?php

namespace App\Repository\Client;

use App\Contracts\Client\IClient;
use App\Http\Resources\ClientResource;
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
            $clients = KeycloakAdmin::clients()->all($realm, [
                'max' => $data['limit'],
                'first' => $data['offset']
            ]);
            if(count($clients) == 0) return [false, 'No se encontraron clientes.', null, 404];

            $new_data = [];
            foreach ($clients as $client) {
                $new_data [] = new ClientResource($client);
            }
            $response = [
                'limit' => $data['limit'],
                'offset' => $data['offset'],
                'date' => $new_data
            ];
            return [true, 'Operación exitosa', $response, 200];
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
        $clientId = $data['clientId'];
        unset($data['realm']);

        // Crear el cliente
        $clientRepresentation = ClientRepresentation::from($data);
        KeycloakAdmin::clients()->import($realm, $clientRepresentation);

        // Buscar el cliente recién creado usando su clientId
       /*  $clients = KeycloakAdmin::clients()->all($realm, [
            'clientId' => $clientId
        ]);

        if (empty($clients)) {
            return [false, 'El cliente fue creado, pero no se pudo recuperar.', null, 404];
        }

        $createdClient = $clients[0]; // Primer coincidencia */

        return [true, 'Cliente creado exitosamente.', null, 201];

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
                    $message = 'Reino no encontrado.';
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
            $clientUuid = $data['client_id'];

            unset($data['realm'], $data['client_id']);

            $client = ClientRepresentation::from($data);

            KeycloakAdmin::clients()->update($realm, $clientUuid, $client);

            // importante: usar clientUuid directamente
            $updated = KeycloakAdmin::clients()->get($realm, $clientUuid);

            return [true, 'Cliente actualizado correctamente.', new ClientResource($updated), 200];
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


}
