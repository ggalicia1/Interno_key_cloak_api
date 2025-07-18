<?php

namespace App\Http\Middleware;

use App\Contracts\Realm\IRealm;
use App\Repository\Realm\RealmRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtKeyCloakMiddleware
{
    protected IRealm $realm_repository;
    public function __construct(IRealm $realm_repository)
    {
        $this->realm_repository= $realm_repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $realm = $request->realm ?? config('keycloak-admin.keycloack_realm_default');

        if(!$token || strpos($token, ' ') !== false){
            return Response()->json([
                'status'   => false,
                'message' => 'No autorizado.',
                'error' => 'Token invalido.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {

/*            list($status, $message, $realm_keys, $code ) = $this->realm_repository->keys($realm);
            if(!$status){
                return response()->json([
                    'status'   => false,
                    'message' => $message,
                    'error' => $realm_keys ?? 'No autorizado.',
                ],
                Response::HTTP_UNAUTHORIZED);
            }

            if (!$realm_keys) {
                return response()->json([
                    'status'   => false,
                    'message' => 'No autorizado.',
                    'error' => "No autorizado, la clave secreta no coincide con la firma"
                ],
                Response::HTTP_UNAUTHORIZED);
            }
            $keys = $realm_keys->keys;
            (string) $public_key_pem = '';
            foreach ($keys as $key) {
                if($key->algorithm == 'RS256') {
                    $public_key_pem = "-----BEGIN PUBLIC KEY-----\n{$key->publicKey}\n-----END PUBLIC KEY-----";*/
		      $public_key_pem = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnLbm0eBtkegTJqWTnPeGKQ6/+Tjr99cH455NGHyRIK0tYuh2Y0Y8U7m0923eTqUwfJvCNp1OZVdWADJFhUc0xcQeSMR0lAowx14e8KxEHU+zuYbBFPDltBA8FYti3mn5HskGD6xOh23FTgIEoEbrecYrnHm2jD/qED1AREeUTZRf/MHoUhZ0fBAz6XNBEUXL7PFmkTxHoBKVO298lHtwLYJbOdQWLprj9GJLcADa20prAirQMaYfhGZfvtmXlOno6GoK4DYnspiDx9wM/ZFumUgQIV9faGyHaodE9fCrcTClhcuxrHmXJ/Ws38i8WiGqXpZbPYILHdoOwXG+LwD9ewIDAQAB
-----END PUBLIC KEY-----";
    /*
            }
            }
*/
            if(!$public_key_pem) {
                return response()->json([
                    'status'   => false,
                    'message' => 'No autorizado.',
                    'error' => "No autorizado, la clave secreta no coincide con la firma"
                ],
                Response::HTTP_UNAUTHORIZED);
            }

            $jwt = JWT::decode($token, new Key($public_key_pem, 'RS256'));
            $jwt_decoded = json_decode(json_encode($jwt), true);
            $user = array();
            $user['client_id'] = $jwt_decoded['azp'];
            $user['user_uuid'] = $jwt_decoded['sub'];
            $user['username'] = $jwt_decoded['preferred_username'];
            $user['name'] = $jwt_decoded['name'];
            $user['email'] = $jwt_decoded['email'];
            $user['email_verified'] = $jwt_decoded['email_verified'];
            $user['resource_access'] = $jwt_decoded['resource_access'] ?? null;
            session()->put('user', $user);
            return $next($request);
        } catch (\Exception $th) {
            session()->forget('user');
            $code = $th->getCode();
            if($code != 500){
                Log::error('Error al validar token: ' . $th->getMessage());
                return response()->json([
                    'status'   => false,
                    'message' => 'No autorizado.',
                    'error' => $th->getMessage()
                ], ($code == 0) ? 500 : $code);
            }
            Log::error('Error al validar token: ' . $th->getMessage());
            return response()->json([
                'status'   => false,
                'message' => 'No autorizado.',
                'error' => 'Token invalido.'
            ], Response::HTTP_UNAUTHORIZED);
        }

    }
}
