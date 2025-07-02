<?php

namespace App\Repository\Client;

use App\Contracts\Client\IClient;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;

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
}
