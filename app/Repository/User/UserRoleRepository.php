<?php

namespace App\Repository\User;

use App\Contracts\User\IUserRole;
use App\Http\Resources\ClientRolesResource;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class UserRoleRepository implements IUserRole
{

    protected $keycloakAdmin;
    public function __construct(KeycloakAdmin $keycloakAdmin)
    {
        $this->keycloakAdmin = $keycloakAdmin;
    }

    public function roles(array $data) : array
    {
        try {
            $roles = KeycloakAdmin::users()->retrieveClientRoles($data['realm'], $data['user_uuid'], $data['client_uuid']);
            if(count($roles) == 0 ) return [false, 'No se encontraron usuarios.', null, 404];
            foreach ($roles as $role) {
                $new_data [] = new ClientRolesResource($role);
            }
            return [true, 'Operación exitosa', $new_data, 200];
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error al obtener lista de usuarios: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, 500];
        }

    }
    public function addClientRole(array $data) : array
    {
        try {

            $role = KeycloakAdmin::clients()->clientRoleByName($data['realm'], $data['client_uuid'], $data['role_name']);
            $role_data [] = $role;
            $assign = KeycloakAdmin::users()->addClientRole($data['realm'], $data['user_uuid'], $data['client_uuid'], $role_data);
            return [true, 'Operación exitosa', null, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al intentar asignar rol: ' . $th->getMessage());
                return [false, 'Error al intentar asignar rol.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en el servidor al intentar asignar rol: ' . $th->getMessage());
            return [false, 'Error en el servidor al intentar asignar rol.', null, $status_code];
        }

    }
    public function removeClientRole(string $realm, string $user_uuid, string $client_uuid, string $role_name) : array
    {
        try {

            $role = KeycloakAdmin::clients()->clientRoleByName($realm, $client_uuid, $role_name);
            $role_data [] = $role;
            $assign = KeycloakAdmin::users()->removeClientRole($realm, $user_uuid, $client_uuid, $role_data);
            return [true, 'Operación exitosa', null, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al intentar quitar rol: ' . $th->getMessage());
                return [false, 'Error al intentar quitar rol.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en el servidor al intentar quitar rol: ' . $th->getMessage());
            return [false, 'Error en el servidor al intentar quitar rol.', null, $status_code];
        }

    }
}
