<?php

namespace App\Http\Controllers;

use App\Contracts\Client\IClient;
use App\Http\Requests\Client\ClientByIdRequest;
use App\Http\Requests\Client\ClientRequest;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    protected $client_repository;
    public function __construct(IClient $client_repository) {
        $this->client_repository = $client_repository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Listar clientes del reino.",
     *     tags={"Clients"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Nombre del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Tamaño del paginado",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numero de Pagina.",
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
    public function clients(ClientRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->clients($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/clients/{realm}/catalog",
     *     summary="Listar clientes del reino.",
     *     tags={"Clients"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="path",
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
    public function clientsTypeCatalog(string $realm) : JsonResponse
    {
        list($status, $message, $response, $code) = $this->client_repository->clientsTypeCatalog($realm);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/clients/client-by-id",
     *     summary="Obtiene cliente por Id de un reino",
     *     tags={"Clients"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_uuid",
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
    public function clientById(ClientByIdRequest $request)
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->clientById($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);

    }

    /**
     * @OA\Post(
     *     path="/api/v1/clients/create",
     *     summary="Crear un nuevo cliente en el reino especificado",
     *     tags={"Clients"},
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"realm", "protocol", "clientId", "name", "publicClient", "authorizationServicesEnabled", "serviceAccountsEnabled", "implicitFlowEnabled", "directAccessGrantsEnabled", "standardFlowEnabled", "frontchannelLogout", "alwaysDisplayInConsole", "rootUrl", "baseUrl", "redirectUris", "webOrigins", "attributes"},
     *             @OA\Property(property="realm", type="string", example="mi-reino"),
     *             @OA\Property(property="protocol", type="string", example="openid-connect"),
     *             @OA\Property(property="client_id", type="string", example="test-client"),
     *             @OA\Property(property="name", type="string", example="test-client"),
     *             @OA\Property(property="description", type="string", example="Cliente de prueba"),
     *             @OA\Property(property="root_url", type="string", format="url", example="http://mi-app.test"),
     *             @OA\Property(property="base_url", type="string", format="url", example="http://mi-app.test"),
     *             @OA\Property(
     *                 property="redirect_uris",
     *                 type="array",
     *                 @OA\Items(type="string", format="url"),
     *                 example={"http://mi-app.test/login/keycloak/callback"}
     *             ),
     *             @OA\Property(
     *                 property="web_origins",
     *                 type="array",
     *                 @OA\Items(type="string", format="url"),
     *                 example={"http://mi-app.test"}
     *             ),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="object",
     *                 @OA\Property(property="post_logout_redirect_uris", type="string", format="url", example="http://mi-app.test")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cliente creado exitosamente."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de datos"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */

    public function create(CreateClientRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->createClient($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/clients/update",
     *     summary="Actualizar un cliente existente en Keycloak",
     *     tags={"Clients"},
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"realm", "client_uuid"},
     *             @OA\Property(property="realm", type="string", example="mi-reino"),
     *             @OA\Property(property="client_uuid", type="string", example="b17ccfa3-8d0d-4ac7-912d-3c71f0c9d5e2"),

     *             @OA\Property(property="client_id", type="string", example="nuevo-client-id"),
     *             @OA\Property(property="name", type="string", example="Nuevo Nombre del Cliente"),
     *             @OA\Property(property="description", type="string", example="Descripción actualizada del cliente"),
     *             @OA\Property(property="enabled", type="boolean", example=true),

     *             @OA\Property(property="root_url", type="string", format="url", example="http://mi-app.test"),
     *             @OA\Property(property="base_url", type="string", format="url", example="http://mi-app.test"),
     *             @OA\Property(property="admin_url", type="string", format="url", example="http://mi-app.test/admin"),
     *             @OA\Property(property="origin", type="string", example="http://mi-app.test"),
     *             @OA\Property(
     *                 property="redirect_uris",
     *                 type="array",
     *                 @OA\Items(type="string", format="url"),
     *                 example={"http://mi-app.test/login/callback"}
     *             ),
     *             @OA\Property(
     *                 property="web_origins",
     *                 type="array",
     *                 @OA\Items(type="string", format="url"),
     *                 example={"http://mi-app.test"}
     *             ),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="object",
     *                 @OA\Property(property="post_logout_redirect_uris", type="string", format="url", example="http://mi-app.test/logout")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cliente actualizado exitosamente."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación o datos inválidos"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */

    public function update(UpdateClientRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->updateClient($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/clients/delete",
     *      tags={"Clients"},
     *      summary="Eliminar rol del cliente.",
     *      description="Endpoint para Eliminar un rol cliente.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_uuid",
     *         in="query",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Rol eliminado correctamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *              @OA\Property(property="username", type="string", example="juanperez"),
     *              @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error en los datos enviados",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Datos inválidos")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interno del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error interno")
     *          )
     *      )
     *  )
     */
    public function deleteClient(ClientByIdRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_repository->deleteClient($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

}
