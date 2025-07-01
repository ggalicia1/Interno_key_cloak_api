<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelKeycloakAdmin\Facades\KeycloakAdmin;

class KeyCloakController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Listar roles del reino",
     *     tags={"Roles"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del reino",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function roles(Request $request)
    {
        try {
            $realm = $request->realm ? $request->realm : 'master';

            $roles = KeycloakAdmin::roles()->all($realm);
            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $roles
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener lista de roles: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/role-by-name",
     *     summary="Obtiene rol por nombre del reino.",
     *     tags={"Roles"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="role_name",
     *         in="query",
     *         description="Id del client",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function roleByName(Request $request)
    {
        try {
            $realm = $request->realm ? $request->realm : 'master';
            $role_name = $request->role_name ? $request->role_name : null;

            if($realm == null || $role_name == null){
                return response()->json([
                'status' => false,
                'message' => 'Es necesario ambos parametros porfavor.',
                'data' => null
            ], 400);
            }

            $role = KeycloakAdmin::roles()->get($realm, $role_name);
            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $role
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener rol por nombre: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Listar clientes del reino.",
     *     tags={"Clients"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del reino",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function clients(Request $request)
    {
        try {
            $realm = $request->realm ? $request->realm : 'master';

            $clients = KeycloakAdmin::clients()->all($realm);
            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $clients
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener lista de clientes: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/clients/client-by-id",
     *     summary="Obtiene cliente por Id de un reino",
     *     tags={"Clients"},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         description="Id del client",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function clientById(Request $request)
    {
        try {
            $realm = $request->realm ? $request->realm : 'master';
            $client_id = $request->realm ? $request->client_id : null;

            if($realm == null || $client_id == null){
                return response()->json([
                'status' => false,
                'message' => 'Es necesario ambos parametros porfavor.',
                'data' => null
            ], 400);
            }

            $client = KeycloakAdmin::clients()->get($realm, $client_id);
            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $client
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener cliente por Id: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/realms",
     *     summary="Listar reinos de keycloak",
     *     tags={"Realms"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function realms(Request $request)
    {
        try {
            $realms = KeycloakAdmin::realms()->all();

            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $realms
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener lista de reinos: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/realms/realm-by-name",
     *     summary="Obtiene reino por nombre",
     *     tags={"Realms"},
     *      @OA\Parameter(
     *         name="realm_name",
     *         in="query",
     *         description="Id del client",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function realmByName(Request $request)
    {
        try {
            $realm_name = $request->realm_name ? $request->realm_name : null;
            $realm = KeycloakAdmin::realms()->get($realm_name);
            return response()->json([
                'status' => true,
                'message' => 'Operación exitosa',
                'data' => $realm
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            $status_code = $th->getCode();
            Log::error('Error al obtener reino por nombre: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en la operación.',
                'data' => null
            ], $status_code);
        }
    }
}
