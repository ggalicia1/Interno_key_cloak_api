<?php

namespace Database\Seeders;

use App\Contracts\Client\IClient;
use App\Contracts\Realm\IRealm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RealmSeeder extends Seeder
{
    protected IRealm $realm_repository;
    protected IClient $client_repository;
    public function __construct(IRealm $realm_repository, IClient $client_repository) {
        $this->realm_repository = $realm_repository;
        $this->client_repository = $client_repository;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $realms = [
                [
                    'realm' => 'Interno',
                    'enabled' => true,
                ],
                [
                    'realm' => 'Externo',
                    'enabled' => true,
                ],
            ];

        $clients = [
            [
                "client_id"=> "sso-keycloak-client",
                "name"=> "SSO keycloak client",
                "description"=> "Cliente para gestionar los usuarios.",
            ],
            [
                "client_id"=> "igt-keycloak-client",
                "name"=> "igt-client",
                "description"=> "Cliente de prueba",
            ],
        ];

            foreach ($realms as $realm){
                list($status, $message, $result, $code) = $this->realm_repository->create($realm);
                if(!$status){
                    Log::error('Error al ejecutar el seeder de realms: ' . $message);
                }else{
                    foreach ($clients as $client) {
                        $client['realm'] = $result->realm;
                        $client['protocol'] = 'openid-connect';
                        list($status, $message, $client_response, $code) = $this->client_repository->createClient($client);
                        if(!$status){
                            Log::error('Error al ejecutar el seeder para crear clientes: ' . $message);
                        }
                    }
                    Log::info('Se creo correctamente el realm: ' . $realm['realm'] . ' con UUI: ' . $result->id);
                }
            }
    }
}
