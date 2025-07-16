<?php

namespace App\Repository\Realm;

use App\Contracts\Realm\IRealm;
use App\Http\Resources\RealmResource;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class RealmRepository implements IRealm
{
    protected $keycloakAdmin;
    public function __construct(KeycloakAdmin $keycloakAdmin)
    {
        $this->keycloakAdmin = $keycloakAdmin;
    }

    public function all() : array
    {
        try {
            $realms = KeycloakAdmin::realms()->all();
            if(count($realms) == 0) return [false, 'No se encontraron reinos.', null, 404];
            $new_realms = [];
            foreach ($realms as $realm) {
                $new_realms[] = new RealmResource($realm);
            }
            return [true, 'Operación exitosa', $new_realms, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener lista de reinos: ' . $th->getMessage());
                return [false, 'Error al obtener lista de reinos.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en servidor: ' . $th->getMessage());
            return [false, 'Error en el servidor', null, $status_code];

        }
    }
    public function keys(string $realm) : array
    {
        try {
            $realm_keys = KeycloakAdmin::realms()->keys($realm);
            if(!$realm_keys) return [false, 'No se encontraron keys.', null, 404];
            return [true, 'Operación exitosa', $realm_keys, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener keys del reino: ' . $th->getMessage());
                return [false, 'Error al obtener keys del reino.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en servidor: ' . $th->getMessage());
            return [false, 'Error en el servidor', null, $status_code];


        }
    }
    public function key(array $data) : array
    {
        try {
            list($status, $message, $realm_keys, $code) = $this->keys($data['realm']);
            if(!$status) return [$status, $message, $realm_keys, $code];
            $keys = $realm_keys->keys;
            $public_key_pem = [];
            foreach ($keys as $key) {
                if($key->algorithm == $data['algorithm']) {
                    $public_key_pem ['public_key_pem'] = "-----BEGIN PUBLIC KEY-----\n{$key->publicKey}\n-----END PUBLIC KEY-----";
                }
            }
            if(!$public_key_pem) return [false, 'No se encontró la keys.', null, 404];
            return [true, 'Operación exitosa', $public_key_pem, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al obtener keys del reino: ' . $th->getMessage());
                return [false, 'Error al obtener keys del reino.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en servidor: ' . $th->getMessage());
            return [false, 'Error en el servidor', null, $status_code];


        }
    }
    public function create(array $data) : array
    {
        try {
            $realm = KeycloakAdmin::realms()->import($data);
            return [true, 'Creación de reino exitosa', $realm, 200];
        } catch (\Throwable $th) {
            $status_code = $th->getCode();
            if($status_code != 500) {
                $response = ($th->getResponse());
                Log::error('Error al crear el reino: ' . $th->getMessage());
                return [false, 'Error al crear el reino.', json_decode($response->getBody()->getContents()), $status_code];
            }
            Log::error('Error en servidor: ' . $th->getMessage());
            return [false, 'Error en el servidor', null, $status_code];


        }
    }
}
