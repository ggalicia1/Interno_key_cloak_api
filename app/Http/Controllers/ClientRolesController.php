<?php

namespace App\Http\Controllers;

use App\Contracts\Client\IClientRoles;
use App\Http\Requests\Client\ClientByIdRequest;
use App\Http\Requests\Client\Roles\RoleByNameRequest;
use App\Http\Requests\Client\Roles\RoleCreateRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientRolesController extends Controller
{
    protected IClientRoles $client_roles_repository;
    public function __construct(IClientRoles $client_roles_repository) {
        $this->client_roles_repository = $client_roles_repository;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/clients/roles",
     *     summary="Obtiene una lista de los roles por Id de cliente de un reino",
     *     tags={"Client Roles"},
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
     *      @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Tamaño del paginado",
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numero de Pagina.",
     *         required=false,
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
    public function clientRoles(ClientByIdRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_roles_repository->clientRoles($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/clients/roles/role-by-name",
     *     summary="Obtiene un roles por nombre.",
     *     tags={"Client Roles"},
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
     *      @OA\Parameter(
     *         name="role_name",
     *         in="query",
     *         description="Nombre del client",
     *         required=true,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Obtiene el role",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         ))
     *     )
     * )
     */
    public function clientRoleByName(RoleByNameRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_roles_repository->clientRoleByName($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/clients/roles/create",
     *     summary="Crear un nuevo rol de cliente.",
     *     tags={"Client Roles"},
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"realm",  "client_uuid", "name", "description"},
     *             @OA\Property(property="realm", type="string", example="mi-reino"),
     *             @OA\Property(property="client_uuid", type="string", example="1c742755-b30d-44a4-8b4b-4a60d6059a27"),
     *             @OA\Property(property="name", type="string", example="test-client-role"),
     *             @OA\Property(property="description", type="string", example="Role de prueba"),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="object",
     *                 @OA\Property(
     *                      property="permission",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                      example={"read"}
     *                  ),
     *                 @OA\Property(
     *                      property="permission_2",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                      example={"write"}
     *                  ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rol creado exitosamente."),
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
    public function createClientRole(RoleCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_roles_repository->createClientRole($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
    /**
     * @OA\Put(
     *     path="/api/v1/clients/roles/update",
     *     summary="Update un rol de cliente.",
     *     tags={"Client Roles"},
     *      security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"realm",  "client_uuid", "name", "description"},
     *             @OA\Property(property="realm", type="string", example="mi-reino"),
     *             @OA\Property(property="client_uuid", type="string", example="1c742755-b30d-44a4-8b4b-4a60d6059a27"),
     *             @OA\Property(property="name", type="string", example="test-client-role"),
     *             @OA\Property(property="description", type="string", example="Role de prueba"),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="object",
     *                 @OA\Property(
     *                      property="permission",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                      example={"read"}
     *                  ),
     *                 @OA\Property(
     *                      property="permission_2",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                      example={"write"}
     *                  ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Rol creado exitosamente."),
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
    public function updateClientRole(RoleCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_roles_repository->updateClientRole($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
    /**
     * @OA\Delete(
     *      path="/api/v1/clients/roles/delete",
     *      tags={"Client Roles"},
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
     *      @OA\Parameter(
     *         name="role_name",
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
    public function deleteClientRole(RoleByNameRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->client_roles_repository->deleteClientRole($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

}
