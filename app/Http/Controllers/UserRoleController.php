<?php

namespace App\Http\Controllers;

use App\Contracts\User\IUserRole;
use App\Http\Requests\User\Role\ClientRoleRequest;
use App\Response\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    protected IUserRole $user_role_repository;
    public function __construct(IUserRole $user_role_repository) {
        $this->user_role_repository = $user_role_repository;
    }

    /**
     * @OA\Get(
     *     path="/api/users/clients/roles",
     *     summary="Obtiene lista de roles por un usuario.",
     *     tags={"User Role"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="query",
     *         description="Id del reina",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_uuid",
     *         in="query",
     *         description="Uuid del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_uuid",
     *         in="query",
     *         description="Uuid del cliente",
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
    public function roles(ClientRoleRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_role_repository->roles($data);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Post(
     *      path="/api/users/clients/assign-role",
     *      tags={"User Role"},
     *      summary="Asignar un rol de cliente a un usuario.",
     *      description="Endpoint para asignar el rol de cliente a un usuario.",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"realm","user_uuid", "client_uuid", "role_name"},
     *              @OA\Property(property="realm", type="string", example="Interno"),
     *              @OA\Property(property="user_uuid", type="string", example="8b692f22-acb8-4944-9c2f-aec21c4da809"),
     *              @OA\Property(property="client_uuid", type="string", example="1c742755-b30d-44a4-8b4b-4a60d6059a27"),
     *              @OA\Property(property="role_name", type="boolean", example="Técnico de administración"),

     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
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
    public function assignRole(ClientRoleRequest $request) : JsonResponse
    {
        $data = $request->validated();
        list($status, $message, $response, $code) = $this->user_role_repository->addClientRole($data);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }

    /**
     * @OA\Delete(
     *      path="/api/users/clients/remove-role/{realm}/{user_id}/{client_id}/{role_name}",
     *      tags={"User Role"},
     *      summary="Eliminar usuario a un grupo.",
     *      description="Endpoint para Eliminar un asuario a un grupo.",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *         name="realm",
     *         in="path",
     *         description="Id del reino",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="client_id",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Parameter(
     *         name="role_name",
     *         in="path",
     *         description="Id del usuario",
     *         required=true,
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado correctamente",
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
    public function removeRole(string $realm, string $user_uuid, string $client_uuid, string $role_name) : JsonResponse
    {

        list($status, $message, $response, $code) = $this->user_role_repository->removeClientRole($realm, $user_uuid, $client_uuid, $role_name);
        if(!$status && $response != null) return Response::errorMessage($status, $message, $response, $code);
        if(!$status) return Response::error($status, $message, $code);
        return Response::success($status, $message, $response, $code);
    }
}
