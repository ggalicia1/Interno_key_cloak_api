<?php

namespace App\Repository\User;

use App\Contracts\User\IUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;
use Overtrue\Keycloak\Collection\CredentialCollection;
use Overtrue\Keycloak\Representation\Credential;
use Overtrue\Keycloak\Representation\User as UserRepresentation;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;


class UserRepository implements IUser
{
    /**
     * Create a new class instance.
     */
    protected $keycloakAdmin;
    public function __construct(keycloakAdmin $keycloakAdmin)
    {
        $this->keycloakAdmin = $keycloakAdmin;
    }

    public function users(array $request) : array
    {
        try {
            $realm = $request['realm'] ?? 'Interno';
            $limit = isset($request['limit']) ? (int)$request['limit'] : 10;
            $offset = isset($request['offset']) ? (int)$request['offset'] : 0;


            $total = KeycloakAdmin::users()->count($realm, ['enabled' => true]);
            $users = KeycloakAdmin::users()->all($realm, [
                                                            'max' => $limit,
                                                            'first' => $offset,
                                                            'enabled' => true,
                                                        ]);
            if(count($users) == 0 ) return [false, 'No se encontraron usuarios.', null, 404];

            $data = [];
            foreach ($users as $user) {
                $data [] = new UserResource($user);
            }
            $response = [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
                'date' => $users
            ];
            return [true, 'Operación exitosa', $response, 200];
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error al obtener lista de usuarios: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, 500];
        }
    }

     public function userById(array $data) : array
    {
        try {
            $user = KeycloakAdmin::users()->get($data['realm'], $data['user_id']);
            return [true, 'Operación exitosa', new UserResource($user), 200];
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener usuario por Id: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        }
    }
    public function create(array $data) : array
    {
        try {
            $realm = $data['realm'];
            $user_representation = $this->setInformationUser($data);
            $user = KeycloakAdmin::users()->create($realm, $user_representation);
            if($user) return [true, 'Operación exitosa', null, 200];
            return [false, 'Algo salio mal, intente mas tarde.', null, 400];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al crear el usuario: ' . $th->getMessage());
                return [false, 'Error al crear el usuario.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en el servidor no se pudo crear el usuario: ' . $th->getMessage());
            return [false, 'Error en el servidor no se pudo crear el usuario.', null, $status_code];
        }
    }
    public function update(string $realm, string $user_id, array $data) : array
    {
        try {
            $user_representation = KeycloakAdmin::users()->get($realm, $user_id);

            $user_representation = $this->setInformationUser($data, $user_representation);
            $user = KeycloakAdmin::users()->update($realm, $user_id, $user_representation);
            if($user) return [true, 'Operación exitosa', null, 200];
            return [false, 'Algo salio mal, intente mas tarde.', null, 400];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al actualizar el usuario: ' . $th->getMessage());
                return [false, 'Error al actualizar el usuario.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en el servidor no se pudo actualizar el usuario: ' . $th->getMessage());
            return [false, 'Error en el servidor no se pudo actualizar el usuario.', null, $status_code];
        }
    }
    public function userCredential(string $user_id, array $data) : array
    {
        try {
            $realm = $data['realm'];
            $user_representation = KeycloakAdmin::users()->get($realm, $user_id);
            $user_representation = $this->setInformationUser($data, $user_representation);
            $user = KeycloakAdmin::users()->update($realm, $user_id, $user_representation);
            if($user)return [true, 'Operación exitosa', null, 200];
            return [false, 'Algo salio mal, intente mas tarde.', null, 400];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al crear la contraseña: ' . $th->getMessage());
                return [false, 'Error al crear la contraseña.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al crear la contraseña: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        }
    }

    public function search(array $data): array
    {
        try {
            $realm = $data['realm'] ? $data['realm'] : 'Interno';
            if(isset($data['limit'])){
                $limit = $data['limit'];
                $offset = $data['offset'];
            }else{
                $limit = 10; // cantidad de usuarios por página
                $offset = 0; // desplazamiento inicial
            }

            $users = KeycloakAdmin::users()->search($realm, [
                                                            'max' => $limit,
                                                            'first' => $offset,
                                                            'enabled' => true,
                                                            'search' => $data['search'],

                                                            ]);

            if(count($users) == 0 ) return [false, 'No se encontraron usuarios.', null, 404];
            $new_date = [];
            foreach ($users as $user) {
                $new_data = new UserResource($user);
            }
            $response = [
                'limit' => $limit,
                'offset' => $offset,
                'date' => $new_data
            ];
            return [true, 'Operación exitosa', $response, 200];
        } catch (\Exception $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {

                $response = ($th->getResponse());
                Log::error('Error al realizar la busqueda: ' . $th->getMessage());
                return [false, 'Error al realizar la busqueda.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al realizar la busqueda: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        }
    }
    public function retrieveRealmRoles(array $data) : array
    {
        try {
            $realm = $data['realm'] ? $data['realm'] : 'Interno';
            $user = KeycloakAdmin::users()->retrieveRealmRoles($realm, $data['user_id']);
            if(count($user) == 0) return [false, 'No se encontraron roles para este usuario.', null, 404];
            return [true, 'Operación exitosa', $user, 200];
        } catch (\Exception $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error intentar recuperar los roles: ' . $th->getMessage());
                return [false, 'Error intentar recuperar los roles.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error intentar recuperar los roles: ' . $th->getMessage());
            return [false, 'Error intentar recuperar los roles.', null, $status_code];
        }
    }
    public function joinGroup(string $realm, string $user_id, string $group_id) : array
    {
        try {

            $user = KeycloakAdmin::users()->joinGroup($realm, $user_id, $group_id);
            return [true, 'Operación exitosa', new UserResource($user), 200];
        } catch (\Exception $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al intentar agregar a grupo: ' . $th->getMessage());
                return [false, 'Error al intentar agregar a grupo.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al intentar agregar a grupo: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        };
    }
    public function retrieveGroups(string $realm, string $user_id, array $criteria) : array
    {
        try {

            $groups = KeycloakAdmin::users()->retrieveGroups($realm, $user_id, $criteria);
            if(count($groups) == 0) return [false, 'No se encontraron resultados.', null, 404];
            return [true, 'Operación exitosa', $groups, 200];
        } catch (\Exception $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al realizar la busqueda de grupos para este usuario.: ' . $th->getMessage());
                return [false, 'Error en la busqueda.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error la busqueda: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        }
    }
    public function leaveGroup(string $realm, string $user_id, string $group_id) : array
    {
        try {

            $user = KeycloakAdmin::users()->leaveGroup($realm, $user_id, $group_id);
            return [true, 'Operación exitosa', $user, 200];
        } catch (\Exception $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al realizar la busqueda: ' . $th->getMessage());
                return [false, 'Error al realizar la busqueda.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error al crear la contraseña: ' . $th->getMessage());
            return [false, 'Error en la operación.', null, $status_code];
        }
        return [true, 'Operación exitosa.', null, 200];
    }

    public function setInformationUser(array $data, UserRepresentation $user_representation) : UserRepresentation
    {

        foreach ($data as $key => $value) {
                switch ($key) {
                    case 'username':
                        $user_representation = $user_representation->withUsername($value);
                        break;
                    case 'email':
                        $user_representation = $user_representation->withEmail($value);
                        break;
                    case 'email_verified':
                        $user_representation = $user_representation->withEmailVerified($value);
                        break;
                    case 'first_name':
                        $user_representation = $user_representation->withFirstName($value);
                        break;
                    case 'last_name':
                        $user_representation = $user_representation->withLastName($value);
                        break;
                    case 'enabled':
                        $user_representation = $user_representation->withEnabled($value);
                        break;
                    case 'required_actions':
                        $user_representation = $user_representation->withRequiredActions($value);
                        break;
                    case 'credentials':
                        $credentials = [];
                        foreach ($data['credentials'] as $credential) {
                            $new_credential = new Credential();
                            $new_credential = $new_credential->withType($credential['type']);
                            $new_credential = $new_credential->withValue($credential['value']);
                            $new_credential = $new_credential->withTemporary($credential['temporary']);
                            $credentials[] = $new_credential;
                        }
                        $credentials = new CredentialCollection($credentials);

                        if(!empty($credentials)) {
                            $user_representation = $user_representation->withCredentials($credentials);
                        }
                        break;
                    case 'attributes':
                        if(!empty($credentials)) {
                            $user_representation = $user_representation->withAttributes($credentials);
                        }
                        break;
                    case 'groups':
                        if(!empty($credentials)) {
                            $user_representation = $user_representation->withGroups($credentials);
                        }
                        break;
                    default:
                        break;
                }
            }
        return $user_representation;
    }
}
