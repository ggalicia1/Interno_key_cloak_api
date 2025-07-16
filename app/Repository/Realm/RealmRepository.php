<?php

namespace App\Repository\Realm;

use App\Contracts\Realm\IRealm;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class RealmRepository implements IRealm
{
    protected $keycloakAdmin;
    public function __construct(KeycloakAdmin $keycloakAdmin)
    {
        $this->keycloakAdmin = $keycloakAdmin;
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
}
