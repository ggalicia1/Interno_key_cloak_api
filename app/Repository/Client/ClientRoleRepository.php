<?php

namespace App\Repository\Client;

use App\Contracts\Client\IClientRoles;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;
use App\Http\Resources\ClientRolesResource;
use App\Paginate\GeneratePagination;
use Illuminate\Support\Facades\Log;

class ClientRoleRepository implements IClientRoles
{

    public function clientRoles(array $data): array
    {
        try {
            if(isset($data['page_size']) && isset($data['page'])){
                $result = GeneratePagination::pagination($data, null);
                $roles = KeycloakAdmin::clients()->getClientRoles($data['realm'], $data['client_uuid'], [
                    'max' => $result->page_size,
                    'first' => $result->page_index
                ]);
            }else{
                $roles = KeycloakAdmin::clients()->getClientRoles($data['realm'], $data['client_uuid']);
            }


            if(count($roles) == 0) return [false, 'No se encontraron roles para este cliente.', null, 404];

            $new_data = array();
            foreach ($roles as $role) {
                $new_data [] = new ClientRolesResource($role);
            }

            if(isset($data['page_size']) && isset($data['page'])){
                $result->data = $new_data;
            }else{
                $result = $new_data;
            }

            return [true, 'Roles recuperados exitosamente.', $result, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error obtener los roles del cliente: ' . $th->getMessage());
                return [false, 'Error obtener los roles del cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error obtener los roles del  cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor.', null, $status_code];
        }
    }

    public function clientRoleByName(array $data) : array
    {
        try {

            $role = KeycloakAdmin::clients()->clientRoleByName($data['realm'], $data['client_uuid'], $data['role_name']);

            if(!$role) return [false, 'No se encontró el rol.', null, 404];
            return [true, 'Roles recuperados exitosamente.', new ClientRolesResource($role), 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error obtener el rol del cliente: ' . $th->getMessage());
                return [false, 'Error obtener el rol del cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error obtener el rol del  cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor.', null, $status_code];
        }
    }


    public function createClientRole(array $data) : array
    {
        try {
            $realm = $data['realm'];
            $client_uuid = $data['client_uuid'];
            unset($data['realm']);
            unset($data['client_uuid']);
            $role = KeycloakAdmin::clients()->createClientRole($realm, $client_uuid, $data);
            if(!$role) return [false, 'No se pudo crear el rol.', null, 400];
            return [true, 'Rol creado exitosamente.', new ClientRolesResource($role), 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error al intentar crear el rol del cliente: ' . $th->getMessage());
                return [false, 'Error al intentar crear el rol del cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al intentar crear el rol del  cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor.', null, $status_code];
        }

    }
    public function updateClientRole(array $data) : array
    {
        try {
            $realm = $data['realm'];
            $client_uuid = $data['client_uuid'];
                        $role = KeycloakAdmin::clients()->clientRoleByName($data['realm'], $data['client_uuid'], $data['name']);
            if(!$role) return [false, 'No se encontro el role.', null, 404];
            unset($data['realm']);
            unset($data['client_uuid']);
            foreach ($data as $key => $value) {
                switch($key){
                    case 'name':
                        $role = $role->withName($data[$key]);
                    break;
                    case 'description':
                        $role = $role->withDescription($data[$key]);
                    break;
                    case 'attributes':
                        $role = $role->withAttributes($data[$key]);
                    break;
                    default:
                    break;
                }
            }
            $role = KeycloakAdmin::clients()->updateClientRole($realm, $client_uuid, $data);
            if(!$role) return [false, 'No se pudo actualizar el rol.', null, 400];
            return [true, 'Rol actualizado exitosamente.', new ClientRolesResource($role), 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error al intentar actualizar el rol del cliente: ' . $th->getMessage());
                return [false, 'Error al intentar actualizar el rol del cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al intentar actualizar el rol del  cliente: ' . $th->getMessage());
            return [false, 'Error en el servidor.', null, $status_code];
        }

    }

    public function deleteClientRole(array $data) : array
    {
        try {
            KeycloakAdmin::clients()->deleteClientRole($data['realm'], $data['client_uuid'], $data['role_name']);
            return [true, 'Roles eliminado exitosamente.', null, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if ($status_code != 500 && method_exists($th, 'getResponse')) {
                $response = ($th->getResponse());
                Log::error('Error al eliminar el rol del cliente: ' . $th->getMessage());
                return [false, 'Error al eliminar el rol del cliente.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error de servidor: ' . $th->getMessage());
            return [false, 'Error en el servidor.', null, $status_code];
        }
    }

}
